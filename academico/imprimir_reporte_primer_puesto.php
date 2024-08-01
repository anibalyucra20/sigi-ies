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

        $id_pe = $_POST['car_consolidado'];
        $id_sem = $_POST['sem_consolidado'];
        $turno = $_POST['turno'];
        $seccion = $_POST['seccion'];

        //bsucar programa-sede
        $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_pe);
        $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
        $id_programa_sede = $rb_pe_sede['id'];


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

            $b_pe = buscarProgramaEstudioById($conexion, $id_pe);
            $r_b_pe = mysqli_fetch_array($b_pe);

            $b_sem = buscarSemestreById($conexion, $id_sem);
            $r_b_sem = mysqli_fetch_array($b_sem);

            $per_select = $_SESSION['periodo'];

            $b_per = buscarPeriodoAcadById($conexion, $per_select);
            $r_b_per = mysqli_fetch_array($b_per);
            $array_estudiantes = [];
            // armar la nomina de estudiantes para poder mostrar todos los estudiantes del semestre
            $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
            $cont_ud_sem = mysqli_num_rows($b_ud_pe_sem);
            $cont_ind_capp = 0;
            $suma_creditos = 0;
            while ($r_b_ud = mysqli_fetch_array($b_ud_pe_sem)) {
                $id_ud = $r_b_ud['id'];
                $suma_creditos += $r_b_ud['creditos'];
                //buscar capacidades
                $b_capp = buscarCapacidadByIdUd($conexion, $id_ud);
                while ($r_b_capp = mysqli_fetch_array($b_capp)) {
                    $id_capp = $r_b_capp['id'];
                    //buscar indicadores de logro de capacidad
                    $b_ind_l_capp = buscarIndCapacidadByIdCapacidad($conexion, $id_capp);
                    $cont_ind_capp += mysqli_num_rows($b_ind_l_capp);
                }



                //buscar si la unidad didactica esta programado en el presente periodo
                $b_ud_prog = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_ud, $id_programa_sede, $id_periodo_act, $turno, $seccion);
                $r_b_ud_prog = mysqli_fetch_array($b_ud_prog);
                $cont_res = mysqli_num_rows($b_ud_prog);
                if ($cont_res > 0) {
                    $id_prog_ud = $r_b_ud_prog['id'];
                    //buscar detalle de matricula matriculas a la programacion de la unidad didactica
                    $b_det_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog_ud);
                    while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
                        // buscar matricula para obtener datos del estudiante
                        $id_mat = $r_b_det_mat['id_matricula'];
                        $b_mat = buscarMatriculaById($conexion, $id_mat);
                        $r_b_mat = mysqli_fetch_array($b_mat);
                        $id_estudiante = $r_b_mat['id_estudiante'];
                        // buscar estudiante
                        $b_estudiante = buscarUsuarioById($conexion, $id_estudiante);
                        $r_b_estudiante = mysqli_fetch_array($b_estudiante);
                        $array_estudiantes[] = $r_b_estudiante['apellidos_nombres'];
                    }
                    $aa = "SI";
                } else {
                    $aa = "NO";
                }
                //echo $r_b_ud['descripcion']." - ".$aa."<br>";
            }
            $n_array_estudiantes = array_unique($array_estudiantes);
            $collator = collator_create("es");
            $collator->sort($n_array_estudiantes);


            $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetTitle("Reporte Primeros Puestos - " . $r_b_pe['nombre'] . " - " . $r_b_sem['descripcion']);
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
            $pdf->AddPage('P', 'A3');




            $text_title = 8;
            $text_size = 8;


            //crear el contenido 

            $contenido = '';

            $primeros_puestos = [];
            foreach ($n_array_estudiantes as $key => $val) {
                $key += 1;
                //buscar estudiante para su id
                $b_est = buscarUsuarioByNomAp($conexion, $val);
                $r_b_est = mysqli_fetch_array($b_est);
                $id_est = $r_b_est['id'];

                //buscar si estudiante esta matriculado en una unidad didactica
                $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion,$id_sem);
                $min_ud_desaprobar = round(mysqli_num_rows($b_ud_pe_sem) / 2, 0, PHP_ROUND_HALF_DOWN);

                $suma_califss = 0;
                $suma_ptj_creditos = 0;
                $cont_ud_desaprobadas = 0;
                while ($r_bb_ud = mysqli_fetch_array($b_ud_pe_sem)) {
                    $id_udd = $r_bb_ud['id'];

                    $b_prog_ud = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_udd, $id_programa_sede, $id_periodo_act, $turno, $seccion);
                    $r_b_prog_ud = mysqli_fetch_array($b_prog_ud);
                    $id_prog = $r_b_prog_ud['id'];

                    //buscar matricula de estudiante
                    $b_mat_est = buscarMatriculaByEstPeriodoSede($conexion, $id_est, $id_periodo_act, $id_sede_act);
                    $r_b_mat_est = mysqli_fetch_array($b_mat_est);
                    $id_mat_est = $r_b_mat_est['id'];
                    //buscar detalle de matricula
                    $b_det_mat_est = buscarDetalleMatriculaByIdMatriculaAndProgrmacion($conexion, $id_mat_est, $id_prog);
                    $r_b_det_mat_est = mysqli_fetch_array($b_det_mat_est);
                    $cont_r_b_det_mat = mysqli_num_rows($b_det_mat_est);
                    $id_det_mat = $r_b_det_mat_est['id'];
                    if ($cont_r_b_det_mat > 0) {
                        //echo "<td>SI</td>";

                        //buscar las calificaciones
                        $b_calificaciones = buscarCalificacionByIdDetalleMatricula($conexion, $id_det_mat);

                        $suma_calificacion = 0;
                        $cont_calif = 0;
                        while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                            $id_calificacion = $r_b_calificacion['id'];
                            //buscamos las evaluaciones
                            $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                            $suma_calificacion += $suma_evaluacion;
                            if ($suma_evaluacion > 0) {
                                $cont_calif += 1;
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
                        if ($r_b_det_mat_est['recuperacion'] != '') {
                            $calificacion = $r_b_det_mat_est['recuperacion'];
                        }
                        if ($calificacion > 12) {
                        } else {
                            $cont_ud_desaprobadas += 1;
                        }
                    } else {
                        $calificacion = 0;
                        //echo '<td></td>';
                    }
                    if (is_numeric($calificacion)) {
                        $suma_califss += $calificacion;
                        $suma_ptj_creditos += $calificacion * $r_bb_ud['creditos'];
                    } else {
                        $suma_ptj_creditos += 0 * $r_bb_ud['creditos'];
                    }
                }
                $primeros_puestos[$id_est] = $suma_ptj_creditos;
            }
            arsort($primeros_puestos);

            //los estudiantes que desaprobaron alguna UD se pasan al final de la lista
            foreach ($primeros_puestos as $key => $value) {
                $cant_ud_desaprobado = calc_ud_desaprobado_sin_recuperacion($conexion, $key, $id_periodo_act,$id_sede_act, $id_programa_sede, $id_sem,$turno,$seccion);
                if ($cant_ud_desaprobado > 0) {
                    $id_est_des = $key;
                    $ptj_est_des = $value;
                    unset($primeros_puestos[$key]);
                    $primeros_puestos[$id_est_des] = $ptj_est_des;
                }
            }
            // los estudiantes de repitencia pasan al ultimo del ranking
            foreach ($primeros_puestos as $key => $value) {
                $mat_todos = calcular_mat_ud($conexion, $key, $id_periodo_act,$id_sede_act, $id_programa_sede, $id_sem,$turno,$seccion);
                if ($mat_todos == 0) {
                    $id_est_des = $key;
                    $ptj_est_des = $value;
                    unset($primeros_puestos[$key]);
                    $primeros_puestos[$id_est_des] = $ptj_est_des;
                }
            }

            $cont = 0;
            foreach ($primeros_puestos as $key => $value) {
                $cont += 1;
                $b_estt = buscarUsuarioById($conexion, $key);
                $r_b_estt = mysqli_fetch_array($b_estt);
                $contenido .= '
        <tr>
            <td border="0.2" align="center">' . $cont . ' º Puesto</td>
            <td border="0.2" align="center">' . $r_b_estt['dni'] . '</td>
            <td border="0.2">' . $r_b_estt['apellidos_nombres'] . '</td>
            <td border="0.2" align="center">' . $value . '</td>
            <td border="0.2" align="center">' . round($value / $suma_creditos, 2) . '</td>
        </tr>
        ';
            }
            $content_one = '';
            $content_one .= '
    
        <table border="0" width="100%" cellspacing="0" cellpadding="3">
        <tr>
            <td width="40%"><img src="../images/logo_minedu.jpeg" alt="" height="40px"></td>
            <td width="10%"></td>
            <td width="50%" align="right"><img src="../images/logo.png" alt="" height="40px"></td>
        </tr>
        <tr>
            <td colspan="4" align="center"><b>REPORTE PRIMEROS PUESTOS - ' . $r_b_pe['nombre'] . ' - SEMESTRE ' . $r_b_sem['descripcion'] . ' ' . $r_b_per['nombre'] . '</b></td>
        </tr>
        <tr bgcolor="#CCCCCC">
            <td width="10%" align="center" border="0.2"><b>ORDEN DE MÉRITO</b></td>
            <td width="12%" align="center" border="0.2"><b>DNI</b></td>
            <td width="50%" align="center" border="0.2"><b>APELLIDOS Y NOMBRES</b></td>
            <td width="15%" align="center" border="0.2"><b>PUNTAJE TOTAL CRÉDITOS</b></td>
            <td width="13%" align="center" border="0.2"><b>PROMEDIO PONDERADO</b></td>
        </tr>
                
          ';

            $content_one .= $contenido;
            $content_one .= '</table>';
            $pdf->writeHTML($content_one);

            $footer = '

        <table border="0" cellspacing="0" cellpadding="0.5">  
        <tr>
            <th><b>NOTA:</b></th>
            
        </tr>
        <tr>
            <td >- Los estudiantes que tienen 1 o más unidades didácticas desaprobadas no participan el en ranking de los primeros puestos, Aún teniendo el más alto puntaje.</td>
        </tr>
        <tr>
            <td >- Los estudiantes matriculados en unidades didácticas de repitencia no participan el en ranking de los primeros puestos.</td>
        </tr>
        </table>

      ';
            $pdf->writeHTML($footer);








            $pdf->Output('Reporte Primeros Puestos - ' . $r_b_pe['nombre'] . ' ' . $r_b_sem['descripcion'] . '.pdf', 'I');
        }
    }
