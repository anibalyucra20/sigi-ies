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

    if ($contar_permiso == 0  || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 2) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $id = $_POST['id'];
        $dni_a = $_POST['dni_a'];
        $dni = $_POST['dni'];
        $nom_ap = $_POST['ap_nom'];
        $genero = $_POST['genero'];
        $fecha_nac = $_POST['fecha_nac'];
        $direccion = $_POST['direccion'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $carrera = $_POST['carrera'];
        $discapacidad = $_POST['discapacidad'];

        $rol = 6;

        //verificar que el dni solo este registrado en 1 carrera
        $busc_est_car = "SELECT * FROM sigi_usuarios WHERE dni='$dni' AND id_programa_estudios='$carrera' AND id_rol='$rol'";
        $ejec_busc_est_car = mysqli_query($conexion, $busc_est_car);
        $conteo = mysqli_num_rows($ejec_busc_est_car);


        $sql = "UPDATE sigi_usuarios SET dni='$dni', apellidos_nombres='$nom_ap', genero='$genero', fecha_nac='$fecha_nac', direccion='$direccion', correo='$email', telefono='$telefono', id_programa_estudios='$carrera', discapacidad='$discapacidad' WHERE id=$id";
        $ejec_consulta = mysqli_query($conexion, $sql);

        registrar_usuario_pe($conexion, $id, $carrera, $id_periodo_act);

        if ($ejec_consulta) {
            echo "<script>
			alert('Registro Actualizado de manera Correcta');
			window.location= '../estudiantes';
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
