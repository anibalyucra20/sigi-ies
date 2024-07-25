<?php
include "../../include/conexion.php";
include "../../include/busquedas.php";
include "../../include/funciones.php";
include("../../include/verificar_sesion_tutoria.php");
if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_TUTORIA');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {

    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['tutoria_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_TUTORIA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $id_periodo_act = $_SESSION['tutoria_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['tutoria_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../../tutoria/'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_tutoria.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
       
      
        $b_tutoria = buscarTutoriaByIdDocenteAndIdPeriodoSede($conexion, $id_usuario, $id_periodo_act, $id_sede_act);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);

        $b_est_tutoria = buscarTutoriaEstudiantesByIdTutoria($conexion, $r_b_tutoria['id']);
        $error = 0;
        while ($r_b_est_tutoria = mysqli_fetch_array($b_est_tutoria)) {
            $id_est_tutoria  = $r_b_est_tutoria['id'];
            $observacion = $_POST['obs_' . $id_est_tutoria];
            $b_tut_rec_info = buscarTutoriaRecojoInfoByIdTutEst($conexion, $id_est_tutoria);
            while ($r_b_tut_rec_info = mysqli_fetch_array($b_tut_rec_info)) {
                $id_rec_info = $r_b_tut_rec_info['id'];
                $valor = $_POST[$id_est_tutoria . '_' . $id_rec_info];
                $consulta = "UPDATE tutoria_recojo_informacion SET valor='$valor' WHERE id=$id_rec_info";
                $actualizar = mysqli_query($conexion, $consulta);
                if (!$actualizar) {
                    $error++;
                }
            }
            $consultar = "UPDATE tutoria_estudiantes SET observacion='$observacion' WHERE id=$id_est_tutoria";
            $ejecutar = mysqli_query($conexion, $consultar);
            if (!$ejecutar) {
                $error++;
            }
        }
        if ($error == 0) {
            echo "<script>
                alert('Actualizado Correctamente');
                window.location= '../tutoria_recojo_informacion'
            </script>
        ";
        } else {
            echo "<script>
                alert('Error al Actualizar " . $error . " Registros');
                window.history.back();
            </script>
        ";
        }
    }
}
