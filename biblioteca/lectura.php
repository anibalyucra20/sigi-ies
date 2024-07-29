<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_biblioteca.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BIBLIO');

    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['biblioteca_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

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
    <a href='../biblioteca/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        $link_libro = base64_decode($_GET['libro']);
        $b_libro = buscar_libroById($conexion, $link_libro);
        $r_b_libro = mysqli_fetch_array($b_libro);

?>


        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <title>Lectura <?php include("../include/header_title.php"); ?></title>
            <?php include "include/header.php"; ?>

            <!-- Script obtenido desde CDN jquery -->
            <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        </head>

        <body>

            <!-- Begin page -->
            <div id="layout-wrapper">
                <div class="main-content">
                    <?php include "include/menu.php"; ?>
                    <div class="page-content">
                        <div class="container-fluid">
                            <!-- start page title -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <input type="hidden" id="librodd" value="<?php echo $link_libro; ?>">
                                                            <?php
                                                            $b_favorito = buscar_favoritosByidLibroUsuTipo($conexion, $r_b_libro['id'], $r_buscar_sesion['id_usuario'], $tipo_usuario);
                                                            $cont = mysqli_num_rows($b_favorito);
                                                            if ($cont > 0) {
                                                                $color = "danger";
                                                                $texto = "Quitar de Favoritos";
                                                            } else {
                                                                $color = "dark";
                                                                $texto = "Agregar a Favoritos";
                                                            }
                                                            ?>
                                                            <div id="mostrar_noti">
                                                                <button type="button" class="btn btn-outline-<?php echo $color; ?> waves-effect waves-light" id="btn_agregar"> <?php echo $texto; ?> <i class="fas fa-heart"></i></button>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <center>
                                                                <h4><?php echo $r_b_libro['titulo'] ?></h4>
                                                            </center>
                                                        </div>
                                                        <div class="col-md-4" id="mostrar_noti">
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                            <iframe src="archivos/<?php echo $r_b_libro['libro'];  ?>" type="application/pdf" width="100%" height="900px"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end page title -->
                        <?php
                        // registrar lectura


                        $id_libro = $r_b_libro['id'];
                        $id_sesion = $_SESSION['id_sesion_biblioteca'];
                        $id_usuario = $r_buscar_sesion['id_usuario'];
                        $fecha_hora = date("Y-m-d H:i:s");
                        $cont = 0;

                        $b_lecturas = buscar_lecturasByidLibroUsuTipo($conexion, $id_libro, $id_usuario, $tipo_usuario);
                        while ($r_b_lecturas = mysqli_fetch_array($b_lecturas)) {
                            if (date("Y-m-d", strtotime($r_b_lecturas['fecha_hora'])) == date("Y-m-d")) {
                                $cont++;
                            }
                        }
                        if ($cont < 1) {
                            $consulta = "INSERT INTO lecturas (id_sesion, id_usuario, tipo_usuario, id_libro, fecha_hora) VALUES ('$id_sesion', '$id_usuario', '$tipo_usuario', '$id_libro', '$fecha_hora')";
                            $ejecutar = mysqli_query($conexion, $consulta);
                        }

                        ?>



                    </div> <!-- container-fluid -->
                </div>
                <!-- End Page-content -->

                <?php include "include/footer.php"; ?>

            </div>
            <!-- end main content-->

            </div>
            <!-- END layout-wrapper -->

            <?php include "include/pie_scripts.php"; ?>

            <script type="text/javascript">
                $(document).ready(function() {
                    $('#btn_agregar').click(function() {
                        agregar_favorito();
                    });
                })
            </script>
            <script type="text/javascript">
                function agregar_favorito() {
                    $.ajax({
                        type: "POST",
                        url: "add_favorito.php",
                        data: "libro=" + $('#librodd').val(),
                        success: function(r) {
                            $('#mostrar_noti').html(r);
                        }
                    });
                }
            </script>


        </body>

        </html>


<?php }
}
