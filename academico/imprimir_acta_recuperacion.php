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
        $b_coordinador = buscarUsuarioCoordinador_sedeAndPe($conexion,$rb_pe_sede['id_sede'],$rb_pe_sede['id_programa_estudio']);
        $r_b_coordinador = mysqli_fetch_array($b_coordinador);
        //buscar datos de director
        $b_director = buscarUsuarioById($conexion, $r_b_perio['director']);
        $r_b_director = mysqli_fetch_array($b_director);


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



        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle("Acta de Evaluacion de Recuperacion - " . $r_b_ud['nombre']);
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



        $text_size = 8;

        //crear el contenido 

        $contenido = '';

        $b_det_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
        $ord = 1;
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {

            $id_mat = $r_b_det_mat['id_matricula'];
            $b_mat = buscarMatriculaById($conexion, $id_mat);
            $r_b_mat = mysqli_fetch_array($b_mat);
            $id_est = $r_b_mat['id_estudiante'];
            $b_est = buscarUsuarioById($conexion, $id_est);
            $r_b_est = mysqli_fetch_array($b_est);

            if ($r_b_det_mat['recuperacion'] != "") {
                if ($r_b_det_mat['recuperacion'] > 12) {
                    $recuperacion = '<td align="center" ><font color="blue" size="' . $text_size . '">' . $r_b_det_mat['recuperacion'] . '</font></td>';
                } else {
                    $recuperacion = '<td align="center" ><font color="red" size="' . $text_size . '">' . $r_b_det_mat['recuperacion'] . '</font></td>';
                }
                $puntaje = $r_b_det_mat['recuperacion'] * $r_b_ud['creditos'];
                $contenido .= '
                <tr>
                    <td align="center" ><font size="' . $text_size . '">' . $ord . '</font></td>
                    <td align="center" ><font size="' . $text_size . '">' . $r_b_est['dni'] . '</font></td>
                    <td ><font size="' . $text_size . '"> ' . $r_b_est['apellidos_nombres'] . '</font></td>
                    ' . $recuperacion . '
                    <td align="center"><font size="' . $text_size . '"> ' . $r_b_ud['creditos'] . '</font></td>
                    <td align="center"><font size="' . $text_size . '"> ' . $puntaje . '</font></td>
                </tr>
            ';
                $ord += 1;
            }
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
            <td colspan="3" align="center"><b>ACTA DE EVALUACIÓN DE RECUPERACIÓN</b></td>
        </tr>
        
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>INSTITUCIÓN EDUCATIVA</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">'.$r_b_datos_sistema['nombre_completo'].'</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>PROGRAMA DE ESTUDIOS</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_pe['nombre'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>MÓDULO FORMATIVO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_mod['descripcion'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>UNIDAD DIDÁCTICA</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_ud['nombre'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>CRÉDITOS</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_ud['creditos'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>SEMESTRE ACADÉMICO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_sem['descripcion'] . ' - ' . $r_b_perio['nombre'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>TURNO</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $turno. '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>SECCIÓN</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $res_b_prog['seccion'] . '</font></td>
        </tr>
        <tr>
            <td width="30%"><font size="' . $text_size . '"><b>DOCENTE</b></font></td>
            <td width="5%">:</td>
            <td width="65%"><font size="' . $text_size . '">' . $r_b_docente['apellidos_nombres'] . '</font></td>
        </tr>
        <tr>
            <table border="0.2" cellspacing="0" cellpadding="0.5">
                <tr>
                    <td rowspan="2" width="10%" align="center"><font size="' . $text_size . '"><b><br>Nro Orden</b></font></td>
                    <td rowspan="2" width="15%" align="center"><font size="' . $text_size . '"><b><br>DNI</b></font></td>
                    <td rowspan="2" width="45%"  align="center"><font size="' . $text_size . '"><b><br>APELLIDOS Y NOMBRES</b></font></td>
                    <td colspan="3" width="30%" align="center"><font size="' . $text_size . '"><b>LOGRO FINAL</b></font></td>
                </tr>
                <tr>
                    <td align="center"><font size="' . $text_size . '"><b>EN NUMEROS</b></font></td>
                    <td align="center"><font size="' . $text_size . '"><b>CRÉDITOS</b></font></td>
                    <td align="center"><font size="' . $text_size . '"><b>PUNTAJE</b></font></td>
                </tr>
          ';

        $content_one .= $contenido;
        $content_one .= '</table></tr></table>';
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
            <td colspan="2" align="center"><br><br><br><br><br><br><br><br>...............................................<br>Docente</td>
        </tr>
        </table>

      ';
        $pdf->writeHTML($footer);








        $pdf->Output('Acta Ev. Recuperacion - ' . $r_b_ud['descripcion'] . '.pdf', 'I');
    }
}
