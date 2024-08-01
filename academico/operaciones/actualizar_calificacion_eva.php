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

    $id_prog = $_POST['data'];
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

    $nro_calificacion = $_POST['nro_calificacion'];

    $b_periodo_acad = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
    $r_per_acad = mysqli_fetch_array($b_periodo_acad);
    $fecha_actual = strtotime(date("d-m-Y"));
    $fecha_fin_per = strtotime($r_per_acad['fecha_fin']);
    if ($fecha_actual <= $fecha_fin_per) {


        $b_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
            $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
            $r_b_mat = mysqli_fetch_array($b_matricula);
            $b_estudiante = buscarUsuarioById($conexion, $r_b_mat['id_estudiante']);
            $r_b_est = mysqli_fetch_array($b_estudiante);

            $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
            while ($r_b_calificacion = mysqli_fetch_array($b_calificacion)) {
                $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                    $b_criterio_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);


                    while ($r_b_criterio_evaluacion = mysqli_fetch_array($b_criterio_evaluacion)) {
                        $nota =  $_POST[$r_b_est['dni'] . '_' . $r_b_criterio_evaluacion['id']];
                        if ((is_numeric($nota)) && ($nota >= 0 && $nota <= 20)) {
                            if (($nota >= 0 && $nota < 10) && strlen($nota) == 1) {
                                $calificacion = "0" . $nota;
                            } else {
                                $calificacion = $nota;
                            }
                        } else {
                            $calificacion = "";
                        }
                        $id_crit = $r_b_criterio_evaluacion['id'];
                        $consulta = "UPDATE acad_criterio_evaluacion SET calificacion='$calificacion' WHERE id='$id_crit'";
                        $ejec_consulta = mysqli_query($conexion, $consulta);
                    }
                }
            }
        }
    } else {
        echo "<script>
            alert('Periodo Finalizado, No puede Realizar Cambios');
			window.location= '../evaluaciones?data=" . base64_encode($id_prog) . "&data2=" . base64_encode($nro_calificacion) . "';
		</script>
	";
    }

    echo "<script>
			window.location= '../evaluaciones?data=" . base64_encode($id_prog) . "&data2=" . base64_encode($nro_calificacion) . "';
		</script>
	";
}
}