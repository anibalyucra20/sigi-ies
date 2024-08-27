<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['acad_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_ACAD');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    $id_periodo_act = $_SESSION['acad_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['acad_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $id_prog = base64_decode($_GET['data']);
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $cont_res = mysqli_num_rows($b_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    $nro_calificacion = base64_decode($_GET['data2']);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        require_once('../librerias/tcpdf/tcpdf.php');

        setlocale(LC_ALL, "es_ES");

        // Extend the TCPDF class to create custom Header and Footer
        class MYPDF extends TCPDF
        {

            // Page footer
            public function Footer()
            {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'I', 8);
                // Page number
                $this->Cell(0, 10, '´Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

        //buscamos los datos para imprimir

        //buscar datos de institucion
        $b_datos_insti = buscarDatosInstitucional($conexion);
        $r_b_datos_insti = mysqli_fetch_array($b_datos_insti);

        //buscar periodo 
        $b_perio = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
        $r_b_perio = mysqli_fetch_array($b_perio);
        //buscar unidad didactica
        $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
        $r_b_ud = mysqli_fetch_array($b_ud);
        //buscar semestre
        $b_sem = buscarSemestreById($conexion, $r_b_ud['id_semestre']);
        $r_b_sem = mysqli_fetch_array($b_sem);
        //buscar modulo profesional
        $b_mod = buscarModuloFormativoById($conexion, $r_b_sem['id_modulo_formativo']);
        $r_b_mod = mysqli_fetch_array($b_mod);
        //buscar programa de estudio
        $b_pe = buscarProgramaEstudioById($conexion, $r_b_mod['id_programa_estudio']);
        $r_b_pe = mysqli_fetch_array($b_pe);

        //bsucar programa-sede
        $b_pe_sede = buscarProgramaEstudioSedeById($conexion, $res_b_prog['id_programa_sede']);
        $rb_pe_sede = mysqli_fetch_array($b_pe_sede);

        //buscamos el silabo y sus datos
        $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
        $r_b_silabo = mysqli_fetch_array($b_silabo);
        $id_silabo = $r_b_silabo['id'];
        //buscar datos de docente
        $b_docente = buscarUsuarioById($conexion, $res_b_prog['id_docente']);
        $r_b_docente = mysqli_fetch_array($b_docente);
        //buscar datos de coordinador de area
        $b_coordinador = buscarUsuarioCoordinador_sedeAndPe($conexion, $rb_pe_sede['id_sede'], $rb_pe_sede['id_programa_estudio']);
        $r_b_coordinador = mysqli_fetch_array($b_coordinador);
        //buscar datos de director
        $b_director = buscarUsuarioById($conexion, $r_b_perio['director']);
        $r_b_director = mysqli_fetch_array($b_director);

        switch ($res_b_prog['turno']) {
            case 'M':
                $turno = 'MAÑANA';
                break;
            case 'T':
                $turno = 'TARDE';
                break;
            case 'N':
                $turno = 'NOCHE';
                break;
            default:
                $turno = '';
                break;
        }

        //buscamos la cantidad de indicadores para definir la cantidad de calificaciones
        $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        $total_indicadores = 0;
        while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
            $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
            $cont_indicadores = mysqli_num_rows($b_indicador_capac);
            $total_indicadores = $total_indicadores + $cont_indicadores;
        };

        //funcion para cambia numeros a romanos
        function a_romano($integer, $upcase = true)
        {
            $table = array(
                'M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100,
                'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9,
                'V' => 5, 'IV' => 4, 'I' => 1
            );
            $return = '';
            while ($integer > 0) {
                foreach ($table as $rom => $arb) {
                    if ($integer >= $arb) {
                        $integer -= $arb;
                        $return .= $rom;
                        break;
                    }
                }
            }
            return $return;
        }

        $n_modulo = a_romano($r_b_mod['nro_modulo']);



        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("calificaciones - " . $r_b_ud['nombre']);
        $pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont('helvetica');
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->SetFont('helvetica', '', 11);
        $pdf->AddPage('L', 'A3');



        $detalle = '';
        $encabezado = '';

        $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
        $r_b_det_mat = mysqli_fetch_array($b_detalle_mat);
        $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
        $r_b_calificacion = mysqli_fetch_array($b_calificacion);
        $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
        $cont_col = 0;
        while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
            $b_critt_eva = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);
            $c_b_critt = mysqli_num_rows($b_critt_eva) + 1;
            $detalle .= '<td colspan="' . $c_b_critt . ' " style=" text-align: center;">
                        <center>' . $r_b_evaluacion['detalle'] . '<br>Ponderado: ' . $r_b_evaluacion['ponderado'] . '% </center>
                        </td>
            ';

            while ($r_b_critt_eva = mysqli_fetch_array($b_critt_eva)) {
                $encabezado .= '
                <td height="auto" style=" text-align: center;">
                    <center>
                        <p>' . $r_b_critt_eva['detalle'] . '</p>
                    </center>

                </td>
                ';
            }
            $encabezado .= '
            <td height="auto" bgcolor="#D5D2D2" style=" text-align: center;">
                <center>
                    <p>Promedio </p>
                </center>
            </td>
            ';

            $cont_col += $c_b_critt;
        }

        $contenido = '';
        $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
        $orden = 0;
        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
            $orden++;
            $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
            $r_b_mat = mysqli_fetch_array($b_matricula);
            $b_estudiante = buscarUsuarioById($conexion, $r_b_mat['id_estudiante']);
            $r_b_est = mysqli_fetch_array($b_estudiante);

            $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
            $r_b_silabo = mysqli_fetch_array($b_silabo);

            $cont_inasistencia = contar_inasistencia($conexion, $r_b_silabo['id'], $r_b_est['id']);
            if ($cont_inasistencia > 0) {
                $porcent_ina = round($cont_inasistencia * 100 / 16);
            } else {
                $porcent_ina = 0;
            }

            if ($r_b_mat['licencia'] != "") {
                $si_falta = '';
                $si_licencia = ' readonly title="Licencia"';
                $nom_ap = '<font color="red">' . $r_b_est['apellidos_nombres'] . ' (Licencia)</font>';
                $dni = '<font color="red">' . $r_b_est['dni'] . '</font>';
                $fila = ' style="background-color:pink"';
            } elseif ($porcent_ina >= 30) {
                $si_licencia = ' readonly title="Inasistencia mayor de 30%"';
                $si_falta = 'si';
                $nom_ap = '<font color="red">' . $r_b_est['apellidos_nombres'] . '</font>';
                $dni = '<font color="red">' . $r_b_est['dni'] . '</font>';
                $fila = ' style="background-color:pink"';
            } else {
                $si_licencia = "";
                $si_falta = '';
                $nom_ap = $r_b_est['apellidos_nombres'];
                $dni = $r_b_est['dni'];
                $fila = '';
            }

            $contenido .= '
            <tr ' . $fila . '>
            <td style=" text-align: center;">' . $r_b_det_mat['orden'] . '</td>
            <td>' . $dni . '</td>
            <td>' . $nom_ap . '</td>
            ';

            $suma_notas = 0;
            $cont_notas = 0;
            $suma_calificacion = 0;
            $opcion = 1;

            //buscamos las evaluaciones
            $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
            while ($r_b_calificacion = mysqli_fetch_array($b_calificacion)) {
                $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                $suma_evaluacion = 0;
                while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                    $id_evaluacion = $r_b_evaluacion['id'];

                    //buscamos los criterios de evaluacion
                    $b_criterio_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $id_evaluacion);
                    $suma_criterios = 0;
                    $cont_c = 0;
                    while ($r_b_criterio_evaluacion = mysqli_fetch_array($b_criterio_evaluacion)) {
                        if (is_numeric($r_b_criterio_evaluacion['calificacion'])) {
                            $suma_criterios += $r_b_criterio_evaluacion['calificacion'];
                            $cont_c += 1;
                        }
                        if ($r_b_criterio_evaluacion['calificacion'] > 12 && $r_b_criterio_evaluacion['calificacion'] <= 20) {
                            $colort = 'style="color:blue; "';
                        } else {
                            $colort = 'style="color:red; "';
                        }

                        if ($editar_doc) {
                            $contenido .= '<td style=" text-align: center;"><label ' . $colort . $si_licencia . ' >' . $r_b_criterio_evaluacion['calificacion'] . '</label></td>';
                        } else {
                            $contenido .= '<td style=" text-align: center;"><label ' . $colort . '>' . $r_b_criterio_evaluacion['calificacion'] . '</label></td>';
                        }
                    }

                    if ($cont_c > 0) {
                        $calificacion = round($suma_criterios / $cont_c);
                    } else {
                        $calificacion = round($suma_criterios);
                    }
                    if ($calificacion == 0) {
                        $mostrar = "";
                    } else {
                        $mostrar = round($calificacion);
                    }
                    if ($mostrar > 12) {
                        $contenido .= '<td style=" text-align: center;"><center><font color="blue">' . $mostrar . '</font></center></td>';
                    } else {
                        $contenido .= '<td style=" text-align: center;"><center><font color="red">' . $mostrar . '</font></center></td>';
                    }
                    if (is_numeric($r_b_evaluacion['ponderado'])) {
                        $suma_evaluacion += ($r_b_evaluacion['ponderado'] / 100) * $calificacion;
                    }
                }
                if ($suma_evaluacion != 0) {
                    $calificacion_e = round($suma_evaluacion);
                } else {
                    $calificacion_e = "";
                }
                if ($si_falta != '') {
                    $contenido .= '<td style=" text-align: center;"><center><font color="red">0</font></center></td>';
                } elseif ($si_licencia != '') {
                    $contenido .= '<td style=" text-align: center;"><center><font></font></center></td>';
                } else {
                    if ($calificacion_e > 12) {
                        $contenido .= '<td style=" text-align: center;"><center><font color="blue">' . $calificacion_e . '</font></center></td>';
                    } else {
                        $contenido .= '<td style=" text-align: center;"><center><font color="red">' . $calificacion_e . '</font></center></td>';
                    }
                }
            }
            $contenido .= '</tr>';
        }
        $content_one = '';
        $content_one .= '
        <table border="1" width="100%" cellspacing="0" cellpadding="3">
        
        <tr class="headings">
            <th rowspan="3" style=" text-align: center; width: 5%;"><b>ORDEN</b></th>
            <th rowspan="3" style=" text-align: center; width: 8%;"><b>DNI</b></th>
            <th rowspan="3" style=" text-align: center; width: 25%;" ><b>APELLIDOS Y NOMBRES</b></th>
            <th colspan="' . $cont_col . '" style=" text-align: center; width: 54%;"><b>EVALUACIÓN</b></th>
            <th rowspan="3" style=" text-align: center; width: 8%;"><b>PROMEDIO DE CALIFICACIÓN</b></th>
        </tr>
        
        <tr>
        ' . $detalle . '
        </tr>
        <tr>
        ' . $encabezado . '
        </tr>
        ' . $contenido . '
        </table>
        ';

        $logos = '
        <table>
        <tr >
                        <td width="50%" colspan="2" height="40px"></td>
                        <td width="50%" colspan="2" align="right"><img src="../images/logo.png" alt="" height="30px"></td>
                    </tr>
                    <tr>
                    <th colspan="4" style="text-align: center; font-size:15;" ><b>REGISTRO AUXILIAR - Indicador de Logro'.  $nro_calificacion . ' - ' . $r_b_ud['nombre'].'</b></th>
                </tr>
        </table>
        ';
        //echo $content_one;
        $pdf->writeHTML($logos);
        $pdf->writeHTML($content_one);
        $pdf->Output('califcaciones - ' . $r_b_ud['descripcion'] . '.pdf', 'I');
    }
}
