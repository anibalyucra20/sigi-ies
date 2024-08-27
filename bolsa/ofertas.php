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
                                    <div class="page-title-box d-flex align-items-center justify-content-between">
                                        <h4 class="mb-0 font-size-18">Ofertas Laborales <i class="fas fa-book-open"></i> </h4>
                                    </div>
                                    <button type="button" class="btn btn-success waves-effect waves-light" data-toggle="modal" data-target=".nuevo">Nuevo <i class="fas fa-plus-square"></i></button>
                                    <br><br>
                                    <!--    MODAL REGISTRAR -->

                                    <div class="modal fade nuevo" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title h4" id="myLargeModalLabel">Registrar Oferta Laboral</h5>
                                                    <button type="button" class="close waves-effect waves-light" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="operaciones/registrar_oferta" id="myform" method="POST" enctype="multipart/form-data">
                                                        <div class="form-row">
                                                            <div class="col-md-12 mb-3">
                                                                <label>Empresa :</label>
                                                                <select class="form-control" name="empresa" id="empresa">
                                                                    <option value=""></option>
                                                                    <option value=""></option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label>Título :</label>
                                                                <input type="text" class="form-control" name="empresa" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label>Dirección :</label>
                                                                <input type="text" class="form-control" name="direccion" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label>Teléfono :</label>
                                                                <input type="number" class="form-control" name="telefono" required>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <label>Correo :</label>
                                                                <input type="email" class="form-control" name="email" required>
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label>Responsable :</label>
                                                                <input type="number" class="form-control" name="dni_est" placeholder="DNI">
                                                                <input type="hidden" class="form-control" name="user" id="user">
                                                            </div>
                                                            <div class="col-md-6 mb-3">
                                                                <label> </label><br>
                                                                <button type="button" class="btn btn-outline-info waves-effect waves-light" onclick="cargarusu();"><i class="fa fa-search"></i> Buscar</button>
                                                            </div>
                                                            <div class="col-md-12 mb-3">
                                                                <input type="text" class="form-control" id="usuario" readonly>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-primary">Registrar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FIN MODAL REGISTRAR -->
                                    <div class="card">
                                        <div id="accordion" class="custom-accordion mb-4">
                                            <div class="card mb-0">
                                                <div class="card-header" id="headingTwo">
                                                    <h5 class="m-0 font-size-15">
                                                        <a class="collapsed d-block pt-2 pb-2 text-dark" data-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                            Búsqueda <span class="fas fa-search"></span> <span class="float-right"><i class="mdi mdi-chevron-down accordion-arrow"></i></span>
                                                        </a>
                                                    </h5>
                                                </div>
                                                <div id="collapseTwo" class="collapse show" aria-labelledby="headingTwo" data-parent="#accordion">
                                                    <div class="card-body">
                                                        <form action="" method="GET">
                                                            <input type="hidden" name="pagina" value="<?php echo $_GET['pagina']; ?>">
                                                            <div class="form-row">
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Por Titulo:</label>
                                                                    <input type="text" class="form-control" name="titulo" value="<?php echo $titulo; ?>" autocomplete="off">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Por Estado:</label>
                                                                    <select name="estado" id="estado" class="form-control col-md-12 col-sm-12" required>
                                                                        <option value="TODOS">TODOS</option>
                                                                        <option value="Suspendido">Suspendido</option>
                                                                        <option value="Activo">Activo</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Por Empresa:</label>
                                                                    <input type="text" class="form-control" name="empresa" value="<?php echo $autor; ?>" autocomplete="off">
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Por Fecha:</label>
                                                                    <div class="center col-12 row m-0 p-0 ">
                                                                        <div class="col-5 "><input type="date" class="form-control col-md-12 " name="temas" value="<?php echo $temas; ?>"></div>-
                                                                        <div class="col-5 "><input type="date" class="form-control col-md-12 " name="temas" value="<?php echo $hoy; ?>"></div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Por Salario:</label>
                                                                    <div class="center col-12 row m-0 p-0 ">
                                                                        <input class=" col-5" id="uno" type="range" min="0" max="900" step="1" value="0" /> <label id="valUno">0</label>
                                                                        <input class=" col-5" id="dos" type="range" min="100" max="1000" step="1" value="1000" /> <label id="valDos">1000</label>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label>Programa de Estudios</label>
                                                                    <select name="programa_estudio" id="programa_estudio_m" class="form-control" value="<?php echo $programa_estudio; ?>">
                                                                        <option value="TODOS">TODOS</option>
                                                                        <?php $b_carreras = buscarProgramaEstudio($conexion);
                                                                        while ($r_b_carreras = mysqli_fetch_array($b_carreras)) { ?>
                                                                            <option value="<?php echo $r_b_carreras['id']; ?>" <?php if ($programa_estudio == $r_b_carreras['id']) {
                                                                                                                                    echo "selected";
                                                                                                                                } ?>><?php echo $r_b_carreras['nombre']; ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-3 mb-3">
                                                                    <label for=""></label><br>
                                                                    <button class="btn btn-primary waves-effect waves-light" type="submit">Buscar</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <?php
                                $b_lecturas = buscar_4lecturas_invert($conexion, $id_usuario, $tipo_usuario);
                                while ($r_b_lecturas = mysqli_fetch_array($b_lecturas)) {
                                    $b_libro = buscar_libroById($conexion, $r_b_lecturas['id_libro']);
                                    $r_b_libro = mysqli_fetch_array($b_libro);
                                    $b_programa = buscarProgramaEstudioById($conexion, $r_b_libro['id_programa_estudio']);
                                    $r_b_programa = mysqli_fetch_array($b_programa);
                                    $b_semestre = buscarSemestreById($conexion, $r_b_libro['id_semestre']);
                                    $r_b_semestre = mysqli_fetch_array($b_semestre);
                                    $b_ud = buscarUnidadDidacticaById($conexion, $r_b_libro['id_unidad_didactica']);
                                    $r_b_ud = mysqli_fetch_array($b_ud);
                                ?>
                                    <div class="card col-lg-3 col-md-3 col-sm-6 mb-2">
                                        <img src="https://www.unp.edu.pe/unp/wp-content/uploads/2023/08/bolsa_trabajo.png" alt="" style="width:100%; height:500px; margin-top:5px;">
                                        <div class="card-body">
                                            <h5 class="card-title" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $r_b_libro['titulo']; ?></h5>
                                            <p class="card-text"><?php echo $r_b_programa['nombre'] . ' - S-' . $r_b_semestre['descripcion']; ?></p>
                                            <p class="card-text"><?php echo $r_b_ud['nombre']; ?></p>
                                            <p class="card-text">Autor: <?php echo $r_b_libro['autor']; ?></p>
                                            <center>
                                                <a href="detalle.php?libro=<?php echo base64_encode($r_b_libro['id']); ?>" class="btn btn-info">Ver</a>
                                                <a href="lectura?libro=<?php echo base64_encode($r_b_libro['id']); ?>" class="btn btn-success">Leer Libro</a>
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
