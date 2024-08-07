<?php
include "../include/conexion.php";
include '../include/busquedas.php';
include '../include/funciones.php';


$b_datos_institucion = buscarDatosInstitucional($conexion);
  $r_b_datos_institucion = mysqli_fetch_array($b_datos_institucion);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Recuperar Contraseña<?php include ("../include/header_title.php"); ?></title>
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
            <form role="form" action="enviar_correo.php" method="POST">
              <h1>Recuperar Acceso</h1>

              <div>
                <input type="text" class="form-control" placeholder="Correo" required="" name="email" maxlength="80"/>
              </div>  
              <div>
                <input type="text" class="form-control" placeholder="Dni" required="" onkeypress="return (event.charCode >= 48 && event.charCode <= 57)" name="dni" maxlength="8"/>
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
