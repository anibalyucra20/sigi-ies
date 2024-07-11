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
        $sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
        $sistemas = decodificar($sistemas);
        $usu_id = $_POST['id'];

        $b_sistemas = buscarSistemas($conexion);
        while ($rb_sistemas = mysqli_fetch_array($b_sistemas)) {
            $cod_sistema = $rb_sistemas['codigo'];

            $sis_id = $rb_sistemas['id'];
            $b_permisos_sis = buscarPermisoUsuarioByUsuarioSistema($conexion, $usu_id, $sis_id);
            $cont_permisos_usu = mysqli_num_rows($b_permisos_sis);
            $rb_permisos_sis = mysqli_fetch_array($b_permisos_sis);
            $id_permiso_usu = $rb_permisos_sis['id'];
            //echo $rb_sistemas['nombre'];
            if (in_array($cod_sistema, $sistemas)) {
                $dato = $_POST['sistema_' . $sis_id . '_' . $usu_id];
                $rol = $_POST['rolsistema_' . $sis_id . '_' . $usu_id];
                if ($rol==0) {
                    $rol = 1;
                }
                if ($cont_permisos_usu > 0 && $dato == 0) {
                    //elimnar permiso
                    //echo " - eliminar <br>";
                    $consulta = "DELETE FROM sigi_permisos_usuarios WHERE id='$id_permiso_usu'";
                    $ejec_consulta = mysqli_query($conexion, $consulta);
                } elseif ($cont_permisos_usu == 0 && $dato == 1) {
                    // registrar permiso
                    //echo " - registrar - ".$rol." <br>";
                    $consulta = "INSERT INTO sigi_permisos_usuarios (id_usuario,id_sistema,id_rol) VALUES ('$usu_id','$sis_id','$rol')";
                    $ejec_consulta = mysqli_query($conexion, $consulta);
                } else {
                    // no se cambio nada
                    //echo " - actualizar rol<br>";
                    $consulta = "UPDATE sigi_permisos_usuarios SET id_rol='$rol'  WHERE id='$id_permiso_usu'";
                    $ejec_consulta = mysqli_query($conexion, $consulta);
                }
            } else {
                // en caso de que no tenga el sistema habilitado pero tenga permisos en ese sistema
                if ($cont_permisos_usu > 0) {
                    //elimnar permiso
                    //echo " - eliminar <br>";
                    $consulta = "DELETE FROM sigi_permisos_usuarios WHERE id='$id_permiso_usu'";
                    $ejec_consulta = mysqli_query($conexion, $consulta);
                } else {
                    // no se cambio nada
                    //echo " - no hacer nada <br>";
                }
            }
        }
        echo "<script>
        alert('Permisos Actualizados');
        window.location= '../docentes'
    </script>
";
        mysqli_close($conexion);
    }
}
