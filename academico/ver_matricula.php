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

        $id_mat = base64_decode($_GET['data']);

        $b_mat = buscarMatriculaById($conexion, $id_mat);
        $r_b_mat = mysqli_fetch_array($b_mat);
        $id_est = $r_b_mat['id_estudiante'];
        $id_periodo_select = $_SESSION['acad_periodo'];

        $b_perido_act = buscarPeriodoAcadById($conexion, $id_periodo_select);
        $r_b_per_act = mysqli_fetch_array($b_perido_act);
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($r_b_per_act['fecha_fin']);
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

            <title>Ver Matrícula <?php include("../include/header_title.php"); ?></title>
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
                function confirmarEliminar() {
                    var r = confirm("Estas Seguro Eliminar Registro?");
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
                                            <?php
                                            $b_est = buscarUsuarioById($conexion, $id_est);
                                            $r_b_est = mysqli_fetch_array($b_est);
                                            ?>
                                            <h2 align="center">Detalle de Matrícula - <?php echo $r_b_est['apellidos_nombres']; ?></h2>
                                            <?php if ($agregar) { ?>
                                                <a title="Agregar Unidad Didáctica" class="btn btn-success" href="agregar_ud_matricula?data=<?php echo base64_encode($id_mat); ?>">Agregar Unidad Didáctica</a>
                                            <?php
                                            } ?>
                                            <br>
                                            <a href="matriculas" class="btn btn-danger">Regresar</a>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />

                                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Orden</th>
                                                        <th>Programa de Estudios</th>
                                                        <th>Semestre</th>
                                                        <th>Unidad Didáctica</th>
                                                        <?php if ($agregar) {
                                                            echo '<th>Acciones</th>';
                                                        } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cont_table = 0;
                                                    $ejec_busc_matricula = buscarMatriculaById($conexion, $id_mat);
                                                    while ($res_busc_matricula = mysqli_fetch_array($ejec_busc_matricula)) {

                                                        $b_detalle_matricula = buscarDetalleMatriculaByIdMatricula($conexion, $res_busc_matricula['id']);
                                                        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_matricula)) {
                                                            $cont_table += 1;

                                                            $b_prog = buscarProgramacionUDById($conexion, $r_b_det_mat['id_programacion_ud']);
                                                            $r_b_prog = mysqli_fetch_array($b_prog);

                                                            $b_ud = buscarUnidadDidacticaById($conexion, $r_b_prog['id_unidad_didactica']);
                                                            $r_b_ud = mysqli_fetch_array($b_ud);
                                                    ?>
                                                            <tr>
                                                                <td><?php echo $cont_table; ?></td>
                                                                <?php
                                                                $busc_semestre = buscarSemestreById($conexion, $r_b_ud['id_semestre']);
                                                                $res_b_semestre = mysqli_fetch_array($busc_semestre);

                                                                $busc_mf = buscarModuloFormativoById($conexion, $res_b_semestre['id_modulo_formativo']);
                                                                $res_busc_mf = mysqli_fetch_array($busc_mf);

                                                                $ejec_busc_carrera = buscarProgramaEstudioById($conexion, $res_busc_mf['id_programa_estudio']);
                                                                $res_busc_carrera = mysqli_fetch_array($ejec_busc_carrera);
                                                                ?>
                                                                <td><?php echo $res_busc_carrera['nombre']; ?></td>
                                                                <td><?php echo $res_b_semestre['descripcion']; ?></td>
                                                                <td><?php echo $r_b_ud['nombre']; ?></td>
                                                                <?php if ($agregar) { ?>
                                                                    <td><a title="Eliminar" class="btn btn-danger" href="operaciones/eliminar_ud_matricula?data=<?php echo base64_encode($r_b_det_mat['id']); ?>" onclick="return confirmarEliminar();">Eliminar</a></td>
                                                                <?php } ?>
                                                            </tr>
                                                    <?php
                                                        };
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
            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php
    }
}
