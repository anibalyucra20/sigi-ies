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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 2) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $detalle_matricula = explode(",", $_POST['mat_relacion']);


        $id_matricula = $_POST['id_mat'];
        $semestre = $_POST['semestre'];
        $turno = $_POST['turno'];
        $seccion = $_POST['seccion'];

        //buscamos datos de matricula
        $b_mat = buscarMatriculaById($conexion, $id_matricula);
        $r_b_mat = mysqli_fetch_array($b_mat);

        //actualizamos semestre, turno y seccion de matricula
        $consulta = "UPDATE acad_matricula SET id_semestre='$semestre', turno='$turno',seccion='$seccion' WHERE id='$id_matricula'";
        mysqli_query($conexion, $consulta);

        $id_est = $r_b_mat['id_estudiante'];
        //recorremos el array del detalle para buscar datos complementarios y registrar el detalle y las calificaciones
        foreach ($detalle_matricula as $valor) {
            $b_det_mat_prog = buscarDetalleMatriculaByIdMatriculaAndProgrmacion($conexion, $id_matricula, $valor);
            $cont_b_det_mat_prog = mysqli_num_rows($b_det_mat_prog);
            if ($cont_b_det_mat_prog == 0) {
                registrar_detalle_matricula($conexion, $valor, $id_matricula);
            }
        }
        echo "<script>
                
                window.location= '../ver_matricula?data=" . base64_encode($id_matricula) . "'
    			</script>";
    }
}
