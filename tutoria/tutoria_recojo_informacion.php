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


        $b_tutoria = buscarTutoriaByIdDocenteAndIdPeriodoSede($conexion, $id_usuario, $id_periodo_act, $id_sede_act);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);
        $id_tutoria = $r_b_tutoria['id'];
        $b_docente_tutoria = buscarUsuarioById($conexion, $r_b_tutoria['id_docente']);
        $r_b_docente_tutoria = mysqli_fetch_array($b_docente_tutoria);
        if ($r_b_tutoria['id_docente'] == $id_usuario) {
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
                <style>
                    p.vertical {
                        /* idéntico a rotateZ(45deg); */

                        writing-mode: vertical-lr;
                        transform: rotate(180deg);


                    }

                    .nota_input {
                        width: 3em;
                    }
                </style>

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
                                            <h2 align="center">Recojo de Información</h2>
                                            <a href="tutoria" class="btn btn-danger">Regresar</a>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="table-responsive">
                                            <br />
                                            <form action="operaciones/actualizar_recojo_informacion" role="form" class="form-horizontal form-label-left input_mask" method="POST">
                                                <table class="table table-bordered jambo_table" style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th rowspan="2">
                                                                <center>Nro</center>
                                                            </th>
                                                            <th rowspan="2">
                                                                <center>Estudiante</center>
                                                            </th>
                                                            <?php
                                                            $tut_est = buscarTutoriaEstudiantesByIdTutoria($conexion, $id_tutoria);
                                                            $r_b_tut_est = mysqli_fetch_array($tut_est);
                                                            $b_tut_rec_info = buscarTutoriaRecojoInfoByIdTutEst($conexion, $r_b_tut_est['id']);
                                                            $cont_tut_rec_info = mysqli_num_rows($b_tut_rec_info);
                                                            while ($r_b_tut_rec_info = mysqli_fetch_array($b_tut_rec_info)) {
                                                            ?>
                                                                <th>
                                                                    <p class="vertical"><?php echo $r_b_tut_rec_info['descripcion']; ?></p>
                                                                </th>
                                                            <?php
                                                            }
                                                            ?>
                                                            <th rowspan="2">
                                                                <center>Observación</center>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $contador = 0;
                                                        //buscar cantidad de estudiantes asisgandos a la turoria
                                                        $b_est_tutoria = buscarTutoriaEstudiantesByIdTutoria($conexion, $id_tutoria);
                                                        $cont_est_tutoria = mysqli_num_rows($b_est_tutoria);
                                                        while ($r_b_tutoria = mysqli_fetch_array($b_est_tutoria)) {
                                                            $b_est = buscarUsuarioById($conexion, $r_b_tutoria['id_estudiante']);
                                                            $r_b_est = mysqli_fetch_array($b_est);

                                                            $b_pe = buscarProgramaEstudioById($conexion, $r_b_est['id_programa_estudios']);
                                                            $r_b_pe = mysqli_fetch_array($b_pe);
                                                            $b_sem = buscarSemestreById($conexion, $r_b_est['id_semestre']);
                                                            $r_b_sem = mysqli_fetch_array($b_sem);
                                                            $contador++;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $contador; ?></td>
                                                                <td><?php echo $r_b_est['apellidos_nombres']; ?></td>
                                                                <?php
                                                                $b_tut_rec_info = buscarTutoriaRecojoInfoByIdTutEst($conexion, $r_b_tutoria['id']);
                                                                while ($r_b_tut_rec_info = mysqli_fetch_array($b_tut_rec_info)) {
                                                                ?>
                                                                    <td>
                                                                        <center>
                                                                            <select name="<?php echo $r_b_tutoria['id'] . "_" . $r_b_tut_rec_info['id']; ?>" id="">
                                                                                <option value="1" <?php if ($r_b_tut_rec_info['valor'] == 1) {
                                                                                                        echo "selected";
                                                                                                    } ?>>SI</option>
                                                                                <option value="0" <?php if ($r_b_tut_rec_info['valor'] == 0) {
                                                                                                        echo "selected";
                                                                                                    } ?>>NO</option>
                                                                            </select>
                                                                        </center>
                                                                    </td>
                                                                <?php
                                                                }
                                                                ?>
                                                                <td><input class="form-control" type="text" name="obs_<?php echo $r_b_tutoria['id']; ?>" value="<?php echo $r_b_tutoria['observacion']; ?>"></td>
                                                            </tr>
                                                        <?php
                                                        }

                                                        ?>
                                                    </tbody>


                                                </table>
                                                <br>
                                                <div align="center">
                                                    <a href="tutoria" class="btn btn-danger">Cancelar</a>
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
                <?php mysqli_close($conexion); ?>
            </body>

            </html>
<?php
        } else {
            echo "<script>
			window.history.back();
				</script>
			";
        }
    }
}
