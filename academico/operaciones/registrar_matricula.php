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

    if ($contar_permiso == 0  || $rb_usuario['estado'] == 0 || $rb_permiso['id_rol'] != 2) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {


        $id_periodo_acad = $id_periodo_act;
        $id_est = $_POST['id_est'];
        $carrera = $_POST['carrera_m'];
        $semestre = $_POST['semestre'];
        $turno = $_POST['turno'];
        $seccion = $_POST['seccion'];
        $hoy = date("Y-m-d H:m:i");
        $detalle_matricula = explode(",", $_POST['mat_relacion']);

        //VERIFICAMOS QUE EL ESTUDIANTE NO ESTE MATRICULADO EN ESTE PERIODO
        $busc_matricula = buscarMatriculaByEstPeriodoSede($conexion, $id_est, $id_periodo_acad, $id_sede_act);
        $cont_b_matricula = mysqli_num_rows($busc_matricula);

        if ($cont_b_matricula > 0) {
            echo "<script>
			alert('El estudiante ya esta matriculado en esta sede y periodo Académico');
			window.history.back();
		</script>
		";
        } else {
            //REGISTRAMOS LA MATRICULA
            $reg_matricula = "INSERT INTO acad_matricula (id_periodo_academico,id_sede,id_programa_estudio,id_semestre,turno,seccion,id_estudiante,licencia,fecha_hora_registro) VALUES ('$id_periodo_acad','$id_sede_act','$carrera','$semestre','$turno','$seccion','$id_est','','$hoy')";
            $ejecutar_reg_matricula = mysqli_query($conexion, $reg_matricula);
            //buscamos el ultimo registro de la matricula
            $id_matricula = mysqli_insert_id($conexion);


            //recorremos el array del detalle para buscar datos complementarios y registrar el detalle y las calificaciones
            foreach ($detalle_matricula as $valor) {
                registrar_detalle_matricula($conexion, $valor, $id_matricula);
            }
            echo "<script>
            alert('Matricula Exitosa');
            window.location= '../matriculas'
            </script>";
        }
    }
}
