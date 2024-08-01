<?php
$id_docente = base64_decode($_POST['data']);

include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");


    $pass = generar_contrasenia();

    $pass_secure = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "UPDATE sigi_usuarios SET password='$pass_secure' WHERE id='$id_docente'";
    $ejec_consulta = mysqli_query($conexion, $sql);
    if ($ejec_consulta) {
        echo '<div class="alert alert-info alert-dismissible fade in" role="alert">
        <strong>Contraseña Cambiado Correctamente, la nueva contraseña es: '.$pass.'</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        </div>
	';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
        <strong>Error al cambiar Contraseña</strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        </div>
	';
    }
