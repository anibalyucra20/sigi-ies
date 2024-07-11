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


        $id = $_POST['id'];
        $cod_modular = $_POST['cod_modular'];
        $nombre = $_POST['nombre'];
        $departamento = $_POST['departamento'];
        $provincia = $_POST['provincia'];
        $distrito = $_POST['distrito'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $responsable = $_POST['responsable'];

        //actualizar
        $sql = "UPDATE sigi_sedes SET cod_modular='$cod_modular', nombre='$nombre', departamento='$departamento', provincia='$provincia', distrito='$distrito', direccion='$direccion', telefono='$telefono', correo='$email', responsable='$responsable' WHERE id=$id";
        $ejec_consulta = mysqli_query($conexion, $sql);
        if ($ejec_consulta) {
            echo "<script>
			alert('Registro Actualizado de manera Correcta');
			window.location= '../sedes';
		</script>
	";
        } else {
            echo "<script>
			alert('Error al Actualizar Registro, por favor contacte con el administrador..');
			window.history.back();
		</script>
	";
        }
        mysqli_close($conexion);
    }
}
