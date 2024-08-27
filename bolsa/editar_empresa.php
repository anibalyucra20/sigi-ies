<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_bolsa.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BOLSA');

    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['bolsa_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BOLSA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='admin'>Regresar</a><br>
    <a href='../include/cerrar_sesion_bolsa.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('admin');
              </script>";
        } else {
            $id = base64_decode($_REQUEST['data']);
            $b_empresa = buscar_empresa($conexion,$id);
            $rb_empresa = mysqli_fetch_array($b_empresa);

            $id_usu = $rb_empresa['id_usuario'];
            $b_usu = buscarUsuarioById($conexion, $id_usu);
            $rb_usu = mysqli_fetch_array($b_usu);

?>
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="utf-8" />
                <title>Bolsa <?php include("../include/header_title.php"); ?></title>
                <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
                <meta http-equiv="X-UA-Compatible" content="IE=edge" />

                <!-- App favicon -->
                <link rel="shortcut icon" href="../images/favicon.ico">

                <!-- Plugins css -->
                <link href="../plantilla/biblioteca/plugins/datatables/dataTables.bootstrap4.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/plugins/datatables/responsive.bootstrap4.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/plugins/datatables/buttons.bootstrap4.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/plugins/datatables/select.bootstrap4.css" rel="stylesheet" type="text/css" />

                <!-- App css -->
                <link href="../plantilla/biblioteca/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
            </head>

            <body>
                <!-- Begin page -->
                <div id="layout-wrapper">
                    <div class="main-content">
                        <?php include "include/menu.php"; ?>
                        <div class="page-content">
                            <div class="container-fluid">
                                <!-- start page title -->
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card">
                                            <div class="card-body">

                                                <h4 class="card-title">Editar datos de Empresas</h4>
                                                <form action="operaciones/actualizar_empresa" id="myform" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="data" value="<?php echo $_REQUEST['data']; ?>">
                                                    <div class="form-row">
                                                        <div class="col-md-12 mb-3">
                                                            <label>Ruc :</label>
                                                            <input type="number" class="form-control" name="ruc" value="<?php echo $rb_empresa['ruc']; ?>" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Razón Social :</label>
                                                            <input type="text" class="form-control" name="empresa" value="<?php echo $rb_empresa['empresa']; ?>" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Dirección :</label>
                                                            <input type="text" class="form-control" name="direccion" value="<?php echo $rb_empresa['direccion']; ?>" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Teléfono :</label>
                                                            <input type="number" class="form-control" name="telefono" value="<?php echo $rb_empresa['telefono']; ?>" required>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Correo :</label>
                                                            <input type="email" class="form-control" name="email" value="<?php echo $rb_empresa['email']; ?>" required>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Responsable :</label>
                                                            <input type="number" class="form-control" name="dni_est" placeholder="DNI" value="<?php echo $rb_usu['dni']; ?>">
                                                            <input type="hidden" class="form-control" name="user" id="user" value="<?php echo $rb_usu['id']; ?>">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label> </label><br>
                                                            <button type="button" class="btn btn-outline-info waves-effect waves-light" onclick="cargarusu();"><i class="fa fa-search"></i> Buscar</button>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <input type="text" class="form-control" id="usuario" readonly value="<?php echo $rb_usu['apellidos_nombres']; ?>">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Estado :</label>
                                                            <select name="estado" id="estado" class="form-control col-md-4 col-sm-12" required>
                                                                <option value="0" <?php if($rb_empresa['estado']==0){echo "selected";} ?>>Suspendido</option>
                                                                <option value="1" <?php if($rb_empresa['estado']==1){echo "selected";} ?>>Activo</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <a href="empresas" class="btn btn-light">Cancelar</a>
                                                        <button type="submit" class="btn btn-primary">Actualizar</button>
                                                    </div>
                                                </form>
                                                <br><br>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end page title -->
                            </div> <!-- container-fluid -->
                        </div>
                        <!-- End Page-content -->

                        <?php include "../include/footer.php"; ?>

                    </div>
                    <!-- end main content-->

                </div>
                <!-- END layout-wrapper -->

                <!-- jQuery  -->
                <script src="../plantilla/biblioteca/assets/js/jquery.min.js"></script>
                <script src="../plantilla/biblioteca/assets/js/bootstrap.bundle.min.js"></script>
                <script src="../plantilla/biblioteca/assets/js/waves.js"></script>
                <script src="../plantilla/biblioteca/assets/js/simplebar.min.js"></script>

                <!-- third party js -->
                <script src="../plantilla/biblioteca/plugins/datatables/jquery.dataTables.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/jquery.dataTables.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/dataTables.bootstrap4.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/dataTables.responsive.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/responsive.bootstrap4.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/dataTables.buttons.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/buttons.bootstrap4.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/buttons.html5.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/buttons.flash.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/buttons.print.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/dataTables.keyTable.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/dataTables.select.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/pdfmake.min.js"></script>
                <script src="../plantilla/biblioteca/plugins/datatables/vfs_fonts.js"></script>
                <!-- third party js ends -->

                <!-- Datatables init -->
                <script src="../plantilla/biblioteca/assets/pages/datatables-demo.js"></script>

                <!-- App js -->
                <script src="../plantilla/biblioteca/assets/js/theme.js"></script>

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
                    function cargarusu() {
                        // funcion para traer datos del estudiante
                        // Creando el objeto para hacer el request
                        var request = new XMLHttpRequest();
                        request.responseType = 'json';
                        // Objeto PHP que consultaremos
                        request.open("POST", "../academico/operaciones/obtener_estudiante.php");
                        // Definiendo el listener
                        request.onreadystatechange = function() {
                            // Revision si fue completada la peticion y si fue exitosa
                            if (this.readyState === 4 && this.status === 200) {
                                // Ingresando la respuesta obtenida del PHP
                                document.getElementById("user").value = this.response.id_est;
                                document.getElementById("usuario").value = this.response.nombre;
                            }
                        };
                        // Recogiendo la data del HTML
                        var myForm = document.getElementById("myform");
                        var formData = new FormData(myForm);
                        // Enviando la data al PHP
                        request.send(formData);
                    }
                </script>
            </body>

            </html>

<?php }
    }
}
