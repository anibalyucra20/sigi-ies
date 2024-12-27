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
    <a href='../bolsa/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_bolsa.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

        $hoy = date("Y-m-d");

        $id_oferta = base64_decode($_REQUEST['oferta']);
        $b_oferta = buscar_oferta($conexion, $id_oferta);
        $rb_oferta = mysqli_fetch_array($b_oferta);

        $b_empresa = buscar_empresa($conexion, $rb_oferta['id_empresa']);
        $rb_empresa = mysqli_fetch_array($b_empresa);

        $b_programa = buscarProgramaEstudioById($conexion, $rb_oferta['programa_estudio']);
        $r_b_programa = mysqli_fetch_array($b_programa);
?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <title>Bolsa <?php include("../include/header_title.php"); ?></title>
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
                                    <div class="page-title-box d-flex align-items-center justify-content-between ">
                                        <h4 class="mb-0 font-size-18 ">Detalle de Oferta Laboral</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">

                                                <div class="col-md-8 mb-3">
                                                    <h4 class="text-center"><?php echo $rb_oferta['titulo']; ?></h4>
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p><b>Detalle</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p class="text-justify">: <?php echo $rb_oferta['detalle']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Empresa</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p class="text-justify">: <?php echo $rb_empresa['empresa']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Requisitos</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p class="text-justify">: <?php echo $rb_oferta['requisitos']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Lugar</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $rb_oferta['ubicacion']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Salario</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $rb_oferta['salario']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Tipo de contrato</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $rb_oferta['tipo_contrato']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Programa de Estudio</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $r_b_programa['nombre']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Fecha de Publicación</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $rb_oferta['fecha_publicacion']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Fecha de Cierre</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <p>: <?php echo $rb_oferta['fecha_cierre']; ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p><b>Estado</b></p>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <?php
                                                            switch ($rb_oferta['estado']) {
                                                                case '1':
                                                                    echo "<p>: Activo</p>";
                                                                    break;
                                                                case '2':
                                                                    echo "<p>: <font color='red'>Finalizado</font></p>";
                                                                    break;

                                                                default:
                                                                    # code...
                                                                    break;
                                                            } ?>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <center>
                                                        <?php
                                                        $b_favorito = buscar_favoritosByidLibroUsu($conexion, $r_b_libro['id'], $id_usuario);
                                                        $cont = mysqli_num_rows($b_favorito);
                                                        if ($cont > 0) {
                                                            $color = "danger";
                                                            $texto = "Quitar de Favoritos";
                                                        } else {
                                                            $color = "dark";
                                                            $texto = "Agregar a Favoritos";
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-md-6" id="mostrar_noti">
                                                                <button type="button" class="btn btn-outline-<?php echo $color; ?> waves-effect waves-light" onclick="agregar_favorito();"> <?php echo $texto; ?> <i class="fas fa-heart"></i></button>
                                                            </div>
                                                            <input type="hidden" id="librodd" value="<?php echo $link_libro; ?>">
                                                            <a href="lectura?libro=<?php echo base64_encode($link_libro); ?>" class="btn btn-success">Postular</a>
                                                        </div>
                                                    </center>
                                                    <img src="portadas/<?php echo $r_b_libro['portada'];  ?>" alt="" style="width:100%; height:500px; margin-top:5px;">
                                                    <br>
                                                    <br>


                                                </div>
                                                <a href="javascript: history.go(-1)" class="btn btn-danger">Regresar</a>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end page title -->

                        </div>
                        <!-- End Page-content -->

                        <?php include "include/footer.php"; ?>

                    </div>
                    <!-- end main content-->

                </div>
                <!-- END layout-wrapper -->


                <?php include "include/pie_scripts.php"; ?>
                <script>
                    const uno = document.getElementById('uno');
                    const dos = document.getElementById('dos');
                    const label1 = document.getElementById('valUno');
                    const label2 = document.getElementById('valDos');
                    uno.addEventListener('input', () => {
                        //el atributo value es un string, el signo "+" transforma en number
                        let val = +uno.value;
                        let val2 = +dos.value;
                        if ((val + 100) >= val2) {
                            dos.value = val + 100;
                        }
                        label1.innerHTML = val;
                        label2.innerHTML = val2;
                    });
                    dos.addEventListener('input', () => {
                        //el atributo value es un string, el signo "+" transforma en number
                        let val = +uno.value;
                        let val2 = +dos.value;
                        if ((val2 - 100) <= val) {
                            uno.value = val2 - 100;
                        }
                        label1.innerHTML = val;
                        label2.innerHTML = val2;
                    });
                </script>
        </body>


        </html>

<?php
    }
}
