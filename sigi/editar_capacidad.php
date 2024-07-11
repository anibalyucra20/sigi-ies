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
        $id_capacidad = base64_decode($_GET['id']);
        $ejec_busc_cap = buscarCapacidadById($conexion, $id_capacidad);
        $res_busc_cap = mysqli_fetch_array($ejec_busc_cap);
        $id_competencia = $res_busc_cap['id_competencia'];
        $busc_competencia = buscarCompetenciasById($conexion, $id_competencia);
        $res_b_competencia = mysqli_fetch_array($busc_competencia);
        $id_modulo = $res_b_competencia['id_modulo_formativo'];
        $ejec_busc_modulo = buscarModuloFormativoById($conexion, $id_modulo);
        $res_busc_modulo = mysqli_fetch_array($ejec_busc_modulo);
        $id_carrera = $res_busc_modulo['id_programa_estudio'];
        $ejec_busc_carrera = buscarProgramaEstudioById($conexion, $id_carrera);
        $res_busc_carrera = mysqli_fetch_array($ejec_busc_carrera);
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

            <title>Capacidades <?php include("../include/header_title.php"); ?></title>
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
            <!-- bootstrap-wysiwyg -->
            <link href="../plantilla/Gentella/vendors/google-code-prettify/bin/prettify.min.css" rel="stylesheet">
            <!-- Select2 -->
            <link href="../plantilla/Gentella/vendors/select2/dist/css/select2.min.css" rel="stylesheet">
            <!-- Switchery -->
            <link href="../plantilla/Gentella/vendors/switchery/dist/switchery.min.css" rel="stylesheet">
            <!-- starrr -->
            <link href="../plantilla/Gentella/vendors/starrr/dist/starrr.css" rel="stylesheet">
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
                    include("include/menu_director.php"); ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center">Editar Capacidad</h2>


                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />



                                            <div class="x_panel">


                                                <div class="x_content">
                                                    <br />
                                                    <form role="form" action="operaciones/actualizar_capacidad.php" class="form-horizontal form-label-left input_mask" method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="id" value="<?php echo $id_capacidad; ?>">

                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <select class="form-control" id="carrera_m" name="carrera_m" required="required">
                                                                    <option></option>
                                                                    <?php
                                                                    $ejec_busc_carr = buscarProgramaEstudio($conexion);
                                                                    while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                                                                        $id_carr = $res__busc_carr['id'];
                                                                        $carr = $res__busc_carr['nombre'];
                                                                    ?>
                                                                        <option value="<?php echo $id_carr;
                                                                                        ?>" <?php if ($id_carr == $id_carrera) {
                                                                                                echo "selected";
                                                                                            } ?>><?php echo $carr; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Módulo Formativo : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <select class="form-control" id="modulo" name="modulo" required="required">
                                                                    <!--las opciones se cargan con ajax y javascript  dependiendo de la carrera elegida,verificar en la parte final-->
                                                                    <?php
                                                                    $ejec_busc_mod_car = buscarModuloFormativoByIdPe($conexion, $id_carrera);
                                                                    while ($res_busc_mod_carr = mysqli_fetch_array($ejec_busc_mod_car)) {
                                                                        $id_mod = $res_busc_mod_carr['id'];
                                                                        $mod = $res_busc_mod_carr['descripcion'];
                                                                    ?>
                                                                        <option value="<?php echo $id_mod;
                                                                                        ?>" <?php if ($id_mod == $id_modulo) {
                                                                                                echo "selected";
                                                                                            } ?>><?php echo "M" . $res_busc_mod_carr['nro_modulo'] . " - " . $mod; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Unidad Didáctica : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <select class="form-control" id="unidad_didactica" name="unidad_didactica" required="required">
                                                                    <!--las opciones se cargan con ajax y javascript  dependiendo de la carrera elegida,verificar en la parte final-->
                                                                    <?php
                                                                    $b_sem = buscarSemestreByIdModulo_Formativo($conexion, $id_modulo);
                                                                    while ($rb_sem = mysqli_fetch_array($b_sem)) {

                                                                        $ejec_busc_ud = buscarUnidadDidacticaByIdSemestre($conexion, $rb_sem['id']);
                                                                        while ($res_busc_ud = mysqli_fetch_array($ejec_busc_ud)) {
                                                                            $id_ud = $res_busc_ud['id'];
                                                                            $ud = $res_busc_ud['nombre'];
                                                                    ?>
                                                                            <option value="<?php echo $id_ud;
                                                                                            ?>" <?php if ($id_ud == $res_busc_cap['id_unidad_didactica']) {
                                                                                                echo "selected";
                                                                                            } ?>><?php echo "- ".$ud; ?></option>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Competencia : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <select class="form-control" id="competencia" name="competencia" value="<?php echo $id_competencia; ?>" required="required">
                                                                    <!--las opciones se cargan con ajax y javascript  dependiendo de la carrera elegida,verificar en la parte final-->
                                                                    <?php
                                                                    $ejec_busc_comp = buscarCompetenciasByIdModuloFormativo($conexion, $id_modulo);
                                                                    while ($res_busc_comp = mysqli_fetch_array($ejec_busc_comp)) {
                                                                        $id_comp = $res_busc_comp['id'];
                                                                        $comp = $res_busc_comp['descripcion'];
                                                                    ?>
                                                                        <option value="<?php echo $id_comp;
                                                                                        ?>" <?php if ($id_comp == $id_competencia) {
                                                                                                echo "selected";
                                                                                            } ?>><?php echo $res_busc_comp['codigo'] . ' - ' . $comp; ?></option>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Código : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <input type="text" class="form-control" name="codigo" value="<?php echo $res_busc_cap['codigo']; ?>" required="required">
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Descripción : </label>
                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                <textarea class="form-control" rows="3" style="width: 100%; height: 165px;" name="descripcion" required="required"><?php echo $res_busc_cap['descripcion']; ?></textarea>

                                                                <br>
                                                                <br>
                                                            </div>
                                                        </div>
                                                        <div align="center">

                                                            <a href="javascript:history.back(-1);" class="btn btn-danger">Cancelar</a>
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
            <script type="text/javascript">
                $(document).ready(function() {


                    $('#carrera_m').change(function() {
                        recargarlista();
                    });
                    $('#modulo').change(function() {
                        recargar_ud();
                    });
                    $('#modulo').change(function() {
                        recargar_competencias();
                    });
                })
            </script>
            <script type="text/javascript">
                function recargarlista() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_modulos.php",
                        data: "id_carrera=" + $('#carrera_m').val(),
                        success: function(r) {
                            $('#modulo').html(r);
                        }
                    });
                }
            </script>
            <script type="text/javascript">
                function recargar_ud() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_ud_modulo.php",
                        data: "id_modulo=" + $('#modulo').val(),
                        success: function(r) {
                            $('#unidad_didactica').html(r);
                        }
                    });
                }
            </script>
            <script type="text/javascript">
                function recargar_competencias() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_competencias.php",
                        data: "id_modulo=" + $('#modulo').val(),
                        success: function(r) {
                            $('#competencia').html(r);
                        }
                    });
                }
            </script>

            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php }
}
