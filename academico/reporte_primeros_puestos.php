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

        $suma_creditos = 0;

        while ($r_b_ud = mysqli_fetch_array($b_ud_pe_sem)) {
            $id_ud = $r_b_ud['id'];
            $suma_creditos += $r_b_ud['creditos'];

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

                    writing-mode: vertical-rl;
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
                                <form role="form" action="imprimir_reporte_primer_puesto" class="form-horizontal form-label-left input_mask" method="POST" target="_blank">
                                    <input type="hidden" name="car_consolidado" value="<?php echo $id_pe; ?>">
                                    <input type="hidden" name="sem_consolidado" value="<?php echo $id_sem; ?>">
                                    <input type="hidden" name="turno" value="<?php echo $turno; ?>">
                                    <input type="hidden" name="seccion" value="<?php echo $seccion; ?>">
                                    <button type="submit" class="btn btn-info"><i class="fa fa-print"></i> Imprimir Reporte</button>
                                </form>
                                <a href="reportes_coordinador" class="btn btn-danger">Regresar</a>
                                <h2 align="center"><b>REPORTE PRIMEROS PUESTOS - <?php echo $rb_pe['nombre'] . " - SEMESTRE " . $rb_semestre['descripcion'] . " " . $rb_periodo_act['nombre']; ?></b></h2>
                                <div class="table-responsive">
                                    <table id="" class="table table-striped table-bordered" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">
                                                    <center>Orden de Mérito</center>
                                                </th>
                                                <th rowspan="2">
                                                    <center>DNI</center>
                                                </th>
                                                <th rowspan="2">
                                                    <center>APELLIDOS Y NOMBRES</center>
                                                </th>

                                                <th rowspan="2">
                                                    <center>PUNTAJE TOTAL CRÉDITOS</center>
                                                </th>
                                                <th rowspan="2">
                                                    <center>PROMEDIO PONDERADO</center>
                                                </th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $primeros_puestos = [];
                                            foreach ($n_array_estudiantes as $key => $val) {
                                                $key += 1;
                                                //buscar estudiante para su id
                                                $b_est = buscarUsuarioByNomAp($conexion, $val);
                                                $r_b_est = mysqli_fetch_array($b_est);
                                                $id_est = $r_b_est['id'];

                                                //buscar si estudiante esta matriculado en una unidad didactica
                                                $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion,$id_sem);
                                                $min_ud_desaprobar = round(mysqli_num_rows($b_ud_pe_sem) / 2, 0, PHP_ROUND_HALF_DOWN);

                                                $suma_califss = 0;
                                                $suma_ptj_creditos = 0;
                                                $cont_ud_desaprobadas = 0;
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
                                                        $cont_calif = 0;
                                                        while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                                                            $id_calificacion = $r_b_calificacion['id'];
                                                            //buscamos las evaluaciones
                                                            $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                                                            $suma_calificacion += $suma_evaluacion;
                                                            if ($suma_evaluacion > 0) {
                                                                $cont_calif += 1;
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
                                                        if ($r_b_det_mat_est['recuperacion'] != '') {
                                                            $calificacion = $r_b_det_mat_est['recuperacion'];
                                                        }

                                                        if ($calificacion > 12) {
                                                            //echo '<td align="center" ><font color="blue">' . $calificacion . '</font></td>';
                                                        } else {
                                                            //echo '<td align="center" ><font color="red">' . $calificacion . '</font></td>';
                                                            $cont_ud_desaprobadas += 1;
                                                        }
                                                    } else {
                                                        $calificacion = 0;
                                                        //echo '<td></td>';
                                                    }
                                                    if (is_numeric($calificacion)) {
                                                        $suma_califss += $calificacion;
                                                        $suma_ptj_creditos += $calificacion * $r_bb_ud['creditos'];
                                                    } else {
                                                        $suma_ptj_creditos += 0 * $r_bb_ud['creditos'];
                                                    }
                                                }
                                                $primeros_puestos[$id_est] = $suma_ptj_creditos;
                                            }
                                            arsort($primeros_puestos);

                                            //los estudiantes que desaprobaron alguna UD se pasan al final de la lista
                                            foreach ($primeros_puestos as $key => $value) {
                                                $cant_ud_desaprobado = calc_ud_desaprobado_sin_recuperacion($conexion, $key, $id_periodo_act,$id_sede_act, $id_programa_sede, $id_sem,$turno,$seccion);
                                                if ($cant_ud_desaprobado > 0) {
                                                    $id_est_des = $key;
                                                    $ptj_est_des = $value;
                                                    unset($primeros_puestos[$key]);
                                                    $primeros_puestos[$id_est_des] = $ptj_est_des;
                                                }
                                            }
                                            // los estudiantes de repitencia pasan al ultimo del ranking
                                            foreach ($primeros_puestos as $key => $value) {
                                                $mat_todos = calcular_mat_ud($conexion, $key, $id_periodo_act,$id_sede_act, $id_programa_sede, $id_sem,$turno,$seccion);
                                                if ($mat_todos == 0) {
                                                    $id_est_des = $key;
                                                    $ptj_est_des = $value;
                                                    unset($primeros_puestos[$key]);
                                                    $primeros_puestos[$id_est_des] = $ptj_est_des;
                                                }
                                            }

                                            // imprime el ranking
                                            $cont = 0;
                                            foreach ($primeros_puestos as $key => $value) {

                                                $cont += 1;
                                                $b_estt = buscarUsuarioById($conexion, $key);
                                                $r_b_estt = mysqli_fetch_array($b_estt);

                                                //var_dump($primeros_puestos);

                                            ?>
                                                <tr>
                                                    <td><?php echo $cont . 'º Puesto'; ?></td>
                                                    <td><?php echo $r_b_estt['dni'] ?></td>
                                                    <td><?php echo $r_b_estt['apellidos_nombres']; ?></td>
                                                    <td><?php echo $value; ?></td>
                                                    <td><?php echo round($value / $suma_creditos, 2); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>

                                        </tbody>
                                    </table>
                                    <b>NOTA:</b><BR>
                                    <P>- Los estudiantes que tienen 1 o más unidades didácticas desaprobadas no participan el en ranking de los primeros puestos, Aún teniendo el más alto puntaje.</P>
                                    <p>- Los estudiantes matriculados en unidades didácticas de repitencia no participan el en ranking de los primeros puestos.</p>
                                    <br>
                                    <br>
                                </div>


                                <center><a href="reportes_coordinador" class="btn btn-danger">Regresar</a></center>
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

        </html><?php }
        }
