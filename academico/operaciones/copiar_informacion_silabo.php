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

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 ) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $id_prog_actual = $_POST['myidactual'];
        $id_prog_a_copiar = $_POST['silabo_copi'];


        $b_silabos_prog_actual = buscarSilabosByIdProgramacion($conexion, $id_prog_actual);
        $r_b_silabo_prog_actual = mysqli_fetch_array($b_silabos_prog_actual);
        $id_silabo_actual = $r_b_silabo_prog_actual['id'];

        $b_silabos_prog_copiar = buscarSilabosByIdProgramacion($conexion, $id_prog_a_copiar);
        $r_b_silabos_prog_copiar = mysqli_fetch_array($b_silabos_prog_copiar);
        $id_silabo_a_copiar = $r_b_silabos_prog_copiar['id'];

        $b_silabo = buscarSilabosById($conexion, $id_silabo_a_copiar);
        $r_b_silabo = mysqli_fetch_array($b_silabo);

        $metodologia = $r_b_silabo['metodologia'];
        $recursos_didacticos = $r_b_silabo['recursos_didacticos'];
        $sistema_evaluacion = $r_b_silabo['sistema_evaluacion'];
        $estrategia_evaluacion_indicadores = $r_b_silabo['estrategia_evaluacion_indicadores'];
        $estrategia_evaluacion_tecnica = $r_b_silabo['estrategia_evaluacion_tecnica'];
        $promedio_indicadores_logro = $r_b_silabo['promedio_indicadores_logro'];
        $recursos_bibliograficos_impresos = $r_b_silabo['recursos_bibliograficos_impresos'];
        $recursos_bibliograficos_digitales = $r_b_silabo['recursos_bibliograficos_digitales'];

        //ACTUALIZAR INFORMACION
        $consulta_silabo = "UPDATE acad_silabos SET metodologia='$metodologia', recursos_didacticos='$recursos_didacticos', sistema_evaluacion='$sistema_evaluacion',estrategia_evaluacion_indicadores='$estrategia_evaluacion_indicadores',estrategia_evaluacion_tecnica='$estrategia_evaluacion_tecnica',promedio_indicadores_logro='$promedio_indicadores_logro',recursos_bibliograficos_impresos='$recursos_bibliograficos_impresos',recursos_bibliograficos_digitales='$recursos_bibliograficos_digitales' WHERE id='$id_silabo_actual'";
        $ejec_update_silabo = mysqli_query($conexion, $consulta_silabo);



        // buscamos las actividades
        $b_actividades_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $r_b_silabo_prog_actual['id']);
        while ($r_b_act_silabo = mysqli_fetch_array($b_actividades_silabo)) {
            $semana_actual = $r_b_act_silabo['semana'];
            $id_actividad_actual = $r_b_act_silabo['id'];

            $b_act_silabo_a_copiar = buscarProgActividadesSilaboByIdSilaboAndSemana($conexion, $id_silabo_a_copiar, $semana_actual);
            $r_b_act_silabo_a_copiar = mysqli_fetch_array($b_act_silabo_a_copiar);
            $id_actividad_a_copiar = $r_b_act_silabo_a_copiar['id'];


            //---------- DATOS DE ACTIVIDAD A COPIAR ----------------
            $b_actividad_copiar = buscarProgActividadesSilaboById($conexion, $id_actividad_a_copiar);
            $rb_actividad_copiar = mysqli_fetch_array($b_actividad_copiar);

            $elemento_capacidad = $rb_actividad_copiar['elemento_capacidad'];
            $actividades_aprendizaje = $rb_actividad_copiar['actividades_aprendizaje'];
            $contenidos_basicos = $rb_actividad_copiar['contenidos_basicos'];
            $tareas_previas = $rb_actividad_copiar['tareas_previas'];

            //ACTUALIZAR INFORMACION
            $consulta_actividad = "UPDATE acad_programacion_actividades_silabo SET elemento_capacidad='$elemento_capacidad', actividades_aprendizaje='$actividades_aprendizaje', contenidos_basicos='$contenidos_basicos',tareas_previas='$tareas_previas' WHERE id='$id_actividad_actual'";
            $ejec_update_actividad = mysqli_query($conexion, $consulta_actividad);
        }

        mysqli_close($conexion);


        echo "<script>
			alert('Datos Copiados Correctamente');
			window.location= '../silabos?data=" . base64_encode($id_prog_actual) . "';
				</script>
			";
    }
}
