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

        $id_docente = $_POST['docente'];

        $b_tutoria = buscarTutoriaByIdDocenteAndIdPeriodoSede($conexion, $id_docente, $id_periodo_act, $id_sede_act);
        $cont_tutoria = mysqli_num_rows($b_tutoria);
        if ($cont_tutoria == 0) {
            $insertar = "INSERT INTO tutoria_programacion (id_sede, id_periodo_academico, id_docente, conclusiones) VALUES ('$id_sede_act', '$id_periodo_act','$id_docente', '')";
            $ejecutar_insetar = mysqli_query($conexion, $insertar);
            if ($ejecutar_insetar) {
                echo "<script>
                alert('Registro Existoso');
                window.location= '../programacion'
    			</script>";
            } else {
                echo "<script>
			alert('Error al registrar, por favor verifique sus datos');
			window.history.back();
				</script>
			";
            };
        } else {
            echo "<script>
			alert('Error, El docente ya se encuentra registrado como tutor');
			window.history.back();
				</script>
			";
        }








        mysqli_close($conexion);
    }
}
