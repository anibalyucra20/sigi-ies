<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
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

    $id_sesion = base64_decode($_POST['ddata']);
    $id_prog = base64_decode($_POST['ddata2']);
    $asistencia = $_POST['asistencia'];

    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $b_periodo_acad = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
        $r_per_acad = mysqli_fetch_array($b_periodo_acad);
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($r_per_acad['fecha_fin']);
        if ($fecha_actual <= $fecha_fin_per) {
            $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
            while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {

                $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $id_sesion, $r_b_det_mat['id']);
                $r_b_asistencia = mysqli_fetch_array($b_asistencia);

                $id_asistencia = $r_b_asistencia['id'];

                $consulta = "UPDATE acad_asistencia SET asistencia='$asistencia' WHERE id='$id_asistencia'";
                $ejec_consulta = mysqli_query($conexion, $consulta);
            }
        }
    }
}
