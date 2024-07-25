<?php
include "../../include/conexion.php";
include "../../include/busquedas.php";
include "../../include/funciones.php";
include("../../include/verificar_sesion_tutoria.php");
if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_TUTORIA');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {

    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['tutoria_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_TUTORIA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $id_periodo_act = $_SESSION['tutoria_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['tutoria_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../../tutoria/'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_tutoria.php'>Cerrar Sesión</a><br>
    </center>";
    } else {


        $id_tutoria = base64_decode($_GET['data']);
        $id_estudiante = base64_decode($_GET['data2']);



        $b_tutoria = buscarTutoriaById($conexion, $id_tutoria);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);
        $b_docente_tutoria = buscarUsuarioById($conexion, $r_b_tutoria['id_docente']);
        $r_b_docente_tutoria = mysqli_fetch_array($b_docente_tutoria);

            $b_tutoria_est = buscarTutoriaEstudiantesByIdTutoriaAndIdEst($conexion, $id_tutoria, $id_estudiante);
            $r_b_tutoria_est = mysqli_fetch_array($b_tutoria_est);
            $id_tutoria_estudiante = $r_b_tutoria_est['id'];

            $b_tut_rec_info = buscarTutoriaRecojoInfoByIdTutEst($conexion, $id_tutoria_estudiante);
            while ($r_b_tut_rec_info = mysqli_fetch_array($b_tut_rec_info)) {
                $id_rec_info = $r_b_tut_rec_info['id'];
                $eliminar_rec_info = "DELETE FROM tutoria_recojo_informacion WHERE id='$id_rec_info'";
                $ejec_delete = mysqli_query($conexion, $eliminar_rec_info);
            }
            $b_tutoria_ses_indiv = buscarTutoriaSesIndivByIdTutEst($conexion, $id_tutoria_estudiante);
            while ($r_b_tutoria_ses_indiv = mysqli_fetch_array($b_tutoria_ses_indiv)) {
                $id_ses_indiv = $r_b_tutoria_ses_indiv['id'];
                $eliminar_ses_indiv = "DELETE FROM tutoria_sesion_individual WHERE id='$id_ses_indiv'";
                $ejec_delete = mysqli_query($conexion, $eliminar_ses_indiv);
            }
            $eliminar_tut_est = "DELETE FROM tutoria_estudiantes WHERE id='$id_tutoria_estudiante'";
            $ejec_delete = mysqli_query($conexion, $eliminar_tut_est);
            if ($ejec_delete) {
                echo "<script>
			alert('Eliminado Correctamente');
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
