<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";
include("../../include/verificar_sesion_biblioteca.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BIBLIO');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {

    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['biblioteca_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);
    $id_sesion = $rb_sesion['id'];
    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BIBLIO');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
  <a href='../admin'>Regresar</a><br>
  <a href='../../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
  </center>";
    } else {

        $id_libro = $_POST['libro'];
        $fecha_hora = date("Y-m-d H:i:s");

        $b_favoritos = buscar_favoritosByidLibroUsu($conexion, $id_libro, $id_usuario);
        $cont_favoritos = mysqli_num_rows($b_favoritos);

        if ($cont_favoritos > 0) {
            $r_b_favoritos = mysqli_fetch_array($b_favoritos);
            $id_fav = $r_b_favoritos['id'];
            $consulta = "DELETE FROM biblioteca_libros_favoritos WHERE id='$id_fav'";
            $delete = mysqli_query($conexion, $consulta);
            $color = "dark";
            $texto = "Agregar a Favoritos";
            echo '<button type="button" class="btn btn-outline-' . $color . ' waves-effect waves-light" onclick="agregar_favorito();"> ' . $texto . ' <i class="fas fa-heart"></i></button>
    <div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
<strong>Eliminado de Favoritos </strong>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
</button>
</div>';
        } else {
            $color = "danger";
            $texto = "Quitar de Favoritos";
            $c_registrar = "INSERT INTO biblioteca_libros_favoritos (id_usuario, id_libro, fecha_hora) VALUES ('$id_usuario','$id_libro','$fecha_hora')";
            $registrar = mysqli_query($conexion, $c_registrar);
            echo '<button type="button" class="btn btn-outline-' . $color . ' waves-effect waves-light" onclick="agregar_favorito();"> ' . $texto . ' <i class="fas fa-heart"></i></button>
    <div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
<strong>Agregado a Favoritos</strong>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">×</span>
</button>
</div>';
        }
    }
}
