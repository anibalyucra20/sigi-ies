<?php
@session_start();
unset($_SESSION['bolsa_id_sesion']);
unset($_SESSION['bolsa_periodo']);
unset($_SESSION['bolsa_token']);
unset($_SESSION['bolsa_sede']);
header("Location: ../index");
?>