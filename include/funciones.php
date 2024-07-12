<?php
function generate_string($input, $strength)
{
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}
function generar_llave()
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/}{[]@#$%&*()\|';
    $llave = generate_string($permitted_chars, 30);
    return $llave;
}
function generar_contrasenia()
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/}{[]@#$%&*()\|';
    $llave = generate_string($permitted_chars, 10);
    return $llave;
}

function buscar_usuario_sesion($conexion, $id_sesion, $token)
{
    $b_sesion = buscarSesionLoginById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);
    if (password_verify($r_b_sesion['token'], $token)) {
        return $r_b_sesion['id_usuario'];
    }
    return 0;
}


function reg_sesion($conexion, $id_usuario, $token, $sistema)
{
    $fecha_hora_inicio = date("Y-m-d H:i:s");
    $fecha_hora_fin = strtotime('+2 minute', strtotime($fecha_hora_inicio));
    $fecha_hora_fin = date("Y-m-d H:i:s", $fecha_hora_fin);

    $insertar = "INSERT INTO sigi_sesiones (id_usuario,id_sistema_integrado, fecha_hora_inicio, fecha_hora_fin, token) VALUES ('$id_usuario','$sistema','$fecha_hora_inicio','$fecha_hora_fin','$token')";
    $ejecutar_insertar = mysqli_query($conexion, $insertar);
    if ($ejecutar_insertar) {
        //ultimo registro de sesion
        $id_sesion = mysqli_insert_id($conexion);
        return $id_sesion;
    } else {
        return 0;
    }
}




function sesion_si_activa($conexion, $id_sesion, $token)
{


    $hora_actuals = date("Y-m-d H:i:s");
    $hora_actual = strtotime('-1 minute', strtotime($hora_actuals));
    $hora_actual = date("Y-m-d H:i:s", $hora_actual);

    $b_sesion = buscarSesionLoginById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);

    $fecha_hora_fin_sesion = $r_b_sesion['fecha_hora_fin'];
    $fecha_hora_fin = strtotime('+8 hour', strtotime($fecha_hora_fin_sesion));
    $fecha_hora_fin = date("Y-m-d H:i:s", $fecha_hora_fin);

    if ((password_verify($r_b_sesion['token'], $token)) && ($hora_actual <= $fecha_hora_fin)) {
        actualizar_sesion($conexion, $id_sesion);
        return true;
    } else {
        return false;
    }
}
function actualizar_sesion($conexion, $id_sesion)
{
    $hora_actual = date("Y-m-d H:i:s");
    $nueva_fecha_hora_fin = strtotime('+1 minute', strtotime($hora_actual));
    $nueva_fecha_hora_fin = date("Y-m-d H:i:s", $nueva_fecha_hora_fin);

    $actualizar = "UPDATE sigi_sesiones SET fecha_hora_fin='$nueva_fecha_hora_fin' WHERE id=$id_sesion";
    mysqli_query($conexion, $actualizar);
}

function obtener_titulo_sistema($data)
{
    switch ($data) {
        case 'S_SIGI':
            $nombre = "Sistema Integrado de Gestión Institucional";
            break;
        case 'S_ACAD':
            $nombre = "SIGI - Académico";
            break;
        case 'S_TUTORIA':
            $nombre = "SIGI - Tutoría";
            break;
        case 'S_BIBLIOTECA':
            $nombre = "SIGI - Biblioteca";
            break;
        case 'S_ADMISION':
            $nombre = "SIGI - Admisión";
            break;
        default:
            $nombre = "SIGI";
            break;
    }
    return $nombre;
}

function solicitarSistemas($conexion, $datos, $tipo, $sistema)
{
    $b_datos_ies = "SELECT * FROM sigi_datos_institucionales LIMIT 1";
    $ejecutar_b = mysqli_query($conexion, $b_datos_ies);
    $rb_datos_ies = mysqli_fetch_array($ejecutar_b);

    $b_datos_sistema = "SELECT * FROM sigi_datos_sistema LIMIT 1";
    $ejecutar = mysqli_query($conexion, $b_datos_sistema);
    $rb_datos_sistema = mysqli_fetch_array($ejecutar);
    $postdata = http_build_query(
        array(
            'datos' => $datos,
            'ruc' => $rb_datos_ies['ruc'],
            'host' => $_SERVER['HTTP_HOST'],
            'token' => '' . $rb_datos_sistema['token_sistema'],
            'tipo' => $tipo,
            'sistema' => $sistema,
        )
    );
    $opts = array(
        'http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );
    $context  = stream_context_create($opts);
    $result = file_get_contents('https://sigi.cecitec.pe/consultas.php', false, $context);
    return $result;
}



function decodificar($datos)
{
    $datos = base64_decode($datos);
    $datos = unserialize($datos);
    return $datos;
}


function codificar($datos)
{
    $datos = serialize($datos);
    $datos = base64_encode($datos);
    $datos = urlencode($datos);
    return $datos;
}



function registrar_usuario_pe($conexion, $id_usuario, $id_pe, $periodo)
{
    $b_est_pe = buscarEstudianteByEst_Pe($conexion, $id_usuario, $id_pe);
    $cont_rb = mysqli_num_rows($b_est_pe);

    if ($cont_rb > 0) {
        // si ya existe
        return 0;
    } else {
        # si no existe
        $insertar = "INSERT INTO acad_estudiante_programa (id_usuario, id_programa_estudio, id_periodo) VALUES ('$id_usuario','$id_pe','$periodo')";
        $ejecutar = mysqli_query($conexion, $insertar);
        if ($ejecutar) {
            return 1;
        }
        return 0;
    }
}




//>>>>>>>>>>>>>>>>>>>>> FUNCION PARA REALIZAR PROGRAMACION DE UNIDAD DIDACTICA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

function realizar_programacion($conexion, $unidad_didactica, $id_ult_periodo,$programa_sede, $docente, $cant_semanas)
{

    $hoy = date("Y-m-d");

    $consulta = "INSERT INTO acad_programacion_unidad_didactica (id_unidad_didactica, id_docente, id_periodo_academico,id_programa_sede, supervisado, reg_evaluacion, reg_auxiliar, prog_curricular, otros, logros_obtenidos, dificultades, sugerencias) VALUES ('$unidad_didactica','$docente','$id_ult_periodo','$programa_sede', 0, 0, 0, 0, 0, '', '', '')";
    $ejec_reg_programacion = mysqli_query($conexion, $consulta);
    //buscamos el id de la programacion hecha
    $id_programacion = mysqli_insert_id($conexion);

    //crear silabo de la programacion hecha
    $metodologia = "Deductivo,Analítico,Aprendizaje basado en competencias";
    $recursos_didacticos = "Libros digitales,Foros,Chats,Video Tutoriales,Wikis,Videos explicativos";
    $sistema_evaluacion = "* La escala de calificación es vigesimal y el calificativo mínimo aprobatorio es trece (13). En todos los casos la fracción 0.5 o más se considera como una unidad a favor del estudiante.
    * El estudiante que en la evaluación de una o más Capacidades Terminales programadas en la Unidad Didáctica (Asignaturas), obtenga nota desaprobatoria entre diez (10) y doce (12), tiene derecho a participar en el proceso de recuperación programado.
    * El estudiante que después de realizado el proceso de recuperación obtuviera nota menor a trece (13), en una o más capacidades terminales de una Unidad Didáctica, desaprueba la misma, por tanto, repite la Unidad Didáctica.
    * El estudiante que acumulará inasistencias injustificadas en número igual o mayor al 30% del total de horas programadas en la Unidad Didáctica, será desaprobado en forma automática, sin derecho a recuperación.";
    $indicadores_evaluacion = "Identificación o reconocimiento del tema tratado, Valoración del dominio de los nuevos temas tratados, Capacidad de Resumen, Participación y contribución en clase, Capacidad para el trabajo en equipo";
    $tecnicas_evaluacion = "Observación,Exposición,Pruebas escritas,Estudio de caso,El debate,Exposición oral,Guías";

    $reg_silabo = "INSERT INTO acad_silabos (id_prog_unidad_didactica, id_coordinador, horario, metodologia, recursos_didacticos, sistema_evaluacion, estrategia_evaluacion_indicadores, estrategia_evaluacion_tecnica, promedio_indicadores_logro, recursos_bibliograficos_impresos, recursos_bibliograficos_digitales) VALUES ('$id_programacion','$docente',' ','$metodologia','$recursos_didacticos','$sistema_evaluacion','$indicadores_evaluacion','$tecnicas_evaluacion',' ',' ',' ')";
    $ejec_reg_silabo = mysqli_query($conexion, $reg_silabo);
    if (!$ejec_reg_silabo) {
        /*echo "<script>
			alert('Error al Registrar Silabo');
			window.history.back();
		</script>
		";*/
        return 0;
    }

    //buscamos el id del silabo registrado mediante el id de la programacion
    $id_silabo = mysqli_insert_id($conexion);


    //buscamos los indicadores de logro de la unidad didactica para evitar hacer 16 busquedas lo hacemos antes del loop for
    $busc_logro_capacidad = buscarCapacidadByIdUd($conexion, $unidad_didactica);
    $res_b_logro_capacidad = mysqli_fetch_array($busc_logro_capacidad);
    $id_capacidad = $res_b_logro_capacidad['id'];
    $id_competencia = $res_b_logro_capacidad['id_competencia'];
    $busc_ind_logro_capacidad = buscarIndCapacidadByIdCapacidad($conexion, $id_capacidad);
    $res_b_i_logro_capacidad = mysqli_fetch_array($busc_ind_logro_capacidad);
    $id_ind_logro_capacidad = $res_b_i_logro_capacidad['id']; // id indicador logro de capacidad
    $busc_ind_logro_competencia = buscarIndCompetenciasByIdCompetencia($conexion, $id_competencia);
    $res_b_i_logro_competencia = mysqli_fetch_array($busc_ind_logro_competencia);
    $id_ind_logro_competencia = $res_b_i_logro_competencia['id']; // id indicador logro competencia

    //crear la programacion del silabo la misma cantidad de semanas especificado en la tabla datos de sistema
    //echo $id_silabo."<br>";
    for ($i = 1; $i <= $cant_semanas; $i++) {

        $reg_prog_act_silabo = "INSERT INTO acad_programacion_actividades_silabo (id_silabo, semana, fecha, elemento_capacidad, actividades_aprendizaje, contenidos_basicos, tareas_previas) VALUES ('$id_silabo', '$i', '$hoy',' ',' ',' ',' ')";
        $ejec_reg_prog_act_silabo = mysqli_query($conexion, $reg_prog_act_silabo);

        //buscamos el id de lo ingresado para registrar la siguiente tabla
        $id_prog_act_silabo = mysqli_insert_id($conexion);

        //procedemos a registrar la tabla sesion_aprendizaje-- 1 para cada tabla anterior
        $reg_sesion_aprendizaje = "INSERT INTO acad_sesion_aprendizaje (id_prog_actividad_silabo, tipo_actividad, tipo_sesion, fecha_desarrollo, id_ind_logro_competencia_vinculado, id_ind_logro_capacidad_vinculado, logro_sesion, bibliografia_obligatoria_docente, bibliografia_opcional_docente, bibliografia_obligatoria_estudiante, bibliografia_opcional_estudiante, anexos) VALUES ('$id_prog_act_silabo',' ',' ','$hoy', '$id_ind_logro_competencia', '$id_ind_logro_capacidad',' ',' ',' ',' ',' ',' ')";
        $ejec_reg_sesion = mysqli_query($conexion, $reg_sesion_aprendizaje);

        //buscamos el id de la anterior tabla para hacer registros en las siguientes tablas
        $id_sesion = mysqli_insert_id($conexion);
        //echo $id_sesion." - ";
        //crearemos los momentos de la sesion 3 por cada sesion Inicio, Desarrollo y cierre
        for ($j = 1; $j <= 3; $j++) {
            if ($j == 1) {
                $momento = "Inicio";
            }
            if ($j == 2) {
                $momento = "Desarrollo";
            }
            if ($j == 3) {
                $momento = "Cierre";
            }
            // momentos de la sesion
            $reg_momentos_sesion = "INSERT INTO acad_momentos_sesion_aprendizaje (id_sesion_aprendizaje, momento, estrategia, actividad, recursos, tiempo) VALUES ('$id_sesion', '$momento',' ',' ',' ',10)";
            $ejec_reg_momentos_sesion = mysqli_query($conexion, $reg_momentos_sesion);

            // actividad de evaluacion en la sesion
            $reg_act_eva = "INSERT INTO acad_actividad_evaluacion_sesion_aprendizaje (id_sesion_aprendizaje, indicador_logro_sesion, tecnica, instrumentos, peso, momento) VALUES ('$id_sesion',' ',' ',' ',33,'$momento')";
            $ejec_reg_act_eva = mysqli_query($conexion, $reg_act_eva);
        }
    }
    //echo "<br>";
    if (!$ejec_reg_prog_act_silabo) {
        /*  echo "<script>
			alert('Error al Generar las programacion para el silabo');
			window.history.back();
		</script>
		";*/
        return 0;
    }
    if (!$ejec_reg_sesion) {
        /*echo "<script>
			alert('Error al Registrar las sesiones de aprendizaje');
			window.history.back();
		</script>
		";*/
        return 0;
    }
    if (!$ejec_reg_momentos_sesion) {
        /*echo "<script>
			        alert('Error al Registrar Momentos de la sesion M1');
			        window.history.back();
		        </script>
		        ";*/
        return 0;
    }
    if (!$ejec_reg_act_eva) {
        /* echo "<script>
			        alert('Error al Registrar Momentos de la sesion M2');
			        window.history.back();
		        </script>
		        ";*/
        return 0;
    }

    /*echo "<script>
                
                window.location= '../programacion.php'
    			</script>";*/
    return 1;
}


//>>>>>>>>>>>>>>>>>>>>> FIN DE FUNCION PARA REALIZAR PROGRAMACION DE UNIDAD DIDACTICA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


