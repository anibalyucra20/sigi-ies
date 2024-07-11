<?php
@session_start();
unset($_SESSION['acad_id_sesion']);
unset($_SESSION['acad_periodo']);
unset($_SESSION['acad_token']);
unset($_SESSION['acad_sede']);
header("Location: ../index");
?>