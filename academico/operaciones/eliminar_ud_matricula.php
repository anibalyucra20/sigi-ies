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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 2) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {



        $id_detalle_mat = base64_decode($_GET['data']);

        //buscar detalle de matricula
        $b_det_mat = buscarDetalleMatriculaById($conexion, $id_detalle_mat);
        $r_b_det_mat = mysqli_fetch_array($b_det_mat);

        //buscar silabo
        $b_silabo = buscarSilabosByIdProgramacion($conexion, $r_b_det_mat['id_programacion_ud']);
        $r_b_silabo = mysqli_fetch_array($b_silabo);

        //buscar programaciones de actividades del silabo para eliminar asistencias
        $b_prog_act_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo['id']);
        while ($r_b_prog_act_silabo = mysqli_fetch_array($b_prog_act_silabo)) {
            //buscamos las sesiones que corresponden a la programacion de actividades del silabo
            $b_sesion_a = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $r_b_prog_act_silabo['id']);
            while ($r_b_sesion_a = mysqli_fetch_array($b_sesion_a)) {
                //buscar asistencia del estudiante en la sesion para eliminar
                $b_asistencia = buscarAsistenciaByIdDetalleMatricula($conexion, $id_detalle_mat);
                $r_b_asistencia = mysqli_fetch_array($b_asistencia);
                $id_asitencia = $r_b_asistencia['id'];

                //eliminar asistencia
                $consulta_asis = "DELETE FROM acad_asistencia WHERE id='$id_asitencia'";
                $ejec_d_asis = mysqli_query($conexion, $consulta_asis);
            }
        }

        // ---------------------- PROCESO PARA ELIMINAR DETALLE DE MATRICULA

        //busco las calificaciones
        $b_calificacion = buscarCalificacionByIdDetalleMatricula($conexion, $id_detalle_mat);
        while ($r_b_calificacion = mysqli_fetch_array($b_calificacion)) {
            $id_calificacion = $r_b_calificacion['id'];
            // busco las evaluaciones
            $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $id_calificacion);
            while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                $id_evaluacion = $r_b_evaluacion['id'];
                //buscar criterios de evaluacion
                $b_crit_eva = buscarCriterioEvaluacionByEvaluacion($conexion, $id_evaluacion);
                while ($r_b_crit_eva = mysqli_fetch_array($b_crit_eva)) {
                    $id_crit_eva = $r_b_crit_eva['id'];
                    //eliminar criterio de evaluacion
                    $consulta_crit = "DELETE FROM acad_criterio_evaluacion WHERE id='$id_crit_eva'";
                    $ejec_d_crit = mysqli_query($conexion, $consulta_crit);
                }
                //eliminar evaluacion
                $consulta_eva = "DELETE FROM acad_evaluacion WHERE id='$id_evaluacion'";
                $ejec_d_eva = mysqli_query($conexion, $consulta_eva);
            }
            //eliminar calificacion
            $consulta_calif = "DELETE FROM acad_calificacion WHERE id='$id_calificacion'";
            $ejec_d_calif = mysqli_query($conexion, $consulta_calif);
        }

        //eliminar detalle de matricula
        $consulta_det_mat = "DELETE FROM acad_detalle_matricula WHERE id='$id_detalle_mat'";
        $ejec_d_det_mat = mysqli_query($conexion, $consulta_det_mat);

        if ($ejec_d_det_mat) {
            echo "<script>
			window.history.back();
		</script>
	";
        } else {
            echo "<script>
			alert('Error, No se pudo eliminar el registro');
			window.history.back();
		</script>
	";
        }
    }
}
