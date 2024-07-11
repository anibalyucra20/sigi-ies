<?php
@session_start();
unset($_SESSION['admision_id_sesion']);
unset($_SESSION['admision_periodo']);
unset($_SESSION['admision_token']);
unset($_SESSION['admision_sede']);
header("Location: ../index");
?>