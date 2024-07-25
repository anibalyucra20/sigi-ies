<?php
@session_start();
unset($_SESSION['tutoria_id_sesion']);
unset($_SESSION['tutoria_periodo']);
unset($_SESSION['tutoria_token']);
unset($_SESSION['tutoria_sede']);
header("Location: ../index");
?>