<?php
include "../include/funciones.php";
include "../include/conexion.php";
include "../include/busquedas.php";
$fecha_hora_inicio = date("Y-m-d h:i:s");


if (!isset($_GET['data'])) {
  // si no existe la data
  echo "<script>
                  window.location.replace('../intranet');
              </script>";
} else {

  $sistema =  base64_decode($_GET['data']);
  $nombre_sistema = obtener_titulo_sistema($sistema);
  if ($nombre_sistema == "SIGI") {
    echo "<script>
    window.location.replace('../intranet');
</script>";
  }

  $b_datos_sistema = buscarDatosSistema($conexion);
  $rb_datos_sistema = mysqli_fetch_array($b_datos_sistema);


?>

  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - <?php echo $nombre_sistema; ?></title>
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
        <div class="animate form login_form">
          <center><img src="../images/logo.png" width="150px"></center>
          <section class="login_content">
            <form role="form" action="iniciar_sesion" method="POST">
              <h1>Inicio de Sesión</h1>
              <h2><?php
                  echo $nombre_sistema . "<br><br>";

                  ?></h2>
              <div>
                <input type="hidden" name="sistema" value="<?php echo base64_encode($sistema); ?>">
                <input type="text" class="form-control" placeholder="usuario" required="" name="usuario" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Contraseña" required="" name="password" />
              </div>
              <div>
                <button type="submit" class="btn btn-default">Iniciar Sesión</button>
              </div>
              <br>
              <div><a class="" href="reset_password">¿Olvidaste tu Contraseña?</a></div>
              <div><a class="" href="../"><h2><i class="fa fa-home"></i>Volver a Inicio</h2></a></div>
              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />
                <div>
                  <h1><?php echo $rb_datos_sistema['nombre_corto']; ?></h1>
                  <p>SIGI - Sistema Integrado de Gestión Institucional</p>
                </div>
              </div>
            </form>
          </section>
        </div>


      </div>
    </div>
  </body>

  </html>
<?php
}
