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

        $id_sesion_indiv = $_POST['id_sesion_indiv'];

        $b_sesion_indiv = buscarTutoriaSesIndivById($conexion, $id_sesion_indiv);
        $r_b_sesion_indiv = mysqli_fetch_array($b_sesion_indiv);
        $id_tut_est = $r_b_sesion_indiv['id_tutoria_estudiante'];
        $b_tutoria_est = buscarTutoriaEstudiantesById($conexion, $id_tut_est);
        $r_b_tutoria_est = mysqli_fetch_array($b_tutoria_est);
        $id_tutoria = $r_b_tutoria_est['id_tutoria'];
        $b_tutoria = buscarTutoriaById($conexion, $id_tutoria);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);
        $id_docente_tutoria = $r_b_tutoria['id_docente'];
        if ($id_docente_tutoria == $id_docente_sesion) {

            $titulo = $_POST['titulo'];
            $motivo = $_POST['motivo'];
            $fecha_hora = $_POST['fecha_hora'];
            $link = $_POST['link'];
            $resultados = $_POST['resultados'];
            $asistencia = $_POST['asistencia'];

            $consulta = "UPDATE tutoria_sesion_individual SET titulo='$titulo',fecha_hora='$fecha_hora',motivo='$motivo',link_reunion='$link',resultados='$resultados',asistencia='$asistencia' WHERE id='$id_sesion_indiv'";
            $ejecutar_consulta = mysqli_query($conexion, $consulta);
            if ($ejecutar_consulta) {
                echo "<script>
                alert('Actualizado Correctamente');
                window.location= '../tutoria_sesion_individual?data=" . base64_encode($id_tut_est) . "'
    			</script>";
            } else {
                echo "<script>
			alert('Error al Actualizar, por favor verifique sus datos');
			window.history.back();
				</script>
			";
            };
        } else {
            echo "<script>
    window.history.back();
        </script>
    ";
        }






        mysqli_close($conexion);
    }
}
