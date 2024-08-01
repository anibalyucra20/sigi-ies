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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
        $mostrar_archivo = 0;
    } else {
        $nro_calificacion = base64_decode($_GET['data2']);
        $mostrar_archivo = 1;

        $b_periodo_acad = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
        $r_per_acad = mysqli_fetch_array($b_periodo_acad);
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($r_per_acad['fecha_fin']);
        if ($fecha_actual <= $fecha_fin_per) {
            $editar_doc = 1;
        } else {
            $editar_doc = 0;
        }
        OrdenarEstudiantesUnidadDidactica($conexion, $id_prog);

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

            <title>Evaluacion<?php include("../include/header_title.php"); ?></title>
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

            <!-- Custom Theme Style -->
            <link href="../plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
            <!-- Script obtenido desde CDN jquery -->
            <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
            <script>
                function confirmar_agregar() {
                    var r = confirm("Estas Seguro de Agregar nuevos Criterios de Evaluación?");
                    if (r == true) {
                        return true;
                    } else {
                        return false;
                    }
                }
            </script>

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
                    <!--menu-->
                    <?php
                    include("include/menu_docente.php");

                    ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">
                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <?php

                                        if ($cont_res == 0) {
                                            //filtro si es que no existe el id de prog_ud 
                                            echo "<h2>No Existen Registros</h2>";
                                        } else {
                                            $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
                                            $r_b_ud = mysqli_fetch_array($b_ud);
                                            //buscamos la cantidad de indicadores para definir la cantidad de calificaciones
                                            $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
                                            $total_indicadores = 0;
                                            while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
                                                $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
                                                $cont_indicadores = mysqli_num_rows($b_indicador_capac);
                                                $total_indicadores = $total_indicadores + $cont_indicadores;
                                            };
                                            if ($nro_calificacion < 1 ||  $nro_calificacion > $total_indicadores) {
                                                //filtro si es que no existe el indicador pasado como parametro
                                                echo "<h2>No Existen Registros - Indicadores</h2>";
                                            } else {

                                        ?>
                                                <div class="">
                                                    <h2 align="center"><b>Evaluación - <?php echo "Indicador de Logro " . $nro_calificacion . " - " . $r_b_ud['nombre']; ?></b></h2>
                                                    <a href="calificaciones?data=<?php echo base64_encode($id_prog); ?>" class="btn btn-danger">Regresar</a>
                                                    <div class="clearfix"></div>
                                                </div>
                                                <div class="x_content">
                                                    <form role="form" action="operaciones/actualizar_calificacion_eva.php" class="form-horizontal form-label-left input_mask" method="POST">
                                                        <input type="hidden" name="data" id="id_prog" value="<?php echo $id_prog; ?>">
                                                        <input type="hidden" name="nro_calificacion" id="nro_calificacion" value="<?php echo $nro_calificacion; ?>">
                                                        <input type="hidden" name="cant_calif" value="<?php echo $total_indicadores; ?>">
                                                        <table id="" class="table table-striped table-bordered">
                                                            <?php
                                                            $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
                                                            $r_b_det_mat = mysqli_fetch_array($b_detalle_mat);
                                                            $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
                                                            $r_b_calificacion = mysqli_fetch_array($b_calificacion);
                                                            $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                                                            $cont_col = 0;
                                                            while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                                                                $b_critt_eva = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);
                                                                $c_b_critt = mysqli_num_rows($b_critt_eva);
                                                                $cont_col += $c_b_critt + 1;
                                                            }
                                                            ?>
                                                            <tr class="headings">
                                                                <th rowspan="3">
                                                                    <center>
                                                                        <p class="verticalll">ORDEN</p>
                                                                    </center>
                                                                </th>
                                                                <th rowspan="3">
                                                                    <center>DNI</center>
                                                                </th>
                                                                <th rowspan="3">
                                                                    <center>APELLIDOS Y NOMBRES</center>
                                                                </th>
                                                                <th colspan="<?php echo $cont_col; ?>">
                                                                    <center>EVALUACIÓN</center>
                                                                </th>
                                                                <th rowspan="3" bgcolor="#D5D2D2">
                                                                    <center>
                                                                        <p class="verticalll">PROMEDIO DE CALIFICACIÓN</p>
                                                                    </center>
                                                                </th>

                                                            </tr>

                                                            <tr class="headings">
                                                                <?php
                                                                $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
                                                                $r_b_det_mat = mysqli_fetch_array($b_detalle_mat);
                                                                $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
                                                                $r_b_calificacion = mysqli_fetch_array($b_calificacion);
                                                                $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                                                                $count = 1;
                                                                while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                                                                    $b_critt_eva = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);
                                                                    $c_b_critt = mysqli_num_rows($b_critt_eva);
                                                                ?>
                                                                    <th colspan="<?php echo $c_b_critt + 1; ?>">
                                                                        <center><?php echo $r_b_evaluacion['detalle'] ?><br>Ponderado: <?php echo $r_b_evaluacion['ponderado']; ?>%
                                                                            <?php if ($editar_doc) { ?>
                                                                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target=".edit_eva<?php echo $r_b_evaluacion['id']; ?>"><i class="fa fa-edit"></i></button>
                                                                                <a title="Agregar Criterio de Evaluación" class="btn btn-success" href="operaciones/agregar_criterio_evaluacion?data=<?php echo base64_encode($id_prog); ?>&data2=<?php echo base64_encode($nro_calificacion); ?>&data3=<?php echo base64_encode($r_b_evaluacion['detalle']); ?>" onclick="return confirmar_agregar();"><i class="fa fa-plus-square"></i></a>
                                                                            <?php } ?>

                                                                        </center>
                                                                    </th>
                                                                <?php
                                                                    if ($editar_doc) {
                                                                        include('include/acciones_evaluacion.php');
                                                                    }
                                                                    $count += 1;
                                                                }
                                                                ?>

                                                            </tr>
                                                            <tr class="headings">
                                                                <?php
                                                                $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
                                                                $r_b_det_mat = mysqli_fetch_array($b_detalle_mat);
                                                                $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
                                                                $r_b_calificacion = mysqli_fetch_array($b_calificacion);
                                                                $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                                                                $count = 1;
                                                                while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                                                                    $b_critt_eva = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);
                                                                    while ($r_b_critt_eva = mysqli_fetch_array($b_critt_eva)) {
                                                                ?>
                                                                        <th height="auto" width="20px">
                                                                            <center>
                                                                                <?php
                                                                                if ($editar_doc) { ?>
                                                                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target=".edit_crit_<?php echo $r_b_critt_eva['id']; ?>"><i class="fa fa-edit"></i></button>
                                                                                <?php } ?>


                                                                                <p class="verticalll" id=""><?php echo $r_b_critt_eva['detalle']; ?></p>
                                                                                <br>

                                                                            </center>
                                                                        </th>
                                                                    <?php
                                                                        $count += 1;
                                                                        if ($editar_doc) {
                                                                            include('include/acciones_criterio.php');
                                                                        }
                                                                    }
                                                                    ?>
                                                                    <th height="auto" width="20px" bgcolor="#D5D2D2">
                                                                        <center>
                                                                            <p class="verticalll">Promedio <?php echo $r_b_evaluacion['detalle']; ?></p>
                                                                        </center>
                                                                    </th>
                                                                <?php

                                                                }  ?>
                                                            </tr>


                                                            <tbody>
                                                                <?php
                                                                $b_detalle_mat = buscarDetalleMatriculaByIdProgramacionOrden($conexion, $id_prog);
                                                                $orden = 0;
                                                                while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
                                                                    $orden++;
                                                                    $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
                                                                    $r_b_mat = mysqli_fetch_array($b_matricula);
                                                                    $b_estudiante = buscarUsuarioById($conexion, $r_b_mat['id_estudiante']);
                                                                    $r_b_est = mysqli_fetch_array($b_estudiante);

                                                                    $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
                                                                    $r_b_silabo = mysqli_fetch_array($b_silabo);

                                                                    $cont_inasistencia = contar_inasistencia($conexion, $r_b_silabo['id'], $r_b_est['id']);
                                                                    if ($cont_inasistencia > 0) {
                                                                        $porcent_ina = round($cont_inasistencia * 100 / 16);
                                                                    } else {
                                                                        $porcent_ina = 0;
                                                                    }

                                                                    if ($r_b_mat['licencia'] != "") {
                                                                        $si_falta = '';
                                                                        $si_licencia = ' readonly title="Licencia"';
                                                                        $nom_ap = '<font color="red">' . $r_b_est['apellidos_nombres'] . ' (Licencia)</font>';
                                                                        $dni = '<font color="red">' . $r_b_est['dni'] . '</font>';
                                                                        $fila = ' style="background-color:pink"';
                                                                    } elseif ($porcent_ina >= 30) {
                                                                        $si_licencia = ' readonly title="Inasistencia mayor de 30%"';
                                                                        $si_falta = 'si';
                                                                        $nom_ap = '<font color="red">' . $r_b_est['apellidos_nombres'] . '</font>';
                                                                        $dni = '<font color="red">' . $r_b_est['dni'] . '</font>';
                                                                        $fila = ' style="background-color:pink"';
                                                                    } else {
                                                                        $si_licencia = "";
                                                                        $si_falta = '';
                                                                        $nom_ap = $r_b_est['apellidos_nombres'];
                                                                        $dni = $r_b_est['dni'];
                                                                        $fila = '';
                                                                    }

                                                                ?>
                                                                    <tr <?php echo  $fila; ?>>

                                                                        <td><?php echo $r_b_det_mat['orden']; ?></td>
                                                                        <td><?php echo $dni; ?></td>
                                                                        <td><?php echo $nom_ap; ?></td>

                                                                        <?php
                                                                        $suma_notas = 0;
                                                                        $cont_notas = 0;
                                                                        $suma_calificacion = 0;
                                                                        $opcion = 1;
                                                                        //buscamos las evaluaciones
                                                                        $b_calificacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calificacion);
                                                                        while ($r_b_calificacion = mysqli_fetch_array($b_calificacion)) {
                                                                            $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $r_b_calificacion['id']);
                                                                            $suma_evaluacion = 0;
                                                                            while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
                                                                                $id_evaluacion = $r_b_evaluacion['id'];

                                                                                //buscamos los criterios de evaluacion
                                                                                $b_criterio_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $id_evaluacion);
                                                                                $suma_criterios = 0;
                                                                                $cont_c = 0;
                                                                                while ($r_b_criterio_evaluacion = mysqli_fetch_array($b_criterio_evaluacion)) {
                                                                                    if (is_numeric($r_b_criterio_evaluacion['calificacion'])) {
                                                                                        $suma_criterios += $r_b_criterio_evaluacion['calificacion'];
                                                                                        $cont_c += 1;
                                                                                    }
                                                                                    if ($r_b_criterio_evaluacion['calificacion'] > 12 && $r_b_criterio_evaluacion['calificacion'] <= 20) {
                                                                                        $colort = 'style="color:blue; "';
                                                                                    } else {
                                                                                        $colort = 'style="color:red; "';
                                                                                    }

                                                                                    if ($editar_doc) {
                                                                                        echo '<td width="20px"><input class="nota_input" type="number" ' . $colort . $si_licencia . ' id="" name="' . $r_b_est['dni'] . '_' . $r_b_criterio_evaluacion['id'] . '" value="' . $r_b_criterio_evaluacion['calificacion'] . '" min="0" max="20" size="1" maxlength="1"></td>';
                                                                                    } else {
                                                                                        echo '<td width="20px"><label ' . $colort . '>' . $r_b_criterio_evaluacion['calificacion'] . '</label></td>';
                                                                                    }
                                                                                }

                                                                                if ($cont_c > 0) {
                                                                                    $calificacion = round($suma_criterios / $cont_c);
                                                                                } else {
                                                                                    $calificacion = round($suma_criterios);
                                                                                }
                                                                                if ($calificacion == 0) {
                                                                                    $mostrar = "";
                                                                                } else {
                                                                                    $mostrar = round($calificacion);
                                                                                }
                                                                                if ($mostrar > 12) {
                                                                                    echo '<th><center><font color="blue">' . $mostrar . '</font></center></th>';
                                                                                } else {
                                                                                    echo '<th><center><font color="red">' . $mostrar . '</font></center></th>';
                                                                                }
                                                                                if (is_numeric($r_b_evaluacion['ponderado'])) {
                                                                                    $suma_evaluacion += ($r_b_evaluacion['ponderado'] / 100) * $calificacion;
                                                                                }
                                                                            }
                                                                            if ($suma_evaluacion != 0) {
                                                                                $calificacion_e = round($suma_evaluacion);
                                                                            } else {
                                                                                $calificacion_e = "";
                                                                            }
                                                                            if ($si_falta != '') {
                                                                                echo '<th><center><font color="red">0</font></center></th>';
                                                                            } elseif ($si_licencia != '') {
                                                                                echo '<th><center><font></font></center></th>';
                                                                            } else {
                                                                                if ($calificacion_e > 12) {
                                                                                    echo '<th><center><font color="blue">' . $calificacion_e . '</font></center></th>';
                                                                                } else {
                                                                                    echo '<th><center><font color="red">' . $calificacion_e . '</font></center></th>';
                                                                                }
                                                                            }
                                                                        }

                                                                        ?>


                                                                    </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                        </table>
                                                        <div align="center">
                                                            <br>
                                                            <a href="calificaciones?data=<?php echo base64_encode($id_prog); ?>" class="btn btn-danger">Regresar</a>
                                                            <?php if ($editar_doc) { ?>
                                                                <button type="submit" class="btn btn-success">Guardar</button>
                                                            <?php } ?>
                                                        </div>
                                                    </form>
                                                </div>
                                        <?php }
                                        } ?>
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

            <script type="text/javascript">
                function actualizarCriterio(data) {
                    let detalle_eva = document.getElementById("detalle_eva_" + data).value
                    let orden_crit = document.getElementById("ord_crit_" + data).value
                    let detalle_crit = document.getElementById("ndetalle_" + data).value
                    let id_prog = document.getElementById("id_prog").value
                    let nro_calificacion = document.getElementById("nro_calificacion").value
                    window.location = 'operaciones/actualizar_criterio?id=' + data + '&id_prog=' + id_prog + '&ncalif=' + nro_calificacion + '&detalle_eva=' + detalle_eva + '&detalle_crit=' + detalle_crit + '&orden_crit=' + orden_crit;

                };

                function actualizarEvaluacion(id) {
                    let peso_eva = document.getElementById("peso_evav_" + id).value
                    let id_prog = document.getElementById("id_prog").value
                    let nro_calificacion = document.getElementById("nro_calificacion").value
                    window.location = 'operaciones/actualizar_evaluacion?id=' + id + '&id_prog=' + id_prog + '&ncalif=' + nro_calificacion + '&peso_eva=' + peso_eva;

                };
            </script>
            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php
    }
}
