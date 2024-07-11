<?php
include "../include/conexion.php";
include '../include/busquedas.php';
include '../include/funciones.php';

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//enviar correo
require '../librerias/PHPMailer/Exception.php';
require '../librerias/PHPMailer/PHPMailer.php';
require '../librerias/PHPMailer/SMTP.php';

if (!isset($_POST['email'])&&!isset($_POST['dni'])) {

    if (isset($_SESSION['sigi_id_sesion'])) {
        $id_sesion = $_SESSION['sigi_id_sesion'];
        $token = $_SESSION['sigi_token'];
    }
    if (isset($_SESSION['acad_id_sesion'])) {
        $id_sesion = $_SESSION['sigi_id_sesion'];
        $token = $_SESSION['acad_token'];
    }
    if (isset($_SESSION['admision_id_sesion'])) {
        $id_sesion = $_SESSION['admision_id_sesion'];
        $token = $_SESSION['admision_token'];
    }
    
    
    
    $b_sesion = buscarSesionLoginById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);
    if (password_verify($r_b_sesion['token'], $token)) {
        $id_usuario = buscar_usuario_sesion($conexion, $id_sesion, $token);
        $enviar = 1;
    } else {
        $enviar = 0;
    }
    
} else {
    $correo = $_POST['email'];
    $dni = $_POST['dni'];

    $ejec_busc_user = buscarUsuarioByDniCorreo($conexion, $dni, $correo);
    $cont_user = mysqli_num_rows($ejec_busc_user);
    if ($cont_user > 0) {
        $res_busc_user = mysqli_fetch_array($ejec_busc_user);
        $id_usuario = $res_busc_user['id'];
        $enviar = 1;
    } else {
        $enviar = 0;
    }
    //echo $enviar;
}

$llave = generar_llave();
$token = password_hash($llave, PASSWORD_DEFAULT);

if ($enviar) {
    

    $b_datos_institucion = buscarDatosInstitucional($conexion);
    $r_b_datos_institucion = mysqli_fetch_array($b_datos_institucion);

    $b_datos_sistema = buscarDatosSistema($conexion);
    $r_b_datos_sistema = mysqli_fetch_array($b_datos_sistema);

    $b_usuario = buscarUsuarioById($conexion, $id_usuario);
    $r_b_usuario = mysqli_fetch_array($b_usuario);

    //enviamos correo


    $asunto = "Cambio de Contraseña SIGI - ".$r_b_datos_institucion['nombre_institucion'];
    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = $r_b_datos_sistema['host_mail'];                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $r_b_datos_sistema['email_email'];                     //SMTP username
        $mail->Password   = $r_b_datos_sistema['password_email'];                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = $r_b_datos_sistema['puerto_email'];                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        $titulo_correo = 'MENSAJERÍA SIGI - '.$r_b_datos_sistema['nombre_completo'];
        //Recipients
        $mail->setFrom($r_b_datos_sistema['email_email'], $titulo_correo);
        $mail->addAddress($r_b_usuario['correo'], $r_b_usuario['apellidos_nombres']);     //Add a recipient
        //$mail->addAddress('ellen@example.com');               //Name is optional
        //$mail->addReplyTo('info@example.com', 'Information');
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');

        //Attachments
        //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;


        $link = 'https://'.$r_b_datos_sistema['dominio_pagina'].'/passport/recuperar_password?data=' . $id_usuario . '&token='.$token;
        $mail->Body = '<!DOCTYPE html>
                    <html lang="es">
                    <head>
                        <meta charset="UTF-8">
                    </head>
                    <body>
                    <div style="width: 100%; font-family: Roboto; font-size: 0.8em; display: inline;">
                        <div style="background-color:'.$r_b_datos_sistema['color_correo'].'; border-radius: 10px 10px 0px 0px; text-align: center;">
                            <img src="https://'.$r_b_datos_sistema['dominio_pagina'].'/images/logo.png" alt="'.$r_b_datos_sistema['dominio_pagina'].'" style="padding: 0.5em; text-align: center;" height="50px">
                        </div>
                        <div style="background-color:'.$r_b_datos_sistema['color_correo'].'; border-radius: 0px 0px 0px 0px; height: 60px; margin-top: 0px; padding-top: 2px; padding-bottom: 10px;">
                            <p style="text-align: center; font-size: 1.0rem; color: #f1f1f1; text-shadow: 2px 2px 2px #cfcfcf; ">'.$r_b_datos_institucion['nombre_institucion'].'</p>
                        </div>
                        <div>
                            <h2 style="text-align:center;">SIGI (Sistema Integrado de Gestión Institucional)</h2>
                            <h3 style="text-align:center; color: #3c4858;">CAMBIO DE CONTRASEÑA</h3>
                            <p style="font-size:1.0rem; color: #2A2C2B; margin-top: 2em; margin-bottom: 2em; margin-left: 1.5em;">
                    
                                Hola ' . $r_b_usuario['apellidos_nombres'] . ', para poder recuperar tu contraseña, Haz click <a href="'.$link.'">Aquí</a>.<br>
                                
                                
                                <br>
                                <br>
                                Por favor, no responda sobre este correo.
                                <br><br><br>
                    
                            </p>
                        </div>
                        <div style="color: #f1f1f1; width: 100%; height: 120px; background:'.$r_b_datos_sistema['color_correo'].'; text-align: center;  border-radius: 0px 0px 10px 10px; ">
                            <br>
                            <p style="margin: 0px;">
                                <strong>
                                    <a"
                                       style="text-decoration: none; color: #f1f1f1; ">'.$r_b_datos_institucion['direccion'].'
                                        &nbsp;|&nbsp; Teléfono: '.$r_b_datos_institucion['telefono'].'</a>
                                    <br> '.$r_b_datos_institucion['nombre_institucion'].'
                                </strong>
                            </p>
                        </div>
                    </div>
                    </body>
                    </html>';
        //$mail->AltBody = '';

        $mail->send();
        //echo 'Correo enviado';
        $sql = "UPDATE sigi_usuarios SET reset_password=1, token_password='$llave' WHERE id=$id_usuario";
        $ejec_consulta = mysqli_query($conexion, $sql);
        echo "<script>
    alert('Verifique su correo, sino encuentra en su bandeja de entrada. Verifique en Seccion de Spam');
    window.history.back();
    </script>
    ";
    } catch (Exception $e) {
        echo "Error correo: {$mail->ErrorInfo}";
    }
}else{
    echo "<script>
    alert('Ops, Ocurrio un Error al enviar Correo');
    window.history.back();
    </script>";
}
