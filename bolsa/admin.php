<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_bolsa.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BIBLIO');

    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['bolsa_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

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
    <a href='admin'>Regresar</a><br>
    <a href='../include/cerrar_sesion_bolsa'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../bolsa/');
              </script>";
        } else {

?>

            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="utf-8" />
                <title>Bola <?php include("../include/header_title.php"); ?></title>
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                <!-- App favicon -->
                <link rel="shortcut icon" href="../images/favicon.ico">

                <!-- App css -->
                <link href="../plantilla/biblioteca/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
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
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h5 class="card-title mb-0">Ofertas Laborales</h5>
                                                </div>
                                                <div class="row d-flex align-items-center mb-4">
                                                    <div class="col-8">
                                                        <h2 class="d-flex align-items-center mb-0">
                                                            <?php
                                                            $b_libros = buscar_libro($conexion);
                                                            echo mysqli_num_rows($b_libros); ?>
                                                        </h2>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <a href="libros"> Ver</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end card body-->
                                        </div><!-- end card-->
                                    </div> <!-- end col-->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h5 class="card-title mb-0">Estudiantes</h5>
                                                </div>
                                                <div class="row d-flex align-items-center mb-4">
                                                    <div class="col-8">
                                                        <h2 class="d-flex align-items-center mb-0">
                                                            <?php echo date('Y-m-d'); ?>
                                                        </h2>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <a href="libros">Ver</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end card body-->
                                        </div><!-- end card-->
                                    </div> <!-- end col-->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h5 class="card-title mb-0">Empresas</h5>
                                                </div>
                                                <div class="row d-flex align-items-center mb-4">
                                                    <div class="col-8">
                                                        <h2 class="d-flex align-items-center mb-0">
                                                            <?php echo date('Y-m-d'); ?>
                                                        </h2>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <a href="empresas">Ver</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end card body-->
                                        </div><!-- end card-->
                                    </div> <!-- end col-->
                                    <div class="col-md-6 col-xl-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h5 class="card-title mb-0">Accesos</h5>
                                                </div>
                                                <div class="row d-flex align-items-center mb-4">
                                                    <div class="col-8">
                                                        <h2 class="d-flex align-items-center mb-0">
                                                            <?php echo date('Y-m-d'); ?>
                                                        </h2>
                                                    </div>
                                                    <div class="col-4 text-right">
                                                        <a href="accesos">Ver</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end card body-->
                                        </div><!-- end card-->
                                    </div> <!-- end col-->
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

                <!-- jQuery  -->
                <script src="../plantilla/biblioteca/assets/js/jquery.min.js"></script>
                <script src="../plantilla/biblioteca/assets/js/bootstrap.bundle.min.js"></script>
                <script src="../plantilla/biblioteca/assets/js/waves.js"></script>
                <script src="../plantilla/biblioteca/assets/js/simplebar.min.js"></script>

                <!-- App js -->
                <script src="../plantilla/biblioteca/assets/js/theme.js"></script>
            </body>

            </html>

<?php }
    }
}
