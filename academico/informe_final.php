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
    $cont_res = mysqli_num_rows($b_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_usuario'] == $id_usuario) {
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
            <meta http-equiv="Content-Language" content="es-ES">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Informe Final<?php include("../include/header_title.php"); ?></title>
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
            <!-- script para tags -->
            <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css" rel="stylesheet" />

        </head>

        <body class="nav-md">
            <div class="container body">
                <div class="main_container">
                    <!--menu-->
                    <?php
                        include("include/menu_docente.php");

                    $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
                    $r_b_ud = mysqli_fetch_array($b_ud);

                    ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center"><b>INFORME FINAL - <?php echo $r_b_ud['nombre']; ?></b></h2>
                                            <form action="imprimir_informe_final" method="POST" target="_blank">
                                                <input type="hidden" name="data" value="<?php echo $id_prog; ?>">
                                                <button type="submit" class="btn btn-info">Imprimir</button>
                                            </form>
                                            <a href="unidades_didacticas" class="btn btn-danger">Regresar</a>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br>

                                            <form role="form" action="operaciones/actualizar_informe_final" class="form-horizontal form-label-left input_mask" method="POST">
                                                <input type="hidden" name="id_prog" value="<?php echo $id_prog; ?>">
                                                <div class="table-responsive">
                                                    <table class="table table-striped jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2">
                                                                    <center>SOBRE LA SUPERVISIÓN Y EVALUACIÓN</center>
                                                                </th>
                                                            </tr>
                                                            <tr>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">Fue Supervisado</td>
                                                                <td>:
                                                                    <select name="fue_supervisado" id="fue_supervisado">
                                                                        <option value="1" <?php if ($res_b_prog['supervisado'] == 1) {
                                                                                                echo "selected";
                                                                                            } ?>>SI</option>
                                                                        <option value="0" <?php if ($res_b_prog['supervisado'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>NO</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th colspan="2">
                                                                    <center>DOCUMENTOS DE EVALUACIÓN UTILIZADAS</center>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <td>Registro de Evaluación</td>
                                                                <td>:
                                                                    <select name="reg_evaluacion" id="reg_evaluacion">
                                                                        <option value="1" <?php if ($res_b_prog['reg_evaluacion'] == 1) {
                                                                                                echo "selected";
                                                                                            } ?>>SI</option>
                                                                        <option value="0" <?php if ($res_b_prog['reg_evaluacion'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>NO</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Registro Auxiliar</td>
                                                                <td>:
                                                                    <select name="reg_auxiliar" id="reg_auxiliar">
                                                                        <option value="1" <?php if ($res_b_prog['reg_auxiliar'] == 1) {
                                                                                                echo "selected";
                                                                                            } ?>>SI</option>
                                                                        <option value="0" <?php if ($res_b_prog['reg_auxiliar'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>NO</option>
                                                                    </select>
                                                            </tr>
                                                            <tr>
                                                                <td>Programación Curricular</td>
                                                                <td>:
                                                                    <select name="prog_curricular" id="prog_curricular">
                                                                        <option value="1" <?php if ($res_b_prog['prog_curricular'] == 1) {
                                                                                                echo "selected";
                                                                                            } ?>>SI</option>
                                                                        <option value="0" <?php if ($res_b_prog['prog_curricular'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>NO</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Otros</td>
                                                                <td>:
                                                                    <select name="otros" id="otros" >
                                                                        <option value="1" <?php if ($res_b_prog['otros'] == 1) {
                                                                                                echo "selected";
                                                                                            } ?>>SI</option>
                                                                        <option value="0" <?php if ($res_b_prog['otros'] == 0) {
                                                                                                echo "selected";
                                                                                            } ?>>NO</option>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <center>LOGROS OBTENIDOS</center>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="logros_obtenidos" style="width:100%; resize: none; height:auto;" rows="3" class="form-control"><?php echo $res_b_prog['logros_obtenidos']; ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <center>DIFICULTADES</center>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="dificultades" style="width:100%; resize: none; height:auto;" rows="3" class="form-control"><?php echo $res_b_prog['dificultades']; ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th>
                                                                    <center>SUGERENCIAS</center>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="sugerencias" style="width:100%; resize: none; height:auto;" rows="3" class="form-control"><?php echo $res_b_prog['sugerencias']; ?></textarea>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>


                                                <div align="center">
                                                    <br>
                                                    <br>
                                                    <a href="unidades_didacticas" class="btn btn-danger">Regresar</a>
                                                    <button type="submit" class="btn btn-success">Guardar</button>
                                                </div>
                                            </form>


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
            <!-- bootstrap-progressbar -->
            <script src="../plantilla/Gentella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>
            <!-- iCheck -->
            <script src="../plantilla/Gentella/vendors/iCheck/icheck.min.js"></script>
            <!-- bootstrap-daterangepicker -->
            <script src="../plantilla/Gentella/vendors/moment/min/moment.min.js"></script>
            <script src="../plantilla/Gentella/vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
            <!-- bootstrap-wysiwyg -->
            <script src="../plantilla/Gentella/vendors/bootstrap-wysiwyg/js/bootstrap-wysiwyg.min.js"></script>
            <script src="../plantilla/Gentella/vendors/jquery.hotkeys/jquery.hotkeys.js"></script>
            <script src="../plantilla/Gentella/vendors/google-code-prettify/src/prettify.js"></script>
            <!-- jQuery Tags Input -->
            <script src="../plantilla/Gentella/vendors/jquery.tagsinput/src/jquery.tagsinput.js"></script>
            <!-- Switchery -->
            <script src="../plantilla/Gentella/vendors/switchery/dist/switchery.min.js"></script>
            <!-- Select2 -->
            <script src="../plantilla/Gentella/vendors/select2/dist/js/select2.full.min.js"></script>
            <!-- Parsley -->
            <script src="../plantilla/Gentella/vendors/parsleyjs/dist/parsley.min.js"></script>
            <!-- Autosize -->
            <script src="../plantilla/Gentella/vendors/autosize/dist/autosize.min.js"></script>
            <!-- jQuery autocomplete -->
            <script src="../plantilla/Gentella/vendors/devbridge-autocomplete/dist/jquery.autocomplete.min.js"></script>
            <!-- starrr -->
            <script src="../plantilla/Gentella/vendors/starrr/dist/starrr.js"></script>

            <!-- Custom Theme Scripts -->
            <script src="../plantilla/Gentella/build/js/custom.min.js"></script>

            <!-- para tags -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"></script>

            <script>
                $(document).ready(function() {
                    $('#example').DataTable({
                        "order": [
                            [1, "asc"]
                        ],
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
