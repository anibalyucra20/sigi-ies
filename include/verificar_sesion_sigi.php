<?php
function verificar_sesion($conexion)
{
    session_start();
    if (isset($_SESSION['sigi_id_sesion'])) {
        $id_usuario = buscar_usuario_sesion($conexion, $_SESSION['sigi_id_sesion'], $_SESSION['sigi_token']);
        $b_usuario = buscarUsuarioById($conexion, $id_usuario);
        $r_b_usuario = mysqli_fetch_array($b_usuario);
        $id_cargo = $r_b_usuario['id_rol'];
        $sesion_activa = sesion_si_activa($conexion, $_SESSION['sigi_id_sesion'], $_SESSION['sigi_token']);
        if (!$sesion_activa) {
            return 0;
        } else {
            return $id_cargo;
        }
    } else {
        return 0;
    }
}
