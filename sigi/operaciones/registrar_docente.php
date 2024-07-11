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

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        $dni = $_POST['dni'];
        $nom_ap = $_POST['nom_ap'];
        $genero = $_POST['genero'];
        $fecha_nac = $_POST['fecha_nac'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $discapacidad = $_POST['discapacidad'];
        $id_sede = $_POST['sede'];
        $id_cargo = $_POST['cargo'];
        $id_pe = $_POST['pe'];

        $b_ult_periodo = buscarPresentePeriodoAcad($conexion);
        $rb_ult_periodo = mysqli_fetch_array($b_ult_periodo);
        $id_periodo_registro = $rb_ult_periodo['id'];
        //verificar si el docente ya esta registrado
        $busc_ult_docente = "SELECT * FROM sigi_usuarios WHERE dni='$dni'";
        $ejec_busc_ult_doc = mysqli_query($conexion, $busc_ult_docente);
        $conteo = mysqli_num_rows($ejec_busc_ult_doc);
        $pass = generar_contrasenia();
        $pass_secure = password_hash($pass, PASSWORD_DEFAULT);
        if ($conteo > 0) {

            $actualizar = "UPDATE sigi_usuarios SET apellidos_nombres='$nom_ap', genero='$genero', fecha_nac='$fecha_nac', direccion='$direccion', correo='$email', telefono='$telefono', id_programa_estudios='$id_pe', discapacidad='$discapacidad', id_rol='$id_cargo',id_sede='$id_sede', estado=1 WHERE dni=$dni";
            $ejecutar_actualizar = mysqli_query($conexion, $actualizar);
            echo "<script>
			alert('El usuario se registró correctamente');
			window.history.back();
				</script>
			";
        } else {

            $insertar = "INSERT INTO sigi_usuarios (dni, apellidos_nombres, genero, fecha_nac, direccion, correo, telefono, id_periodo_registro, id_programa_estudios, discapacidad, id_rol, id_sede, estado, password,reset_password,token_password) VALUES ('$dni','$nom_ap','$genero', '$fecha_nac', '$direccion', '$email', '$telefono', '$id_periodo_registro', '$id_pe', '$discapacidad', '$id_cargo', '$id_sede', 1, '$pass_secure',0,' ')";
            $ejecutar_insetar = mysqli_query($conexion, $insertar);
            if ($ejecutar_insetar) {
                echo "<script>
                alert('Registro Existoso, la contraseña del Usuario es : " . $pass . "');
                window.location= '../docentes'
    			</script>";
            } else {
                echo "<script>
			alert('Error al registrar docente, por favor verifique sus datos');
			window.history.back();
				</script>
			";
            };
        };
        mysqli_close($conexion);
    }
}
