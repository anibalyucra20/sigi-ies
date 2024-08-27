<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_bolsa.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BOLSA');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['bolsa_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);
    $id_sesion = $rb_sesion['id'];
    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BOLSA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../admin'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_bolsa.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../admin');
              </script>";
        } else {

            $id = base64_decode($_POST['data']);
            $ruc = $_POST['ruc'];
            $empresa = $_POST['empresa'];
            $direccion = $_POST['direccion'];
            $telefono = $_POST['telefono'];
            $email = $_POST['email'];
            $user = $_POST['user'];
            $estado = $_POST['estado'];

            $b_permiso_usu = buscarPermisoUsuarioByUsuarioSistema($conexion, $user, $id_sistema);
            if (mysqli_num_rows($b_permiso_usu) == 0) {
                $consulta = "INSERT INTO sigi_permisos_usuarios (id_usuario,id_sistema,id_rol) VALUES ('$user','$id_sistema',7)";
                $ejec_consulta = mysqli_query($conexion, $consulta);
            }
            $b_empresa = buscar_empresaRuc($conexion,$ruc);
            if (mysqli_num_rows($b_empresa)>0) {
               $consultaa = "UPDATE bolsa_empresa SET empresa='$empresa', direccion='$direccion', telefono='$telefono', email='$email', id_usuario='$user',estado='$estado' WHERE id='$id'";
            }else {
                $consultaa = "UPDATE bolsa_empresa SET ruc='$ruc', empresa='$empresa', direccion='$direccion', telefono='$telefono', email='$email', id_usuario='$user',estado='$estado' WHERE id='$id'";
            }

            $ejec_consulta = mysqli_query($conexion, $consultaa);

            if ($ejec_consulta) {
                echo "<script>
			        alert('Se Actualizó correctamente');
                    window.location= '../empresas';
		            </script>   
		            ";
            } else {
                //echo mysqli_error($conexion);
                echo "<script>
                        alert('Error, No se pudo realizar el proceso');
                        window.history.back();
                        </script>
                    ";
                    
            }
        }
    }
}
