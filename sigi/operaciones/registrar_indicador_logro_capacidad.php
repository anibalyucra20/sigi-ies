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

        $id_capacidad = $_POST['id'];
        $data = base64_encode($id_capacidad);
        $descripcion = $_POST['descripcion'];

        //buscar codigo para asignar
        $buscar_indicador_capacidad = buscarIndCapacidadByIdCapacidad($conexion, $id_capacidad);
        $conteo_res = mysqli_num_rows($buscar_indicador_capacidad);
        $conteo = $conteo_res + 1;
        $codigo = "I" . $conteo;

        $insertar = "INSERT INTO sigi_ind_logro_capacidad (id_capacidad, codigo, descripcion) VALUES ('$id_capacidad', '$codigo', '$descripcion')";
        $ejecutar_insetar = mysqli_query($conexion, $insertar);
        if ($ejecutar_insetar) {
            echo "<script>
                window.location= '../indicador_logro_capacidad?id=".$data."';
    			</script>";
        } else {
            echo "<script>
			alert('Error al registrar Indicador de Logro de Capacidad');
			window.history.back();
				</script>
			";
        };
        mysqli_close($conexion);
    }
}
