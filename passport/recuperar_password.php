<?php
include "../include/conexion.php";
include '../include/busquedas.php';
include '../include/funciones.php';



$id_usuario = $_GET['data'];
$token = $_GET['token'];


$b_doc = buscarUsuarioById($conexion, $id_usuario);
$r_b_doc = mysqli_fetch_array($b_doc);
$validar = $r_b_doc['reset_password'];
$llave = $r_b_doc['token_password'];
if ($validar == 1 && password_verify($llave, $token)) {
  $mostrar = 1;
} else {
  echo "<script>
			alert('Error, Link Caducado o Ya utilizado');
			window.location= '../login/';
		</script>
	";
}
if ($mostrar) {

  $b_datos_institucion = buscarDatosInstitucional($conexion);
  $r_b_datos_institucion = mysqli_fetch_array($b_datos_institucion);
?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cambiar Contraseña<?php include("../include/header_title.php"); ?></title>
    <!--icono en el titulo-->
    <link rel="shortcut icon" href="../images/favicon.ico">
    <!-- Bootstrap -->
    <link href="../plantilla/Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../plantilla/Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../plantilla/Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../plantilla/Gentella/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <div class="login_wrapper">
        <div id="register" class="">
          <center><img src="../images/logo.png" width="150px"></center>
          <section class="login_content">
            <form role="form" action="change_password.php" method="POST">
              <h1>Cambiar Contraseña</h1>
              <div>
                <input type="password" class="form-control" placeholder="Ingrese Nueva Contraseña" required="" name="new_password" maxlength="80" />
                <input type="hidden" name="id" value="<?php echo $id_usuario; ?>">
                <input type="hidden" name="token" value="<?php echo $token; ?>">
              </div>
              <div>
                <button type="submit" class="btn btn-default submit">Enviar</button>
              </div>
              <div class="clearfix"></div>
              <div class="separator">
                <p class="change_link">¿Ya tienes una Cuenta?
                  <a href="../passport/" class="to_register"> Iniciar Sesión </a>
                </p>
                <div class="clearfix"></div>
                <br />
                <div>
                  <h1><?php echo $r_b_datos_institucion['nombre_institucion']; ?></h1>
                  <p>SIGI (Sistema Integrado de Gestión Institucional)</p>
                </div>
              </div>
            </form>
          </section>
        </div>


      </div>
    </div>
  </body>

  </html>
<?php }
