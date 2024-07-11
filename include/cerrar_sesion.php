<?php
@session_start();
unset($_SESSION['sigi_id_sesion']);
unset($_SESSION['sigi_periodo']);
unset($_SESSION['sigi_token']);
unset($_SESSION['sigi_sede']);

unset($_SESSION['acad_id_sesion']);
unset($_SESSION['acad_periodo']);
unset($_SESSION['acad_token']);
unset($_SESSION['acad_sede']);

unset($_SESSION['admision_id_sesion']);
unset($_SESSION['admision_periodo']);
unset($_SESSION['admision_token']);
unset($_SESSION['admision_sede']);


header("Location: ../index");
?>