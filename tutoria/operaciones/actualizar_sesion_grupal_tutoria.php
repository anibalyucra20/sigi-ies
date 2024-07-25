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
       
        $id_sesion_grupal = $_POST['id_sesion_grupal'];

        $b_sesion_grupal = buscarTutoriaSesGrupalById($conexion, $id_sesion_grupal);
        $r_b_sesion_grupal = mysqli_fetch_array($b_sesion_grupal);
        $b_tutoria = buscarTutoriaById($conexion, $r_b_sesion_grupal['id_tutoria']);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);
        if ($id_usuario == $r_b_tutoria['id_docente']) {

            $titulo = $_POST['titulo'];
            $fecha_hora = date("Y-m-d H:i:s", strtotime($_POST['fecha_hora']));
            $tema = $_POST['tema'];
            $link = $_POST['link'];
            $resultados = $_POST['resultados'];
            $array_asistentes = $_POST['array_asistentes'];


            $consulta = "UPDATE tutoria_sesion_grupal SET titulo='$titulo',fecha_hora='$fecha_hora',tema='$tema',link_reunion='$link',resultados='$resultados',asistentes='$array_asistentes' WHERE id='$id_sesion_grupal'";
            $ejec_consulta = mysqli_query($conexion, $consulta);

            if ($ejec_consulta) {
                echo "<script>
            alert('Datos Modificados Correctamente');
			window.location= '../datos_sesion_grupal_tutoria?data=" . base64_encode($id_sesion_grupal) . "';
		</script>
	";
            } else {
                echo "<script>
			alert('Error al Actualizar Registro, por favor contacte con el administrador');
			window.history.back();
		</script>
	";
            }

            mysqli_close($conexion);
        } else {
            echo "<script>
        window.history.back();
            </script>
        ";
        }
    }
}
