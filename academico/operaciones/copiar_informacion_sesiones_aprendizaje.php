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

    $id_prog_actual = $_POST['myidactual'];
    $id_prog_a_copiar = $_POST['sesion_copi'];

    $b_prog = buscarProgramacionUDById($conexion, $id_prog_actual);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {


       

        $b_silabos_prog_actual = buscarSilabosByIdProgramacion($conexion, $id_prog_actual);
        $r_b_silabo_prog_actual = mysqli_fetch_array($b_silabos_prog_actual);

        $b_actividades_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo_prog_actual['id']);
        while ($r_b_act_silabo = mysqli_fetch_array($b_actividades_silabo)) {
            $semana_actual = $r_b_act_silabo['semana'];
            $b_sesion_actual = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $r_b_act_silabo['id']);
            $r_b_sesion_actual = mysqli_fetch_array($b_sesion_actual);
            $id_actual = $r_b_sesion_actual['id'];

            //buscar sesion a copiar
            $b_silabos_prog_a_copiar = buscarSilabosByIdProgramacion($conexion, $id_prog_a_copiar);
            $r_b_silabos_prog_a_copiar = mysqli_fetch_array($b_silabos_prog_a_copiar);
            $id_silabo_a_copiar = $r_b_silabos_prog_a_copiar['id'];
            $b_act_silabo_a_copiar = buscarProgActividadesSilaboByIdSilaboAndSemana($conexion, $id_silabo_a_copiar, $semana_actual);
            $r_b_act_silabo_a_copiar = mysqli_fetch_array($b_act_silabo_a_copiar);
            $b_secion_a_copiar = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $r_b_act_silabo_a_copiar['id']);
            $r_b_sesion_a_copiar = mysqli_fetch_array($b_secion_a_copiar);
            $id_a_copiar = $r_b_sesion_a_copiar['id'];





            //---------- DATOS DE SESION A COPIAR ----------------
            $b_sesion = buscarSesionAprendizajeById($conexion, $id_a_copiar);
            $r_b_sesion = mysqli_fetch_array($b_sesion);

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

            //ACTUALIZAR INFORMACION
            $consulta_sesion = "UPDATE acad_sesion_aprendizaje SET tipo_actividad='$tipo_actividad', tipo_sesion='$tipo_sesion', fecha_desarrollo='$fecha_desarrollo',id_ind_logro_competencia_vinculado='$id_ind_logro_competencia_vinculado',id_ind_logro_capacidad_vinculado='$id_ind_logro_capacidad_vinculado',logro_sesion='$logro_sesion',bibliografia_obligatoria_docente='$bibliografia_obligatoria_docente',bibliografia_opcional_docente='$bibliografia_opcional_docente',bibliografia_obligatoria_estudiante='$bibliografia_obligatoria_estudiantes',bibliografia_opcional_estudiante='$bibliografia_opcional_estudiante',anexos='$anexos' WHERE  id='$id_actual'";
            $ejec_act_sesion = mysqli_query($conexion, $consulta_sesion);


            //---------- DATOS DE MOMENTOS_SESION_APRENDIZAJE A COPIAR ----------------
            $b_momentos = buscarMomentosSesionAprendizajeByIdSesion($conexion, $id_a_copiar);
            while ($r_b_momentos = mysqli_fetch_array($b_momentos)) {
                $momento = $r_b_momentos['momento'];
                $estrategia = $r_b_momentos['estrategia'];
                $actividad = $r_b_momentos['actividad'];
                $recursos = $r_b_momentos['recursos'];
                $tiempo = $r_b_momentos['tiempo'];

                //---------- COPIAR  MOMENTOS DE SESION ----------------
                $reg_momentos_sesion = "UPDATE acad_momentos_sesion_aprendizaje SET estrategia='$estrategia', actividad='$actividad', recursos='$recursos',tiempo='$tiempo' WHERE  id_sesion_aprendizaje='$id_actual' AND momento='$momento'";
                $ejec_reg_momentos_sesion = mysqli_query($conexion, $reg_momentos_sesion);
            }

            //---------- DATOS DE ACTIVIDADES EVALUACION SESION A COPIAR----------------
            $b_act_eva_sesion = buscarActividadEvaluacionByIdSesion($conexion, $id_a_copiar);
            while ($r_b_act_eva_sesion = mysqli_fetch_array($b_act_eva_sesion)) {
                $indicador_logro_sesion = $r_b_act_eva_sesion['indicador_logro_sesion'];
                $tecnica = $r_b_act_eva_sesion['tecnica'];
                $instrumentos = $r_b_act_eva_sesion['instrumentos'];
                $peso = $r_b_act_eva_sesion['peso'];
                $momento_act = $r_b_act_eva_sesion['momento'];

                //---------- COPIAR ACTIVIDADES EVALUACION SESION ----------------
                $reg_act_eva = "UPDATE acad_actividad_evaluacion_sesion_aprendizaje SET indicador_logro_sesion='$indicador_logro_sesion', tecnica='$tecnica', instrumentos='$instrumentos', peso='$peso' WHERE  id_sesion_aprendizaje='$id_actual' AND momento='$momento_act'";
                $ejec_reg_act_eva = mysqli_query($conexion, $reg_act_eva);
            }
        }




        echo "<script>
			alert('Datos Copiados Correctamente');
			window.location= '../sesiones?data=" . base64_encode($id_prog_actual) . "';
				</script>
			";

        mysqli_close($conexion);
    }
}
