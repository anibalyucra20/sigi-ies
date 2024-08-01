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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 4) {
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

        $id_est = $_POST['data'];

        //buscar matricula de estudiante
        $b_mat = buscarMatriculaByEstPeriodoSedePe($conexion, $id_est, $id_periodo_act, $id_sede_act, $rb_usuario['id_programa_estudios']);
        $r_b_mat = mysqli_fetch_array($b_mat);
        $id_mat_est = $r_b_mat['id'];

        $b_estudiante = buscarUsuarioById($conexion, $id_est);
        $r_b_estudiante = mysqli_fetch_array($b_estudiante);

        $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
        $cant_ud_mat = mysqli_num_rows($b_det_mat);
        $cont_ind_capp = 0;
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
            $id_prog = $r_b_det_mat['id_programacion_ud'];
            $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
            $r_b_prog = mysqli_fetch_array($b_prog_ud);
            $id_udd = $r_b_prog['id_unidad_didactica'];
            //BUSCAR UD
            $b_uddd = buscarUnidadDidacticaById($conexion, $id_udd);
            $r_b_udd = mysqli_fetch_array($b_uddd);
            //buscar capacidad
            $cont_ind_logro_cap_ud = 0;
            $b_cap_ud = buscarCapacidadByIdUd($conexion, $id_udd);
            while ($r_b_cap_ud = mysqli_fetch_array($b_cap_ud)) {
                $id_cap_ud = $r_b_cap_ud['id'];
                // buscar indicadores de capacidad
                $b_ind_l_cap_ud = buscarIndCapacidadByIdCapacidad($conexion, $id_cap_ud);
                $cant_id_cap_ud = mysqli_num_rows($b_ind_l_cap_ud);
                $cont_ind_logro_cap_ud += $cant_id_cap_ud;
            }
            $cont_ind_capp += $cont_ind_logro_cap_ud;
        }
        $total_columnas = $cont_ind_capp + $cant_ud_mat + 11;

        $b_datos_sistema = buscarDatosSistema($conexion);
        $r_b_datos_sistema = mysqli_fetch_array($b_datos_sistema);




        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Reporte Individual - " . $r_b_estudiante['apellidos_nombres']);
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
        $pdf->AddPage('L', 'A4');

        $text_size = 7;

        $contenido_encabezado .= '';
        $udssss = '';
        $orden_ud = 0;
        $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
            $orden_ud++;
            $id_prog = $r_b_det_mat['id_programacion_ud'];
            $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
            $r_b_prog = mysqli_fetch_array($b_prog_ud);
            $id_udd = $r_b_prog['id_unidad_didactica'];
            //BUSCAR UD
            $b_uddd = buscarUnidadDidacticaById($conexion, $id_udd);
            $r_b_udd = mysqli_fetch_array($b_uddd);
            //buscar capacidad
            $cont_ind_logro_cap_ud = 0;
            $b_cap_ud = buscarCapacidadByIdUd($conexion, $id_udd);
            while ($r_b_cap_ud = mysqli_fetch_array($b_cap_ud)) {
                $id_cap_ud = $r_b_cap_ud['id'];
                // buscar indicadores de capacidad
                $b_ind_l_cap_ud = buscarIndCapacidadByIdCapacidad($conexion, $id_cap_ud);
                $cant_id_cap_ud = mysqli_num_rows($b_ind_l_cap_ud);
                $cont_ind_logro_cap_ud += $cant_id_cap_ud;
            }
            $udssss .= '<tr><td width="10%"><font size="' . $text_size . '">U.D. ' . $orden_ud . '</font></td><td width="90%"><font size="' . $text_size . '">' . $r_b_udd['nombre'] . '</font></td></tr>';
            $contenido_encabezado .= '
            <th colspan="' . $cont_ind_logro_cap_ud . '" align="center"><font size="' . $text_size . '">U.D. ' . $orden_ud . '</font>
            </th>
            <th>
                <font size="9"><font size="6" align="center"><b>PROMEDIO</b></font></font>
            </th>';
        }


        //buscar estudiante para su id
        $b_est = buscarUsuarioById($conexion, $id_est);
        $r_b_est = mysqli_fetch_array($b_est);


        $contenido_calificaciones = '';
        //buscar si estudiante esta matriculado en una unidad didactica
        $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
        $suma_califss = 0;
        $suma_ptj_creditos = 0;
        $cont_ud_desaprobadas = 0;
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
            $id_det_mat = $r_b_det_mat['id'];


            //echo "<td>SI</td>";

            //buscar las calificaciones
            $b_calificaciones = buscarCalificacionByIdDetalleMatricula($conexion, $id_det_mat);

            $suma_calificacion = 0;
            $cont_calif = 0;
            while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                $id_calificacion = $r_b_calificacion['id'];
                //buscamos las evaluaciones
                $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                if ($suma_evaluacion != 0) {
                    $cont_calif += 1;
                    $suma_calificacion += $suma_evaluacion;
                    $suma_evaluacion = round($suma_evaluacion);

                    if ($suma_evaluacion > 12) {
                        $contenido_calificaciones .= '<th><font color="blue" size="' . $text_size . '">' . $suma_evaluacion . '</font></th>';
                    } else {
                        $contenido_calificaciones .= '<th><font color="red" size="' . $text_size . '">' . $suma_evaluacion . '</font></th>';
                    }
                } else {
                    $suma_evaluacion = "";
                    $contenido_calificaciones .= '<th></th>';
                }
            }


            if ($cont_calif > 0) {
                $calificacion = round($suma_calificacion / $cont_calif);
            } else {
                $calificacion = round($suma_calificacion);
            }
            if ($calificacion != 0) {
                $calificacion = round($calificacion);
            } else {
                $calificacion = "";
            }
            //buscamos si tiene recuperacion
            if ($r_b_det_mat['recuperacion'] != '') {
                $calificacion = $r_b_det_mat['recuperacion'];
            }

            if ($calificacion > 12) {
                $contenido_calificaciones .= '<th align="center" bgcolor="#BEBBBB"><font color="blue" size="' . $text_size . '">' . $calificacion . '</font></th>';
            } else {
                $contenido_calificaciones .= '<th align="center" bgcolor="#BEBBBB"><font color="red" size="' . $text_size . '">' . $calificacion . '</font></th>';
                $cont_ud_desaprobadas += 1;
            }
            if (is_numeric($calificacion)) {
                $suma_califss += $calificacion;
                $suma_ptj_creditos += $calificacion * $r_bb_ud['creditos'];
            } else {
                $suma_ptj_creditos += 0 * $r_bb_ud['creditos'];
            }
        }
        $contenido_calificaciones .= '<td align="center" colspan="3"><font color="black" size="' . $text_size . '">' . $suma_califss . '</font></td>';
        $contenido_calificaciones .= '<td align="center" colspan="3"><font color="black" size="' . $text_size . '">' . $suma_ptj_creditos . '</font></td>';
        if ($cont_ud_desaprobadas == 0) {
            $contenido_calificaciones .= '<td align="center" colspan="5"><font color="black" size="' . $text_size . '">Promovido</font></td>';
        } elseif ($cont_ud_desaprobadas <= $min_ud_desaprobar) {
            $contenido_calificaciones .= '<td align="center" colspan="5"><font color="black" size="' . $text_size . '">Repite U.D. del Módulo Profesional</font></td>';
        } else {
            $contenido_calificaciones .= '<td align="center" colspan="5"><font color="black" size="' . $text_size . '">Repite el Módulo Profesional</font></td>';
        }



        // para asistencias


        $asistencias_header = '';
        for ($i = 1; $i <= $r_b_datos_sistema['cant_semanas']; $i++) {
            $asistencias_header .= '<th align="center" ><font size="5"><b>Semana <br>' . $i . '</b></font></th>';
        }

        $asistencias_body = '';
        $orden_ud_a = 0;
        $b_detalle_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
            $id_prog = $r_b_det_mat['id_programacion_ud'];
            $asistencias_body .= "<tr>";
            $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
            $r_b_prog = mysqli_fetch_array($b_prog_ud);

            $b_ud = buscarUnidadDidacticaById($conexion, $r_b_prog['id_unidad_didactica']);
            $r_b_ud = mysqli_fetch_array($b_ud);


            $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
            $r_b_silabo = mysqli_fetch_array($b_silabo);
            $b_prog_act = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo['id']);
            $orden_ud_a++;
            $asistencias_body .= '<td colspan="2"><font size="'. $text_size . '">U.D. ' . $orden_ud_a . '</font></td>';
            $cont_inasistencia = 0;
            $cont_asis = 0;
            while ($res_b_prog_act = mysqli_fetch_array($b_prog_act)) {
                
                // buscamos la sesion que corresponde
                $id_act = $res_b_prog_act['id'];
                $b_sesion = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_act);
                $r_b_sesion = mysqli_fetch_array($b_sesion);
                $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $r_b_sesion['id'], $r_b_det_mat['id']);
                $r_b_asistencia = mysqli_fetch_array($b_asistencia);
                $cont_asis += mysqli_num_rows($b_asistencia);

                if ($r_b_asistencia['asistencia'] == "P") {
                    $asistencias_body .= "<td><font color='blue' size='" . $text_size . "'>" . $r_b_asistencia['asistencia'] . "</font></td>";
                } elseif ($r_b_asistencia['asistencia'] == "F") {
                    $asistencias_body .= "<td><font color='red' size='" . $text_size . "'>" . $r_b_asistencia['asistencia'] . "</font></td>";
                    $cont_inasistencia += 1;
                } else {
                    $asistencias_body .= "<td></td>";
                }
            }
            if ($cont_inasistencia > 0) {
                $porcent_ina = $cont_inasistencia * 100 / $cont_asis;
            } else {
                $porcent_ina = 0;
            }
            if (round($porcent_ina) >= 30) {
                $asistencias_body .= '<td colspan="2"><font color="red" size="' . $text_size . '">' . round($porcent_ina) . '%</font></td>';
            } else {
                $asistencias_body .= '<td colspan="2"><font color="blue" size="' . $text_size . '">' . round($porcent_ina) . '%</font></td>';
            }

            $asistencias_body .= "</tr>";
        }







        // imprimir cuerpo general

        $content_one = '';
        $content_one .= '
        <table border="0" width="100%" cellspacing="0" cellpadding="0.1">
        <tr>
            <td width="40%"><img src="../images/logo_minedu.jpeg" alt="" height="30px"></td>
            <td width="10%"></td>
            <td width="50%" align="right"><img src="../images/logo.png" alt="" height="30px"></td>
        </tr>
        </table>
        <table border="" width="100%" cellspacing="0" cellpadding="5" >
            <tr><td align="center">AVANCE DE CALIFICACIONES - ' . $r_b_est['apellidos_nombres'] . '</td></tr>
        </table>
        <br>    <br>
            <table border="0" width="100%" cellspacing="0" cellpadding="0" >
            ' . $udssss . '
        </table>
        <table border="1" width="100%" cellspacing="0" cellpadding="3" >
                <tr>
                    <th colspan="' . $total_columnas . '" bgcolor="#CCC" align="center" width="100%"><font size="' . $text_size . '"><b>CALIFICACIONES - UNIDADES DIDACTICAS</b></font>
                    </th>
                </tr>
                <tr>
                    ' . $contenido_encabezado . '
                    <th colspan="3"><font size="' . $text_size . '"><b>Ptj. Total</b></font></th>
                    <th colspan="3"><font size="' . $text_size . '"><b>Ptj. Créditos</b></font></th>
                    <th colspan="5" align="center" ><font size="' . $text_size . '"><b>CONDICIÓN</b></font></th>
                </tr>
                <tr>
                ' . $contenido_calificaciones . '
                </tr>
        </table>
        

    ';
        //echo $content_one;

        $pdf->writeHTML($content_one);


        $reporte_asistencias = '
                                        <table border="1" style="width:100%" cellspacing="0" cellpadding="2" >
                                            <thead>
                                                <tr>
                                                    <th bgcolor="#CCC" align="center" colspan="' . ($r_b_datos_sistema['cant_semanas'] + 4) . '">
                                                        <center>ASISTENCIA</center>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2"><font size="6" >UNIDAD DIDÁCTICA</font></th>
                                                        ' . $asistencias_header . '                                                    
                                                    <th colspan="2"><font size="6">Inasistencia</font></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ' . $asistencias_body . '
                                            </tbody>
                                        </table>
        
        ';

        $pdf->writeHTML($reporte_asistencias);







        $pdf->Output('Reporte Individual - ' . $r_b_estudiante['apellidos_nombres'] . '.pdf', 'I');
    }
}
