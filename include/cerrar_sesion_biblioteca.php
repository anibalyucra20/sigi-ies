<?php
@session_start();
unset($_SESSION['biblioteca_id_sesion']);
unset($_SESSION['biblioteca_periodo']);
unset($_SESSION['biblioteca_token']);
unset($_SESSION['biblioteca_sede']);
header("Location: ../index");
?>