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
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($rb_periodo_act['fecha_fin']);
        if ($fecha_fin_per >= $fecha_actual) {
            $agregar = 1;
        } else {
            $agregar = 0;
        }
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

            <title>Licencias <?php include("../include/header_title.php"); ?></title>
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
            <!-- Script obtenido desde CDN jquery -->
            <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>


            <script>
                function confirmardelete() {
                    var r = confirm("Estas Seguro de Eliminar la Licencia de Estudios?");
                    if (r == true) {
                        return true;
                    } else {
                        return false;
                    }
                }
            </script>

        </head>

        <body class="nav-md">
            <div class="container body">
                <div class="main_container">
                    <!--menu-->
                    <?php
                    include("include/menu_docente.php"); ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center">Licencias</h2>
                                            <?php if ($agregar) { ?>
                                                <a href="registrar_licencia" class="btn btn-success"> <i class="fa fa-plus-square"></i> Nuevo</a>
                                            <?php
                                            } ?>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />

                                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Identificador</th>
                                                        <th>DNI</th>
                                                        <th>Estudiante</th>
                                                        <th>Programa de Estudios</th>
                                                        <th>Semestre</th>
                                                        <th>Nro. Resolución</th>
                                                        <?php if ($agregar) {
                                                            echo '<th>Acciones</th>';
                                                        } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cont_table = 0;
                                                    $ejec_busc_licencia = buscarLicenciaPeriodo($conexion, $id_periodo_act, $id_sede_act);
                                                    while ($res_busc_licencia = mysqli_fetch_array($ejec_busc_licencia)) {
                                                        $cont_table += 1;
                                                    ?>
                                                        <tr>
                                                            <td><?php echo $cont_table; ?></td>
                                                            <?php
                                                            $id_estudiante = $res_busc_licencia['id_estudiante'];
                                                            $b_estu = buscarUsuarioById($conexion, $id_estudiante);
                                                            $res_b_estudiante = mysqli_fetch_array($b_estu);

                                                            $id_programa_estudio = $res_busc_licencia['id_programa_estudio'];
                                                            $id_semestre = $res_busc_licencia['id_semestre'];

                                                            $busc_semestre = buscarSemestreById($conexion, $id_semestre);
                                                            $res_b_semestre = mysqli_fetch_array($busc_semestre);

                                                            $ejec_busc_carrera = buscarProgramaEstudioById($conexion, $id_programa_estudio);
                                                            $res_busc_carrera = mysqli_fetch_array($ejec_busc_carrera);
                                                            ?>
                                                            <td><?php echo $res_b_estudiante['dni']; ?></td>
                                                            <td><?php echo $res_b_estudiante['apellidos_nombres']; ?></td>
                                                            <td><?php echo $res_busc_carrera['nombre']; ?></td>
                                                            <?php

                                                            ?>
                                                            <td><?php echo $res_b_semestre['descripcion']; ?></td>
                                                            <td><?php echo $res_busc_licencia['licencia']; ?></td>
                                                            <?php if ($agregar) {
                                                                echo '<td>
                                                        <a class="btn btn-success"  data-toggle="modal" data-target=".modal_edit_' . $res_busc_licencia['id'] . '"><i class="fa fa-pencil-square-o"></i></a>
                                                        <a href="operaciones/registrar_licencia_estudios?data=' . base64_encode($res_busc_licencia['id']) . '&data2=" class="btn btn-danger" onclick="return confirmardelete();"><i class="fa fa-trash"></i></a>   
                            
                          </td>';
                                                            ?>

                                                                <!--MODAL EDITAR LICENCIA-->
                                                                <div class="modal fade modal_edit_<?php echo $res_busc_licencia['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                                    <div class="modal-dialog modal-lg">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                                                                                <h4 class="modal-title" id="myModalLabel" align="center">Modificar Datos de Licencia para <?php echo $res_b_estudiante['apellidos_nombres']; ?></h4>
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
                                                                                        <form role="form" action="operaciones/registrar_licencia_estudios" class="form-horizontal form-label-left input_mask" method="GET">
                                                                                            <input type="hidden" name="id_mat" value="<?php echo $res_busc_licencia['id']; ?>">

                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nro de Resolución : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <input type="text" class="form-control" name="res_licencia" value="<?php echo $res_busc_licencia['licencia']; ?>" required>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <div class="col-md-12 col-sm-12 col-xs-12">
                                                                                                    <center>
                                                                                                        <button type="submit" class="btn btn-success ">Registrar</button>
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

                                                                <!-- FIN MODAL EDITAR LICENCIA-->

                                                            <?php } ?>
                                                        </tr>
                                                    <?php
                                                    };
                                                    ?>

                                                </tbody>
                                            </table>



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
            <!--script para obtener los modulos dependiendo de la carrera que seleccione-->


            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php
    }
}
