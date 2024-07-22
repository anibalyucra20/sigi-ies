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

    $id_prog = $_POST['data'];
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $cont_res = mysqli_num_rows($b_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_usuario'] == $id_usuario) {
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
        $b_coordinador = buscarUsuarioCoordinador_sedeAndPe($conexion,$rb_pe_sede['id_sede'],$rb_pe_sede['id_programa_estudio']);
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

        $fechas = '';
        $cont_asis = '';
        $cont = 1;
        $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
            $cont_asis .= '<tr>';
            $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
            $r_b_matricula = mysqli_fetch_array($b_matricula);
            $b_estudiante = buscarUsuarioById($conexion, $r_b_matricula['id_estudiante']);
            $r_b_estudiante = mysqli_fetch_array($b_estudiante);
            if ($r_b_matricula['licencia'] != "") {
                $licencia = 1;
            } else {
                $licencia = 0;
            }
            $b_prog_act_sil = buscarProgActividadesSilaboByIdSilabo_16($conexion, $id_silabo);
            $asis = '';
            $cont_inasistencia = 0;
            while ($r_prog_act_sil = mysqli_fetch_array($b_prog_act_sil)) {
                $id_prog_act_s = $r_prog_act_sil['id'];
                $b_sesion_aprendizaje = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_prog_act_s);
                $r_b_sesion_apr = mysqli_fetch_array($b_sesion_aprendizaje);
                $id_sesion_apr = $r_b_sesion_apr['id'];
                $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $id_sesion_apr, $r_b_det_mat['id']);
                $r_b_asistencia = mysqli_fetch_array($b_asistencia);
                $dataaa = $r_b_sesion_apr['fecha_desarrollo'];
                $asistencia = $r_b_asistencia['asistencia'];

                $fechas .= '<td ><font size="10">' . $dataaa . '</font></td>';
                if ($licencia) {
                } else {
                    if ($asistencia == "F") {
                        $asis .= '<td align="center"><font size="10" color="red">' . $asistencia . '</font></td>';
                    } else {
                        $asis .= '<td align="center"><font size="10" color="blue">' . $asistencia . '</font></td>';
                    }
                }
                if ($r_b_asistencia['asistencia'] == "F") {
                    $cont_inasistencia += 1;
                }
            }
            if ($cont_inasistencia > 0) {
                $porcent_ina = round($cont_inasistencia * 100 / 16);
            } else {
                $porcent_ina = 0;
            }
            if ($licencia) {
                $cont_asis .= '<td align="center" height="5px"><font size="10">' . $cont . '</font></td><td colspan="18" align="center" height="5px"><font size="10">Licencia</font></td>';
            } else {
                if ($porcent_ina > 30) {
                    $cont_asis .= '<td align="center" height="5px"><font size="10">' . $cont . '</font></td>' . $asis . '<td align="center"><font size="10" color="red">' . $cont_inasistencia . '</font></td><td align="center"><font size="10" color="red">' . $porcent_ina . '</font></td>';
                } else {
                    $cont_asis .= '<td align="center" height="5px"><font size="10">' . $cont . '</font></td>' . $asis . '<td align="center"><font size="10" color="blue">' . $cont_inasistencia . '</font></td><td align="center"><font size="10" color="blue">' . $porcent_ina . '</font></td>';
                }
            }


            $cont += 1;
            $cont_asis .= '</tr>';
        }
        $m_rest = '';
        for ($i = $cont; $i <= 40; $i++) {
            $vacios = '';
            for ($j = 1; $j <= 16; $j++) {
                $vacios .= '<td></td>';
            }
            $m_rest .= '<tr><td align="center"><font size="10">' . $i . '</font></td>' . $vacios . '<td></td><td></td></tr>';
        }

        // mostrar indicadores
        $capacid = '';
        $num_contr_ind = 1;
        $b_capacidad = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        while ($r_b_capacidad = mysqli_fetch_array($b_capacidad)) {
            $b_indicador_capaci = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidad['id']);
            while ($r_b_indicador_capaci = mysqli_fetch_array($b_indicador_capaci)) {
                $detalle_ind_log = $r_b_indicador_capaci['descripcion'];
                $capacid .= '<tr><td width="8%" align="center">' . $num_contr_ind . '</td><td width="92%">' . $detalle_ind_log . '</td></tr>';
                $num_contr_ind += 1;
            }
        };
        $vacios_ind_log = '';
        for ($i = $total_indicadores + 1; $i <= 12; $i++) {
            $vacios_ind_log .= '<tr><td height="60px" align="center">' . $i . '</td><td></td></tr>';
        }


        $content_one = '';
        $content_one .= '
    
        <table border="0" width="100%" cellspacing="0" cellpadding="3">
        <tr>
            <td width="34%" valign="top">
                <table width=100% border="0.2" cellspacing="0" cellpadding="0.5">
                    <tr>
                        <td colspan="19" align="center">CONTROL DE ASISTENCIA</td>
                    </tr>
                    <tr>
                       <td rowspan="2" width="5%" style="text-orientation: sideways;">No</td>
                       <td colspan="16" width="85%" align="center"> FECHAS <br> Registra el dia y mes de la asistencia</td>
                       <td rowspan="2" width="5%"><font size="8">Total Inasistencia</font></td>
                       <td rowspan="2" width="5%"><font size="8">% Inasistencia</font></td>
                    </tr>
                    <tr>
                        ' . $fechas . '
                    </tr>
                    
                        ' . $cont_asis . $m_rest . '
                    
                </table>
            </td>
            <td width="33%" valign="top">
                <table width=100% border="1" cellspacing="0" cellpadding="3">
                    <tr>
                        <td align="center" colspan="2">INDICADORES DE LOGRO</td>
                    </tr>
                    
                    ' . $capacid . $vacios_ind_log . '
                </table>
            </td>
            <td width="33%" valign="top">
                <table width=100% border="0">
                    <tr >
                        <td width="50%" colspan="2" height="40px"><img src="../img/logo_minedu.jpeg" alt="" height="30px"></td>
                        <td width="50%" colspan="2" align="right"><img src="../img/logo.jpeg" alt="" height="30px"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" colspan="2" height="150px" style="border: black 3px solid;" align="center"><font size="25"><br>REGISTRO DE EVALUACION Y NOTAS ' . $r_b_perio['nombre'] . '</font></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" colspan="2" align="center"><font size="14"><b><br>PROGRAMA DE ESTUDIOS:</b></font></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%" ></td>
                        <td width="80%" colspan="2" align="center"><font size="14"><br>' . $r_b_pe['nombre'] . '<br></font></td>
                        <td width="10%" ></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="60%"><b>MODULO FORMATIVO NRO</b></td>
                        <td width="20%">: ' . $n_modulo . '<br></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%" ></td>
                        <td width="80%" colspan="2"><b>MODULO FORMATIVO:<br></b></td>
                        <td width="10%" ></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="80%" align="center">' . $r_b_mod['descripcion'] . '<br></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" align="center"><font size="15"><b>UNIDAD DIDACTICA:<br></b></font> </td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" align="center">' . $r_b_ud['nombre'] . '<br><br></td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="60%"><b>PERIODO ACADEMICO</b> : ' . $r_b_perio['nombre'] . '<br></td>
                        <td width="20%"></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="60%"><b>CREDITOS</b> : ' . $r_b_ud['creditos'] . '<br></td>
                        <td width="20%"></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="60%"><b>HORAS POR SEMANA </b>: ' . ($r_b_ud['horas'] / 16) . '<br></td>
                        <td width="20%"></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td width="10%"></td>
                        <td width="60%"><b>DOCENTE </b>: ' . $r_b_docente['apellidos_nombres'] . '<br></td>
                        <td width="20%"></td>
                        <td width="10%"></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td ><b>SECCION </b>: '.$res_b_prog['seccion'].'<br></td>
                        <td ></td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td ><b>TURNO</b> : '.$turno.'<br></td>
                        <td ></td>
                        <td ></td>
                    </tr>
                    <tr>
                        <td ></td>
                        <td colspan="2" align="center"><br><br><br><br><br><br><br>.................................<br>Firma del docente</td>
                        <td ></td>
                    </tr>
                </table>
            </td>
        </tr>
                
          ';

        $content_one .= '</table>';
        $pdf->writeHTML($content_one);


        $pdf->AddPage();


        function generateRow()
        {
            include('../include/conexion.php');
            include_once('include/busquedas.php');
            $id_prog = $_POST['data'];
            $contents_extra = '';
            $content = '';
            $b_prog = buscarProgramacionUdById($conexion, $id_prog);
            $res_b_prog = mysqli_fetch_array($b_prog);
            $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
            $total_indicadores = 0;
            while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
                $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
                $cont_indicadores = mysqli_num_rows($b_indicador_capac);
                $total_indicadores = $total_indicadores + $cont_indicadores;
            };

            $b_det_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
            $ord = 1;
            while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {

                $id_mat = $r_b_det_mat['id_matricula'];
                $b_mat = buscarMatriculaById($conexion, $id_mat);
                $r_b_mat = mysqli_fetch_array($b_mat);
                $id_est = $r_b_mat['id_estudiante'];
                $b_est = buscarUsuarioById($conexion, $id_est);
                $r_b_est = mysqli_fetch_array($b_est);

                $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
                $r_b_silabo = mysqli_fetch_array($b_silabo);

                $cont_inasistencia = contar_inasistencia($conexion, $r_b_silabo['id'], $r_b_est['id']);
                if ($cont_inasistencia > 0) {
                    $porcent_ina = round($cont_inasistencia * 100 / 16);
                } else {
                    $porcent_ina = 0;
                }

                $notass = '';
                if ($r_b_mat['licencia'] != "") {
                    $licencia = 1;
                } else {
                    $licencia = 0;
                }



                $b_calif = buscarCalificacionByIdDetalleMatricula($conexion, $r_b_det_mat['id']);
                $suma_calificacion = 0;
                $cont_calif = 0;
                while ($r_b_calif = mysqli_fetch_array($b_calif)) {


                    $suma_evaluacion = calc_evaluacion($conexion, $r_b_calif['id']);


                    $suma_calificacion += $suma_evaluacion;
                    if ($suma_evaluacion > 0) {
                        $cont_calif += 1;
                    }
                    /*if (is_numeric($r_b_calif['ponderado'])) {
                    $suma_calificacion += ($r_b_calif['ponderado'] / 100) * $suma_evaluacion;
                }*/

                    if ($suma_evaluacion != 0) {
                        $calificacion = round($suma_evaluacion);
                    } else {
                        $calificacion = "";
                    }

                    if ($calificacion > 12) {
                        $notass .= '<td align="center" ><font color="blue" size="10">' . $calificacion . '</font></td>';
                    } else {
                        $notass .= '<td align="center" ><font color="red" size="10">' . $calificacion . '</font></td>';
                    }
                }
                if ($cont_calif > 0) {
                    $suma_calificacion = round($suma_calificacion / $cont_calif);
                } else {
                    $suma_calificacion = round($suma_calificacion);
                }
                if ($suma_calificacion != 0) {
                    $calificacion = round($suma_calificacion);
                } else {
                    $calificacion = "";
                }
                // columnas extra para indicadores
                $n_conts = $total_indicadores + 1;
                for ($i = $n_conts; $i <= 12; $i++) {
                    $notass .= '<td align="center"></td>';
                }


                if ($licencia) {
                    $recuperacion = '<td align="center" ></td>';
                    $promedio = '<td align="center" size="10">Licencia</td>';
                    $promedio_final = '<td align="center" size="10">Licencia</td>';
                } else {
                    if ($r_b_det_mat['recuperacion'] > 12) {
                        $recuperacion = '<td align="center" ><font color="blue" size="10">' . $r_b_det_mat['recuperacion'] . '</font></td>';
                    } else {
                        $recuperacion = '<td align="center" ><font color="red" size="10">' . $r_b_det_mat['recuperacion'] . '</font></td>';
                    }

                    if ($porcent_ina > 30) {
                        $calificacion = 0;
                    } else {
                        # code...
                    }
                    if ($calificacion > 12) {
                        $promedio = '<td align="center" ><font color="blue" size="10">' . $calificacion . '</font></td>';
                    } else {
                        $promedio = '<td align="center" ><font color="red" size="10">' . $calificacion . '</font></td>';
                    }


                    if ($r_b_det_mat['recuperacion'] != '') {
                        $calificacion_final = $r_b_det_mat['recuperacion'];
                    } else {
                        $calificacion_final = $calificacion;
                    }

                    if ($calificacion_final > 12) {
                        $promedio_final = '<td align="center" ><font color="blue" size="10">' . $calificacion_final . '</font></td>';
                    } else {
                        $promedio_final = '<td align="center" ><font color="red" size="10">' . $calificacion_final . '</font></td>';
                    }
                }






                $content .= '
            <tr>
                <td align="center" ><font size="10">' . $ord . '</font></td>
                <td ><font size="10">' . $r_b_est['apellidos_nombres'] . '</font></td>
                ' . $notass . $promedio . $recuperacion . $promedio_final . '
            </tr>
            ';

                $ord += 1;
            }
            // campos extra debajo de la relacion
            for ($i = $ord; $i <= 40; $i++) {
                $contents_extra .= '<tr><td align="center" height="5px"><font size="10">' . $i . '</font></td><td></td>';
                for ($j = 0; $j <= 12; $j++) {
                    $contents_extra .= '<td></td>';
                }

                $contents_extra .= '<td></td><td></td><td></td></tr>';
            }
            $content .= $contents_extra;
            return $content;
        }
        $hho = '';
        for ($i = 1; $i <= $total_indicadores; $i++) {
            $hho .= '<td align="center">' . $i . '</td>';
        };
        $n_cont = $total_indicadores + 1;
        for ($i = $n_cont; $i <= 12; $i++) {
            $hho .= '<td align="center">' . $i . '</td>';
        }
        $n_cont = (12 - $total_indicadores) + 5;
        $content = '';
        $content .= '

        <table border="0.2" cellspacing="0" cellpadding="0.5">  
        <tr>
        <th colspan="' . $n_cont . '" align="center">CALIFICACIONES DE ' . $r_b_ud['descripcion'] . '</th>
        </tr>
        <tr height="auto">
            <td rowspan="2" align="center" width="4%"><small >Nro de Orden</small></td>
            <td rowspan="2" align="center" width="23%">APELLIDOS Y NOMBRES</td>
            <td colspan="12" align="center" width="47%">INDICADORES DE LOGRO</td>
            <td rowspan="2" align="center" width="8%"><small >PROMEDIO</small></td>
            <td rowspan="2" align="center" width="10%"><small >EVALUACION DE RECUPERACION</small></td>
            <td rowspan="2" align="center" width="8%"><small >NOTA FINAL</small></td>
        </tr>
        <tr>
            ' . $hho . '
        </tr>
            
      ';
        $content .= generateRow();
        $content .= '</table>';
        $pdf->writeHTML($content);

        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        $fechaaa = date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y');
        $footer = '

        <table border="0" cellspacing="0" cellpadding="0.5">  
        <tr>
            <th width="75%"></th>
            <th >Huanta, ' . $fechaaa . '</th>
        </tr>
        </table>    
      ';
        $pdf->writeHTML($footer);








        $pdf->Output('califcaciones - ' . $r_b_ud['descripcion'] . '.pdf', 'I');
    }
}
