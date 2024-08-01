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

        $id_silabo = $_POST['id_silabo'];
        $coordinador = $_POST['coordinador'];
        $horario = $_POST['horario'];
        $metodologia = $_POST['metodologia'];
        $recursos_didacticos = $_POST['recursos_didacticos'];
        $sistema_evaluacion = $_POST['sistema_evaluacion'];
        $indicadores_estrategias = $_POST['indicadores_estrategias'];
        $tecnicas_estrategias = $_POST['tecnicas_estrategias'];
        $recursos_bib_imp = $_POST['recursos_bib_imp'];
        $recursos_bib_digi = $_POST['recursos_bib_digi'];

        $b_act_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
        while ($r_b_act_silabo = mysqli_fetch_array($b_act_silabo)) {
            //actualizamos los registros de la programacion de la actividad
            $id_prog_act = $r_b_act_silabo['id'];
            $elemento = $_POST['elemento_' . $r_b_act_silabo['id']];
            $actividad = $_POST['actividad_' . $r_b_act_silabo['id']];
            $contenido = $_POST['contenidos_' . $r_b_act_silabo['id']];
            $tareas = $_POST['tareas_' . $r_b_act_silabo['id']];

            $consulta = "UPDATE acad_programacion_actividades_silabo SET elemento_capacidad='$elemento',actividades_aprendizaje='$actividad',contenidos_basicos='$contenido',tareas_previas='$tareas' WHERE  id='$id_prog_act'";
            $ejec_consulta = mysqli_query($conexion, $consulta);
        }

        $actualizar = "UPDATE acad_silabos SET id_coordinador='$coordinador',horario='$horario',metodologia='$metodologia',recursos_didacticos='$recursos_didacticos',sistema_evaluacion='$sistema_evaluacion',estrategia_evaluacion_indicadores='$indicadores_estrategias',estrategia_evaluacion_tecnica='$tecnicas_estrategias',recursos_bibliograficos_impresos='$recursos_bib_imp',recursos_bibliograficos_digitales='$recursos_bib_digi' WHERE  id='$id_silabo'";
        $ejecutar = mysqli_query($conexion, $actualizar);
        echo "<script>
			window.location= '../silabos?data=" . base64_encode($id_prog) . "';
		</script>
	";
        mysqli_close($conexion);
    }
}
