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

    $id_prog = base64_decode($_GET['data']);
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    if ($contar_permiso == 0  || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {


        $nro_calificacion = base64_decode($_GET['data2']);
        $evaluacion = base64_decode($_GET['data3']);

        $b_det_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
            //buscamos la calificacion que correspone
            $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
            $r_b_calificacion = mysqli_fetch_array($b_calificacion);
            $id_calificacion = $r_b_calificacion['id'];
            $b_evaluacion = buscarEvaluacionByIdCalificacion_detalle($conexion, $id_calificacion, $evaluacion);
            $r_b_evaluacion = mysqli_fetch_array($b_evaluacion);
            $id_evaluacion = $r_b_evaluacion['id'];
            $b_crit_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $id_evaluacion);

            $cant_crit = mysqli_num_rows($b_crit_evaluacion) + 1;
            //agregaremos nueva criterio de evaluacion
            $consulta = "INSERT INTO acad_criterio_evaluacion (id_evaluacion, orden, detalle, calificacion) VALUES ('$id_evaluacion','$cant_crit','','')";
            mysqli_query($conexion, $consulta);
        }
        echo "<script>
				  window.location.replace('../evaluaciones?data=" . base64_encode($id_prog) . "&data2=" . base64_encode($nro_calificacion) . "');
			  </script>";
    }
}
