<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['acad_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_ACAD');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    $id_periodo_act = $_SESSION['acad_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['acad_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $id_prog = $_POST['id_prog'];
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {


        $fue_supervisado = $_POST['fue_supervisado'];
        $reg_evaluacion = $_POST['reg_evaluacion'];
        $reg_auxiliar = $_POST['reg_auxiliar'];
        $prog_curricular = $_POST['prog_curricular'];
        $otros = $_POST['otros'];
        $logros_obtenidos = $_POST['logros_obtenidos'];
        $dificultades = $_POST['dificultades'];
        $sugerencias = $_POST['sugerencias'];


        $actualizar = "UPDATE acad_programacion_unidad_didactica SET supervisado='$fue_supervisado',reg_evaluacion='$reg_evaluacion',reg_auxiliar='$reg_auxiliar',prog_curricular='$prog_curricular',otros='$otros',logros_obtenidos='$logros_obtenidos',dificultades='$dificultades',sugerencias='$sugerencias' WHERE  id='$id_prog'";
        $ejecutar = mysqli_query($conexion, $actualizar);
        echo "<script>
			
			window.location= '../informe_final?data=" . base64_encode($id_prog) . "';
		</script>
	";
    }
}
