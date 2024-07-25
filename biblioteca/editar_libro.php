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
    <a href='admin/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../biblioteca/');
              </script>";
        } else {

            $id_libro = base64_decode($_GET['data']);
            $b_libro = buscar_libroById($conexion, $id_libro);
            $r_b_libro = mysqli_fetch_array($b_libro);

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

                <!-- App css -->
                <link href="../plantilla/biblioteca/plugins/dropify/dropify.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
                <link href="../plantilla/biblioteca/assets/css/theme.min.css" rel="stylesheet" type="text/css" />
                <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
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
                                    <div class="col-xl-12 col-md-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="mb-4">
                                                    <h5 class="card-title mb-0">Editar datos de libro</h5>
                                                    <br>
                                                    <a href="libros" class="btn btn-danger">Regresar</a>
                                                </div>
                                                <form role="form" action="operaciones/actualizar_libro.php" method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="data" id="libro_m" value="<?php echo $r_b_libro['id']; ?>">
                                                    <input type="hidden" name="ud" id="id_ud_m" value="<?php echo $r_b_libro['id_unidad_didactica']; ?>">
                                                    <input type="hidden" name="paginas" value="<?php echo $r_b_libro['paginas']; ?>">
                                                    <input type="hidden" name="link_portada" value="<?php echo $r_b_libro['portada']; ?>">
                                                    <input type="hidden" name="link_libro" value="<?php echo $r_b_libro['libro']; ?>">
                                                    <div class="form-row">
                                                        <div class="col-md-12 mb-3">
                                                            <label>Programa de Estudios :</label>
                                                            <select name="id_programa" id="id_programa_m" class="form-control" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $b_carreras = buscarProgramaEstudio($conexion);
                                                                while ($r_b_carreras = mysqli_fetch_array($b_carreras)) { ?>
                                                                    <option value="<?php echo $r_b_carreras['id']; ?>" <?php if ($r_b_carreras['id'] == $r_b_libro['id_programa_estudio']) {
                                                                                                                            echo "selected";
                                                                                                                        } ?>><?php echo $r_b_carreras['nombre']; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Semestre :</label>
                                                            <select name="id_semestre" id="id_semestre_m" class="form-control" required>
                                                                <option value=""></option>
                                                                <?php
                                                                $id_pe = $r_b_libro['id_programa_estudio'];
                                                                $b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
                                                                while ($rb_mf = mysqli_fetch_array($b_mf)) {
                                                                    $id_mf = $rb_mf['id'];

                                                                    $b_semestre = buscarSemestreByIdModulo_Formativo($conexion, $id_mf);
                                                                    while ($r_b_semestre = mysqli_fetch_array($b_semestre)) { ?>
                                                                        <option value="<?php echo $r_b_semestre['id']; ?>" <?php if ($r_b_semestre['id'] == $r_b_libro['id_semestre']) {
                                                                                                                                echo "selected";
                                                                                                                            } ?>><?php echo $r_b_semestre['descripcion']; ?></option>
                                                                <?php }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Unidad Didáctica :</label>
                                                            <select name="id_unidad_didactica" id="id_unidad_didactica_m" class="form-control" required value="<?php echo $r_b_libro['id_unidad_didactica']; ?>">
                                                                <option value=""></option>
                                                                <?php
                                                                $b_ud = buscarUnidadDidacticaByIdSemestre($conexion, $r_b_libro['id_semestre']);
                                                                while ($rb_ud = mysqli_fetch_array($b_ud)) {
                                                                ?>
                                                                    <option value="<?php echo $rb_ud['id']; ?>" <?php if ($rb_ud['id'] == $r_b_libro['id_unidad_didactica']) {
                                                                                                                    echo "selected";
                                                                                                                } ?>><?php echo $rb_ud['nombre']; ?></option>
                                                                <?php
                                                                }

                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Título del Libro :</label>
                                                            <input type="text" class="form-control" name="titulo" required value="<?php echo $r_b_libro['titulo']; ?>">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Autor :</label>
                                                            <input type="text" class="form-control" name="autor" required value="<?php echo $r_b_libro['autor']; ?>">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Editorial :</label>
                                                            <input type="text" class="form-control" name="editorial" required value="<?php echo $r_b_libro['editorial']; ?>" placeholder="Opcional">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Año de Edicion :</label>
                                                            <input type="text" class="form-control" name="edicion" value="<?php echo $r_b_libro['edicion']; ?>" placeholder="Opcional">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label>Tomo :</label>
                                                            <input type="text" class="form-control" name="tomo" value="<?php echo $r_b_libro['tomo']; ?>" placeholder="Opcional">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Categoria :</label>
                                                            <input type="text" class="form-control" name="categoria" required value="<?php echo $r_b_libro['tipo_libro']; ?>">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>ISBN :</label>
                                                            <input type="text" class="form-control" name="isbn" value="<?php echo $r_b_libro['isbn']; ?>" placeholder="Opcional">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Temas Relacionados :</label>
                                                            <textarea class="form-control" name="temas_relacionados" id="" cols="30" rows="10" style="resize: none;" required><?php echo $r_b_libro['temas_relacionados']; ?></textarea>
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Archivo :</label>
                                                            <input type="file" name="archivo" accept=".pdf" class="form-control">
                                                        </div>
                                                        <div class="col-md-12 mb-3">
                                                            <label>Portada :</label>
                                                            <input type="file" name="portada" accept="image/*" class="form-control">
                                                        </div>
                                                    </div>
                                                    <center><a href="libros" class="btn btn-danger" style="margin-right: 50px;">Cancelar</a>
                                                        <button class="btn btn-primary waves-effect waves-light" type="submit">Actualizar</button>
                                                    </center>
                                                </form>
                                            </div>
                                            <!--end card body-->
                                        </div><!-- end card-->
                                    </div> <!-- end col-->

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

                <script src="../plantilla/biblioteca/assets/pages/validation-demo.js"></script>

                <script src="../plantilla/biblioteca/plugins/dropify/dropify.min.js"></script>

                <!-- Init js-->
                <script src="../plantilla/biblioteca/assets/pages/fileuploads-demo.js"></script>
                <!-- App js -->
                <script src="../plantilla/biblioteca/assets/js/theme.js"></script>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#id_programa_m').change(function() {
                            cargarsemestres();
                        });
                        $('#id_semestre_m').change(function() {
                            recargar_ud();
                        });

                    })
                </script>
                <script type="text/javascript">
                    function cargarsemestres() {
                        $.ajax({
                            type: "POST",
                            url: "../academico/operaciones/obtener_semestre_pe.php",
                            data: "id=" + $('#id_programa_m').val(),
                            success: function(r) {
                                $('#id_semestre_m').html(r);
                                recargar_ud();
                            }
                        });
                    }
                </script>
                <script type="text/javascript">
                    function recargar_ud() {
                        var sem = $('#id_semestre_m').val();
                        $.ajax({
                            type: "POST",
                            url: "../academico/operaciones/obtener_ud_semestre.php",
                            data: {
                                id_semestre: sem
                            },
                            success: function(r) {
                                $('#id_unidad_didactica_m').html(r);
                            }
                        });
                    }
                </script>
            </body>

            </html>
<?php }
    }
}
