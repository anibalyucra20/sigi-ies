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

        $b_datos_sistema = buscarDatosSistema($conexion);
        $r_b_datos_sistema = mysqli_fetch_array($b_datos_sistema);

        $id_est = $_POST['id'];

        //buscar matricula de estudiante
        $b_mat = buscarMatriculaByEstPeriodoSedePe($conexion, $id_est, $id_periodo_act, $id_sede_act, $rb_usuario['id_programa_estudios']);
        $r_b_mat = mysqli_fetch_array($b_mat);
        $id_mat_est = $r_b_mat['id'];

        $b_estudiante = buscarUsuarioById($conexion, $id_est);
        $r_b_estudiante = mysqli_fetch_array($b_estudiante);

        $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
        $cant_ud_mat = mysqli_num_rows($b_det_mat);
        $cont_ind_capp = 0;
        while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
            $id_prog = $r_b_det_mat['id_programacion_ud'];
            $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
            $r_b_prog = mysqli_fetch_array($b_prog_ud);
            $id_udd = $r_b_prog['id_unidad_didactica'];
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
            $cont_ind_capp += $cont_ind_logro_cap_ud;
        }
        $total_columnas = $cont_ind_capp + $cant_ud_mat + 3;
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
                                <form role="form" action="imprimir_reporte_individual" class="form-horizontal form-label-left input_mask" method="POST" target="_blank">
                                    <input type="hidden" name="data" value="<?php echo $id_est; ?>">
                                    <button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Imprimir Reporte</button>
                                </form>
                                <a href="reportes_coordinador" class="btn btn-danger">Regresar</a>
                                <h2 align="center"><b>REPORTE INDIVIDUAL - <?php echo $r_b_estudiante['dni'] . " - " . $r_b_estudiante['apellidos_nombres']; ?></b></h2>
                                <form role="form" action="" class="form-horizontal form-label-left input_mask" method="POST">
                                    <div class="table-responsive">
                                        <table id="" class="table table-striped table-bordered jambo_table bulk_action" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th colspan="<?php echo $total_columnas; ?>" bgcolor="black">
                                                        <center>CALIFICACIONES - UNIDADES DIDÁCTICAS</center>
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <?php
                                                    $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
                                                    while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
                                                        $id_prog = $r_b_det_mat['id_programacion_ud'];
                                                        $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
                                                        $r_b_prog = mysqli_fetch_array($b_prog_ud);
                                                        $id_udd = $r_b_prog['id_unidad_didactica'];
                                                        //BUSCAR UD
                                                        $b_uddd = buscarUnidadDidacticaById($conexion, $id_udd);
                                                        $r_b_udd = mysqli_fetch_array($b_uddd);

                                                        $b_Semestre = buscarSemestreById($conexion, $r_b_udd['id_semestre']);
                                                        $r_b_semestre = mysqli_fetch_array($b_Semestre);
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
                                                            <p class="verticalll">
                                                                <center><?php echo $r_b_udd['nombre'] . "<br>S-" . $r_b_semestre['descripcion']; ?></center>
                                                            </p>
                                                        </th>
                                                        <th>
                                                            <p class="verticalll">PROMEDIO</p>
                                                        </th>

                                                    <?php
                                                    }
                                                    ?>
                                                    <th>
                                                        <p class="verticalll">Ptj. Total</p>
                                                    </th>
                                                    <th>
                                                        <p class="verticalll">Ptj. Créditos</p>
                                                    </th>
                                                    <th>
                                                        <p>
                                                            <center>CONDICIÓN</center>
                                                        </p>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                //buscar estudiante para su id
                                                $b_est = buscarUsuarioById($conexion, $id_est);
                                                $r_b_est = mysqli_fetch_array($b_est);

                                                $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $r_b_est['id_semestre']);
                                                $min_ud_desaprobar = round(mysqli_num_rows($b_ud_pe_sem) / 2, 0, PHP_ROUND_HALF_DOWN);

                                                ?>
                                                <tr>
                                                    <?php
                                                    //buscar si estudiante esta matriculado en una unidad didactica
                                                    $suma_califss = 0;
                                                    $suma_ptj_creditos = 0;
                                                    $cont_ud_desaprobadas = 0;
                                                    $b_det_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
                                                    while ($r_b_det_mat = mysqli_fetch_array($b_det_mat)) {
                                                        $b_prog = buscarProgramacionUdById($conexion, $r_b_det_mat['id_programacion_ud']);
                                                        $r_b_prog = mysqli_fetch_array($b_prog);
                                                        $b_ud = buscarUnidadDidacticaById($conexion, $r_b_prog['id_unidad_didactica']);
                                                        $r_bb_ud = mysqli_fetch_array($b_ud);

                                                        $id_det_mat = $r_b_det_mat['id'];


                                                        //echo "<td>SI</td>";

                                                        //buscar las calificaciones
                                                        $b_calificaciones = buscarCalificacionByIdDetalleMatricula($conexion, $id_det_mat);

                                                        $suma_calificacion = 0;
                                                        $cont_calif = 0;
                                                        while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                                                            $id_calificacion = $r_b_calificacion['id'];
                                                            //buscamos las evaluaciones
                                                            $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                                                            if ($suma_evaluacion != 0) {
                                                                $cont_calif += 1;
                                                                $suma_calificacion += $suma_evaluacion;
                                                                $suma_evaluacion = round($suma_evaluacion);
                                                                if ($suma_evaluacion > 12) {
                                                                    echo '<td><center><font color="blue">' . $suma_evaluacion . '</font></center></td>';
                                                                    //echo '<th><center><input type="number" class="nota_input" style="color:blue;" value="' . $calificacion_final . '" min="0" max="20" disabled></center></th>';
                                                                } else {
                                                                    echo '<td><center><font color="red">' . $suma_evaluacion . '</font></center></td>';
                                                                    //echo 
                                                                }
                                                            } else {
                                                                $suma_evaluacion = "";
                                                                echo '<th></th>';
                                                            }
                                                        }
                                                        if ($cont_calif > 0) {
                                                            $calificacion = round($suma_calificacion / $cont_calif);
                                                        } else {
                                                            $calificacion = round($suma_calificacion);
                                                        }
                                                        if ($calificacion != 0) {
                                                            $calificacion = round($calificacion);
                                                        } else {
                                                            $calificacion = "";
                                                        }
                                                        //buscamos si tiene recuperacion
                                                        if ($r_b_det_mat['recuperacion'] != '') {
                                                            $calificacion = $r_b_det_mat['recuperacion'];
                                                        }

                                                        if ($calificacion > 12) {
                                                            echo '<th align="center" bgcolor="#BEBBBB"><font color="blue">' . $calificacion . '</font></th>';
                                                        } else {
                                                            echo '<th align="center" bgcolor="#BEBBBB"><font color="red">' . $calificacion . '</font></th>';
                                                            $cont_ud_desaprobadas += 1;
                                                        }
                                                        if (is_numeric($calificacion)) {
                                                            $suma_califss += $calificacion;
                                                            $suma_ptj_creditos += $calificacion * $r_bb_ud['creditos'];
                                                        } else {
                                                            $suma_ptj_creditos += 0 * $r_bb_ud['creditos'];
                                                        }
                                                    }
                                                    echo '<td align="center" ><font color="black">' . $suma_califss . '</font></td>';
                                                    echo '<td align="center" ><font color="black">' . $suma_ptj_creditos . '</font></td>';
                                                    if ($cont_ud_desaprobadas == 0) {
                                                        echo '<td align="center" ><font color="black">Promovido</font></td>';
                                                    } elseif ($cont_ud_desaprobadas <= $min_ud_desaprobar) {
                                                        echo '<td align="center" ><font color="black">Repite U.D. del Módulo Profesional</font></td>';
                                                    } else {
                                                        echo '<td align="center" ><font color="black">Repite el Módulo Profesional</font></td>';
                                                    }

                                                    ?>
                                                </tr>


                                            </tbody>
                                        </table>
                                        <table class="table table-striped table-bordered jambo_table bulk_action" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th bgcolor="black" colspan="<?php echo $r_b_datos_sistema['cant_semanas']+2;  ?>">
                                                        <center>ASISTENCIA</center>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>UNIDAD DIDÁCTICA</th>


                                                    <?php
                                                    for ($i = 1; $i <= $r_b_datos_sistema['cant_semanas']; $i++) {
                                                        echo "<th><center>Semana <br>" . $i . "</center></th>";
                                                    }
                                                    ?>
                                                    <th>Inasistencia</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $b_detalle_mat = buscarDetalleMatriculaByIdMatricula($conexion, $id_mat_est);
                                                while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
                                                    $id_prog = $r_b_det_mat['id_programacion_ud'];
                                                    echo "<tr>";
                                                    $b_prog_ud = buscarProgramacionUdById($conexion, $id_prog);
                                                    $r_b_prog = mysqli_fetch_array($b_prog_ud);

                                                    $b_ud = buscarUnidadDidacticaById($conexion, $r_b_prog['id_unidad_didactica']);
                                                    $r_b_ud = mysqli_fetch_array($b_ud);


                                                    $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
                                                    $r_b_silabo = mysqli_fetch_array($b_silabo);
                                                    $b_prog_act = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo['id']);

                                                    echo "<td>" . $r_b_ud['nombre'] . "</td>";
                                                    $cont_inasistencia = 0;
                                                    $cont_asis = 0;
                                                    while ($res_b_prog_act = mysqli_fetch_array($b_prog_act)) {
                                                        // buscamos la sesion que corresponde
                                                        $id_act = $res_b_prog_act['id'];
                                                        $b_sesion = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_act);
                                                        $r_b_sesion = mysqli_fetch_array($b_sesion);
                                                        $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $r_b_sesion['id'], $r_b_det_mat['id']);
                                                        $r_b_asistencia = mysqli_fetch_array($b_asistencia);
                                                        $cont_asis += mysqli_num_rows($b_asistencia);

                                                        if ($r_b_asistencia['asistencia'] == "P") {
                                                            echo "<td><center><font color='blue'>" . $r_b_asistencia['asistencia'] . "</font></center></td>";
                                                        } elseif ($r_b_asistencia['asistencia'] == "F") {
                                                            echo "<td><center><font color='red'>" . $r_b_asistencia['asistencia'] . "</font></center></td>";
                                                            $cont_inasistencia += 1;
                                                        } else {
                                                            echo "<td></td>";
                                                        }
                                                    }
                                                    if ($cont_inasistencia > 0) {
                                                        $porcent_ina = $cont_inasistencia * 100 / $cont_asis;
                                                    } else {
                                                        $porcent_ina = 0;
                                                    }
                                                    if (round($porcent_ina) >= 30) {
                                                        echo "<td><font color='red'><center>" . round($porcent_ina) . "%</font></center></td>";
                                                    } else {
                                                        echo "<td><font color='blue'><center>" . round($porcent_ina) . "%</font></center></td>";
                                                    }

                                                    echo "</tr>";
                                                }

                                                ?>
                                            </tbody>
                                        </table>
                                        <center><a href="reportes_coordinador" class="btn btn-danger">Regresar</a></center>
                                    </div>



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
