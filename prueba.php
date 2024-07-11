<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Inicio <?php include("include/header_title.php"); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="Sistema Integrado de Gestión Institucional" name="description" />
    <meta content="Anibal Yucra Curo" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- App css -->
    <link href="plantilla/Principal/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="plantilla/Principal/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="plantilla/Principal/assets/css/theme.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include "menu.php"; ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <?php
                    include("include/conexion.php");
                    include("include/busquedas.php");
                    include("include/funciones.php");
                    $sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
                    $sistemas = decodificar($sistemas);
                    foreach ($sistemas as $key => $value) {
                        echo $value."<br>";
                    }
                    ?>

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0 font-size-18">Starter</h4>
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

    <!-- Overlay-->
    <div class="menu-overlay"></div>


    <!-- jQuery  -->
    <script src="plantilla/Principal/assets/js/jquery.min.js"></script>
    <script src="plantilla/Principal/assets/js/bootstrap.bundle.min.js"></script>
    <script src="plantilla/Principal/assets/js/metismenu.min.js"></script>
    <script src="plantilla/Principal/assets/js/waves.js"></script>
    <script src="plantilla/Principal/assets/js/simplebar.min.js"></script>

    <!-- App js -->
    <script src="plantilla/Principal/assets/js/theme.js"></script>

</body>

</html>