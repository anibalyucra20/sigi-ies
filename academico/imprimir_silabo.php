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

        //buscamos el silabo y sus datos
        $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
        $r_b_silabo = mysqli_fetch_array($b_silabo);
        $id_silabo = $r_b_silabo['id'];
        //buscar datos de docente
        $b_docente = buscarUsuarioById($conexion, $res_b_prog['id_docente']);
        $r_b_docente = mysqli_fetch_array($b_docente);
        //buscar datos de coordinador de area
        $b_coordinador = buscarUsuarioById($conexion, $r_b_silabo['id_coordinador']);
        $r_b_coordinador = mysqli_fetch_array($b_coordinador);
        if (mysqli_num_rows($b_coordinador) == 0) {
            $coordinador = "";
        } else {
            $coordinador = $r_b_coordinador['apellidos_nombres'];
        }
        //buscar datos de director
        $b_director = buscarUsuarioById($conexion, $rb_periodo_act['director']);
        $r_b_director = mysqli_fetch_array($b_director);

        //buscamos la cantidad de indicadores para definir la cantidad de calificaciones
        $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        $total_indicadores = 0;
        while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
            $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
            $cont_indicadores = mysqli_num_rows($b_indicador_capac);
            $total_indicadores = $total_indicadores + $cont_indicadores;
        };


        $pdf = new MYPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Silabo - " . $r_b_sem['descripcion']);

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

        $horario1 = explode(",", $r_b_silabo['horario']);
        $horario = count($horario1);
        $horario2 = "";
        for ($i = 0; $i < $horario; $i++) {
            $horario2 = $horario2 . $horario1[$i];
            if ($horario1[$i + 1] != "") {
                $horario2 .= "<br>";
            }
        }
        //buscar competencias
        $b_mods = buscarModuloFormativoByIdPe($conexion, $r_b_mod['id_programa_estudio']);
        $competencias = '';
        while ($r_b_mods = mysqli_fetch_array($b_mods)) {
            $b_mod_form = buscarCompetenciasEspecialidadByIdModulo($conexion, $r_b_mods['id']);
            while ($r_b_mod_form = mysqli_fetch_array($b_mod_form)) {
                $competencias = $competencias . "* " . $r_b_mod_form['descripcion'] . "<br>";
            }
        }
        //buscar indicadores de logro de capacidad
        $buscar_cap = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
        $total_cap = mysqli_num_rows($buscar_cap);
        $caps = '';
        $cont_cap = 0;
        $num = 0;
        $ind_cap = '';
        while ($r_b_cap = mysqli_fetch_array($buscar_cap)) {
            $cont_cap += 1;
            $caps = $caps . $r_b_cap['codigo'] . " - " . $r_b_cap['descripcion'];
            if ($total_cap > $cont_cap) {
                $caps .= "<br>";
            }
            //buscar indicadores de capacidad
            $b_ind_cap = buscarIndCapacidadByIdCapacidad($conexion, $r_b_cap['id']);
            while ($r_b_ind_cap = mysqli_fetch_array($b_ind_cap)) {
                $num += 1;
                $ind_cap = $ind_cap . $r_b_cap['codigo'] . "." . $r_b_ind_cap['codigo'] . ".- " . $r_b_ind_cap['descripcion'];
                if ($total_indicadores > $num) {
                    $ind_cap .= "<br>";
                }
            }
        };

        //buscar programacion de actividades del silabo
        $b_act_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
        $cant_actividades = mysqli_num_rows($b_act_silabo);
        $actividades_silabo = '';
        while ($r_b_act_silabo = mysqli_fetch_array($b_act_silabo)) {
            $actividades_silabo .= '<tr>
            <td style="text-align: center;">' . $r_b_act_silabo['semana'] . ' </td>
            <td style="text-align: justify; align-content: start; ">' . nl2br($r_b_act_silabo['elemento_capacidad']) . '</td>
            <td style="text-align: justify; align-content: start; ">' . nl2br($r_b_act_silabo['actividades_aprendizaje']) . '</td>
            <td style="text-align: justify; align-content: start; ">' . nl2br($r_b_act_silabo['contenidos_basicos']) . '</td>
            <td style="text-align: justify; align-content: start; ">' . nl2br($r_b_act_silabo['tareas_previas']) . '</td>
        </tr>';
        }
        $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

        $contenido = '<table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th colspan="3" style=" text-align: center;"><b>SILABO DE ' . $r_b_ud['nombre'] . '</b></th>
        </tr>
        <tr>
            <th style="width: 7%; text-align: left;"><b>I. </b></th>
            <th style="width: 93%; text-align: left;" colspan="2"><b>INFORMACION GENERAL</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="width: 35%; text-align: left;"><b>Programa de Estudios</b></th>
            <th style="width: 60%; text-align: left;"><b>: ' . $r_b_pe['nombre'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Plan de Estudios</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_pe['plan_estudios'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Periodo Académico </b></th>
            <th style="text-align: left;"><b>: ' . $r_b_perio['nombre'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Módulo</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_mod['descripcion'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Unidad Didáctica</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_ud['nombre'] . ' </b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Créditos </b></th>
            <th style="text-align: left;"><b>: ' . $r_b_ud['creditos'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Semestre Académico </b></th>
            <th style="text-align: left;"><b>: ' . $r_b_sem['descripcion'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>N° de Horas Semanal </b></th>
            <th style="text-align: left;"><b>: ' . ($r_b_ud['horas'] / 16) . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>N° de Horas Semestral</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_ud['horas'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Horario</b></th>
            <th style="text-align: left;"><b>: ' . $horario2 . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Docente</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_docente['apellidos_nombres'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Coordinador de Área</b></th>
            <th style="text-align: left;"><b>: ' . $r_b_coordinador['apellidos_nombres'] . '</b></th>
        </tr>
        <tr>
            <th></th>
            <th style="text-align: left;"><b>Director General </b></th>
            <th style="text-align: left;"><b>: ' . $r_b_director['apellidos_nombres'] . '</b></th>
        </tr>
        <tr>
            <td> </td>
        </tr>
        
        <tr>
            <th style="width: 7%; text-align: left;"><b>II. </b></th>
            <th style="width: 93%; text-align: left;" colspan="2"><b>COMPETENCIA DEL PROGRAMA DE ESTUDIOS</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify;">' . $competencias . '</td>
        </tr>
        <tr>
            <th style="width: 7%; text-align: left;"><b>III. </b></th>
            <th style="width: 93%; text-align: left;" colspan="2"><b>CAPACIDADES TERMINALES Y CRITERIOS DE EVALUACIÓN</b></th>
        </tr>
        </table>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr style="background-color: #CCCCCC;">
            <th colspan="3" style="text-align: center;"><b>CAPACIDAD TERMINAL</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify; ">' . $caps . '</td>
        </tr>
        <tr style="background-color: #CCCCCC;">
            <th colspan="3" style="text-align: center;"><b>CRITERIOS DE EVALUACION</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify;">' . $ind_cap . '</td>
        </tr>
        </table>
        <table>
        <tr>
            <td> </td>
        </tr>
        <tr>
            <th style="width: 7%; text-align: left;"><b>IV. </b></th>
            <th style="width: 93%; text-align: left;" colspan="2"><b>PERFIL DE EGRESADO</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify;">' . nl2br($r_b_pe['perfil_egresado']) . '</td>
        </tr>
        <tr>
            <td> </td>
        </tr>
        <tr>
            <th style="width: 7%; text-align: left;"><b>V. </b></th>
            <th style="width: 93%; text-align: left;" colspan="2"><b>ORGANIZACIÓN DE ACTIVIDADES Y CONTENIDOS BÁSICOS</b></th>
        </tr>
    </table>
    <table style="width: 100%;" border="1"  cellspacing="0" cellpadding="3">
        <tr>
            <th style="width: 10%; text-align: center;"><b>Semana</b></th>
            <th style="width: 25%; text-align: center;"><b>Elementos de capacidad</b></th>
            <th style="width: 25%; text-align: center;"><b>Actividades de aprendizaje</b></th>
            <th style="width: 25%; text-align: center;"><b>Contenidos Básicos</b></th>
            <th style="width: 15%; text-align: center;"><b>Tareas previas</b></th>
        </tr>
        ' . $actividades_silabo . '
        
    </table>
    <p></p>
    <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th style="width: 7%; text-align: left;"><b>VI. </b></th>
            <th style="width: 93%; text-align: left;"><b>METODOLOGÍA</b></th>
        </tr>
        <tr>
            <td colspan="2" style="text-align: justify;">' . nl2br($r_b_silabo['metodologia']) . '</td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <th style="text-align: left;"><b>VII. </b></th>
            <th style="text-align: left;"><b>RECURSOS DIDÁCTICOS</b></th>
        </tr>
        <tr>
            <td colspan="2" style="text-align: justify;">' . nl2br($r_b_silabo['recursos_didacticos']) . '</td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <th style="text-align: left;"><b>VIII. </b></th>
            <th style="text-align: left;" colspan="2"><b>SISTEMA DE EVALUACION</b></th>
        </tr>
        
        <tr>
            <td colspan="3" style="text-align: justify;">' . nl2br($r_b_silabo['sistema_evaluacion']) . '</td>
        </tr>
        </table>
        <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th style="width: 7%; text-align: right;"><b>8.1. </b></th>
            <th style="width: 47%; text-align: left;"><b>ESTRATEGÍAS DE EVALUACIÓN</b></th>
            <th style="width: 46%;"></th>
        </tr>
        </table>
        <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <td style="width: 7%;"></td>
            <td style="width: 93%;">
                <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
                    <tr>
                        <td style="width: 50%; text-align: center;"><b>INDICADORES</b></td>
                        <td style="width: 50%; text-align: center;"><b>TÉCNICAS</b></td>
                    </tr>
                    <tr>
                        <td style="text-align: justify; ">'.$r_b_silabo['estrategia_evaluacion_indicadores'].'</td>
                        <td style="text-align: justify;">'.$r_b_silabo['estrategia_evaluacion_tecnica'].'</td>
                    </tr>
                </table>
            </td>
        </tr>
        </table>
        <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th style="width: 7%; text-align: right;"><b>8.2. </b></th>
            <th style="width: 47%; text-align: left;"><b>PROMEDIO DE INDICADORES DE LOGRO</b></th>
            <th style="width: 46%;"></th>
        </tr>
    </table>
    <table style="width: 100%;" cellspacing="0" cellpadding="3">
        <tr>
            <td style="width: 7%;"></td>
            <td rowspan="2" style="width: 15%; border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align: right; "><br><br>PIL = </td>
            <td style="width: 2%; border-top:1px solid black;border-bottom: 1px solid black;" rowspan="2"></td>
            <td style="width: 62%; border-top:1px solid black;  text-align: center;">Suma de notas promedio de indicadores de logro</td>
            <td rowspan="2" style="width: 13%; border-top:1px solid black; border-right: 1px solid black; border-bottom:1px solid black;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="border-top: black 1px dashed; border-bottom:1px solid black;  text-align: center;">Número de indicadores de logro</td>
        </tr>
        <tr>
        <td></td>
        </tr>
    </table>
    <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr>
            <th style="width: 5%; text-align: left;"><b>IX. </b></th>
            <th style="width: 95%; text-align: left;" colspan="2"><b>RECURSOS BIBLIOGRÁFICOS / BIBLIOGRAFÍA</b></th>
        </tr>
    </table>
    <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
        <tr>
            <th colspan="3" style="text-align: left; "><b> - Impresos</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify; ">'. nl2br($r_b_silabo['recursos_bibliograficos_impresos']).'</td>
        </tr>
        <tr>
            <th colspan="3" style="text-align: left; "><b> - Digitales</b></th>
        </tr>
        <tr>
            <td colspan="3" style="text-align: justify;">'. nl2br($r_b_silabo['recursos_bibliograficos_digitales']).'</td>
        </tr>
    </table>
    <table style="width: 100%;" border="0" cellspacing="0" cellpadding="3">
        <tr><td></td></tr>
        <tr>
            <th style="text-align: right; ">'.$r_b_datos_insti['distrito'].', '.date('d')." de ".$meses[date('n')-1]. " del ".date('Y').'</th>
        </tr>
    </table>
    ';

        $pdf->writeHTML($contenido);

        $pdf->Output('Reporte Primeros Puestos - ' . $r_b_pe['nombre'] . ' ' . $r_b_sem['descripcion'] . '.pdf', 'I');
        mysqli_close($conexion);
    }
}
