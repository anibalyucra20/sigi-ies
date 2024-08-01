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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 4) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        $id_sem = $_POST['sem_consolidado'];
        $turno = $_POST['turno'];
        $seccion = $_POST['seccion'];


        $b_semestre = buscarSemestreById($conexion, $id_sem);
        $rb_semestre = mysqli_fetch_array($b_semestre);
        $id_mf = $rb_semestre['id_modulo_formativo'];

        $b_mf = buscarModuloFormativoById($conexion, $id_mf);
        $rb_mf = mysqli_fetch_array($b_mf);
        $id_pe = $rb_mf['id_programa_estudio'];

        $b_pe = buscarProgramaEstudioById($conexion, $id_pe);
        $rb_pe = mysqli_fetch_array($b_pe);

        //bsucar programa-sede
        $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_pe);
        $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
        $id_programa_sede = $rb_pe_sede['id'];

        $array_estudiantes = [];
        // armar la nomina de estudiantes para poder mostrar todos los estudiantes del semestre
        $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
        $cont_ud_sem = mysqli_num_rows($b_ud_pe_sem);
        $cont_ind_capp = 0;
        while ($r_b_ud = mysqli_fetch_array($b_ud_pe_sem)) {
            $id_ud = $r_b_ud['id'];

            //buscar capacidades
            $b_capp = buscarCapacidadByIdUd($conexion, $id_ud);
            while ($r_b_capp = mysqli_fetch_array($b_capp)) {
                $id_capp = $r_b_capp['id'];
                //buscar indicadores de logro de capacidad
                $b_ind_l_capp = buscarIndCapacidadByIdCapacidad($conexion, $id_capp);
                $cont_ind_capp += mysqli_num_rows($b_ind_l_capp);
            }



            //buscar si la unidad didactica esta programado en el presente periodo
            $b_ud_prog = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_ud, $id_programa_sede, $id_periodo_act, $turno, $seccion);
            $r_b_ud_prog = mysqli_fetch_array($b_ud_prog);
            $cont_res = mysqli_num_rows($b_ud_prog);
            if ($cont_res > 0) {
                $id_prog_ud = $r_b_ud_prog['id'];
                //buscar detalle de matricula matriculas a la programacion de la unidad didactica
                $b_det_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog_ud);
                while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
                    // buscar matricula para obtener datos del estudiante
                    $id_mat = $r_b_det_mat['id_matricula'];
                    $b_mat = buscarMatriculaById($conexion, $id_mat);
                    $r_b_mat = mysqli_fetch_array($b_mat);
                    $id_estudiante = $r_b_mat['id_estudiante'];
                    // buscar estudiante
                    $b_estudiante = buscarUsuarioById($conexion, $id_estudiante);
                    $r_b_estudiante = mysqli_fetch_array($b_estudiante);
                    $array_estudiantes[] = $r_b_estudiante['apellidos_nombres'];
                }
                $aa = "SI";
            } else {
                $aa = "NO";
            }
            //echo $r_b_ud['descripcion']." - ".$aa."<br>";
        }
        $n_array_estudiantes = array_unique($array_estudiantes);
        $collator = collator_create("es");
        $collator->sort($n_array_estudiantes);

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
            <style>
                p.verticalll {
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
                    <?php
                    include("include/menu_docente.php"); ?>
                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <form role="form" action="imprimir_reporte_consolidado_detallado" class="form-horizontal form-label-left input_mask" method="POST" target="_blank">
                                    <input type="hidden" name="sem_consolidado" value="<?php echo $id_sem; ?>">
                                    <button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Imprimir Reporte</button>
                                </form>
                                <a href="reportes_coordinador" class="btn btn-danger">Regresar</a>
                                <h2 align="center"><b>REPORTE CONSOLIDADO DETALLADO - <?php echo $rb_pe['nombre'] . " - SEMESTRE " . $rb_semestre['descripcion']; ?></b></h2>
                                <form role="form" action="" class="form-horizontal form-label-left input_mask" method="POST">
                                    <div class="table-responsive">
                                        <table id="" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th rowspan="2">
                                                        <center>Nro Orden</center>
                                                    </th>
                                                    <th rowspan="2">
                                                        <center>DNI</center>
                                                    </th>
                                                    <th rowspan="2">
                                                        <center>APELLIDOS Y NOMBRES</center>
                                                    </th>
                                                    <th colspan="<?php echo $cont_ind_capp; ?>">
                                                        <center>UNIDADES DIDÁCTICAS</center>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <?php
                                                    $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
                                                    while ($r_bb_ud = mysqli_fetch_array($b_ud_pe_sem)) {
                                                        $id_udd = $r_bb_ud['id'];
                                                        //BUSCAR UD
                                                        $b_uddd = buscarUnidadDidacticaById($conexion, $id_udd);
                                                        $r_b_udd = mysqli_fetch_array($b_uddd);
                                                        //buscar capacidad
                                                        $cont_ind_logro_cap_ud = 0;
                                                        $b_cap_ud = buscarCapacidadByIdUd($conexion, $id_udd);
                                                        while ($r_b_cap_ud = mysqli_fetch_array($b_cap_ud)) {
                                                            $id_cap_ud = $r_b_cap_ud['id'];
                                                            // buscar indicadores de capacidad
                                                            $b_ind_l_cap_ud = buscarIndCapacidadByIdCapacidad($conexion, $id_cap_ud);
                                                            $cant_id_cap_ud = mysqli_num_rows($b_ind_l_cap_ud);
                                                            $cont_ind_logro_cap_ud += $cant_id_cap_ud;
                                                        }

                                                    ?>
                                                        <th colspan="<?php echo $cont_ind_logro_cap_ud; ?>">
                                                            <p class="verticalll"><?php echo $r_b_udd['nombre']; ?></p>
                                                        </th>
                                                    <?php
                                                    }
                                                    ?>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                foreach ($n_array_estudiantes as $key => $val) {
                                                    $key += 1;
                                                    //buscar estudiante para su id
                                                    $b_est = buscarUsuarioByNomAp($conexion, $val);
                                                    $r_b_est = mysqli_fetch_array($b_est);
                                                    $id_est = $r_b_est['id'];
                                                ?>
                                                    <tr>
                                                        <td><?php echo $key; ?></td>
                                                        <td><?php echo $r_b_est['dni']; ?></td>
                                                        <td><?php echo $r_b_est['apellidos_nombres']; ?></td>
                                                        <?php
                                                        //buscar si estudiante esta matriculado en una unidad didactica
                                                        $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
                                                        while ($r_bb_ud = mysqli_fetch_array($b_ud_pe_sem)) {
                                                            $id_udd = $r_bb_ud['id'];
                                                            $b_prog_ud = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_udd, $id_programa_sede, $id_periodo_act, $turno, $seccion);
                                                            $r_b_prog_ud = mysqli_fetch_array($b_prog_ud);
                                                            $id_prog = $r_b_prog_ud['id'];

                                                            //buscar matricula de estudiante
                                                            $b_mat_est = buscarMatriculaByEstPeriodoSede($conexion, $id_est, $id_periodo_act, $id_sede_act);
                                                            $r_b_mat_est = mysqli_fetch_array($b_mat_est);
                                                            $id_mat_est = $r_b_mat_est['id'];
                                                            //buscar detalle de matricula
                                                            $b_det_mat_est = buscarDetalleMatriculaByIdMatriculaAndProgrmacion($conexion, $id_mat_est, $id_prog);
                                                            $r_b_det_mat_est = mysqli_fetch_array($b_det_mat_est);
                                                            $cont_r_b_det_mat = mysqli_num_rows($b_det_mat_est);
                                                            $id_det_mat = $r_b_det_mat_est['id'];
                                                            if ($cont_r_b_det_mat > 0) {
                                                                //echo "<td>SI</td>";

                                                                //buscar las calificaciones
                                                                $b_calificaciones = buscarCalificacionByIdDetalleMatricula($conexion, $id_det_mat);

                                                                $suma_calificacion = 0;
                                                                while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                                                                    $id_calificacion = $r_b_calificacion['id'];
                                                                    //buscamos las evaluaciones
                                                                    $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                                                                    if ($suma_evaluacion != 0) {
                                                                        $suma_evaluacion = round($suma_evaluacion);

                                                                        if ($suma_evaluacion > 12) {
                                                                            echo '<th><center><font color="blue">' . $suma_evaluacion . '</font></center></th>';
                                                                            //echo '<th><center><input type="number" class="nota_input" style="color:blue;" value="' . $calificacion_final . '" min="0" max="20" disabled></center></th>';
                                                                        } else {
                                                                            echo '<th><center><font color="red">' . $suma_evaluacion . '</font></center></th>';
                                                                            //echo 
                                                                        }
                                                                    } else {
                                                                        $suma_evaluacion = "";
                                                                        echo '<th></th>';
                                                                    }
                                                                }
                                                            } else {
                                                                //buscar los indicadores
                                                                $total_ind = 0;
                                                                $b_capacidad = buscarCapacidadByIdUd($conexion, $id_udd);
                                                                while ($r_b_cap = mysqli_fetch_array($b_capacidad)) {
                                                                    $b_ind_log_cap = buscarIndCapacidadByIdCapacidad($conexion, $r_b_cap['id']);
                                                                    $cont_ind = mysqli_num_rows($b_ind_log_cap);
                                                                    $total_ind += $cont_ind;
                                                                }



                                                                echo '<td colspan="' . $total_ind . '"></td>';
                                                            }
                                                        }
                                                        ?>
                                                    </tr>
                                                <?php
                                                }
                                                ?>

                                            </tbody>
                                        </table>

                                    </div>
                                    <center><a href="reportes_coordinador.php" class="btn btn-danger">Regresar</a></center>

                                </form>
                                <?php



                                ?>
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

        </body>

        </html>
<?php }
}
