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
    $rb_permiso = mysqli_fetch_array($b_permiso);

    $id_periodo_act = $_SESSION['acad_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['acad_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    $b_datos_sistema = buscarDatosSistema($conexion);
    $rb_datos_sistema = mysqli_fetch_array($b_datos_sistema);
    $cant_semanas = $rb_datos_sistema['cant_semanas'];

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $turno = $_POST['turno'];
        $seccion = $_POST['seccion'];

        $contar_reg_fallidos = 0;
        $cont_reg_duplicado = 0;
        $hoy = date("Y-m-d");
        if ($rb_periodo_act['fecha_fin'] >= $hoy) {
            $tipo_periodo = substr($rb_periodo_act['nombre'], 5);

            $ejec_busc_carr = buscarProgramaEstudio($conexion);
            while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                $id_carr = $res__busc_carr['id'];
                $carr = $res__busc_carr['nombre'];
                $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_carr);
                $cont_pe_sede = mysqli_num_rows($b_pe_sede);
                $rb_pe_sede = mysqli_fetch_array($b_pe_sede);
                $id_pe_sede = $rb_pe_sede['id'];
                if ($cont_pe_sede > 0 && isset($_POST['pe_' . $id_pe_sede])) {
                    // con esto verificamos que este checkeado
                    //echo "  - " . $carr . "<br>";
                    // buscamos todos los Modulos formativos
                    $b_mf = buscarModuloFormativoByIdPe($conexion, $id_carr);
                    while ($rb_mf = mysqli_fetch_array($b_mf)) {
                        $id_mf = $rb_mf['id'];
                        //echo "  - - " . $rb_mf['descripcion'] . "<br>";
                        //buscamos todas los semestres
                        $b_semestre = buscarSemestreByIdModulo_Formativo($conexion, $id_mf);
                        while ($rb_semestre = mysqli_fetch_array($b_semestre)) {
                            $id_sem = $rb_semestre['id'];
                            //echo "  - - - " . $rb_semestre['descripcion'] . "<br>";
                            // buscamos todas las unidades didacticas
                            $b_uds = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
                            while ($rb_uds = mysqli_fetch_array($b_uds)) {
                                $id_udd = $rb_uds['id'];
                                //echo "  - - - - " . $rb_uds['nombre'] . "<br>";

                                // buscamos si esta programado la unidad didactica
                                $busc_programacion_existe = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_udd, $id_pe_sede, $id_periodo_act,$turno,$seccion);
                                $conteo_b_programacion_existe = mysqli_num_rows($busc_programacion_existe);

                                //si aun no se programó, se realiza la programacion
                                if ($conteo_b_programacion_existe < 1) {

                                    switch ($tipo_periodo) {
                                        case 'I':
                                            if ($rb_semestre['descripcion'] == "I" || $rb_semestre['descripcion'] == "III" || $rb_semestre['descripcion'] == "V" || $rb_semestre['descripcion'] == "VII" || $rb_semestre['descripcion'] == "IX") {
                                                $registro_programacion = realizar_programacion($conexion, $id_udd, $id_periodo_act, $id_pe_sede, $id_usuario, $cant_semanas,$turno,$seccion);
                                                if ($registro_programacion == 0) {
                                                    $contar_reg_fallidos += 1;
                                                }
                                            }
                                            break;
                                        case 'II':
                                            if ($rb_semestre['descripcion'] == "II" || $rb_semestre['descripcion'] == "IV" || $rb_semestre['descripcion'] == "VI" || $rb_semestre['descripcion'] == "VIII" || $rb_semestre['descripcion'] == "X") {
                                                $registro_programacion = realizar_programacion($conexion, $id_udd, $id_periodo_act, $id_pe_sede, $id_usuario, $cant_semanas,$turno,$seccion);
                                                if ($registro_programacion == 0) {
                                                    $contar_reg_fallidos += 1;
                                                }
                                            }
                                            break;
                                        default:
                                            $contar_reg_fallidos += 1;
                                            break;
                                    }
                                } else {
                                    $contar_reg_fallidos += 1;
                                    $cont_reg_duplicado += 1;
                                }
                            }
                        }
                    }
                }
            }

            if ($contar_reg_fallidos > 0) {
                echo "<script>
                alert('Error al registrar Programación de " . $contar_reg_fallidos . " unidades didácticas de las cuales " . $cont_reg_duplicado . " son unidades didácticas ya programadas');
                window.location= '../programacion'
                    </script>
                ";
            } else {
                echo "<script>
                    alert('Registro y Programación Existoso');
                    window.location= '../programacion'
                    </script>";
            }
        } else {
            echo "<script>
                alert('Error, No puede Programar Unidades Didácticas Fuera de Periodo');
                window.location= '../programacion'
    			</script>";
        }
    }
}
