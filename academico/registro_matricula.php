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
?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <meta http-equiv="Content-Language" content="es-ES">
            <!-- Meta, title, CSS, favicons, etc. -->
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>Matrícula<?php include("../include/header_title.php"); ?></title>
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



                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h2>Datos de Matrícula</h2>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />
                                            <form role="form" id="myform" action="operaciones/registrar_matricula.php" class="form-horizontal form-label-left input_mask" method="POST">

                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI estudiante: </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <input class="form-control" type="number" name="dni_est" id="dni_est">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12"></label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <button type="button" class="btn btn-success" onclick="recargarest();">Buscar</button>

                                                        <br>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Estudiante : </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <input type="hidden" id="id_est" name="id_est">
                                                        <input class="form-control" type="text" name="estudiante" id="estudiante" readonly>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <select class="form-control" id="carrera_m" name="carrera_m" value="" required="required">
                                                            <option></option>
                                                            <!-- datos a traer segun los datos del estudiante -->
                                                        </select>
                                                        <br>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <select class="form-control" id="semestre" name="semestre" value="" required="required">
                                                            <option></option>
                                                        </select>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Turno : </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <select class="form-control" name="turno" id="turno" value="" required="required">
                                                            <option value=""></option>
                                                            <option value="M">MAÑANA</option>
                                                            <option value="T">TARDE</option>
                                                            <option value="N">NOCHES</option>
                                                        </select>
                                                        <br>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Sección : </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                                        <select class="form-control" name="seccion" id="seccion" value="" required="required">
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
                                                    <input type="hidden" id="arr_uds" name="arr_uds">
                                                    <input type="hidden" id="mat_relacion" name="mat_relacion" required>
                                                    <label class="col-md-3 col-sm-3 col-xs-12 control-label">Seleccione las unidades didáticas :
                                                    </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12" id="udss">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" id="all_check"><b> SELECCIONAR TODAS LAS UNIDADES DIDÁCTICAS *</b>
                                                            </label>
                                                        </div>

                                                    </div>
                                                </div>
                                                <div align="center">
                                                    <a href="matriculas.php" class="btn btn-default">Cancelar</a>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="x_panel">
                                        <div class="x_title">
                                            <h2>Unidades Didácticas para Matricula</h2>

                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <br />
                                            <form class="form-horizontal form-label-left">
                                                <div class="form-group">
                                                    <label class="col-md-3 col-sm-3 col-xs-12 control-label">
                                                    </label>
                                                    <div class="col-md-9 col-sm-9 col-xs-12" id="uds_selec">
                                                        Aún no hay Unidades Didácticas Agregadas para la Matrícula
                                                    </div>
                                                </div>



                                                <div class="ln_solid"></div>

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
            <!--script para obtener los datos dependiendo del dni-->
            <script type="text/javascript">
                $(document).ready(function() {
                    $('#carrera_m').change(function() {
                        cargarsemestres();
                    });
                    $('#semestre').change(function() {
                        listar_uds();
                    });
                    $('#turno').change(function() {
                        listar_uds();
                    });
                    $('#seccion').change(function() {
                        listar_uds();
                    });
                })
            </script>
            <script type="text/javascript">
                function recargarest() {
                    // funcion para traer datos del estudiante
                    // Creando el objeto para hacer el request
                    var request = new XMLHttpRequest();
                    request.responseType = 'json';
                    // Objeto PHP que consultaremos
                    request.open("POST", "operaciones/obtener_estudiante.php");
                    // Definiendo el listener
                    request.onreadystatechange = function() {
                        // Revision si fue completada la peticion y si fue exitosa
                        if (this.readyState === 4 && this.status === 200) {
                            // Ingresando la respuesta obtenida del PHP
                            document.getElementById("id_est").value = this.response.id_est;
                            document.getElementById("estudiante").value = this.response.nombre;
                            cargarpe();

                        }
                    };
                    // Recogiendo la data del HTML
                    var myForm = document.getElementById("myform");
                    var formData = new FormData(myForm);
                    // Enviando la data al PHP
                    request.send(formData);
                }
            </script>
            <script type="text/javascript">
                function cargarpe() {
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_pe_matricula.php",
                        data: "id=" + $('#id_est').val(),
                        success: function(r) {
                            $('#carrera_m').html(r);
                            listar_uds();

                        }
                    });
                }
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
                function listar_uds() {
                    var carr = $('#carrera_m').val();
                    var sem = $('#semestre').val();
                    var turno = $('#turno').val();
                    var seccion = $('#seccion').val();
                    $.ajax({
                        type: "POST",
                        url: "operaciones/obtener_ud_check.php",
                        data: {
                            id_pe: carr,
                            id_sem: sem,
                            id_turno: turno,
                            id_seccion: seccion
                        },
                        success: function(r) {
                            $('#udss').html(r);
                        }
                    });
                }
            </script>
            <script type="text/javascript">
                unidades_didac = [];

                function gen_arr_uds() {

                    $("input[name='unidades_didacticas']:checked").each(function() {
                        //Mediante la función push agregamos al arreglo los values de los checkbox

                        unidades_didac.push(($(this).attr("value")));
                        unidades_didac = [...new Set(unidades_didac)];
                    });
                    // Utilizamos console.log para ver comprobar que en realidad contiene algo el arreglo
                    document.getElementById("arr_uds").value = unidades_didac;
                    listar_ud_mat();
                    setTimeout(gen_arr_mat, 300);

                }
            </script>

            <script type="text/javascript">
                function listar_ud_mat() {
                    // elimaremos las unidades didacticas para insertar segun el array
                    var div_d = document.getElementById('uds_selec');
                    while (div_d.firstChild) {
                        div_d.removeChild(div_d.firstChild);
                    }
                    // enviamos arra para cargar unidades didacticas a matricular
                    $.ajax({
                        type: "POST",
                        url: "operaciones/listar_ud_matricula.php",
                        data: {
                            datos: unidades_didac
                        },
                        success: function(r) {
                            $('#uds_selec').html(r);
                        }
                    })
                    setTimeout(gen_arr_mat, 300);

                };
            </script>
            <script type="text/javascript">
                function gen_arr_mat() {
                    var unidades_didac_mat = [];
                    $("input[name='uds_matri']:checked").each(function() {
                        //Mediante la función push agregamos al arreglo los values de los checkbox
                        unidades_didac_mat.push(($(this).attr("value")));
                    });
                    // Utilizamos console.log para ver comprobar que en realidad contiene algo el arreglo
                    document.getElementById("mat_relacion").value = unidades_didac_mat;
                };
            </script>
            <script type="text/javascript">
                function select_all() {
                    if ($('#all_check').is(':checked')) {
                        $("input[name='unidades_didacticas']").each(function() {
                            $(this).prop("checked", true);
                        });
                    } else {
                        $("input[name='unidades_didacticas']").each(function() {
                            $(this).prop("checked", false);
                        });
                    }
                    gen_arr_uds();
                };
            </script>
            <?php mysqli_close($conexion); ?>
        </body>

        </html>
<?php
    }
}
