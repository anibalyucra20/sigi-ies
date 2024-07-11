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
        $ud = $_POST['ud'];
        $semestre = $_POST['semestre'];
        $creditos = $_POST['creditos'];
        $horas = $_POST['horas'];
        $tipo = $_POST['tipo'];

        //consulta para poder generar el orden de la ud en el semestre
        $consul = "SELECT * FROM sigi_unidad_didactica WHERE id_semestre='$semestre'";
        $ejec_consl = mysqli_query($conexion, $consul);
        $conteo = mysqli_num_rows($ejec_consl);
        $orden = $conteo + 1;

        $insertar = "INSERT INTO sigi_unidad_didactica (nombre, id_semestre, creditos, horas, tipo, orden) VALUES ('$ud','$semestre','$creditos', '$horas', '$tipo', '$orden')";
        $ejecutar_insetar = mysqli_query($conexion, $insertar);
        if ($ejecutar_insetar) {
            echo "<script>
                alert('Registro Existoso');
                window.location= '../unidades_didacticas'
    			</script>";
        } else {
            echo "<script>
			alert('Error al registrar Unidad Didáctica');
			window.history.back();
				</script>
			";
        };
        mysqli_close($conexion);
    }
}
