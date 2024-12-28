<?php
include("include/conexion.php");
include("include/busquedas.php");
include("include/funciones.php");


$datos_institucion = buscarDatosSistema($conexion);
$rb_datos_iest = mysqli_fetch_array($datos_institucion);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Inicio - SIGI</title>
    <link rel="shortcut icon" href="images/favicon.ico">

    <!-- Bootstrap -->
    <link href="plantilla/Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="plantilla/Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="plantilla/Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="plantilla/Gentella/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
</head>

<body class="login">
    <div>
        <br>
        <br>
        <center>
            <h1>SISTEMA INTEGRADO DE GESTIÓN INSTITUCIONAL - <?php echo $rb_datos_iest['nombre_corto']; ?></h1>
        </center>
        <center>
            <h2></h2>
        </center>
        <div class="login_wrapper">
            <center><img src="images/logo.png" width="150px"></center>
            <br>
        </div>

        <div class="x_content">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
                    $sistemas = decodificar($sistemas);
                    //print_r($sistemas);
                    if (!is_array($sistemas)) {
                        echo "<center><h1>No Cuenta con Sistemas Habilitados, Comuniquese con el Proveedor</h1></center>";
                    }
                    echo $_SERVER['HTTP_HOST'];
                    if (in_array("S_SIGI", $sistemas)) {
                    ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema Institucional</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="sigi/" class="btn btn-primary btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php
                    }
                    if (in_array("S_ACAD", $sistemas)) {
                    ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema Académico</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="academico/" class="btn btn-success btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php
                        # code...
                    }
                    if (in_array("S_ADMISION", $sistemas)) {
                    ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema de Admisión</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="admision/" class="btn btn-info btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php
                        # code...
                    }
                    if (in_array("S_TUTORIA", $sistemas)) {
                    ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema Tutoría</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="tutoria/" class="btn btn-warning btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php
                        # code...
                    }
                    if (in_array("S_BIBLIO", $sistemas)) { ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema Biblioteca</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="biblioteca/" class="btn btn-primary btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php }
                    if (in_array("S_EGRE", $sistemas)) { ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Sistema Egresados</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="biblioteca/" class="btn btn-success btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                        <?php }
                    if (in_array("S_BOLSA", $sistemas)) { ?>
                        <!-- price element -->
                        <div class="col-md-3 col-sm-6 col-xs-12">
                            <div class="pricing">
                                <div class="title">
                                    <h2></h2>
                                    <h1>Bolsa Laboral</h1>
                                </div>
                                <div class="x_content">
                                    <div class="">
                                        <div class="pricing_features">
                                            <img src="images/logo.png" alt="" width="100%" height="100%">
                                        </div>
                                    </div>
                                    <div class="pricing_footer">
                                        <a href="bolsa/" class="btn btn-success btn-block" role="button">Acceder</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- price element -->
                    <?php }

                    ?>




                </div>
            </div>
        </div>



        <div class="clearfix"></div>

        <div class="separator">


            <div class="clearfix"></div>
            <br />

            <div>
                <center>
                    <h2>Copiright (c) 2023 Anibal Yucra</h2>
                </center>
                <p></p>
            </div>
        </div>
        </form>
        <section class="">

        </section>



    </div>
    </div>
</body>

</html>