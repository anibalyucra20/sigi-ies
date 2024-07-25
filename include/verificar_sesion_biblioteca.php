<?php
function verificar_sesion($conexion)
{
    session_start();
    if (isset($_SESSION['biblioteca_id_sesion'])) {
        $id_usuario = buscar_usuario_sesion($conexion, $_SESSION['biblioteca_id_sesion'], $_SESSION['biblioteca_token']);
        $b_usuario = buscarUsuarioById($conexion, $id_usuario);
        $r_b_usuario = mysqli_fetch_array($b_usuario);
        $id_cargo = $r_b_usuario['id_rol'];
        $sesion_activa = sesion_si_activa($conexion, $_SESSION['biblioteca_id_sesion'], $_SESSION['biblioteca_token']);
        if (!$sesion_activa) {
            echo "<script>
                alert('La Sesion Caducó, Inicie Sesión');
                window.location.replace('../include/cerrar_sesion_biblioteca.php');
    		</script>";
        } else {
            return $id_cargo;
        }
    } else {
        return 0;
    }
}
