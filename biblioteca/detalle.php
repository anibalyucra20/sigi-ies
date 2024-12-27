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

        $b_programa = buscarProgramaEstudioById($conexion, $r_b_libro['id_programa_estudio']);
        $r_b_programa = mysqli_fetch_array($b_programa);

        $b_semestre = buscarSemestreById($conexion, $r_b_libro['id_semestre']);
        $r_b_semestre = mysqli_fetch_array($b_semestre);

        $b_ud = buscarUnidadDidacticaById($conexion, $r_b_libro['id_unidad_didactica']);
        $r_b_ud = mysqli_fetch_array($b_ud);

?>


        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <title>Detalle <?php include("../include/header_title.php"); ?></title>
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
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">Información de Libro</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <center>
                                                        <?php
                                                        $b_favorito = buscar_favoritosByidLibroUsu($conexion, $r_b_libro['id'], $id_usuario);
                                                        $cont = mysqli_num_rows($b_favorito);
                                                        if ($cont > 0) {
                                                            $color = "danger";
                                                            $texto = "Quitar de Favoritos";
                                                        } else {
                                                            $color = "dark";
                                                            $texto = "Agregar a Favoritos";
                                                        }
                                                        ?>
                                                        <div class="row">
                                                        <a href="javascript: history.go(-1)" class="btn btn-danger">Regresar</a>
                                                            <div class="col-md-6" id="mostrar_noti">
                                                                <button type="button" class="btn btn-outline-<?php echo $color; ?> waves-effect waves-light" onclick="agregar_favorito();"> <?php echo $texto; ?> <i class="fas fa-heart"></i></button>
                                                            </div>
                                                            <input type="hidden" id="librodd" value="<?php echo $link_libro; ?>">
                                                            <a href="lectura?libro=<?php echo base64_encode($link_libro); ?>" class="btn btn-success">Leer Libro</a>
                                                        </div>
                                                    </center>
                                                    <img src="portadas/<?php echo $r_b_libro['portada'];  ?>" alt="" style="width:100%; height:500px; margin-top:5px;">
                                                    <br>
                                                    <br>

                                                </div>
                                                <div class="col-md-8 mb-3">
                                                    <h4><?php echo $r_b_libro['titulo']; ?></h4>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <p><b>Programa de Estudio</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_programa['nombre']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Semestre</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_semestre['descripcion']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Unidad Didáctica</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_ud['nombre']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Nro de Páginas</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['paginas']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Autor</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['autor']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Editorial</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['editorial']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>ISBN</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['isbn']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Edición</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['edicion']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Tomo</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['tomo']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Categoría</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p>: <?php echo $r_b_libro['tipo_libro']; ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p><b>Temas Relacionados</b></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p class="text-justify">: <?php echo $r_b_libro['temas_relacionados']; ?></p>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end page title -->
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
                function agregar_favorito() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/add_favorito.php",
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
