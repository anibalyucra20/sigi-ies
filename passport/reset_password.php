<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Recuperar Contraseña<?php include ("../../include/header_title.php"); ?></title>
    <!--icono en el titulo-->
    <link rel="shortcut icon" href="../../img/favicon.ico">
    <!-- Bootstrap -->
    <link href="../../Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../../Gentella/vendors/animate.css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../../Gentella/build/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login">
    <div>
     

      <div class="login_wrapper">
        <div id="register" class="">
          <center><img src="../../img/logo.png" width="150px"></center>
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

                  <a href="../login/" class="to_register"> Iniciar Sesión </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1>I.E.S.T.P. "HUANTA"</h1>
                  <!--<p>Bienvenido a la Biblioteca Virtual, Inicie Sesion para acceder a los materiales de lectura</p>-->
                </div>
              </div>
            </form>
          </section>
        </div>

        
      </div>
    </div>
  </body>
</html>
