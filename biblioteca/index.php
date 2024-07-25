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
    <a href='../biblioteca/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <title>Biblioteca <?php include("../include/header_title.php"); ?></title>
            <?php include "include/header.php"; ?>
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
                                <div class="col-12">
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">Últimos Libros Leídos <i class="fas fa-book-open"></i> </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                $b_lecturas = buscar_4lecturas_invert($conexion, $id_usuario, $tipo_usuario);
                                while ($r_b_lecturas = mysqli_fetch_array($b_lecturas)) {
                                    $b_libro = buscar_libroById($conexion, $r_b_lecturas['id_libro']);
                                    $r_b_libro = mysqli_fetch_array($b_libro);

                                    $b_programa = buscarProgramaEstudioById($conexion_sispa, $r_b_libro['id_programa_estudio']);
                                    $r_b_programa = mysqli_fetch_array($b_programa);

                                    $b_semestre = buscarSemestreById($conexion_sispa, $r_b_libro['id_semestre']);
                                    $r_b_semestre = mysqli_fetch_array($b_semestre);

                                    $b_ud = buscarUnidadDidacticaById($conexion_sispa, $r_b_libro['id_unidad_didactica']);
                                    $r_b_ud = mysqli_fetch_array($b_ud);
                                ?>
                                    <div class="card col-lg-3 col-md-3 col-sm-6 mb-2">
                                        <!--<img class=" my-2" src="https://drive.google.com/uc?export=view&id=1VRIuTHH5N3wgecP4o8wvB6YyEXumNECv" width="100%" height="500px">https://drive.google.com/file/d/1l_OW56Qc7h_X1ekH83yI7MWo1_Pw9Qcl/view?usp=drive_link-->
                                        <a href="detalle.php?libro=<?php echo $r_b_libro['link_portada']; ?>">
                                            <iframe src="https://drive.google.com/file/d/<?php echo $r_b_libro['link_portada']; ?>/preview" frameborder="none" style="width:100%; height:500px; overflow: hidden;" scrolling="no"></iframe>
                                        </a>
                                        <div class="card-body">
                                            <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $r_b_libro['titulo']; ?></h5>
                                            <p class="card-text"><?php echo $r_b_programa['nombre'] . ' - S-' . $r_b_semestre['descripcion']; ?></p>
                                            <p class="card-text"><?php echo $r_b_ud['descripcion']; ?></p>
                                            <p class="card-text">Autor: <?php echo $r_b_libro['autor']; ?></p>
                                            <center><a href="detalle.php?libro=<?php echo $r_b_libro['link_portada']; ?>" class="btn btn-info">Ver</a></center>

                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">Mis Libros Favoritos <i class="fas fa-heart"></i> </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                $b_4ultimos_favoritos = buscar_4ultimos_favoritos($conexion, $id_usuario, $tipo_usuario);
                                while ($r_b_favoritos = mysqli_fetch_array($b_4ultimos_favoritos)) {
                                    $b_libro = buscar_libroById($conexion, $r_b_favoritos['id_libro']);
                                    $r_b_libro = mysqli_fetch_array($b_libro);

                                    $b_programa = buscarProgramaEstudioById($conexion_sispa, $r_b_libro['id_programa_estudio']);
                                    $r_b_programa = mysqli_fetch_array($b_programa);

                                    $b_semestre = buscarSemestreById($conexion_sispa, $r_b_libro['id_semestre']);
                                    $r_b_semestre = mysqli_fetch_array($b_semestre);

                                    $b_ud = buscarUnidadDidacticaById($conexion_sispa, $r_b_libro['id_unidad_didactica']);
                                    $r_b_ud = mysqli_fetch_array($b_ud);
                                ?>
                                    <div class="card col-lg-3 col-md-3 col-sm-6 mb-2">
                                        <iframe src="https://drive.google.com/file/d/<?php echo $r_b_libro['link_portada']; ?>/preview" frameborder="none" style="width:100%; height:500px; overflow: hidden;" scrolling="no"></iframe>
                                        <div class="card-body">
                                            <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $r_b_libro['titulo']; ?></h5>
                                            <p class="card-text"><?php echo $r_b_programa['nombre'] . ' - S-' . $r_b_semestre['descripcion']; ?></p>
                                            <p class="card-text"><?php echo $r_b_ud['descripcion']; ?></p>
                                            <p class="card-text">Autor: <?php echo $r_b_libro['autor']; ?></p>
                                            <center><a href="detalle.php?libro=<?php echo $r_b_libro['link_portada'] ?>" class="btn btn-info">Ver</a></center>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <!-- end page title -->


                        </div> <!-- container-fluid -->
                    </div>
                    <!-- End Page-content -->

                    <?php include "include/footer.php"; ?>

                </div>
                <!-- end main content-->

            </div>
            <!-- END layout-wrapper -->


            <?php include "include/pie_scripts.php"; ?>

        </body>

        </html>

<?php
    }
}
