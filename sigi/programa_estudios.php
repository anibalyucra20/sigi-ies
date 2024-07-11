<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_sigi.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_SIGI');
    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
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
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
        <a href='../sigi/'>Regresar</a><br>
        <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
        </center>";
    } else {
        # code...
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta http-equiv="Content-Language" content="es-ES">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Programa de Estudio<?php include("../include/header_title.php"); ?></title>
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
            <!-- Datatables -->
            <link href="../plantilla/Gentella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
            <link href="../plantilla/Gentella/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
            <link href="../plantilla/Gentella/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
            <link href="../plantilla/Gentella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
            <link href="../plantilla/Gentella/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

            <!-- Custom Theme Style -->
            <link href="../plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">

        </head>

        <body class="nav-md">
            <div class="container body">
                <div class="main_container">
                    <!--menu-->
                    <?php
                    include("include/menu_director.php"); ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center">Programas de Estudio</h2>
                                            <button class="btn btn-success" data-toggle="modal" data-target=".registrar"><i class="fa fa-plus-square"></i> Nuevo</button>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />

                                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Identificador</th>
                                                        <th>Código</th>
                                                        <th>Tipo</th>
                                                        <th>Plan de Estudio</th>
                                                        <th>Nombre</th>
                                                        <th>Resolución</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $busc_carrera = buscarProgramaEstudio($conexion);
                                                    while ($res_busc_carrera = mysqli_fetch_array($busc_carrera)) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $res_busc_carrera['id']; ?></td>
                                                            <td><?php echo $res_busc_carrera['codigo']; ?></td>
                                                            <td><?php echo $res_busc_carrera['tipo']; ?></td>
                                                            <td><?php echo $res_busc_carrera['plan_estudios']; ?></td>
                                                            <td><?php echo $res_busc_carrera['nombre']; ?></td>
                                                            <td><?php echo $res_busc_carrera['resolucion']; ?></td>
                                                            <td>
                                                                <button class="btn btn-success" data-toggle="modal" data-target=".edit_<?php echo $res_busc_carrera['id']; ?>"><i class="fa fa-pencil-square-o"></i> Editar</button>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                        include('include/acciones_programa_estudio.php');
                                                    };
                                                    ?>

                                                </tbody>
                                            </table>


                                            <!--MODAL REGISTRAR-->
                                            <div class="modal fade registrar" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                            </button>
                                                            <h4 class="modal-title" id="myModalLabel" align="center">Registrar Carrera Profesional</h4>
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
                                                                    <form role="form" action="operaciones/registrar_programa_estudio.php" class="form-horizontal form-label-left input_mask" method="POST">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Código : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <input type="text" class="form-control" name="codigo" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                                                <br>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <select class="form-control" name="tipo" required="required">
                                                                                    <option value=""></option>
                                                                                    <option value="MODULAR">MODULAR</option>
                                                                                    <option value="TRANSVERSAL">TRANSVERSAL</option>
                                                                                </select>
                                                                                <br>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Plan de Estudios(año) : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <input type="text" class="form-control" name="plan_estudios" value="" required="required">
                                                                                <br>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <input type="text" class="form-control" name="nombre" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                                                <br>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Resolución de Creación : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <input type="text" class="form-control" name="resolucion" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                                                                <br>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Perfil de Egresado : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <textarea class="form-control" rows="3" style="width: 100%; height: 165px;" name="perfil_egresado" required="required"></textarea>

                                                                                <br>
                                                                                <br>
                                                                            </div>
                                                                        </div>

                                                                        <div align="center">
                                                                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                                                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <!--FIN DE CONTENIDO DE MODAL-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FIN MODAL REGISTRAR-->

                                    </div>
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
