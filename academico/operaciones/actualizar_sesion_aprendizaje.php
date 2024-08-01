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

    $id_sesion = $_POST['id_sesion'];
    $b_sesion = buscarSesionAprendizajeById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);
    $id_prog_act = $r_b_sesion['id_prog_actividad_silabo'];

    // buscamos datos de la programacion de actividades
    $b_prog_act = buscarProgActividadesSilaboById($conexion, $id_prog_act);
    $r_b_prog_act = mysqli_fetch_array($b_prog_act);
    $id_silabo = $r_b_prog_act['id_silabo'];
    // buscamos datos de silabo
    $b_silabo = buscarSilabosById($conexion, $id_silabo);
    $r_b_silabo = mysqli_fetch_array($b_silabo);
    $id_prog = $r_b_silabo['id_prog_unidad_didactica'];

    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);
    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0 || $res_b_prog['id_docente'] != $id_usuario) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        

        $tipo_actividad = $_POST['tipo_actividad'];
        $tipo_sesion = $_POST['tipo_sesion'];
        $fecha_desarrollo = $_POST['fecha_desarrollo'];
        $ind_logro_competencia = $_POST['ind_logro_competencia'];
        $ind_logro_capacidad = $_POST['ind_logro_capacidad'];
        $logro_sesion = $_POST['logro_sesion'];

        $bib_doc_oblig = $_POST['bib_doc_oblig'];
        $bib_doc_opci = $_POST['bib_doc_opci'];
        $bib_est_oblig = $_POST['bib_est_oblig'];
        $bib_est_opci = $_POST['bib_est_opci'];
        $anexos = $_POST['anexos'];

        $b_momentos_ses = buscarMomentosSesionAprendizajeByIdSesion($conexion, $id_sesion);
        while ($r_b_momentos_ses = mysqli_fetch_array($b_momentos_ses)) {
            $id_momen = $r_b_momentos_ses['id'];
            //actualizamos los registros de los momentos de sesion
            $estrategia = $_POST['estrategia_' . $r_b_momentos_ses['id']];
            $actividad = $_POST['actividades_' . $r_b_momentos_ses['id']];
            $recursos = $_POST['recursos_' . $r_b_momentos_ses['id']];
            $tiempo = $_POST['tiempo_' . $r_b_momentos_ses['id']];

            $consulta = "UPDATE acad_momentos_sesion_aprendizaje SET estrategia='$estrategia',actividad='$actividad',recursos='$recursos', tiempo='$tiempo' WHERE  id='$id_momen'";
            $ejec_consulta = mysqli_query($conexion, $consulta);
        }

        $b_actividades_eval = buscarActividadEvaluacionByIdSesion($conexion, $id_sesion);
        while ($r_b_actividades_eval = mysqli_fetch_array($b_actividades_eval)) {
            $id_act_eva = $r_b_actividades_eval['id'];
            //actualizamos los registros de las actividades de evaluacion de sesion
            $indicador = $_POST['indicador_eva_' . $r_b_actividades_eval['id']];
            $tecnicas = $_POST['tecnicas_eva_' . $r_b_actividades_eval['id']];
            $instrumentos = $_POST['instrumentos_eva_' . $r_b_actividades_eval['id']];
            $peso = $_POST['peso_eva_' . $r_b_actividades_eval['id']];

            $consulta = "UPDATE acad_actividad_evaluacion_sesion_aprendizaje SET indicador_logro_sesion='$indicador',tecnica='$tecnicas',instrumentos='$instrumentos', peso='$peso' WHERE  id='$id_act_eva'";
            $ejec_consulta = mysqli_query($conexion, $consulta);
        }


        $actualizar = "UPDATE acad_sesion_aprendizaje SET tipo_actividad='$tipo_actividad',tipo_sesion='$tipo_sesion',fecha_desarrollo='$fecha_desarrollo',id_ind_logro_competencia_vinculado='$ind_logro_competencia',id_ind_logro_capacidad_vinculado='$ind_logro_capacidad',logro_sesion='$logro_sesion',bibliografia_obligatoria_docente='$bib_doc_oblig',bibliografia_opcional_docente='$bib_doc_opci',bibliografia_obligatoria_estudiante='$bib_est_oblig',bibliografia_opcional_estudiante='$bib_est_opci',anexos='$anexos' WHERE  id='$id_sesion'";
        $ejecutar = mysqli_query($conexion, $actualizar);
        echo "<script>
			
			window.location= '../sesion_de_aprendizaje?data=" . base64_encode($id_sesion) . "';
		</script>
	";
    }
}
