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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        include_once("../librerias/PHP_XLSXWriter/xlsxwriter.class.php");

        /*header ("Content-Type: application/vnd.ms-excel; charset=iso-8859-1");
    header ("Content-Disposition: attachment; filename=plantilla.xls");*/

        $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
        $r_b_ud = mysqli_fetch_array($b_ud);
        $titulo_archivo = "Reporte_" . $r_b_ud['nombre'] . "_" . date("d") . "_" . date("m") . "_" . date("Y");

        //generamos excel
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        error_reporting(E_ALL & ~E_NOTICE);

        $filename = $titulo_archivo . ".xlsx";
        header('Content-disposition: attachment; filename="' . XLSXWriter::sanitize_filename($filename) . '"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $writer = new XLSXWriter();

        $styles1 = array('font' => 'Calibri', 'font-size' => 11, 'font-style' => 'bold', 'fill' => '#eee', 'halign' => 'center', 'border' => 'left,right,top,bottom');

        $header = array(
            'NRO' => 'NRO', //text
            'CÓDIGO ALUMNO' => 'CÓDIGO ALUMNO', //text
            'ALUMNO' => 'ALUMNO',
            'NOTA' => 'NOTA',
        );

        //imprime encabezado
        $writer->writeSheetRow('Plantilla', $header, $styles1);



        $b_det_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
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

            $b_calif = buscarCalificacionByIdDetalleMatricula($conexion, $r_b_det_mat['id']);
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

            if ($porcent_ina > 30) {
                $calificacion_final = 0;
            }

            if ($r_b_det_mat['recuperacion'] != '') {
                $calificacion_final = $r_b_det_mat['recuperacion'];
            }
            if ($r_b_mat['licencia'] != "") {
                $calificacion_final = "Licencia";
            }

            //imprime contenido
            $writer->writeSheetRow('Plantilla', $rowdata = array($ord, $r_b_est['dni'], $r_b_est['apellidos_nombres'], $calificacion_final), $styles9);

            $ord += 1;
        }

        $writer->writeToStdOut();

        exit(0);
    }
}
