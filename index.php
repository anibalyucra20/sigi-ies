<?php
$nombre_fichero = "include/conexion.php";

if (file_exists($nombre_fichero)) {
    echo "<script> window.location.replace('intranet'); </script>";
} else {

    if (isset($_POST['host'])) {
        $host = $_POST['host'];
    } else {
        $host = '';
    }
    if (isset($_POST['db'])) {
        $db = $_POST['db'];
    } else {
        $db = '';
    }
    if (isset($_POST['usuario'])) {
        $usuario = $_POST['usuario'];
    } else {
        $usuario = '';
    }
    if (isset($_POST['password'])) {
        $password = $_POST['password'];
    } else {
        $password = '';
    }

    if ($host != '' && $db != '') {
        $conexion = mysqli_connect($host, $usuario, $password, $db);
        if ($conexion) {
            $fh = fopen("include/conexion.php", 'w') or die("Se produjo un error al crear el archivo");
            $texto = '<?php';
            $texto .= '
$host = "' . $host . '";
$db = "' . $db . '";
$user_db = "' . $usuario . '";
$pass_db = "' . $password . '";

$conexion = mysqli_connect($host,$user_db,$pass_db,$db);

if ($conexion) {
	date_default_timezone_set("America/Lima"); 
}else{
	echo "error de conexion a la base de datos";
	
}
$conexion->set_charset("utf8");
';
            $texto .= '?>';
            fwrite($fh, $texto) or die("No se pudo escribir en el archivo");
            fclose($fh);
            echo "<script>
			alert('Conexión Exitosa');
			window.location.replace('index.php');
		</script>
		";
        } else {
            echo "<script>
			alert('Error de Conexión a la Base de datos, Intenta Nuevamente');
			window.history.back();
		</script>
		";
        }
    }

?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Inicio - SIGI</title>

        <!-- Bootstrap -->
        <link href="plantilla/Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="plantilla/Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
        <!-- NProgress -->
        <link href="plantilla/Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
        <!-- Animate.css -->
        <link href="plantilla/Gentella/vendors/animate.css/animate.min.css" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
    </head>

    <body class="login">
        <div>
            <br>
            <br>
            <center>
                <h1>SISTEMA INTEGRADO DE GESTIÓN INSTITUCIONAL - ACTIVACIÓN</h1>
            </center>
            <center>
                <h2>Formulario de Activación</h2>
            </center>
            <div class="login_wrapper">
                <!--<center><img src="img/logo.png" width="150px"></center>-->
                <form role="form" action="" class="form-horizontal form-label-left input_mask" method="POST">

                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" name="host" required="required" placeholder="Servidor (Host)" value="<?php echo $host; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" name="db" required="required" placeholder="Nombre de Base de Datos" value="<?php echo $db; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" name="usuario" placeholder="Usuario (Base de Datos)" value="<?php echo $usuario; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <input type="text" class="form-control" name="password" placeholder="Contraseña (Base de Datos)" value="<?php echo $password; ?>">

                        </div>
                    </div>
                    <div class="separator">



                    </div>

                    <div align="center">
                        <button type="submit" class="btn btn-primary">Verificar Conexión y Guardar</button>
                    </div>
                </form>



            </div>



            <div class="clearfix"></div>

            <div class="separator">


                <div class="clearfix"></div>
                <br />

                <div>
                    <center>
                        <h2>Copiright (c) 2023 Anibal Yucra</h2>
                    </center>
                    <p></p>
                </div>
            </div>
            </form>
            <section class="">

            </section>



        </div>
        </div>
    </body>

    </html>

<?php
}
?>