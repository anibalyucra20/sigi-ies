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

    $id_sesion = base64_decode($_GET['data']);

    $b_sesion = buscarSesionAprendizajeById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);
    $id_prog_act = $r_b_sesion['id_prog_actividad_silabo'];
    // buscamos datos de la programacion de actividades
    $b_prog_act = buscarProgActividadesSilaboById($conexion, $id_prog_act);
    $r_b_prog_act = mysqli_fetch_array($b_prog_act);
    $id_silabo = $r_b_prog_act['id_silabo'];
    // buscamos datos de silabo
    $b_silabo = buscarSilabosById($conexion, $id_silabo);
    $r_b_silabo = mysqli_fetch_array($b_silabo);
    $id_prog = $r_b_silabo['id_prog_unidad_didactica'];
    //buscamos datos de la programacion de unidad didactica
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
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
            //Page header
            public function Header()
            {
                // Logo
                $image_file = '../images/cabeza_silabo.png';
                $this->Image($image_file, 5, 3, 190, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            // Page footer
            public function Footer()
            {
                // Position at 15 mm from bottom
                $this->SetY(-15);
                // Set font
                $this->SetFont('helvetica', 'B', 10);
                // Page number
                $this->Image('../images/pie_silabo.png', 15, 278, 181);
                $this->SetX(-30);
                $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            }
        }

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

        //buscamos el silabo y sus datos
        $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
        $r_b_silabo = mysqli_fetch_array($b_silabo);
        $id_silabo = $r_b_silabo['id'];
        //buscar datos de docente
        $b_docente = buscarUsuarioById($conexion, $res_b_prog['id_docente']);
        $r_b_docente = mysqli_fetch_array($b_docente);

        //buscar capacidad de unidad didactica
        $b_capacidad =  buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        $r_b_capacidad = mysqli_fetch_array($b_capacidad);
        //buscar competencias
        $b_competencia = buscarCompetenciasById($conexion, $r_b_capacidad['id_competencia']);
        $r_b_competencia = mysqli_fetch_array($b_competencia);

        //buscamos la cantidad de indicadores para definir la cantidad de calificaciones
        $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        $total_capacidades = mysqli_num_rows($b_capacidades);
        $cont_caps = 0;
        $total_indicadores = 0;
        $contenido_capacidades = '';
        while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
            $cont_caps++;
            $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
            $cont_indicadores = mysqli_num_rows($b_indicador_capac);
            $total_indicadores = $total_indicadores + $cont_indicadores;
            $contenido_capacidades .= $r_b_capacidades['codigo'] . '.- ' . $r_b_capacidades['descripcion'];
            if ($cont_caps < $total_capacidades) {
                $contenido_capacidades .= "<br>";
            }
        };

        $b_ind_comp = buscarIndCompetenciasById($conexion, $r_b_sesion['id_ind_logro_competencia_vinculado']);
        $r_b_ind_comp = mysqli_fetch_array($b_ind_comp);

        $b_ind_cap = buscarIndCapacidadById($conexion, $r_b_sesion['id_ind_logro_capacidad_vinculado']);
        $r_b_ind_cap = mysqli_fetch_array($b_ind_cap);

        $pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Sesión de Aprendizaje - " . $r_b_prog_act['semana'] . " - " . $r_b_ud['nombre']);

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont('helvetica');

        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage('P', 'A4');

        if ($r_b_competencia['tipo'] == "ESPECÍFICA") {
            $competen = 'Competencia técnica o de especialidad';
        }
        if ($r_b_competencia['tipo'] == "EMPLEABILIDAD") {
            $competen = 'Competencia para la empleabilidad';
        }



        $contenido_secuencia_didactica = '';
        $b_momentos_sesion = buscarMomentosSesionAprendizajeByIdSesion($conexion, $id_sesion);
        while ($r_b_momentos_sesion = mysqli_fetch_array($b_momentos_sesion)) {
            $contenido_secuencia_didactica .= '<tr><td style="text-align: center;">' . $r_b_momentos_sesion['momento'] . '</td>';

            $estrategia_actividad = "* Estrategía: <br>";
            $estrategia_actividad .= "" . nl2br($r_b_momentos_sesion['estrategia']) . "<br>";
            $estrategia_actividad .= "* Actividades: <br>";
            $estrategia_actividad .= "" . nl2br($r_b_momentos_sesion['actividad']);

            $contenido_secuencia_didactica .= '<td style="text-align: justify;">' . nl2br($estrategia_actividad) . '</td>';
            $contenido_secuencia_didactica .= '<td style="text-align: justify;">' . nl2br($r_b_momentos_sesion['recursos']) . '</td>';
            $contenido_secuencia_didactica .= '<td style="text-align: center;">' . $r_b_momentos_sesion['tiempo'] . '</td></tr>';
        }

        $contenido_actividades_evaluacion = '';
        $b_actividades_eval = buscarActividadEvaluacionByIdSesion($conexion, $id_sesion);
        while ($r_b_actividades_eval = mysqli_fetch_array($b_actividades_eval)) {
            $contenido_actividades_evaluacion .= '<tr><td style="text-align: justify;">'.nl2br($r_b_actividades_eval['indicador_logro_sesion']).'</td>';
            $contenido_actividades_evaluacion .= '<td style="text-align: justify;">'.nl2br($r_b_actividades_eval['tecnica']).'</td>';
            $contenido_actividades_evaluacion .= '<td style="text-align: justify;">'.nl2br($r_b_actividades_eval['instrumentos']).'</td>';
            $contenido_actividades_evaluacion .= '<td style="text-align: center;">'.nl2br($r_b_actividades_eval['peso']).'</td>';
            $contenido_actividades_evaluacion .= '<td style="text-align: center;">'.nl2br($r_b_actividades_eval['momento']).'</td></tr>';
        }

        $contenido = '<table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <th colspan="2" style="text-align: center;"><b>I. INFORMACIÓN GENERAL</b></th>
        </tr>
        <tr>
            <td style="width: 30%;">Docente a cargo</td>
            <td style="width: 70%;">: ' . $r_b_docente['apellidos_nombres'] . '</td>
        </tr>
        <tr>
            <td>Periodo Académico</td>
            <td>: ' . $r_b_perio['nombre'] . '</td>
        </tr>
        <tr>
            <td>Programa de Estudios </td>
            <td>: ' . $r_b_pe['nombre'] . '</td>
        </tr>
        <tr>
            <td>' . $competen . '</td>
            <td style="text-align: justify;">: ' . $r_b_competencia['descripcion'] . '</td>
        </tr>
        <tr>
            <td>Módulo</td>
            <td>: ' . $r_b_mod['descripcion'] . '</td>
        </tr>
        <tr>
            <td>Unidad didáctica</td>
            <td>: ' . $r_b_ud['nombre'] . '</td>
        </tr>
        <tr>
            <td>Capacidad</td>
            <td style="text-align: justify;">: ' . $contenido_capacidades . '</td>
        </tr>
        <tr>
            <td>Tema o Actividad</td>
            <td>: ' . nl2br($r_b_prog_act['actividades_aprendizaje']) . '</td>
        </tr>
        <tr>
            <td>Actividades de tipo </td>
            <td>: ' . $r_b_sesion['tipo_actividad'] . '</td>
        </tr>
        <tr>
            <td>Tipo de sesión</td>
            <td>: ' . $r_b_sesion['tipo_sesion'] . '</td>
        </tr>
        <tr>
            <td>Fecha de desarrollo</td>
            <td>: ' . $r_b_sesion['fecha_desarrollo'] . '</td>
        </tr>
    </table>
    <table><tr><td></td></tr></table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <th colspan="2" style="text-align: center;"><b>II. PLANIFICACIÓN DEL APRENDIZAJE</b></th>
        </tr>
        <tr>
            <td style="width: 30%; text-align: justify;">Indicador(es) de logro de competencia a la que se vincula.</td>
            <td style="width: 70%; text-align: justify;">: ' . $r_b_ind_comp['descripcion'] . '</td>
        </tr>
        <tr>
            <td style="text-align: justify;">Indicador(es) de logro de capacidad vinculados a la sesión.</td>
            <td style="text-align: justify;">: ' . $r_b_ind_cap['descripcion'] . '</td>
        </tr>
        <tr>
            <td>Logro de la sesión </td>
            <td style="text-align: justify;">: ' . nl2br($r_b_sesion['logro_sesion']) . '</td>
        </tr>
    </table>
    <table><tr><td></td></tr></table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <td colspan="4" style="text-align: center;"><b>III. SECUENCIA DIDÁCTICA</b></td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <td style="width: 12%; text-align: center;"><b>Momento</b></td>
            <td style="width: 53%; text-align: center;"><b>Estrategías y Actividades</b></td>
            <td style="width: 25%; text-align: center;"><b>Recursos Didácticas</b></td>
            <td style="width: 10%; text-align: center;"><b>Tiempo</b></td>
        </tr>
        ' . $contenido_secuencia_didactica . '
    </table>
    <table><tr><td></td></tr></table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <td colspan="5" style="text-align: center;"><b>IV. ACTIVIDADES DE EVALUACIÓN</b></td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <td style="width: 35%; text-align: center;"><b>Indicadores de logro de la sesión</b></td>
            <td style="width: 15%; text-align: center;"><b>Técnicas</b></td>
            <td style="width: 25%; text-align: center;"><b>Instrumentos</b></td>
            <td style="width: 14%; text-align: center;"><b>Peso o Porcentaje</b></td>
            <td style="width: 11%; text-align: center;"><b>Momento</b></td>
        </tr>
       '.$contenido_actividades_evaluacion.'
    </table>
    <table><tr><td></td></tr></table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <td colspan="2" style="text-align: center;"><b>V. BIBLIOGRAFÍA</b></td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <td style="text-align: center;"><b>Para el docente</b></td>
            <td style="text-align: center;"><b>Para el Estudiante</b></td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <td style="text-align: center;"><b>Obligatoria</b></td>
            <td style="text-align: center;"><b>Obligatoria</b></td>
        </tr>
        <tr>
            <td style="text-align: justify;">'.nl2br($r_b_sesion['bibliografia_obligatoria_docente']).'</td>
            <td style="text-align: justify;">'.nl2br($r_b_sesion['bibliografia_obligatoria_estudiante']).'</td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <td style="text-align: center;"><b>Opcional</b></td>
            <td style="text-align: center;"><b>Opcional</b></td>
        </tr>
        <tr>
            <td style="text-align: justify;">'.nl2br($r_b_sesion['bibliografia_opcional_docente']).'</td>
            <td style="text-align: justify;">'.nl2br($r_b_sesion['bibliografia_opcional_estudiante']).'</td>
        </tr>
    </table>
    <table><tr><td></td></tr></table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <td style="text-align: center;"><b>VI. ANEXOS</b></td>
        </tr>
        <tr>
            <td style="text-align: justify;">'.nl2br($r_b_sesion['anexos']).'</td>
        </tr>

    </table>';

        $pdf->writeHTML($contenido);

        $pdf->Output('Sesión de Aprendizaje - ' . $r_b_prog_act['semana'] . ' - ' . $r_b_ud['nombre'] . '.pdf', 'I');
        mysqli_close($conexion);
    }
}
