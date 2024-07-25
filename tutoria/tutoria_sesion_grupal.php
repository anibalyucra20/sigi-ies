<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_tutoria.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_TUTORIA');

    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {

    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['tutoria_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_TUTORIA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $id_periodo_act = $_SESSION['tutoria_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['tutoria_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../tutoria/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_tutoria.php'>Cerrar Sesión</a><br>
    </center>";
    } else {


?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta http-equiv="Content-Language" content="es-ES">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Tutoría<?php include("../include/header_title.php"); ?></title>
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
            <!-- Script obtenido desde CDN jquery -->
            <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

        </head>

        <body class="nav-md">
            <div class="container body">
                <div class="main_container">
                    <!--menu-->
                    <?php
                        include("include/menu.php");
                    ?>
                    <!-- page content -->
                    <div class="right_col" role="main">


                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="">
                                        <h2 align="center">Sesiones Grupales Tutoría</h2>
                                        <button class="btn btn-success" data-toggle="modal" data-target=".registrar"><i class="fa fa-plus-square"></i> Nuevo</button>
                                        <a href="tutoria" class="btn btn-danger">Regresar</a>
                                        <div class="clearfix"></div>
                                    </div>

                                    <!--MODAL NUEVO-->
                                    <div class="modal fade registrar" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                    </button>
                                                    <h4 class="modal-title" id="myModalLabel" align="center">Conclusiones de Tutoría para informe</h4>
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
                                                            <form role="form" action="operaciones/registrar_tutoria_sesion_grupal" class="form-horizontal form-label-left input_mask" method="POST">
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Título : </label>
                                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                                        <input type="text" class="form-control" name="titulo" required="required">
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha y Hora : </label>
                                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                                        <input type="datetime-local" class="form-control" name="fecha_hora" required="required">
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Tema : </label>
                                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                                        <textarea name="tema" rows="2" class="form-control" style="width:100%; resize: none; height:auto;" required></textarea>
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Link Reunión : </label>
                                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                                        <input type="text" class="form-control" name="link">
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Resultados : </label>
                                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                                        <textarea name="resultados" rows="5" class="form-control" style="width:100%; resize: none; height:auto;"></textarea>
                                                                        <br>
                                                                    </div>
                                                                </div>
                                                                <div align="center">
                                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                                                    <button type="submit" class="btn btn-primary">Programar</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <!--FIN DE CONTENIDO DE MODAL-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FIN MODAL NUEVO-->
                                    <div class="x_content">
                                        <br />

                                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>Nro</th>
                                                    <th>titulo</th>
                                                    <th>tema</th>
                                                    <th>Fecha y Hora</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $b_tutoria = buscarTutoriaByIdDocenteAndIdPeriodoSede($conexion, $id_usuario, $id_periodo_act, $id_sede_act);
                                                $r_b_tutoria = mysqli_fetch_array($b_tutoria);
                                                $b_sesion_grupal = buscarTutoriaSesGrupalByIdTutoria($conexion, $r_b_tutoria['id']);
                                                $contador = 0;
                                                while ($r_b_sesion_grupal = mysqli_fetch_array($b_sesion_grupal)) {
                                                    $contador++;
                                                ?>
                                                    <tr>
                                                        <td><?php echo $contador; ?></td>
                                                        <td><?php echo $r_b_sesion_grupal['titulo']; ?></td>
                                                        <td><?php echo $r_b_sesion_grupal['tema']; ?></td>
                                                        <td><?php echo $r_b_sesion_grupal['fecha_hora']; ?></td>
                                                        <td>
                                                            <a href="datos_sesion_grupal_tutoria?data=<?php echo base64_encode($r_b_sesion_grupal['id']); ?>" class="btn btn-primary"><i class="fa fa-eye"></i> Completar</a>
                                                        </td>
                                                    </tr>

                                                <?php
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
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
            <!-- iCheck -->
            <script src="../plantilla/Gentella/vendors/iCheck/icheck.min.js"></script>
            <!-- Datatables -->
            <script src="../plantilla/Gentella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
            <script src="../plantilla/Gentella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
            <script src="../plantilla/Gentella/vendors/jszip/dist/jszip.min.js"></script>
            <script src="../plantilla/Gentella/vendors/pdfmake/build/pdfmake.min.js"></script>
            <script src="../plantilla/Gentella/vendors/pdfmake/build/vfs_fonts.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="../plantilla/Gentella/build/js/custom.min.js"></script>
            <script>
                $(document).ready(function() {
                    $('#example').DataTable({
                        "language": {
                            "processing": "Procesando...",
                            "lengthMenu": "Mostrar _MENU_ registros",
                            "zeroRecords": "No se encontraron resultados",
                            "emptyTable": "Ningún dato disponible en esta tabla",
                            "sInfo": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
                            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
                            "search": "Buscar:",
                            "infoThousands": ",",
                            "loadingRecords": "Cargando...",
                            "paginate": {
                                "first": "Primero",
                                "last": "Último",
                                "next": "Siguiente",
                                "previous": "Anterior"
                            },
                        }
                    });

                });
            </script>


            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php }
}
