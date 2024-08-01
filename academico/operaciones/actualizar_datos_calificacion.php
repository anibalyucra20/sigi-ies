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
    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || ($res_b_prog['id_usuario'] != $id_usuario && $rb_permiso['id_rol'] != 2)) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        

        $b_prog = buscarProgramacionUdById($conexion, $id_prog);
        $res_b_prog = mysqli_fetch_array($b_prog);
        $b_periodo_acad = buscarPeriodoAcadById($conexion, $res_b_prog['id_periodo_academico']);
        $r_per_acad = mysqli_fetch_array($b_periodo_acad);
        $fecha_actual = strtotime(date("d-m-Y"));
        $fecha_fin_per = strtotime($r_per_acad['fecha_fin']);
        if ($fecha_actual <= $fecha_fin_per) {



            if (!isset($_POST['mostrar_calif_final'])) {
                $mostrar_calif_final = 0;
            } else {
                $mostrar_calif_final = 1;
            }
            $b_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
            while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
                $id_det_mat = $r_b_det_mat['id'];
                $c_up_ord = "UPDATE acad_detalle_matricula SET mostrar_calificacion	='$mostrar_calif_final' WHERE id='$id_det_mat'";
                mysqli_query($conexion, $c_up_ord);

                $b_calificacion = buscarCalificacionByIdDetalleMatricula($conexion, $r_b_det_mat['id']);
                $count = 1;
                while ($r_b_calificacion = mysqli_fetch_array($b_calificacion)) {
                    $id = $r_b_calificacion['id'];
                    if (!isset($_POST['mostrar_calif_' . $count])) {
                        $mostrar_calif = 0;
                    } else {
                        $mostrar_calif = 1;
                    }
                    $consulta = "UPDATE acad_calificacion SET mostrar_calificacion='$mostrar_calif' WHERE id='$id'";
                    mysqli_query($conexion, $consulta);
                    $count += 1;
                    
                }
                if (isset($_POST['recuperacion_' . $r_b_det_mat['id']])) {
                    $recuperacion = $_POST['recuperacion_' . $r_b_det_mat['id']];


                    $act_recuperacion = "UPDATE acad_detalle_matricula SET recuperacion='$recuperacion' WHERE id='$id_det_mat'";
                    $ejec_act_recuperacion = mysqli_query($conexion, $act_recuperacion);
                }
            }
            echo "<script>
			    window.location= '../calificaciones?data=" . base64_encode($id_prog) . "';
		        </script>
	            ";
        } else {
            echo "<script>
                alert('Periodo Finalizado, No puede Realizar Cambios');
			    window.location= '../calificaciones?data=" . base64_encode($id_prog) . "';
		        </script>
	            ";
        }
    }
}
