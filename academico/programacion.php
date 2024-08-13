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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 2) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($rb_periodo_act['fecha_fin']);
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

            <title>Programación Unidades Didácticas<?php include("../include/header_title.php"); ?></title>
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
                                            <h2 align="center">Programación Unidades Didácticas</h2>
                                            <?php if ($agregar == 1) {
                                                echo '<button class="btn btn-success" data-toggle="modal" data-target=".registrar"><i class="fa fa-plus-square"></i> Nuevo</button>';
                                            } ?>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />

                                            <table id="example" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th>Identificador</th>
                                                        <th>Programa de Estudios</th>
                                                        <th>Semestre</th>
                                                        <th>Unidad Didáctica</th>
                                                        <th>Turno</th>
                                                        <th>Sección</th>
                                                        <th>Docente</th>
                                                        <?php if ($agregar == 1) {
                                                            echo '<th>Acciones</th>';
                                                        } ?>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $cont = 0;
                                                    $b_pe = buscarProgramaEstudio($conexion);
                                                    while ($rb_pe = mysqli_fetch_array($b_pe)) {
                                                        $id_pe = $rb_pe['id'];
                                                        $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_pe);
                                                        $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
                                                        $id_pe_sede = $rb_pe_sede['id'];

                                                        $ejec_busc_programacion = buscarProgramacionUDByPeriodoSede($conexion, $id_periodo_act, $id_pe_sede);
                                                        while ($res_busc_programacion = mysqli_fetch_array($ejec_busc_programacion)) {
                                                            $data = base64_encode($res_busc_programacion['id']);
                                                            $cont += 1;
                                                    ?>
                                                            <tr>
                                                                <td><?php echo $cont; ?></td>
                                                                <?php
                                                                $id_unidad_didactica = $res_busc_programacion['id_unidad_didactica'];
                                                                $busc_unidad_didactica = buscarUnidadDidacticaById($conexion, $id_unidad_didactica);
                                                                $res_b_unidad_didactica = mysqli_fetch_array($busc_unidad_didactica);

                                                                $id_semestre = $res_b_unidad_didactica['id_semestre'];
                                                                $busc_semestre = buscarSemestreById($conexion, $id_semestre);
                                                                $res_b_semestre = mysqli_fetch_array($busc_semestre);

                                                                $id_modulo_formativo = $res_b_semestre['id_modulo_formativo'];
                                                                $ejec_busc_modulo = buscarModuloFormativoById($conexion, $id_modulo_formativo);
                                                                $res_busc_modulo = mysqli_fetch_array($ejec_busc_modulo);

                                                                $id_programa_estudio = $res_busc_modulo['id_programa_estudio'];
                                                                $b_pprograma_estudio = buscarProgramaEstudioById($conexion, $id_programa_estudio);
                                                                $rb_programa_estudio = mysqli_fetch_array($b_pprograma_estudio);

                                                                $id_docente = $res_busc_programacion['id_docente'];
                                                                $busc_docente = buscarUsuarioById($conexion, $id_docente);
                                                                $res_b_docente = mysqli_fetch_array($busc_docente);

                                                                switch ($res_busc_programacion['turno']) {
                                                                    case 'M':
                                                                        $turno = 'MAÑANA';
                                                                        break;
                                                                    case 'T':
                                                                        $turno = 'TARDE';
                                                                        break;
                                                                    case 'N':
                                                                        $turno = 'NOCHE';
                                                                        break;
                                                                    default:
                                                                        $turno = '';
                                                                        break;
                                                                }

                                                                ?>
                                                                <td><?php echo $rb_programa_estudio['nombre']; ?></td>
                                                                <?php

                                                                ?>
                                                                <td><?php echo $res_b_semestre['descripcion']; ?></td>
                                                                <td><?php echo $res_b_unidad_didactica['nombre']; ?></td>
                                                                <td><?php echo $turno; ?></td>
                                                                <td><?php echo $res_busc_programacion['seccion']; ?></td>
                                                                <td><?php echo $res_b_docente['apellidos_nombres']; ?></td>
                                                                <?php if ($agregar == 1) {
                                                                ?>
                                                                    <td><button class="btn btn-success" data-toggle="modal" data-target=".edit_<?php echo $res_busc_programacion['id']; ?> "><i class="fa fa-pencil-square-o"></i> Editar</button></td>
                                                                <?php } ?>
                                                            </tr>
                                                    <?php
                                                            include('include/acciones_programacion.php');
                                                        }
                                                    }
                                                    ?>

                                                </tbody>
                                            </table>
                                            <?php if ($agregar == 1) {
                                            ?>
                                                <!--MODAL REGISTRAR-->
                                                <div class="modal fade registrar" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">

                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                                </button>
                                                                <h4 class="modal-title" id="myModalLabel" align="center">Registrar Programación de Unidad Didáctica</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!--INICIO CONTENIDO DE MODAL-->
                                                                <div class="x_panel">

                                                                    <div class="" align="center">
                                                                        <h2></h2>
                                                                        <div class="clearfix"></div>
                                                                    </div>
                                                                    <div class="x_content">


                                                                        <div class="col-12">
                                                                            <div class="" role="tabpanel" data-example-id="togglable-tabs">
                                                                                <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                                                                                    <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Registro Individual</a>
                                                                                    </li>
                                                                                    <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Registro Masiva</a>
                                                                                    </li>
                                                                                </ul>
                                                                                <div id="myTabContent" class="tab-content">
                                                                                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                                                                                        <br>
                                                                                        <h4 class="text-center">Registro Individual</h4>
                                                                                        <br>
                                                                                        <!-- formulario de registro de programacion individual-->
                                                                                        <form role="form" action="operaciones/registrar_programacion.php" class="form-horizontal form-label-left input_mask" method="POST">

                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" id="carrera_m" name="carrera_m" value="" required="required">
                                                                                                        <option></option>
                                                                                                        <?php
                                                                                                        $ejec_busc_carr = buscarProgramaEstudio($conexion);
                                                                                                        while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                                                                                                            $id_carr = $res__busc_carr['id'];
                                                                                                            $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_carr);
                                                                                                            $cont_pe_sede = mysqli_num_rows($b_pe_sede);
                                                                                                            $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
                                                                                                            if ($cont_pe_sede > 0) {
                                                                                                                $carr = $res__busc_carr['nombre'];
                                                                                                        ?>
                                                                                                                <option value="<?php echo $id_carr;
                                                                                                                                ?>"><?php echo $carr; ?></option>
                                                                                                        <?php
                                                                                                            }
                                                                                                        }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" id="semestre" name="semestre" value="" required="required">

                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Unidad Didáctica : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" id="unidad_didactica" name="unidad_didactica" value="" required="required">
                                                                                                        <!--las opciones se cargan con ajax y javascript  dependiendo de la carrera elegida,verificar en la parte final-->
                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" name="turno" value="" required="required">
                                                                                                        <option value=""></option>
                                                                                                        <option value="M">Mañana</option>
                                                                                                        <option value="T">Tarde</option>
                                                                                                        <option value="N">Noche</option>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" name="seccion" value="" required="required">
                                                                                                        <option value=""></option>
                                                                                                        <?php
                                                                                                        for ($i = 65; $i <= 90; $i++) {
                                                                                                            $letter = chr($i);
                                                                                                            echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                                                        }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Docente : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" id="docente" name="docente" value="" required="required">
                                                                                                        <option></option>
                                                                                                        <?php
                                                                                                        $ejec_busc_doc = buscarUsuarioDocentesOrderByApellidosNombresAndSede($conexion, $id_sede_act);
                                                                                                        while ($res__busc_docente = mysqli_fetch_array($ejec_busc_doc)) {
                                                                                                            $id_doc = $res__busc_docente['id'];
                                                                                                            $doc = $res__busc_docente['apellidos_nombres'];
                                                                                                        ?>
                                                                                                            <option value="<?php echo $id_doc;
                                                                                                                            ?>"><?php echo $doc; ?></option>
                                                                                                        <?php
                                                                                                        }
                                                                                                        ?>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div align="center">
                                                                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                                                                                                <button type="submit" class="btn btn-primary">Guardar</button>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>

                                                                                    <!-- formulario de registro de programación masiva-->
                                                                                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                                                                                        <br>
                                                                                        <h4 class="text-center">Registro Masivo</h4>
                                                                                        <br>
                                                                                        <form role="form" action="operaciones/registrar_programacion_masiva.php" class="form-horizontal form-label-left input_mask" method="POST">
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Unidades Didácticas a Programar :
                                                                                                </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <?php
                                                                                                    $ejec_busc_carr = buscarProgramaEstudio($conexion);
                                                                                                    while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                                                                                                        $id_carr = $res__busc_carr['id'];
                                                                                                        $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_carr);
                                                                                                        $cont_pe_sede = mysqli_num_rows($b_pe_sede);
                                                                                                        $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
                                                                                                        if ($cont_pe_sede > 0) {
                                                                                                            $carr = $res__busc_carr['nombre'];
                                                                                                    ?>
                                                                                                            <div class="checkbox">
                                                                                                                <label>
                                                                                                                    <input type="checkbox" class="flat" name="pe_<?php echo $rb_pe_sede['id']; ?>"> <?php echo $carr;  ?>
                                                                                                                </label>
                                                                                                            </div>
                                                                                                    <?php }
                                                                                                    } ?>
                                                                                                    <br><br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" name="turno" value="" required="required">
                                                                                                        <option value=""></option>
                                                                                                        <option value="M">Mañana</option>
                                                                                                        <option value="T">Tarde</option>
                                                                                                        <option value="N">Noche</option>
                                                                                                    </select>
                                                                                                    <br>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                                                                    <select class="form-control" name="seccion" value="" required="required">
                                                                                                        <option value=""></option>
                                                                                                        <?php
                                                                                                        for ($i = 65; $i <= 90; $i++) {
                                                                                                            $letter = chr($i);
                                                                                                            echo '<option value="' . $letter . '">' . $letter . '</option>';
                                                                                                        }
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
                                                                            </div>

                                                                        </div>



                                                                        <br />

                                                                    </div>
                                                                </div>
                                                                <!--FIN DE CONTENIDO DE MODAL-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php
                                            } ?>

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
                        cargarsemestres();
                    });
                    $('#semestre').change(function() {
                        recargar_ud();
                    });

                })
            </script>
            <script type="text/javascript">
                function cargarsemestres() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_semestre_pe.php",
                        data: "id=" + $('#carrera_m').val(),
                        success: function(r) {
                            $('#semestre').html(r);
                            listar_uds();

                        }
                    });
                }
            </script>
            <script type="text/javascript">
                function recargar_ud() {
                    var sem = $('#semestre').val();
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_ud_semestre.php",
                        data: {
                            id_semestre: sem
                        },
                        success: function(r) {
                            $('#unidad_didactica').html(r);
                        }
                    });
                }
            </script>

            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php
    }
}
