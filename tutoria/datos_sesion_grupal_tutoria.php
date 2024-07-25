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
    $id_sesion_grupal = base64_decode($_GET['data']);

    $b_sesion_grupal = buscarTutoriaSesGrupalById($conexion, $id_sesion_grupal);
    $r_b_sesion_grupal = mysqli_fetch_array($b_sesion_grupal);
    $b_tutoria = buscarTutoriaById($conexion, $r_b_sesion_grupal['id_tutoria']);
    $r_b_tutoria = mysqli_fetch_array($b_tutoria);
    if ($id_usuario == $r_b_tutoria['id_docente']) {

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

            <title>Tutoría Sesion Grupal<?php include("../include/header_title.php"); ?></title>
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
                                        <h2 align="center">Sesión Grupal</h2>
                                        <a href="tutoria_sesion_grupal.php" class="btn btn-danger">Regresar</a>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <br />
                                        <form role="form" action="operaciones/actualizar_sesion_grupal_tutoria" class="form-horizontal form-label-left input_mask" method="POST">
                                            <input type="hidden" name="id_sesion_grupal" value="<?php echo $id_sesion_grupal; ?>">
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Título : </label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <input type="text" class="form-control" name="titulo" required="required" value="<?php echo $r_b_sesion_grupal['titulo']; ?>">
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha y Hora : </label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <input type="datetime-local" class="form-control" name="fecha_hora" required="required" value="<?php echo $r_b_sesion_grupal['fecha_hora']; ?>">
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Tema : </label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <textarea name="tema" rows="2" class="form-control" style="width:100%; resize: none; height:auto;" required><?php echo $r_b_sesion_grupal['tema']; ?></textarea>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Link Reunión : </label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <input type="text" class="form-control" name="link" required="required" value="<?php echo $r_b_sesion_grupal['link_reunion']; ?>">
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Resultados : </label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <textarea name="resultados" rows="5" class="form-control" style="width:100%; resize: none; height:auto;"><?php echo $r_b_sesion_grupal['resultados']; ?></textarea>
                                                    <br>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Asistentes : </label>
                                                <input type="hidden" name="array_asistentes" id="array_asistentes" value="<?php echo $r_b_sesion_grupal['asistentes']; ?>">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" onchange="select_all();" id="all_check"> <b> SELECCIONAR TODOS LOS ESTUDIANTES *</b>
                                                    </label>
                                                </div>
                                                <?php
                                                $asistentes = explode(",", $r_b_sesion_grupal['asistentes']);
                                                
                                                $b_estudiantes_tutoria = buscarTutoriaEstudiantesByIdTutoria($conexion, $r_b_tutoria['id']);
                                                while ($r_b_estudiantes_tutoria = mysqli_fetch_array($b_estudiantes_tutoria)) {
                                                    $b_estudiante = buscarUsuarioById($conexion, $r_b_estudiantes_tutoria['id_estudiante']);
                                                    $r_b_estudiante = mysqli_fetch_array($b_estudiante);
                                                ?>
                                                    <div class="checkbox">
                                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                                        <label>
                                                            <input type="checkbox" name="estudiantes" onchange="gen_arr_est();" value="<?php echo $r_b_estudiante['id']; ?>" <?php if(in_array($r_b_estudiante['id'], $asistentes)){ echo "checked"; } ?>><?php echo $r_b_estudiante["apellidos_nombres"] ?>
                                                        </label>
                                                    </div>
                                                <?php
                                                }
                                                ?>

                                            </div>
                                            <br>
                                            <div align="center">
                                                <a href="tutoria_sesion_grupal.php" class="btn btn-danger">Regresar</a>
                                                <button type="submit" class="btn btn-success">Guardar</button>
                                            </div>
                                        </form>


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
            <script type="text/javascript">
                

                function gen_arr_est() {
                    arr_estudiantes = [];
                    $("input[name='estudiantes']:checked").each(function() {
                        //Mediante la función push agregamos al arreglo los values de los checkbox
                        arr_estudiantes.push(($(this).attr("value")));
                        arr_estudiantes = [...new Set(arr_estudiantes)];
                    });
                    // Utilizamos console.log para ver comprobar que en realidad contiene algo el arreglo
                    document.getElementById("array_asistentes").value = arr_estudiantes;
                    
                }
            </script>
            <script type="text/javascript">
                function select_all() {
                    if ($('#all_check').is(':checked')) {
                        $("input[name='estudiantes']").each(function() {
                            $(this).prop("checked", true);
                        });
                    } else {
                        $("input[name='estudiantes']").each(function() {
                            $(this).prop("checked", false);
                        });
                    }
                    gen_arr_est();
                };
            </script>


            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php } else {
        echo "<script>
			window.history.back();
				</script>
			";
    }
}
}