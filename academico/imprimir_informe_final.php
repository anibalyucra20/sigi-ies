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

        $b_datos_sistema = buscarDatosSistema($conexion);
        $r_b_datos_sistema = mysqli_fetch_array($b_datos_sistema);

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

        //calcular porcentaje de avance curricular segun el desarrollo de sesiones Y TEMAS DESARROLLADAS
        $ultima_sesion = 0;
        $cantidad_sesiones = 0;
        $b_programacion_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
        while ($r_b_prog_silabo = mysqli_fetch_array($b_programacion_silabo)) {
            $id_prog_silabo = $r_b_prog_silabo['id'];
            $b_sesion = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_prog_silabo);
            $r_b_sesion = mysqli_fetch_array($b_sesion);
            $cantidad_sesiones++;
            if ($r_b_sesion['logro_sesion'] != ' ') {
                $ultima_sesion = $r_b_prog_silabo['semana'];
            }
        }
        $porcentaje_avance = round(($ultima_sesion / $cantidad_sesiones) * 100, 2);

        // calcular la ultima sesion desarrollada
        $b_ult_programacion_silabo = buscarProgActividadesSilaboByIdSilaboAndSemana($conexion, $id_silabo, $ultima_sesion);
        $r_b_ult_programacion_silabo = mysqli_fetch_array($b_ult_programacion_silabo);

        // calcular sesiones no desarrolladas
        $temas_no_desarrolladas = '';
        $ult_sesion_no_desarrollada = $ultima_sesion + 1;
        for ($i = $ult_sesion_no_desarrollada; $i <= $cantidad_sesiones; $i++) {
            $b_prog_silabos = buscarProgActividadesSilaboByIdSilaboAndSemana($conexion, $id_silabo, $i);
            $r_b_prog_silabos = mysqli_fetch_array($b_prog_silabos);
            $temas_no_desarrolladas .= '        ' . $r_b_prog_silabos['contenidos_basicos'] . '<br>';
        }

        //contar matriculados hommbres y mujeres
        $cont_mat_hombres = 0;
        $cont_mat_mujeres = 0;
        $cont_hombres_aprobados = 0;
        $cont_hombres_desaprobados = 0;
        $cont_mujeres_aprobados = 0;
        $cont_mujeres_desaprobados = 0;
        $cont_hombres_retirados = 0;
        $cont_mujeres_retirados = 0;

        $b_matriculados = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
        while ($r_b_matriculados = mysqli_fetch_array($b_matriculados)) {
            $b_matricula = buscarMatriculaById($conexion, $r_b_matriculados['id_matricula']);
            $r_b_matricula = mysqli_fetch_array($b_matricula);
            $b_estudiante = buscarUsuarioById($conexion, $r_b_matricula['id_estudiante']);
            $r_b_estudiante = mysqli_fetch_array($b_estudiante);
            if ($r_b_estudiante['genero'] == 'M') {
                $cont_mat_hombres += 1;
            }
            if ($r_b_estudiante['genero'] == 'F') {
                $cont_mat_mujeres += 1;
            }

            //calificaciones
            $b_calif = buscarCalificacionByIdDetalleMatricula($conexion, $r_b_matriculados['id']);
            $suma_calificacion = 0;
            $cont_calif = 0;
            while ($r_b_calif = mysqli_fetch_array($b_calif)) {

                $id_calificacion = $r_b_calif['id'];
                //buscamos las evaluaciones
                $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);
                $suma_calificacion += $suma_evaluacion;
                if ($suma_evaluacion > 0) {
                    $cont_calif += 1;
                }
            }

            if ($cont_calif > 0) {
                $suma_calificacion = round($suma_calificacion / $cont_calif);
            } else {
                $suma_calificacion = round($suma_calificacion);
            }
            if ($suma_calificacion != 0) {
                $calificacion_final = round($suma_calificacion);
            } else {
                $calificacion_final = "";
            }
            if ($r_b_matriculados['recuperacion'] != '') {
                $calificacion_final = $r_b_matriculados['recuperacion'];
            }

            //asistencia
            $cont_inasistencia = contar_inasistencia($conexion, $id_silabo, $r_b_estudiante['id']);
            if ($cont_inasistencia > 0) {
                $porcent_ina = round($cont_inasistencia * 100 / 16);
            } else {
                $porcent_ina = 0;
            }


            if ($calificacion_final > 12 && $r_b_estudiante['genero'] == 'M' && $porcent_ina <= 30) {
                $cont_hombres_aprobados += 1;
            }
            if ($calificacion_final > 12 && $r_b_estudiante['genero'] == 'F' && $porcent_ina <= 30) {
                $cont_mujeres_aprobados += 1;
            }
            if (($calificacion_final <= 12 || $porcent_ina >= 30) && $r_b_matricula['licencia'] == "" && $r_b_estudiante['genero'] == 'M') {
                $cont_hombres_desaprobados += 1;
            }
            if (($calificacion_final <= 12 || $porcent_ina >= 30) && $r_b_matricula['licencia'] == "" && $r_b_estudiante['genero'] == 'F') {
                $cont_mujeres_desaprobados += 1;
            }


            if ($r_b_matricula['licencia'] != "" && $r_b_estudiante['genero'] == 'M') {
                $cont_hombres_retirados += 1;
            }
            if ($r_b_matricula['licencia'] != "" && $r_b_estudiante['genero'] == 'F') {
                $cont_mujeres_retirados += 1;
            }
        }

        $total_matriculados = $cont_mat_mujeres + $cont_mat_hombres;
        $porcentaje_mat_hombres = round(($cont_mat_hombres / $total_matriculados) * 100, 2);
        $porcentaje_mat_mujeres = round(($cont_mat_mujeres / $total_matriculados) * 100, 2);

        $porcentaje_hombres_aprobados = round(($cont_hombres_aprobados / $total_matriculados) * 100, 2);
        $porcentaje_mujeres_aprobados = round(($cont_mujeres_aprobados / $total_matriculados) * 100, 2);
        $total_aprobados = $cont_hombres_aprobados + $cont_mujeres_aprobados;
        $porcentaje_aprobados = round(($total_aprobados / $total_matriculados) * 100, 2);

        $porcentaje_hombres_desaprobados = round(($cont_hombres_desaprobados / $total_matriculados) * 100, 2);
        $porcentaje_mujeres_desaprobados = round(($cont_mujeres_desaprobados / $total_matriculados) * 100, 2);
        $total_desaprobados = $cont_hombres_desaprobados + $cont_mujeres_desaprobados;
        $porcentaje_desaprobados = round(($total_desaprobados / $total_matriculados) * 100, 2);

        $porcentaje_hombres_retirados = round(($cont_hombres_retirados / $total_matriculados) * 100, 2);
        $porcentaje_mujeres_retirados = round(($cont_mujeres_retirados / $total_matriculados) * 100, 2);
        $total_retirados = $cont_hombres_retirados + $cont_mujeres_retirados;
        $porcentaje_retirados = round(($total_retirados / $total_matriculados) * 100, 2);

        $supervisado_si = '';
        $supervisado_no = '';
        $reg_evaluacion_si = '';
        $reg_evaluacion_no = '';
        $reg_auxiliar_si = '';
        $reg_auxiliar_no = '';
        $prog_curricular_si = '';
        $prog_curricular_no = '';
        $otros_si = '';
        $otros_no = '';

        if ($res_b_prog['supervisado']) {
            $supervisado_si .= 'X';
        } else {
            $supervisado_no .= 'X';
        }
        if ($res_b_prog['reg_evaluacion']) {
            $reg_evaluacion_si .= 'X';
        } else {
            $reg_evaluacion_no .= 'X';
        }
        if ($res_b_prog['reg_auxiliar']) {
            $reg_auxiliar_si .= 'X';
        } else {
            $reg_auxiliar_no .= 'X';
        }
        if ($res_b_prog['prog_curricular']) {
            $prog_curricular_si .= 'X';
        } else {
            $prog_curricular_no .= 'X';
        }
        if ($res_b_prog['otros']) {
            $otros_si .= 'X';
        } else {
            $otros_no .= 'X';
        }


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



        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Informe Final - " . $r_b_ud['nombre']);
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
        $pdf->AddPage('P', 'A4');


        $text_size = 8;

        //crear el contenido 
        $contenido = '';

        $content_one = '';
        $content_one .= '
    
        <table border="0" width="100%" cellspacing="0" cellpadding="0.1">
        <tr>
            <td width="40%"><img src="../images/logo_minedu.jpeg" alt="" height="30px"></td>
            <td width="10%"></td>
            <td width="50%" align="right"><img src="../images/logo.png" alt="" height="30px"></td>
        </tr>
        
        <tr>
            <td colspan="3" align="center"><b>INFORME TÉCNICO - PEDAGÓGICO DEL SEMESTRE - ' . $r_b_perio['nombre'] . '</b></td>
        </tr>
        <br>
        <tr>
            <td colspan="3"><b>I.          DATOS INFORMATIVOS:</b></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       1. INSTITUCIÓN EDUCATIVA</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_datos_sistema['nombre_completo'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       2. PROGRAMA DE ESTUDIOS</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_pe['nombre'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       3. MÓDULO FORMATIVO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_mod['descripcion'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       4. UNIDAD DIDÁCTICA</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_ud['nombre'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       5. SEMESTRE ACADÉMICO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_sem['descripcion'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       5. TURNO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $turno . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       5. SECCIÓN</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $res_b_prog['seccion'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>       6. DOCENTE</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_docente['apellidos_nombres'] . '</font></td>
        </tr>
        <br>
        <tr>
            <td colspan="3"><b>II.        ASPECTOS TECNICO - PEDAGÓGICOS:</b></td>
        </tr>
        <tr>
            <td width="50%"><font size="' . $text_size . '"><b>       7. PORCENTAJE TOTAL DE AVANCE CURRICULAR:</b></font></td>
            <td width="50%"><font size="' . $text_size . '">' . $porcentaje_avance . '%</font></td>
        </tr>
        <tr>
            <td colspan="3"><font size="' . $text_size . '"><b>       8. U.F. Y TEMA DE LA ULTIMA CLASE DESARROLLADA:</b></font></td>
        </tr>
        <tr>
            <td colspan="3"><font size="' . $text_size . '">      semana ' . $ultima_sesion . ' - ' . $r_b_ult_programacion_silabo['contenidos_basicos'] . '</font></td>
        </tr>
        <tr>
            <td colspan="3"><font size="' . $text_size . '"><b>       9. TITULO(S) Y Nro DE LA(S) SESIÓN(ES) NO DESARROLLADAS:</b></font></td>
        </tr>
        <tr>
            <td colspan="3"><font size="' . $text_size . '">      ' . $temas_no_desarrolladas . '</font></td>
        </tr>
        <tr>
            <td colspan="3"><font size="' . $text_size . '"><b>       10. RESUMEN ESTADÍSTICO:</b></font></td>
        </tr>
        <tr>
            <table border="0.2" cellspacing="0" cellpadding="0.5">
                <tr bgcolor="#CCCCCC">
                    <td width="40%" align="center"><font size="' . $text_size . '"><b>DESCRIPCIÓN</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>H</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>M</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>T</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>%</b></font></td>
                </tr>
                <tr>
                    <td width="40%" align="center"><font size="' . $text_size . '"><b>TOTAL MATRICULADOS</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_mat_hombres . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_mat_hombres . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_mat_mujeres . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_mat_mujeres . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $total_matriculados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>100%</b></font></td>
                </tr>
                <tr>
                    <td width="40%" align="center"><font size="' . $text_size . '"><b>RETIRADOS(LICENCIA)</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_hombres_retirados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_hombres_retirados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_mujeres_retirados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_mujeres_retirados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $total_retirados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_retirados . '%</b></font></td>
                </tr>
                <tr>
                    <td width="40%" align="center"><font size="' . $text_size . '"><b>APROBADOS</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_hombres_aprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_hombres_aprobados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_mujeres_aprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_mujeres_aprobados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $total_aprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_aprobados . '%</b></font></td>
                </tr>
                <tr>
                    <td width="40%" align="center"><font size="' . $text_size . '"><b>DESAPROBADOS</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_hombres_desaprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_hombres_desaprobados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $cont_mujeres_desaprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_mujeres_desaprobados . '%</b></font></td>
                    <td width="10%" align="center"><font size="' . $text_size . '"><b>' . $total_desaprobados . '</b></font></td>
                    <td width="10%"  align="center"><font size="' . $text_size . '"><b>' . $porcentaje_desaprobados . '%</b></font></td>
                </tr>
            </table>
        </tr>
        <tr>
        <br>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                    <td width="60%"><font size="' . $text_size . '"><b>11. FUE SUPERVISADO:</b></font></td>
                    <td width="5%">:</td>
                    <td width="5%"><font size="' . $text_size . '">SI</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $supervisado_si . '</font></td>
                    <td width="5%"></td>
                    <td width="5%"><font size="' . $text_size . '">NO</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $supervisado_no . '</font></td>
                    <td width="10%"></td>
                </tr>
            </table>
            
        </tr>
        <tr>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                <td colspan="3"><font size="' . $text_size . '"><b>12. DOCUMENTOS DE EVALUACIÓN UTILIZADAS:</b></font></td>
                </tr>
            </table>
        </tr>
        <tr>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                    <td width="2%"></td>
                    <td width="58%"><font size="' . $text_size . '">Registro de Evaluación</font></td>
                    <td width="5%">:</td>
                    <td width="5%"><font size="' . $text_size . '">SI</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $reg_evaluacion_si . '</font></td>
                    <td width="5%"></td>
                    <td width="5%"><font size="' . $text_size . '">NO</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $reg_evaluacion_no . '</font></td>
                    <td width="10%"></td>
                </tr>
            </table>
        </tr>
        <tr>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                    <td width="2%"></td>
                    <td width="58%"><font size="' . $text_size . '">Registro Auxiliar</font></td>
                    <td width="5%">:</td>
                    <td width="5%"><font size="' . $text_size . '">SI</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $reg_auxiliar_si . '</font></td>
                    <td width="5%"></td>
                    <td width="5%"><font size="' . $text_size . '">NO</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $reg_auxiliar_no . '</font></td>
                    <td width="10%"></td>
                </tr>
            </table>
        </tr>
        <tr>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                    <td width="2%"></td>
                    <td width="58%"><font size="' . $text_size . '">Programación Curricular</font></td>
                    <td width="5%">:</td>
                    <td width="5%"><font size="' . $text_size . '">SI</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $prog_curricular_si . '</font></td>
                    <td width="5%"></td>
                    <td width="5%"><font size="' . $text_size . '">NO</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $prog_curricular_no . '</font></td>
                    <td width="10%"></td>
                </tr>
            </table>
        </tr>
        <tr>
            <table  cellspacing="0" cellpadding="0.5">
                <tr>
                    <td width="2%"></td>
                    <td width="58%"><font size="' . $text_size . '">Otros</font></td>
                    <td width="5%">:</td>
                    <td width="5%"><font size="' . $text_size . '">SI</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $otros_si . '</font></td>
                    <td width="5%"></td>
                    <td width="5%"><font size="' . $text_size . '">NO</font></td>
                    <td width="5%" border="0.2" align="center"><font size="' . $text_size . '">' . $otros_no . '</font></td>
                    <td width="10%"></td>
                </tr>
            </table>
        </tr>
        
        <tr>
            <td colspan="3"><b>III.        LOGROS OBTENIDOS:</b></td>
        </tr>
        <tr>
            <td width="100%"><font size="' . $text_size . '">' . nl2br($res_b_prog['logros_obtenidos']) . '</font></td>
        </tr>
        

        <tr>
            <td colspan="3"><b>III.        DIFICULTADES:</b></td>
        </tr>
        <tr>
            <td width="100%"><font size="' . $text_size . '">' . nl2br($res_b_prog['dificultades']) . '</font></td>
        </tr>
        

        <tr>
            <td colspan="3"><b>III.        SUGERENCIAS:</b></td>
        </tr>
        <tr>
            <td width="100%"><font size="' . $text_size . '">' . nl2br($res_b_prog['sugerencias']) . '</font></td>
        </tr>





                
          ';

        $content_one .= $contenido;
        $content_one .= '</table>';
        $pdf->writeHTML($content_one);



        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

        $fechaaa = date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y');
        $footer = '

        <table border="0" cellspacing="0" cellpadding="0.5">  
        <tr>
            <th width="50%"></th>
            <th align="right">'.$r_b_datos_insti['distrito'].', ' . $fechaaa . '</th>
        </tr>
        <tr>
            <td colspan="2" align="center"><br><br><br><br><br>...............................................<br>Docente</td>
        </tr>
        </table>

      ';
        $pdf->writeHTML($footer);








        $pdf->Output('Informe Final - ' . $r_b_ud['nombre'] . '.pdf', 'I');
    }
}
