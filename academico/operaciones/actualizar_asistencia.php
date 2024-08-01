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
    $cant_as = $_POST['cant_as'];

    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    $b_periodo_acad = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
    $r_per_acad = mysqli_fetch_array($b_periodo_acad);
    $fecha_actual = strtotime(date("d-m-Y"));
    $fecha_fin_per = strtotime($r_per_acad['fecha_fin']);
    if ($fecha_actual <= $fecha_fin_per) {

        $b_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
        while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
            $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
            $r_b_matricula = mysqli_fetch_array($b_matricula);

            $b_estudiante = buscarUsuarioById($conexion, $r_b_matricula['id_estudiante']);
            $r_b_estudiante = mysqli_fetch_array($b_estudiante);

            $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_prog);
            $r_b_silabo = mysqli_fetch_array($b_silabo);
            $b_prog_act = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo['id']);
            while ($res_b_prog_act = mysqli_fetch_array($b_prog_act)) {
                // buscamos la sesion que corresponde
                $id_act = $res_b_prog_act['id'];
                $b_sesion = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_act);
                while ($r_b_sesion = mysqli_fetch_array($b_sesion)) {
                    $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $r_b_sesion['id'], $r_b_det_mat['id']);
                    $r_b_asistencia = mysqli_fetch_array($b_asistencia);
                    $id_ass = $r_b_asistencia['id'];

                    if ($r_b_matricula['licencia'] == "") {
                        $asistencia = $_POST[$r_b_estudiante['dni'] . "_" . $r_b_asistencia['id']];
                        $consulta = "UPDATE acad_asistencia SET asistencia='$asistencia' WHERE id='$id_ass'";
                        $ejec_consulta = mysqli_query($conexion, $consulta);
                    }
                }
            }
        }
    } else {
        echo "<script>
            alert('Periodo Finalizado, No puede realizar Modificaciones');
			window.location= '../asistencias?data=" . base64_encode($id_prog) . "';
		</script>
	";
    }

    echo "<script>
			window.location= '../asistencias?data=" . base64_encode($id_prog) . "';
		</script>
	";
}
}