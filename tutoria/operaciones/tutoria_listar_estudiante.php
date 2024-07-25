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


        $id_pe = $_POST['id_pe'];
        $id_sem = $_POST['id_sem'];
        $turno = $_POST['id_turno'];
        $seccion = $_POST['id_seccion'];

        $ejec_cons = buscarMatriculaByPeriodoSedeTurnoSeccion($conexion, $id_periodo_act, $id_sede_act, $id_pe, $turno, $seccion);

        $cadena = '<div class="checkbox">
		<label>
		  <input type="checkbox" onchange="select_all();" id="all_check"> <b> SELECCIONAR TODOS LOS ESTUDIANTES *</b>
		</label>
		</div>';

        while ($mostrar = mysqli_fetch_array($ejec_cons)) {
            $id_estudiante = $mostrar["id_estudiante"];

            //validar para solo mostrar estudiantes que no esten asignados a otra tutoria en el periodo
            $b_tutoria_est = buscarTutoria_EstudianteByIdEstudiante($conexion, $id_estudiante);
            $contar = 0;
            while ($r_b_tutoria_est = mysqli_fetch_array($b_tutoria_est)) {
                $b_tutoria = buscarTutoriaById($conexion, $r_b_tutoria_est['id_programacion_tutoria']);
                $r_b_tutoria = mysqli_fetch_array($b_tutoria);
                if ($id_per == $r_b_tutoria['id_periodo_academico']) {
                    $contar++;
                }
            }
            if ($contar == 0) {
                $busc_est = buscarUsuarioById($conexion, $id_estudiante);
                $cont = mysqli_num_rows($busc_est);
                if ($cont > 0) {
                    $res_est = mysqli_fetch_array($busc_est);
                    $cadena = $cadena . '<div class="checkbox"><label><input type="checkbox" name="estudiantes" onchange="gen_arr_est();" value="' . $res_est["id"] . '">' . $res_est["apellidos_nombres"] . '</label></div>';
                }
            }
        }
        echo $cadena;
    }
}
