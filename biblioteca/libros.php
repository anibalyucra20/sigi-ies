<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_biblioteca.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BIBLIO');

    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['biblioteca_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BIBLIO');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='admin'>Regresar</a><br>
    <a href='../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../biblioteca/');
              </script>";
        } else {

?>
            <!DOCTYPE html>
            <html lang="es">

            <head>
                <meta charset="utf-8" />
                <title>Biblioteca <?php include("../include/header_title.php"); ?></title>
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
                        <?php include "include/menu_admin.php"; ?>
                        <div class="page-content">
                            <div class="container-fluid">
                                <!-- start page title -->
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="card">
                                            <div class="card-body">

                                                <h4 class="card-title">Relación de Libros</h4>
                                                <a href="registro_libro" class="btn btn-success">Nuevo <i class="fas fa-plus-square"></i></a><br><br>
                                                <table id="example" class="table dt-responsive " width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>Nro</th>
                                                            <th>Imagen</th>
                                                            <th>Titulo</th>
                                                            <th>Autor</th>
                                                            <th>Programa de Estudios</th>
                                                            <th>Semestre</th>
                                                            <th>Unidad Didáctica</th>
                                                            <th>Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $b_libros = buscar_libro($conexion);
                                                        $cont = 0;
                                                        while ($r_b_libro = mysqli_fetch_array($b_libros)) {
                                                            $cont++;
                                                        ?>
                                                            <tr>
                                                                <td><?php echo $cont; ?></td>
                                                                <td><img src="portadas/<?php echo $r_b_libro['portada']; ?>" alt="" style="width: 80px;"></td>
                                                                <td><?php echo $r_b_libro['titulo']; ?></td>
                                                                <td><?php echo $r_b_libro['autor']; ?></td>
                                                                <td><?php
                                                                    $b_programa = buscarProgramaEstudioById($conexion, $r_b_libro['id_programa_estudio']);
                                                                    $r_b_programa = mysqli_fetch_array($b_programa);
                                                                    echo $r_b_programa['nombre'];
                                                                    ?></td>
                                                                <td><?php
                                                                    $b_semestre = buscarSemestreById($conexion, $r_b_libro['id_semestre']);
                                                                    $r_b_semestre = mysqli_fetch_array($b_semestre);
                                                                    echo $r_b_semestre['descripcion'];
                                                                    ?></td>
                                                                <td><?php
                                                                    $b_ud = buscarUnidadDidacticaById($conexion, $r_b_libro['id_unidad_didactica']);
                                                                    $r_b_ud = mysqli_fetch_array($b_ud);
                                                                    echo $r_b_ud['nombre'];
                                                                    ?></td>
                                                                <td>
                                                                    <a href="editar_libro?data=<?php echo base64_encode($r_b_libro['id']); ?>" class="btn btn-success">Editar</a>

                                                                    <button type="button" class="btn btn-primary">Ver</button>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
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
            </body>

            </html>

<?php }
    }
}
