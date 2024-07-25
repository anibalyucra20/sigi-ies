<?php
include "../../include/conexion.php";
include "../../include/busquedas.php";
include "../../include/funciones.php";
include("../../include/verificar_sesion_tutoria.php");
if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_TUTORIA');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {

    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['tutoria_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_TUTORIA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $id_periodo_act = $_SESSION['tutoria_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['tutoria_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../../tutoria/'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_tutoria.php'>Cerrar Sesión</a><br>
    </center>";
    } else {



        $id_tutoria = $_POST['data'];
        $b_tutoria = buscarTutoriaById($conexion, $id_tutoria);
        $r_b_tutoria = mysqli_fetch_array($b_tutoria);
        $b_docente_tutoria = buscarUsuarioById($conexion, $r_b_tutoria['id_docente']);
        $r_b_docente_tutoria = mysqli_fetch_array($b_docente_tutoria);

        $arr_recojo_informacion = array("Tiene confianza en lograr las metas que se propone y enfrentar las dificultades.", "Tiene iniciativa para resolver sus problemas.", "Se comunica en forma clara expresando sus ideas, sentimientos y opiniones.", "Valora y acepta a los demás respetando su diversidad.", "Tiene un Proyecto de Vida personal.", "Toma decisiones en forma asertiva e independiente.", "Asume responsablemente su rol de estudiante.", "Tiene habilidades para solucionar conflictos familiares.", "Se integra rápidamente a los grupos de amigos/as.");

        $detalle_estudiantes = explode(",", $_POST['est_relacion']);
        //recorremos el array del detalle para buscar datos complementarios y registrar el detalle
        $contar_fallas = 0;
        foreach ($detalle_estudiantes as $valor) {
            //validar si ya existe estudiante en la tutoria
            $b_estudiante_tutoria = buscarTutoriaEstudiantesByIdTutoriaAndIdEst($conexion, $id_tutoria, $valor);
            $cont_est_tutoris = mysqli_num_rows($b_estudiante_tutoria);
            if ($cont_est_tutoris > 0) {
                $contar_fallas++;
            } else {
                $reg_est_tutoria =  "INSERT INTO tutoria_estudiantes (id_programacion_tutoria, id_estudiante, observacion) VALUES ('$id_tutoria','$valor','')";
                $ejecutar_reg_est_tutoria = mysqli_query($conexion, $reg_est_tutoria);
                if ($ejecutar_reg_est_tutoria) {
                    $id_tutoria_estudiantes = mysqli_insert_id($conexion);
                    foreach ($arr_recojo_informacion as $value) {
                        $reg_recojo_info = "INSERT INTO tutoria_recojo_informacion (id_tutoria_estudiante, descripcion, valor) VALUES ('$id_tutoria_estudiantes','$value',1)";
                        mysqli_query($conexion, $reg_recojo_info);
                    }
                } else {
                    $contar_fallas++;
                }
            }
        }
        if ($contar_fallas == 0) {
            echo "<script>
				  alert('Se agregó correctamente');
				  window.location.replace('../tutoria_estudiantes_agregar?data=" . base64_encode($id_tutoria) . "');
			  </script>";
        } else {
            echo "<script>
			alert('Carga completa, no se pudo registrar " . $contar_fallas . " registros');
			window.history.back();
				</script>
			";
        }
    }
}
