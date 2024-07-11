<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_sigi.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_SIGI');
    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['sigi_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_SIGI');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado']==0) {
      echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
      <a href='../sigi/'>Regresar</a><br>
      <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
      </center>";
    } else {
        # code...
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="Content-Language" content="es-ES">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Datos de Sistema<?php include("../include/header_title.php"); ?></title>
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

  <body class="nav-md" onload="desactivar_controles();">
    <div class="container body">
      <div class="main_container">
        <!--menu-->
        <?php
        include("include/menu_director.php"); ?>

        <!-- page content -->
        <div class="right_col" role="main">
          <div class="">

            <div class="clearfix"></div>
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                  <div class="">
                    <h2 align="center">Datos de Sistema</h2>


                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <br />
                    <div class="x_panel">
                      <?php
                      $buscar = buscarDatosSistema($conexion);
                      $res = mysqli_fetch_array($buscar);
                      ?>
                      <div class="x_content">
                        <br />
                        <form role="form" id="myform" action="operaciones/actualizar_datos_sistema.php" class="form-horizontal form-label-left input_mask" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="id" value="<?php echo $res['id']; ?>">
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Direccion URL de Sistema : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="dominio_sistema" id="dominio_sistema" required="" value="<?php echo $res['dominio_pagina']; ?>" placeholder="ejemplo.edu.pe">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Favicon : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="favicon" id="favicon" required="" value="<?php echo $res['favicon']; ?>">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Logo : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="logo" id="logo" required="" value="<?php echo $res['logo']; ?>">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre IES completo : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="titulo_c" id="titulo_c" required="" value="<?php echo $res['nombre_completo']; ?>" placeholder="IEST EJEMPLO">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Titulo Abreviado : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="titulo_a" id="titulo_a" required="" value="<?php echo $res['nombre_corto']; ?>" placeholder="IEST EJEMPLO">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Pie de pagina : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="pie_pagina" id="pie_pagina" required="" value="<?php echo $res['pie_pagina']; ?>" placeholder="IEST EJEMPLO">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Host para Email : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="host_email" id="host_email" required="" value="<?php echo $res['host_mail']; ?>" placeholder="sispa.ejemplo.edu.pe">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Dirección Email : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="email_email" id="email_email" required="" value="<?php echo $res['email_email']; ?>" placeholder="admin@sispa.ejemplo.edu.pe">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Password Email : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="password_email" id="password_email" required="" value="<?php echo $res['password_email']; ?>">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Puerto Email : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="text" class="form-control" name="puerto_email" id="puerto_email" required="" value="<?php echo $res['puerto_email']; ?>">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Color Email : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="color" class="form-control" name="color_correo" id="color_correo" required="" value="<?php echo $res['color_correo']; ?>">
                              <br>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12">Cantidad Semanas (sílabos) : </label>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                              <input type="number" class="form-control" name="cant_semanas" id="cant_semanas" required="" value="<?php echo $res['cant_semanas']; ?>">
                              <br>
                            </div>
                          </div>


                          <div align="center">
                            <button type="submit" class="btn btn-primary" id="btn_guardar">Guardar</button>
                            <button type="button" class="btn btn-warning" id="btn_cancelar" onclick="desactivar_controles(); cancelar();">Cancelar</button>
                          </div>

                        </form>
                      </div>
                      <div align="center">
                        <button type="button" class="btn btn-success" id="btn_editar" onclick="activar_controles();">Editar Datos</button>
                      </div>
                    </div>
                    <!--FIN DE CONTENIDO DE MODAL-->




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
    <script type="text/javascript">
                function desactivar_controles() {
                    document.getElementById('dominio_sistema').disabled = true
                    document.getElementById('favicon').disabled = true
                    document.getElementById('logo').disabled = true
                    document.getElementById('titulo_c').disabled = true
                    document.getElementById('titulo_a').disabled = true
                    document.getElementById('pie_pagina').disabled = true
                    document.getElementById('host_email').disabled = true
                    document.getElementById('email_email').disabled = true
                    document.getElementById('password_email').disabled = true
                    document.getElementById('puerto_email').disabled = true
                    document.getElementById('color_correo').disabled = true
                    document.getElementById('cant_semanas').disabled = true

                    document.getElementById('btn_cancelar').style.display = 'none'
                    document.getElementById('btn_guardar').style.display = 'none'
                    document.getElementById('btn_editar').style.display = ''
                };

                function activar_controles() {
                    document.getElementById('dominio_sistema').disabled = false
                    document.getElementById('favicon').disabled = false
                    document.getElementById('logo').disabled = false
                    document.getElementById('titulo_c').disabled = false
                    document.getElementById('titulo_a').disabled = false
                    document.getElementById('pie_pagina').disabled = false
                    document.getElementById('host_email').disabled = false
                    document.getElementById('email_email').disabled = false
                    document.getElementById('password_email').disabled = false
                    document.getElementById('puerto_email').disabled = false
                    document.getElementById('color_correo').disabled = false
                    document.getElementById('cant_semanas').disabled = false

                    document.getElementById('btn_cancelar').removeAttribute('style')
                    document.getElementById('btn_guardar').removeAttribute('style')
                    document.getElementById('btn_editar').style.display = 'none'
                };

                function cancelar() {
                    document.getElementById('myform').reset();
                }
            </script>

    <?php mysqli_close($conexion); ?>
  </body>

  </html>
<?php } }
