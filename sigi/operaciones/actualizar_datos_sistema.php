<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_sigi.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_SIGI');
    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['sigi_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_SIGI');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado']==0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $id = 1;
        $dominio_sistema = $_POST['dominio_sistema'];
        $favicon = $_POST['favicon'];
        $logo = $_POST['logo'];
        $titulo_c = $_POST['titulo_c'];
        $titulo_a = $_POST['titulo_a'];
        $pie_pagina = $_POST['pie_pagina'];
        $host_email = $_POST['host_email'];
        $email_email = $_POST['email_email'];
        $password_email = $_POST['password_email'];
        $puerto_email = $_POST['puerto_email'];
        $color_correo = $_POST['color_correo'];
        $cant_semanas = $_POST['cant_semanas'];

        $consulta = "UPDATE sigi_datos_sistema SET dominio_pagina='$dominio_sistema', favicon='$favicon', logo='$logo', nombre_completo='$titulo_c',nombre_corto='$titulo_a', pie_pagina='$pie_pagina', host_mail='$host_email', email_email='$email_email', password_email='$password_email', puerto_email='$puerto_email', color_correo='$color_correo', cant_semanas='$cant_semanas' WHERE id=$id";
        $ejec_consulta = mysqli_query($conexion, $consulta);
        if ($ejec_consulta) {
            echo "<script>
					alert('Datos actualizados de manera Correcta');
					window.location= '../sistema';
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
    }
}