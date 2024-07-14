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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        $id_sesion = base64_decode($_GET['data']);
        $id_prog = base64_decode($_GET['data2']);

        //---------- DATOS DE SESION ----------------
        $b_sesion = buscarSesionAprendizajeById($conexion, $id_sesion);
        $r_b_sesion = mysqli_fetch_array($b_sesion);

        $id_programacion_actividad_silabo = $r_b_sesion['id_prog_actividad_silabo'];
        $tipo_actividad = $r_b_sesion['tipo_actividad'];
        $tipo_sesion = $r_b_sesion['tipo_sesion'];
        $fecha_desarrollo = $r_b_sesion['fecha_desarrollo'];
        $id_ind_logro_competencia_vinculado = $r_b_sesion['id_ind_logro_competencia_vinculado'];
        $id_ind_logro_capacidad_vinculado = $r_b_sesion['id_ind_logro_capacidad_vinculado'];
        $logro_sesion = $r_b_sesion['logro_sesion'];
        $bibliografia_obligatoria_docente = $r_b_sesion['bibliografia_obligatoria_docente'];
        $bibliografia_opcional_docente = $r_b_sesion['bibliografia_opcional_docente'];
        $bibliografia_obligatoria_estudiantes = $r_b_sesion['bibliografia_obligatoria_estudiante'];
        $bibliografia_opcional_estudiante = $r_b_sesion['bibliografia_opcional_estudiante'];
        $anexos = $r_b_sesion['anexos'];

        //-------- REGISTRAMOS EL NUEVO SESION DE APRENDIZAJE ------------------
        $reg_sesion = "INSERT INTO acad_sesion_aprendizaje (id_prog_actividad_silabo, tipo_actividad, tipo_sesion, fecha_desarrollo, id_ind_logro_competencia_vinculado, id_ind_logro_capacidad_vinculado, logro_sesion, bibliografia_obligatoria_docente, bibliografia_opcional_docente, bibliografia_obligatoria_estudiante, bibliografia_opcional_estudiante, anexos) VALUES ('$id_programacion_actividad_silabo','$tipo_actividad','$tipo_sesion','$fecha_desarrollo','$id_ind_logro_competencia_vinculado','$id_ind_logro_capacidad_vinculado','$logro_sesion','$bibliografia_obligatoria_docente','$bibliografia_opcional_docente','$bibliografia_obligatoria_estudiantes','$bibliografia_opcional_estudiante','$anexos')";
        $ejec_reg_sesion = mysqli_query($conexion, $reg_sesion);

        $id_new_sesion = mysqli_insert_id($conexion);

        //---------- DATOS DE MOMENTOS DE SESION ----------------
        $b_momentos = buscarMomentosSesionAprendizajeByIdSesion($conexion, $id_sesion);
        while ($r_b_momentos = mysqli_fetch_array($b_momentos)) {
            $momento = $r_b_momentos['momento'];
            $estrategia = $r_b_momentos['estrategia'];
            $actividad = $r_b_momentos['actividad'];
            $recursos = $r_b_momentos['recursos'];
            $tiempo = $r_b_momentos['tiempo'];

            //---------- REGISTRAR EL NUEVO MOMENTOS DE SESION ----------------
            $reg_momentos_sesion = "INSERT INTO acad_momentos_sesion_aprendizaje (id_sesion_aprendizaje, momento, estrategia, actividad, recursos, tiempo) VALUES ('$id_new_sesion', '$momento','$estrategia','$actividad','$recursos','$tiempo')";
            $ejec_reg_momentos_sesion = mysqli_query($conexion, $reg_momentos_sesion);
        }


        //---------- DATOS DE ACTIVIDADES EVALUACION SESION ----------------
        $b_act_eva_sesion = buscarActividadEvaluacionByIdSesion($conexion, $id_sesion);
        while ($r_b_act_eva_sesion = mysqli_fetch_array($b_act_eva_sesion)) {
            $indicador_logro_sesion = $r_b_act_eva_sesion['indicador_logro_sesion'];
            $tecnica = $r_b_act_eva_sesion['tecnica'];
            $instrumentos = $r_b_act_eva_sesion['instrumentos'];
            $peso = $r_b_act_eva_sesion['peso'];
            $momento_act = $r_b_act_eva_sesion['momento'];

            //---------- REGISTRAR EL NUEVO ACTIVIDADES EVALUACION SESION ----------------
            $reg_act_eva = "INSERT INTO acad_actividad_evaluacion_sesion_aprendizaje (id_sesion_aprendizaje, indicador_logro_sesion, tecnica, instrumentos, peso, momento) VALUES ('$id_new_sesion','$indicador_logro_sesion','$tecnica','$instrumentos','$peso','$momento_act')";
            $ejec_reg_act_eva = mysqli_query($conexion, $reg_act_eva);
        }

        //---------- DATOS DE ASISTENCIA ----------------
        $b_asistencia = buscarAsistenciaByIdSesion($conexion, $id_sesion);
        while ($r_b_asistencia = mysqli_fetch_array($b_asistencia)) {
            $id_det_mat = $r_b_asistencia['id_detalle_matricula'];
            //------------- REGISTRAMOS LAS ASISTENCCIAS -------------------
            $r_asistencia = "INSERT INTO acad_asistencia (id_sesion_aprendizaje, id_detalle_matricula, asistencia) VALUES ('$id_new_sesion','$id_det_mat','')";
            $ejecutar_r_asistencia = mysqli_query($conexion, $r_asistencia);
        }



        echo "<script>
			
			window.location= '../sesiones?data=" . base64_encode($id_prog) . "';
		</script>
	";
    }
}
