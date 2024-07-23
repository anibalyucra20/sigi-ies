<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['acad_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_ACAD');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    $id_periodo_act = $_SESSION['acad_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['acad_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Reportes<?php include("../include/header_title.php"); ?></title>
            <!--icono en el titulo-->
            <link rel="shortcut icon" href="../images/favicon.ico">
            <!-- Bootstrap -->
            <link href="../plantilla/Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
            <!-- Font Awesome -->
            <link href="../plantilla/Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
            <!-- NProgress -->
            <link href="../plantilla/Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
            <!-- iCheck -->
            <link href="../plantilla/Gentella/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
            <!-- bootstrap-progressbar -->
            <link href="../plantilla/Gentella/vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
            <!-- JQVMap -->
            <link href="../plantilla/Gentella/vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet" />
            <!-- bootstrap-daterangepicker -->
            <link href="../plantilla/Gentella/vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
            <!-- Custom Theme Style -->
            <link href="../plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
        </head>

        <body class="nav-md" onload="listar_est();">
            <div class="container body">
                <div class="main_container">
                    <?php
                    include("include/menu_docente.php");
                    $id_pe = $rb_usuario['id_programa_estudios'];
                    ?>
                    <!-- page content -->
                    <div class="right_col" role="main">
                        <!-- top tiles -->
                        <div class="row tile_count">
                            <div class="row top_tiles">
                                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href="" data-toggle="modal" data-target=".rep_nomina">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-sort-amount-desc"></i></div>
                                            <div class="count">Reporte</div>
                                            <h3>Nómina de Matrícula</h3>
                                            <p>Reporte de Nómina de Matrícula</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href="" data-toggle="modal" data-target=".rep_consolidado">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-anchor"></i></div>
                                            <div class="count"> Reporte</div>
                                            <h3>Consolidado por Semestre</h3>
                                            <p>Reporte Consolidado por Semestre</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href="" data-toggle="modal" data-target=".rep_consolidado_detallado">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-anchor"></i></div>
                                            <div class="count"> Reporte</div>
                                            <h3>Consolidado Detallado</h3>
                                            <p>Reporte Consolidado por Semestre Detallado</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href="" data-toggle="modal" data-target=".rep_individual">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-comments-o"></i></div>
                                            <div class="count">Reporte</div>
                                            <h3>Indivual</h3>
                                            <p>Reporte Individual Por Estudiante</p>
                                        </div>
                                    </a>
                                </div>

                                <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                    <a href="" data-toggle="modal" data-target=".rep_primeros_puestos">
                                        <div class="tile-stats">
                                            <div class="icon"><i class="fa fa-check-square-o"></i></div>
                                            <div class="count">Reporte</div>
                                            <h3>Primeros Puestos</h3>
                                            <p>Reporte de Primeros Puestos por Semestre</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!--MODAL REPORTE CONSOLIDADO-->
                        <div class="modal fade rep_consolidado" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title" id="myModalLabel" align="center">Reporte Consolidado por Semestre</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!--INICIO CONTENIDO DE MODAL-->
                                        <div class="x_panel">
                                            <div class="" align="center">
                                                <h2></h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br />
                                                <form role="form" action="reporte_consolidado_semestre" class="form-horizontal form-label-left input_mask" method="POST">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="sem_consolidado" id="sem_consolidado" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
                                                                while ($rb_mf = mysqli_fetch_array($b_mf)) {
                                                                    $b_sem_consolidado = buscarSemestreByIdModulo_Formativo($conexion, $rb_mf['id']);
                                                                    while ($r_b_sem_conso = mysqli_fetch_array($b_sem_consolidado)) {

                                                                ?>
                                                                        <option value="<?php echo $r_b_sem_conso['id']; ?>"><?php echo $r_b_sem_conso['descripcion']; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="turno" value="" required="required">
                                                                <option value=""></option>
                                                                <option value="M">Mañana</option>
                                                                <option value="T">Tarde</option>
                                                                <option value="N">Noche</option>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="seccion" value="" required="required">
                                                                <option value=""></option>
                                                                <?php
                                                                for ($i = 65; $i <= 90; $i++) {
                                                                    $letter = chr($i);
                                                                    echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <center>
                                                                <button type="submit" class="btn btn-success">Generar Reporte</button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--FIN DE CONTENIDO DE MODAL-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIN MODAL CONSOLIDADO-->


                        <!--MODAL REPORTE CONSOLIDADO DETALLADO-->
                        <div class="modal fade rep_consolidado_detallado" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title" id="myModalLabel" align="center">Reporte Consolidado por Semestre</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!--INICIO CONTENIDO DE MODAL-->
                                        <div class="x_panel">
                                            <div class="" align="center">
                                                <h2></h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br />
                                                <form role="form" action="reporte_consolidado_detallado.php" class="form-horizontal form-label-left input_mask" method="POST">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="sem_consolidado" id="sem_consolidado" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
                                                                while ($rb_mf = mysqli_fetch_array($b_mf)) {
                                                                    $b_sem_consolidado = buscarSemestreByIdModulo_Formativo($conexion, $rb_mf['id']);

                                                                    while ($r_b_sem_conso = mysqli_fetch_array($b_sem_consolidado)) {

                                                                ?>
                                                                        <option value="<?php echo $r_b_sem_conso['id']; ?>"><?php echo $r_b_sem_conso['descripcion']; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="turno" value="" required="required">
                                                                <option value=""></option>
                                                                <option value="M">Mañana</option>
                                                                <option value="T">Tarde</option>
                                                                <option value="N">Noche</option>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="seccion" value="" required="required">
                                                                <option value=""></option>
                                                                <?php
                                                                for ($i = 65; $i <= 90; $i++) {
                                                                    $letter = chr($i);
                                                                    echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <center>
                                                                <button type="submit" class="btn btn-success">Generar Reporte</button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--FIN DE CONTENIDO DE MODAL-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIN MODAL CONSOLIDADO DETALLADO-->



                        <!--MODAL REPORTE NOMINA-->
                        <div class="modal fade rep_nomina" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title" id="myModalLabel" align="center">Reporte - Nómina de Matrícula</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!--INICIO CONTENIDO DE MODAL-->
                                        <div class="x_panel">
                                            <div class="" align="center">
                                                <h2></h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br />
                                                <form role="form" action="reporte_nomina_semestre" class="form-horizontal form-label-left input_mask" method="POST">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="sem_consolidado" id="sem_consolidado" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
                                                                while ($rb_mf = mysqli_fetch_array($b_mf)) {
                                                                    $b_sem_consolidado = buscarSemestreByIdModulo_Formativo($conexion, $rb_mf['id']);

                                                                    while ($r_b_sem_conso = mysqli_fetch_array($b_sem_consolidado)) {

                                                                ?>
                                                                        <option value="<?php echo $r_b_sem_conso['id']; ?>"><?php echo $r_b_sem_conso['descripcion']; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="turno" value="" required="required">
                                                                <option value=""></option>
                                                                <option value="M">Mañana</option>
                                                                <option value="T">Tarde</option>
                                                                <option value="N">Noche</option>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="seccion" value="" required="required">
                                                                <option value=""></option>
                                                                <?php
                                                                for ($i = 65; $i <= 90; $i++) {
                                                                    $letter = chr($i);
                                                                    echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <center>
                                                                <button type="submit" class="btn btn-success">Generar Reporte</button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--FIN DE CONTENIDO DE MODAL-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIN MODAL NOMINA-->


                        <!--MODAL REPORTE INDIVIDUAL-->
                        <div class="modal fade rep_individual" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title" id="myModalLabel" align="center">Reporte Individual Calificaciones y Asistencia</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!--INICIO CONTENIDO DE MODAL-->
                                        <div class="x_panel">
                                            <div class="" align="center">
                                                <h2></h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br />
                                                <form role="form" action="reporte_nomina_semestre" class="form-horizontal form-label-left input_mask" method="POST">
                                                    <input type="hidden" id="car_est" value="<?php echo $id_pe; ?>">

                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <input type="text" class="form-control" name="dni_estt" id="dni_estt">
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombres y Apellidos : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <input type="text" class="form-control" name="na_estt" id="na_estt">
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <center>
                                                                <button type="button" class="btn btn-info " onclick="listar_est();"><i class="fa fa-search"></i> Buscar</button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </form>

                                                <div id="contenido_mm" class="table-responsive">
                                                    <table class="table table-striped table-bordered" style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <th>Nro</th>
                                                                <th>DNI</th>
                                                                <th>Apellidos y Nombres</th>
                                                                <th>Semestre</th>
                                                                <th>Acciones</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <!--FIN DE CONTENIDO DE MODAL-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIN MODAL INDIVIDUAL-->

                        <!--MODAL REPORTE PRIMEROS PUESTOS-->
                        <div class="modal fade rep_primeros_puestos" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                        <h4 class="modal-title" id="myModalLabel" align="center">Reporte de Primeros Puestos</h4>
                                    </div>
                                    <div class="modal-body">
                                        <!--INICIO CONTENIDO DE MODAL-->
                                        <div class="x_panel">
                                            <div class="" align="center">
                                                <h2></h2>
                                                <div class="clearfix"></div>
                                            </div>
                                            <div class="x_content">
                                                <br />
                                                <form role="form" action="reporte_primeros_puestos" class="form-horizontal form-label-left input_mask" method="POST">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="sem_consolidado" id="sem_consolidado" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
                                                                while ($rb_mf = mysqli_fetch_array($b_mf)) {
                                                                    $b_sem_consolidado = buscarSemestreByIdModulo_Formativo($conexion, $rb_mf['id']);
                                                                    while ($r_b_sem_conso = mysqli_fetch_array($b_sem_consolidado)) {

                                                                ?>
                                                                        <option value="<?php echo $r_b_sem_conso['id']; ?>"><?php echo $r_b_sem_conso['descripcion']; ?></option>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="turno" value="" required="required">
                                                                <option value=""></option>
                                                                <option value="M">Mañana</option>
                                                                <option value="T">Tarde</option>
                                                                <option value="N">Noche</option>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                                            <select class="form-control" name="seccion" value="" required="required">
                                                                <option value=""></option>
                                                                <?php
                                                                for ($i = 65; $i <= 90; $i++) {
                                                                    $letter = chr($i);
                                                                    echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                            <br>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                            <center>
                                                                <button type="submit" class="btn btn-success">Generar Reporte</button>
                                                            </center>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <!--FIN DE CONTENIDO DE MODAL-->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FIN MODAL PRIMEROS PUESTOS-->



                    </div>
                    <!-- /page content -->


                    <!-- footer content -->
                    <?php
                    include("../include/footer.php");
                    ?>
                    <!-- /footer content -->
                </div>
            </div>

            <!-- jQuery -->
            <script src="../plantilla/Gentella/vendors/jquery/dist/jquery.min.js"></script>
            <!-- Bootstrap -->
            <script src="../plantilla/Gentella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
            <!-- FastClick -->
            <script src="../plantilla/Gentella/vendors/fastclick/lib/fastclick.js"></script>
            <!-- NProgress -->
            <script src="../plantilla/Gentella/vendors/nprogress/nprogress.js"></script>
            <!-- Chart.js -->
            <script src="../plantilla/Gentella/vendors/Chart.js/dist/Chart.min.js"></script>
            <!-- gauge.js -->
            <script src="../plantilla/Gentella/vendors/gauge.js/dist/gauge.min.js"></script>
            <!-- bootstrap-progressbar -->
            <script src="../plantilla/Gentella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
            <!-- iCheck -->
            <script src="../plantilla/Gentella/vendors/iCheck/icheck.min.js"></script>
            <!-- Skycons -->
            <script src="../plantilla/Gentella/vendors/skycons/skycons.js"></script>
            <!-- Flot -->
            <script src="../plantilla/Gentella/vendors/Flot/jquery.flot.js"></script>
            <script src="../plantilla/Gentella/vendors/Flot/jquery.flot.pie.js"></script>
            <script src="../plantilla/Gentella/vendors/Flot/jquery.flot.time.js"></script>
            <script src="../plantilla/Gentella/vendors/Flot/jquery.flot.stack.js"></script>
            <script src="../plantilla/Gentella/vendors/Flot/jquery.flot.resize.js"></script>
            <!-- Flot plugins -->
            <script src="../plantilla/Gentella/vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
            <script src="../plantilla/Gentella/vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
            <script src="../plantilla/Gentella/vendors/flot.curvedlines/curvedLines.js"></script>
            <!-- DateJS -->
            <script src="../plantilla/Gentella/vendors/DateJS/build/date.js"></script>
            <!-- JQVMap -->
            <script src="../plantilla/Gentella/vendors/jqvmap/dist/jquery.vmap.js"></script>
            <script src="../plantilla/Gentella/vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
            <script src="../plantilla/Gentella/vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>
            <!-- bootstrap-daterangepicker -->
            <script src="../plantilla/Gentella/vendors/moment/min/moment.min.js"></script>
            <script src="../plantilla/Gentella/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="../plantilla/Gentella/build/js/custom.min.js"></script>
            <script type="text/javascript">
                function listar_est() {
                    var dni_e = $('#dni_estt').val();
                    var na_e = $('#na_estt').val();
                    var pe_e = $('#car_est').val();
                    $.ajax({
                        type: "POST",
                        url: "operaciones/listar_est_reporte.php",
                        data: {
                            dni_es: dni_e,
                            na_es: na_e,
                            pe_es: pe_e
                        },
                        success: function(r) {
                            $('#contenido_mm').html(r);
                        }
                    });
                }
            </script>

        </body>

        </html>
<?php }
}
