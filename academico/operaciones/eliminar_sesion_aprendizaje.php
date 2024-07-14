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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        $id_sesion = base64_decode($_GET['data']);
        $id_prog = base64_decode($_GET['data2']);

        // ---------- ELIMINAR ASISTENCIAS
        $b_asistencias = buscarAsistenciaByIdSesion($conexion, $id_sesion);
        while ($r_b_asistencias = mysqli_fetch_array($b_asistencias)) {
            $id_asitencia = $r_b_asistencias['id'];
            $consulta_asis = "DELETE FROM acad_asistencia WHERE id='$id_asitencia'";
            $ejec_d_asis = mysqli_query($conexion, $consulta_asis);
        }

        // ----------- ELIMINAR ACTIVIDAD EVALUACION SESION
        $b_act_eva = buscarActividadEvaluacionByIdSesion($conexion, $id_sesion);
        while ($r_b_act_eva = mysqli_fetch_array($b_act_eva)) {
            $id_act_eva = $r_b_act_eva['id'];
            $consulta_act_eva = "DELETE FROM acad_actividad_evaluacion_sesion_aprendizaje WHERE id='$id_act_eva'";
            $ejec_d_act_eva = mysqli_query($conexion, $consulta_act_eva);
        }

        //----------- ELIMINAR MOMENTOS SESION
        $b_momentos = buscarMomentosSesionAprendizajeByIdSesion($conexion, $id_sesion);
        while ($r_b_momentos = mysqli_fetch_array($b_momentos)) {
            $id_momento = $r_b_momentos['id'];
            $consulta_momentos = "DELETE FROM acad_momentos_sesion_aprendizaje WHERE id='$id_momento'";
            $ejec_d_momento = mysqli_query($conexion, $consulta_momentos);
        }

        //----------- ELIMINAR SESION DE APRENDIZAJE
        $consulta_sesion = "DELETE FROM acad_sesion_aprendizaje WHERE id='$id_sesion'";
        $ejec_d_sesion = mysqli_query($conexion, $consulta_sesion);

        if ($ejec_d_sesion) {
            echo "<script>
			window.location= '../sesiones?data=" . base64_encode($id_prog) . "';
		</script>
	";
        } else {
            echo "<script>
			alert('Error, No se pudo eliminar la Sesión de Aprendizaje');
			window.history.back();
		</script>
	";
        }
    }
}
