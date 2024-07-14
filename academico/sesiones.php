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

    $id_prog = base64_decode($_GET['data']);
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
        $r_b_ud = mysqli_fetch_array($b_ud);
        //buscar semestre
        $b_sem = buscarSemestreById($conexion, $r_b_ud['id_semestre']);
        $r_b_sem = mysqli_fetch_array($b_sem);
        //buscar modulo profesional
        $b_mod = buscarModuloFormativoById($conexion, $r_b_ud['id_modulo_formativo']);
        $r_b_mod = mysqli_fetch_array($b_mod);
        //buscar programa de estudio
        $b_pe = buscarProgramaEstudioById($conexion, $r_b_ud['id_programa_estudio']);
        $r_b_pe = mysqli_fetch_array($b_pe);


        //buscamos el silabo y sus datos
        $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
        $r_b_silabo = mysqli_fetch_array($b_silabo);
        $id_silabo = $r_b_silabo['id'];

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

            <title>Sesiones de Aprendinzaje <?php include("../include/header_title.php"); ?></title>
            <!--icono en el titulo-->
            <link rel="shortcut icon" href="images/favicon.ico">
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
                    include("include/menu_docente.php");
                    ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="clearfix"></div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <div class="x_panel">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center">Sesiones de Aprendizaje - <?php echo $r_b_ud['nombre']; ?></h2>
                                            <a href="unidades_didacticas" class="btn btn-danger">Regresar</a>
                                            <button class="btn btn-success" data-toggle="modal" data-target=".copiar">Copiar Sesiones</button>
                                            <div class="clearfix"></div>
                                            <!--MODAL COPIAR INFFORMACION-->
                                            <div class="modal fade copiar" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                            </button>
                                                            <h4 class="modal-title" id="myModalLabel" align="center">Copiar Información de Sesiones de Apredizaje</h4>
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
                                                                    <form role="form" action="operaciones/copiar_informacion_sesiones.php" method="POST" class="form-horizontal form-label-left input_mask">
                                                                        <input type="hidden" name="myidactual" value="<?php echo $id_prog; ?>">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Copiar Sesiones de : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <select class="form-control" id="sesion_copi" name="sesion_copi" value="" required="required">
                                                                                    <option></option>
                                                                                    <?php
                                                                                    $ejec_busc_prog = buscarProgramacionUDById($conexion, $id_prog);
                                                                                    while ($res_busc_prog = mysqli_fetch_array($ejec_busc_prog)) {
                                                                                        $id_prog_b = $res_busc_prog['id'];
                                                                                        $id_ud = $res_busc_prog['id_unidad_didactica'];
                                                                                        $b_ud = buscarUnidadDidacticaById($conexion, $id_ud);
                                                                                        $res_b_ud = mysqli_fetch_array($b_ud);

                                                                                        $b_ud_nombre = buscarUdByName($conexion, $res_b_ud['nombre']);
                                                                                        while ($r_b_ud_name = mysqli_fetch_array($b_ud_nombre)) {
                                                                                            $id_uddd = $r_b_ud_name['id'];
                                                                                            $b_prog_udd = buscarProgramacionUDByIdUd($conexion, $id_uddd);
                                                                                            $r_b_prog_udd = mysqli_fetch_array($b_prog_udd);
                                                                                            $id_prog_a_copiar = $r_b_prog_udd['id'];

                                                                                            $b_prog_a_copiar = buscarProgramacionUDById($conexion, $id_prog_a_copiar);
                                                                                            $r_b_prog_a_copiar = mysqli_fetch_array($b_prog_a_copiar);

                                                                                            $b_periodo_acadd = buscarPeriodoAcadById($conexion, $r_b_prog_a_copiar['id_periodo_academico']);
                                                                                            $r_b_periodo_acadd = mysqli_fetch_array($b_periodo_acadd);

                                                                                            $id_semestre = $r_b_ud_name['id_semestre'];
                                                                                            $ejec_busc_semestre = buscarSemestreById($conexion, $id_semestre);
                                                                                            $res_busc_semestre = mysqli_fetch_array($ejec_busc_semestre);

                                                                                            $id_modulo = $res_busc_semestre['id_modulo_formativo'];
                                                                                            $ejec_busc_modulo= buscarModuloFormativoById($conexion, $id_modulo);
                                                                                            $res_busc_modulo= mysqli_fetch_array($ejec_busc_modulo);

                                                                                            $id_carrera = $res_busc_modulo['id_programa_estudio'];
                                                                                            $ejec_busc_carrera = buscarProgramaEstudioById($conexion, $id_carrera);
                                                                                            $res_busc_carrera = mysqli_fetch_array($ejec_busc_carrera);

                                                                                            
                                                                                    ?>
                                                                                            <option value="<?php echo $id_prog_a_copiar; ?>"><?php echo $res_b_ud['nombre'] . " - " . $res_busc_carrera['nombre'] . " - " . $res_busc_semestre['descripcion'] . " - " . $r_b_periodo_acadd['nombre']; ?></option>
                                                                                    <?php
                                                                                        }
                                                                                    };
                                                                                    ?>

                                                                                </select>
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
                                                            <!--FIN DE CONTENIDO DE MODAL-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- FIN MODAL COPIAR INFORMACION-->
                                        </div>
                                        <div class="x_content">
                                            <br />

                                            <table id="example" class="table table-striped  table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Semana</th>
                                                        <th>Sesión</th>
                                                        <th>Unidad Didactica</th>
                                                        <th>Docente</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    //buscamos las programaciones de actividades
                                                    $b_prog_act = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
                                                    while ($res_b_prog_act = mysqli_fetch_array($b_prog_act)) {
                                                        // buscamos la sesion que corresponde
                                                        $id_act = $res_b_prog_act['id'];
                                                        $b_sesion = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_act);
                                                        $contar = 0;
                                                        while ($r_b_sesion = mysqli_fetch_array($b_sesion)) {
                                                            $contar += 1;
                                                            $data = base64_encode($r_b_sesion['id']);
                                                            $id_sesion = $r_b_sesion['id'];
                                                            $id_docente = $res_b_prog['id_docente'];
                                                    ?>
                                                            <tr>
                                                                <td><?php echo $res_b_prog_act['semana']; ?></td>
                                                                <td><?php echo $contar; ?></td>
                                                                <td><?php echo $r_b_ud['nombre']; ?></td>
                                                                <?php
                                                                $ejec_busc_docente = buscarUsuarioById($conexion, $id_docente);
                                                                $res_busc_docente = mysqli_fetch_array($ejec_busc_docente);
                                                                ?>
                                                                <td><?php echo $res_busc_docente['apellidos_nombres']; ?></td>
                                                                <td>
                                                                    <a title="Ver / Editar" class="btn btn-success" href="sesion_de_aprendizaje?data=<?php echo $data; ?>"><i class="fa fa-pencil-square-o"></i></a>
                                                                    <a title="Imprimir" class="btn btn-info" target="_blank" href="imprimir_sesion_aprendizaje?data=<?php echo $data; ?>"><i class="fa fa-print"></i></a>
                                                                    <a title="Duplicar Sesión de Aprendizaje" class="btn btn-warning" href="operaciones/duplicar_sesion?data=<?php echo $data; ?>&data2=<?php echo $id_prog; ?>" onclick="return confirmard();"><i class="fa fa-plus-square"></i></a>
                                                                    <?php if ($contar > 1) { ?>
                                                                        <a title="Eliminar Sesión de Aprendizaje" class="btn btn-danger" href="operaciones/eliminar_sesion?data=<?php echo $data; ?>&data2=<?php echo $id_prog; ?>" onclick="return confirmardelete();"><i class="fa fa-remove"></i></a>
                                                                    <?php } ?>

                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    };
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
