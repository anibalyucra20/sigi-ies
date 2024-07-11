<?php
@session_start();
unset($_SESSION['sigi_id_sesion']);
unset($_SESSION['sigi_periodo']);
unset($_SESSION['sigi_token']);
unset($_SESSION['sigi_sede']);
header("Location: ../index");
?>