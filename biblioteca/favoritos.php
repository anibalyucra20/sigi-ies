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

        //https://www.youtube.com/watch?v=tRUg2fSLRJo
        $b_favoritos = buscar_favoritos($conexion, $id_usuario);
        $ejec = mysqli_query($conexion, $b_favoritos);
        $cont = mysqli_num_rows($b_favoritos);

        if ($cont > 0) {

            $total_lib = $cont;
            $articulos_por_pagina = 8;
            $paginas = ceil($total_lib / $articulos_por_pagina);

            $iniciar = ($_GET['pagina'] - 1) * $articulos_por_pagina;


            if (!isset($_GET['pagina'])) header('location:favoritos?pagina=1');
            if ($_GET['pagina'] > $paginas || $_GET['pagina'] < 1) header('location:favoritos?pagina=1');

            $buscar = "SELECT * FROM biblioteca_libros_favoritos WHERE id_usuario = '$id_usuario' ORDER BY id DESC LIMIT $iniciar, $articulos_por_pagina";
            $ejec_buscar = mysqli_query($conexion, $buscar);


            // INICIO PAGINACION ==================================================================================================
            $paginacion = "";
            $paginacion .= '<li class="page-item';
            if ($_GET['pagina'] == 1) {
                $paginacion .= " disabled";
            }
            $paginacion .= ' "><a class="page-link" href="favoritos?pagina=1">Inicio</a></li>';

            $paginacion .= '<li class="page-item ';
            if ($_GET['pagina'] == 1) {
                $paginacion .= "disabled";
            }
            $paginacion .= '"><a class="page-link" href="favoritos?pagina=';
            $paginacion .= $_GET['pagina'] - 1;
            $paginacion .= '">Anterior</a></li>';



            if ($_GET['pagina'] > 4) {
                $iin = $_GET['pagina'] - 2;
            } else {
                $iin = 1;
            }

            for ($i = $iin; $i <= $paginas; $i++) {
                if (($paginas - 7) > $i) {
                    $n_n = $iin + 5;
                }
                if ($i == $n_n) {
                    $nn = $_GET["pagina"] + 1;
                    $paginacion .= '<li class="page-item"><a class="page-link" href="favoritos?pagina=' . $nn . '">...</a></li>';
                    $i = $paginas - 2;
                }
                $paginacion .= '<li class="page-item ';
                if ($_GET['pagina'] == $i) {
                    $paginacion .= "active";
                }
                $paginacion .= '" ><a class="page-link" href="favoritos?pagina=';
                $paginacion .= $i;
                $paginacion .= ' ">' . $i . '</a></li>';
            }

            $paginacion .= '<li class="page-item ';
            if ($_GET['pagina'] >= $paginas) {
                $paginacion .= "disabled";
            }
            $paginacion .= '"><a class="page-link" href="favoritos?pagina=';
            $paginacion .=  $_GET['pagina'] + 1;
            $paginacion .=  '">Siguiente</a></li>';

            $paginacion .= '<li class="page-item ';
            if ($_GET['pagina'] >= $paginas) {
                $paginacion .= "disabled";
            }
            $paginacion .= '"><a class="page-link" href="favoritos?pagina=' . $paginas . '">Final</a></li>';



            // FIN PAGINACION ===================================================================================================

        }

?>

        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <title>Mis Favoritos - IESTP HUANTA</title>
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
                                        <h4 class="mb-0 font-size-18">Mis Libros Favoritos <i class="fas fa-heart"></i> </h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="container d-flex justify-content-center">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <div class="row">
                                                <?php echo $paginacion; ?>
                                            </div>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                while ($r_b_favoritos = mysqli_fetch_array($ejec_buscar)) {
                                    $b_libro = buscar_libroById($conexion, $r_b_favoritos['id_libro']);
                                    $res_bus_libro = mysqli_fetch_array($b_libro);

                                    $b_programa = buscarProgramaEstudioById($conexion, $res_bus_libro['id_programa_estudio']);
                                    $r_b_programa = mysqli_fetch_array($b_programa);

                                    $b_semestre = buscarSemestreById($conexion, $res_bus_libro['id_semestre']);
                                    $r_b_semestre = mysqli_fetch_array($b_semestre);

                                    $b_ud = buscarUnidadDidacticaById($conexion, $res_bus_libro['id_unidad_didactica']);
                                    $r_b_ud = mysqli_fetch_array($b_ud);
                                ?>
                                    <div class="card col-lg-3 col-md-3 col-sm-6 mb-2">
                                        <img src="portadas/<?php echo $res_bus_libro['portada'];  ?>" alt="" style="width:100%; height:500px; margin-top:5px;">

                                        <div class="card-body">
                                            <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $res_bus_libro['titulo']; ?></h5>
                                            <p class="card-text"><?php echo $r_b_programa['nombre'] . ' - ' . $r_b_semestre['descripcion']; ?></p>
                                            <p class="card-text"><?php echo $r_b_ud['nombre']; ?></p>
                                            <p class="card-text">Autor: <?php echo $res_bus_libro['autor']; ?></p>
                                            <center>
                                                <a href="detalle?libro=<?php echo base64_encode($res_bus_libro['id']); ?>" class="btn btn-info">Ver</a>
                                                <a href="lectura?libro=<?php echo base64_encode($res_bus_libro['id']); ?>" class="btn btn-success">Leer Libro</a>
                                            </center>
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
<?php }
}
