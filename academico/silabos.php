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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente']!=$id_usuario) {
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

            <title>Sílabo <?php include("../include/header_title.php"); ?></title>
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
            <script>
                function confirmaragregar() {
                    var r = confirm("Estas Seguro de Agregar Nueva Semana al silabo?");
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

                    include("include/menu_docente.php");


                    $b_ud = buscarUnidadDidacticaById($conexion, $res_b_prog['id_unidad_didactica']);
                    $r_b_ud = mysqli_fetch_array($b_ud);
                    //buscar semestre
                    $b_sem = buscarSemestreById($conexion, $r_b_ud['id_semestre']);
                    $r_b_sem = mysqli_fetch_array($b_sem);
                    //buscar modulo profesional
                    $b_mod = buscarModuloFormativoById($conexion, $r_b_sem['id_modulo_formativo']);
                    $r_b_mod = mysqli_fetch_array($b_mod);
                    //buscar programa de estudio
                    $b_pe = buscarProgramaEstudioById($conexion, $r_b_mod['id_programa_estudio']);
                    $r_b_pe = mysqli_fetch_array($b_pe);
                    //buscamos el silabo y sus datos
                    $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
                    $r_b_silabo = mysqli_fetch_array($b_silabo);
                    $id_silabo = $r_b_silabo['id'];
                    //buscamos la cantidad de indicadores para definir la cantidad de calificaciones
                    $b_capacidades = buscarCapacidadByIdUd($conexion, $res_b_prog['id_unidad_didactica']);
                    $total_indicadores = 0;
                    while ($r_b_capacidades = mysqli_fetch_array($b_capacidades)) {
                        $b_indicador_capac = buscarIndCapacidadByIdCapacidad($conexion, $r_b_capacidades['id']);
                        $cont_indicadores = mysqli_num_rows($b_indicador_capac);
                        $total_indicadores = $total_indicadores + $cont_indicadores;
                    };
                    ?>

                    <!-- page content -->
                    <div class="right_col" role="main">
                        <div class="">

                            <div class="clearfix"></div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="x_panel">
                                        <div class="">
                                            <h2 align="center"><b>Sílabo - <?php echo $r_b_ud['nombre']; ?></b></h2>
                                            <form action="imprimir_silabo" method="POST" target="_blank">
                                                <input type="hidden" name="data" value="<?php echo $id_prog; ?>">
                                                <button type="submit" class="btn btn-info">Imprimir</button>
                                            </form>
                                            <a href="unidades_didacticas" class="btn btn-danger">Regresar</a>
                                            <div class="clearfix"></div>
                                            <button class="btn btn-success" data-toggle="modal" data-target=".copiar">Copiar Silabo</button>
                                            <div class="clearfix"></div>
                                            <!--MODAL COPIAR INFFORMACION-->
                                            <div class="modal fade copiar" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                            </button>
                                                            <h4 class="modal-title" id="myModalLabel" align="center">Copiar Información de Silabo</h4>
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
                                                                    <form role="form" action="operaciones/copiar_informacion_silabo.php" method="POST" class="form-horizontal form-label-left input_mask">
                                                                        <input type="hidden" name="myidactual" value="<?php echo $id_prog; ?>">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Copiar Silabo de : </label>
                                                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                <select class="form-control" id="silabo_copi" name="silabo_copi" value="" required="required">
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
                                                                                            $ejec_busc_modulo = buscarModuloFormativoById($conexion, $id_modulo);
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
                                            <br>

                                            <form role="form" action="operaciones/actualizar_silabo.php" class="form-horizontal form-label-left input_mask" method="POST">
                                                <input type="hidden" name="id_prog" value="<?php echo $id_prog; ?>">
                                                <input type="hidden" name="id_silabo" value="<?php echo $id_silabo; ?>">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered jambo_table bulk_action">
                                                        <thead>
                                                            <tr>

                                                                <th colspan="2">
                                                                    <center>INFORMACIÓN GENERAL</center>
                                                                </th>

                                                            </tr>
                                                            <tr>

                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td width="30%">Programa de Estudios</td>
                                                                <td>: <?php echo $r_b_pe['nombre']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Módulo Profesional</td>
                                                                <td>: <?php echo $r_b_mod['descripcion']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Coordinador de Área</td>
                                                                <td>:
                                                                    <select name="coordinador" id="coordinador">
                                                                        <option value="0"></option>
                                                                        <?php
                                                                        $b_coordinador = buscarUsuarioCoordinador_sede($conexion,$id_sede_act);
                                                                        while ($r_b_coordinador = mysqli_fetch_array($b_coordinador)) { ?>
                                                                            <option value="<?php echo $r_b_coordinador['id']; ?>" <?php if ($r_b_coordinador['id'] == $r_b_silabo['id_coordinador']) {
                                                                                                                                        echo "selected";
                                                                                                                                    } ?>><?php echo $r_b_coordinador['apellidos_nombres']; ?></option>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                            </tr>
                                                            <tr>
                                                                <td>Unidad Didáctica</td>
                                                                <td>: <?php echo $r_b_ud['nombre']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Créditos</td>
                                                                <td>: <?php echo $r_b_ud['creditos']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Semestre Académico</td>
                                                                <td>: <?php echo $r_b_sem['descripcion']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nro de Horas Semanal</td>
                                                                <td>: <?php echo $r_b_ud['horas'] / 16; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Nro de Horas Semestral</td>
                                                                <td>: <?php echo $r_b_ud['horas']; ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Horario</td>
                                                                <td>:
                                                                    <input type="text" name="horario" class="bootstrap-tagsinput form-control" data-role="tagsinput" placeholder="Agregar+" value="<?php echo $r_b_silabo['horario']; ?>">

                                                                </td>

                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="5">
                                                                    <center>ORGANIZACIÓN DE ACTIVIDADES Y CONTENIDOS BÁSICOS</center>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>
                                                                    <center>Semana</center>
                                                                </th>
                                                                <th>
                                                                    <center>Elemento de Capacidad</center>
                                                                </th>
                                                                <th>
                                                                    <center>Actividades Aprendizaje</center>
                                                                </th>
                                                                <th>
                                                                    <center>Contenidos Básicos</center>
                                                                </th>
                                                                <th>
                                                                    <center>Tareas Previas</center>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            //buscar programacion de actividades del silabo
                                                            $b_act_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
                                                            $cant_actividades = mysqli_num_rows($b_act_silabo);
                                                            while ($r_b_act_silabo = mysqli_fetch_array($b_act_silabo)) {
                                                            ?>
                                                                <tr>
                                                                    <td>
                                                                        <center><?php echo $r_b_act_silabo['semana'] . "<br>"; ?><!--<input type="date" name="fecha_<?php echo $r_b_act_silabo['id']; ?>" value="<?php echo $r_b_act_silabo['fecha']; ?>">-->
                                                                            <?php if ($r_b_act_silabo['semana'] > 16) { ?>
                                                                                <!--<button type="button" class="btn btn-danger">Eliminar</button>-->
                                                                            <?php } ?>
                                                                        </center>
                                                                    </td>
                                                                    <td><textarea name="elemento_<?php echo $r_b_act_silabo['id']; ?>" style="width:100%; resize: none; height:auto;" rows="3"><?php echo $r_b_act_silabo['elemento_capacidad']; ?></textarea></td>
                                                                    <td><textarea name="actividad_<?php echo $r_b_act_silabo['id']; ?>" style="width:100%; resize: none; height:auto;" rows="3"><?php echo $r_b_act_silabo['actividades_aprendizaje']; ?></textarea></td>
                                                                    <td><textarea name="contenidos_<?php echo $r_b_act_silabo['id']; ?>" style="width:100%; resize: none; height:auto;" rows="3"><?php echo $r_b_act_silabo['contenidos_basicos']; ?></textarea></td>
                                                                    <td><textarea name="tareas_<?php echo $r_b_act_silabo['id']; ?>" style="width:100%; resize: none; height:auto;" rows="3"><?php echo $r_b_act_silabo['tareas_previas']; ?></textarea></td>
                                                                </tr>
                                                            <?php
                                                            }
                                                            ?>
                                                            <!--<a title="Agregar Semana" class="btn btn-info" href="operaciones/agregar_semana.php?data=<?php echo $id_prog; ?>" onclick="return confirmaragregar();">Agregar Semana</a>-->
                                                            <input type="hidden" name="cant_actividades" value="<?php echo $cant_actividades; ?>">
                                                        </tbody>

                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th>METODOLOGÍA</th>
                                                            </tr>

                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="metodologia" style="width:100%; resize: none; height:auto;" rows="5"><?php echo $r_b_silabo['metodologia']; ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <thead>
                                                                <tr>
                                                                    <th>RECURSOS DIDÁCTICOS</th>
                                                                </tr>
                                                            </thead>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="recursos_didacticos" style="width:100%; resize: none; height:auto;" rows="5"><?php echo $r_b_silabo['recursos_didacticos']; ?></textarea>
                                                                </td>
                                                            </tr>
                                                            <thead>
                                                                <tr>
                                                                    <th>SISTEMA DE EVALUACIÓN</th>
                                                                </tr>
                                                            </thead>
                                                            <tr>
                                                                <td>
                                                                    <div class="control-group">
                                                                        <div class="col-md-12 col-sm-12 col-xs-12">
                                                                            <textarea name="sistema_evaluacion" id="sistema_evaluacion" rows="8" style="width:100%; resize: none; height:auto;"><?php echo $r_b_silabo['sistema_evaluacion']; ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered jambo_table bulk_action">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="2" width="100%">
                                                                    <center>ESTRATEGÍAS DE EVALUACIÓN</center>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th width="50%">
                                                                    <center>INDICADORES</center>
                                                                </th>
                                                                <th width="50%">
                                                                    <center>TÉCNICAS (Instrumentos)</center>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="indicadores_estrategias" style="width:100%; resize: none; height:auto;" rows="8"><?php echo $r_b_silabo['estrategia_evaluacion_indicadores']; ?></textarea>

                                                                </td>
                                                                <td>
                                                                    <textarea name="tecnicas_estrategias" style="width:100%; resize: none; height:auto;" rows="8"><?php echo $r_b_silabo['estrategia_evaluacion_tecnica']; ?></textarea>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered jambo_table bulk_action">
                                                        <tbody>
                                                            <thead>
                                                                <tr>
                                                                    <th>RECURSOS BIBLIOGRAFICOS - Impresos</th>
                                                                </tr>
                                                            </thead>

                                                            <tr>
                                                                <td>
                                                                    <textarea name="recursos_bib_imp" style="width:100%; resize: none; height:auto;" rows="5"><?php echo $r_b_silabo['recursos_bibliograficos_impresos']; ?></textarea>

                                                            </tr>
                                                            <thead>
                                                                <tr>
                                                                    <th>RECURSOS BIBLIOGRAFICOS - Digitales</th>
                                                                </tr>
                                                            </thead>
                                                            <tr>
                                                                <td>
                                                                    <textarea name="recursos_bib_digi" style="width:100%; resize: none; height:auto;" rows="5"><?php echo $r_b_silabo['recursos_bibliograficos_digitales']; ?></textarea>

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
