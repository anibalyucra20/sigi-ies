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
        $hoy = date("Y-m-d");
        if ($rb_periodo_act['fecha_fin'] >= $hoy) {

            $id_ud = $_POST['unidad_didactica'];
            $id_docente = $_POST['docente'];
            $id_pe = $_POST['carrera_m'];
            $turno = $_POST['turno'];
            $seccion = $_POST['seccion'];

            $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_pe);
            $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
            $id_pe_sede = $rb_pe_sede['id'];

            //verificar que  docente no este programado en la unidad didactica
            $busc_programacion_existe = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_ud, $id_pe_sede, $id_periodo_act,$turno,$seccion);
            $conteo_b_programacion_existe = mysqli_num_rows($busc_programacion_existe);


            if ($conteo_b_programacion_existe < 1) {

                $b_datos_sistema = buscarDatosSistema($conexion);
                $rb_datos_sistema = mysqli_fetch_array($b_datos_sistema);
                $cant_semanas = $rb_datos_sistema['cant_semanas'];

                $registro_programacion = realizar_programacion($conexion, $id_ud, $id_periodo_act, $id_pe_sede, $id_docente, $cant_semanas,$turno,$seccion);

                if ($registro_programacion) {
                    echo "<script>
			            alert('Programacion registrado correctamente');
			            window.location= '../programacion'
		                </script>
		            ";
                } else {
                    echo "<script>
                        alert('Error, falló en el registro');
                        window.location= '../programacion'
                        </script>
                    ";
                }
            } else {
                echo "<script>
			        alert('Error, Esta Unidad Didáctica ya está programado para este periodo Académico, Turno y Sección');
			        window.location= '../programacion'
		            </script>
		        ";
            }
        } else {
            echo "<script>
                alert('Error, No puede Registrar Programacion Fuera de Periodo');
                window.location= '../programacion'
    			</script>";
        }
    }
}
