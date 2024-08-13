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
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/}{[]@#$%&*()|';
    $llave = generate_string($permitted_chars, 30);
    return $llave;
}
function generar_contrasenia()
{
    $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ/}{[]@#$%&*()|';
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
        case 'S_BIBLIO':
            $nombre = "SIGI - Biblioteca";
            break;
        case 'S_ADMISION':
            $nombre = "SIGI - Admisión";
            break;
        case 'S_BOLSA':
            $nombre = "SIGI - Bolsa Laboral";
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
    $b_est_pe = buscarEstudiantePeByEst_Pe($conexion, $id_usuario, $id_pe);
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

function realizar_programacion($conexion, $unidad_didactica, $id_ult_periodo, $programa_sede, $docente, $cant_semanas, $turno, $seccion)
{

    $hoy = date("Y-m-d");

    $consulta = "INSERT INTO acad_programacion_unidad_didactica (id_unidad_didactica, id_docente, id_periodo_academico,id_programa_sede,turno,seccion, supervisado, reg_evaluacion, reg_auxiliar, prog_curricular, otros, logros_obtenidos, dificultades, sugerencias) VALUES ('$unidad_didactica','$docente','$id_ult_periodo','$programa_sede','$turno','$seccion', 0, 0, 0, 0, 0, '', '', '')";
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


//>>>>>>>>>>>>>>>>>>>>> INICIO DE FUNCION PARA VER LA CANTIDAD DE CRITERIOS DE EVALUACION <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
function buscar_cantidad_criterios_programacion($conexion, $id_prog, $det_evaluacion, $nro_calif)
{
    $b_det_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
    if (mysqli_num_rows($b_det_mat) < 1) {
        // si no hay ningun matriculado regresamos 5 como los criterios de evaluacion
        return 2;
    }
    $r_b_det_mat = mysqli_fetch_array($b_det_mat);

    $b_califacion = buscarCalificacionByIdDetalleMatricula_nro($conexion, $r_b_det_mat['id'], $nro_calif);
    $r_b_calificacion = mysqli_fetch_array($b_califacion);

    $b_evaluacion = buscarEvaluacionByIdCalificacion_detalle($conexion, $r_b_calificacion['id'], $det_evaluacion);
    $r_b_evaluacion = mysqli_fetch_array($b_evaluacion);

    $b_crit_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $r_b_evaluacion['id']);
    $cant_crit = mysqli_num_rows($b_crit_evaluacion);
    if ($cant_crit < 1) {
        return 2;
    } else {
        return $cant_crit;
    }
}
//>>>>>>>>>>>>>>>>>>>>> FIN DE FUNCION PARA VER LA CANTIDAD DE CRITERIOS DE EVALUACION <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


//>>>>>>>>>>>>>>>>>>>>> INICIO DE FUNCION PARA REGISTRAR DETALLE DE MATRICULA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
function registrar_detalle_matricula($conexion, $valor, $id_matricula)
{
    //$valor es el id_de programacion de unidad didactica
    //buscaremos el id de la unidad didactica en la programacion de unidades didacticas
    $busc_prog = buscarProgramacionUDById($conexion, $valor);
    $res_b_prog = mysqli_fetch_array($busc_prog);
    $id_ud = $res_b_prog['id_unidad_didactica'];

    //buscamos la cantidad de matriculados para ingresar el orden correcto
    $b_cant_mat_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $valor);
    $cont_r_b_cant_mat_det_mat = mysqli_num_rows($b_cant_mat_detalle_mat);
    $new_orden = $cont_r_b_cant_mat_det_mat + 1;

    //REGISTRAMOS EL DETALLE DE LA MATRICULA
    $reg_det_mat =  "INSERT INTO acad_detalle_matricula (id_matricula, orden, id_programacion_ud, recuperacion, mostrar_calificacion) VALUES ('$id_matricula','$new_orden','$valor','',0)";
    $ejecutar_reg_det_mat = mysqli_query($conexion, $reg_det_mat);

    //buscamos el ultimo registro de detalle matricula
    $id_detalle_matricula = mysqli_insert_id($conexion);


    //buscamos la capacidad de la unidad didactica
    $busc_capacidad = buscarCapacidadByIdUd($conexion, $id_ud);
    $orden = 1; //orden en el que inicia las calificaciones

    while ($res_b_capacidad = mysqli_fetch_array($busc_capacidad)) {
        $id_capacidad = $res_b_capacidad['id'];

        // buscar indicadores de logro de capacidad para saber cuantos calificaciones crearemos
        $b_indicador = buscarIndCapacidadByIdCapacidad($conexion, $id_capacidad);

        while ($res_b_capacidad = mysqli_fetch_array($b_indicador)) {
            //REGISTRAMOS LAS CALIFICACION SEGUN LA CANTIDAD DE INDICADORES DE LOGRO
            $reg_calificacion = "INSERT INTO acad_calificacion (id_detalle_matricula, nro_calificacion, mostrar_calificacion) VALUES ('$id_detalle_matricula','$orden',0)";
            $ejecutar_reg_calificacion = mysqli_query($conexion, $reg_calificacion);

            $id_calificacion = mysqli_insert_id($conexion);
            $ponderado_evaluacion = round(100 / 3);
            //registramos las evaluaciones para las calificaciones - se crearan 3 --> conceptual, procedimental y actitudinal
            for ($i = 1; $i <= 3; $i++) {
                if ($i == 1) {
                    $det_eva = "Conceptual";
                };
                if ($i == 2) {
                    $det_eva = "Procedimental";
                };
                if ($i == 3) {
                    $det_eva = "Actitudinal";
                };
                $reg_evaluacion = "INSERT INTO acad_evaluacion (id_calificacion, detalle, ponderado) VALUES ('$id_calificacion','$det_eva','$ponderado_evaluacion')";
                $ejecutar_reg_evaluacion = mysqli_query($conexion, $reg_evaluacion);

                $id_evaluacion = mysqli_insert_id($conexion);
                //buscamos matriculas en la programacion de unidad didactica para saber cuantos criterios agregar 
                // si no existen matriculas por defecto agregara 2 criterios, haya matriculas, contara los criterios de una de las matriculas
                $cant_crit_eva = buscar_cantidad_criterios_programacion($conexion, $valor, $det_eva, $orden);


                $ponderado_c_evaluacion = round(100 / $cant_crit_eva);
                // registramos los criterios de evaluacion para cada evaluacion
                for ($j = 1; $j <= $cant_crit_eva; $j++) {
                    $reg_criterio_evaluacion = "INSERT INTO acad_criterio_evaluacion (id_evaluacion, orden, detalle,calificacion) VALUES ('$id_evaluacion','$j','','')";
                    $ejecutar_reg_criterio_evaluacion = mysqli_query($conexion, $reg_criterio_evaluacion);
                }
            }
            $orden = $orden + 1;
        }
    }
    //procedemos a crear el registro de asistencia para cada sesion de la ud programada
    $id_programacion = $valor;
    //buscar silabo
    $b_silabo = buscarSilabosByIdProgramacion($conexion, $id_programacion);
    $r_b_silabo = mysqli_fetch_array($b_silabo);
    $id_silabo = $r_b_silabo['id'];
    //buscar programacion de actividades de silabo
    $b_prog_act_silabo = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);

    while ($r_b_prog_act_silabo = mysqli_fetch_array($b_prog_act_silabo)) {
        $id_prog_act_silabo = $r_b_prog_act_silabo['id'];
        //buscamos las sesiones de aprendizaje para generar las asistencias
        $b_sesion_apre = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_prog_act_silabo);
        while ($r_b_sesion_apre = mysqli_fetch_array($b_sesion_apre)) {
            //generamos asistencia para cada sesion de aprendizaje
            $id_sesion = $r_b_sesion_apre['id'];
            $r_asistencia = "INSERT INTO acad_asistencia (id_sesion_aprendizaje, id_detalle_matricula, asistencia) VALUES ('$id_sesion','$id_detalle_matricula','')";
            $ejecutar_r_asistencia = mysqli_query($conexion, $r_asistencia);
        }
    }
}
//>>>>>>>>>>>>>>>>>>>>> FIN DE FUNCION PARA VER REGISTRAR DETALLE DE MATRICULA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<





//>>>>>>>>>>>>>>>>>>>>> INICIO DE FUNCION PARA ORDENAR ESTUDIANTES EN UNA UNIDAD DIDACTA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
function OrdenarEstudiantesUnidadDidactica($conexion, $id_prog)
{

    //ORDENAMIENTO DE ESTUDIANTES
    $array_in = [];
    $b_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
    while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
        $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
        $r_b_mat = mysqli_fetch_array($b_matricula);
        $b_estudiante = buscarUsuarioById($conexion, $r_b_mat['id_estudiante']);
        $r_b_est = mysqli_fetch_array($b_estudiante);
        $array_in[] = $r_b_est['apellidos_nombres'];
    }

    $collator = collator_create("es");
    $collator->sort($array_in);

    $b_detalle_mat = buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog);
    while ($r_b_det_mat = mysqli_fetch_array($b_detalle_mat)) {
        $id_det_mat = $r_b_det_mat['id'];
        $mostrar_calif_final = $r_b_det_mat['mostrar_calificacion'];
        $b_matricula = buscarMatriculaById($conexion, $r_b_det_mat['id_matricula']);
        $r_b_mat = mysqli_fetch_array($b_matricula);
        $b_estudiante = buscarUsuarioById($conexion, $r_b_mat['id_estudiante']);
        $r_b_est = mysqli_fetch_array($b_estudiante);
        $indice = array_search($r_b_est['apellidos_nombres'], $array_in) + 1;
        //echo $indice." - ".$r_b_est['apellidos_nombres']."<br>";
        $consulta = "UPDATE acad_detalle_matricula SET orden='$indice' WHERE id='$id_det_mat'";
        mysqli_query($conexion, $consulta);
    }
}



//>>>>>>>>>>>>>>>>>>>>> FIN DE FUNCION PARA ORDENAR ESTUDIANTES EN UNA UNIDAD DIDACTA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


//>>>>>>>>>>>>>>>>>>>>> INICIO DE FUNCION PARA CONTAR ASISTENCIA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

function contar_inasistencia($conexion, $id_silabo, $id_det_mat)
{
    $b_prog_act_sil = buscarProgActividadesSilaboByIdSilabo($conexion, $id_silabo);
    $cont_inasistencia = 0;
    while ($r_prog_act_sil = mysqli_fetch_array($b_prog_act_sil)) {
        $id_prog_act_s = $r_prog_act_sil['id'];
        $b_sesion_aprendizaje = buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id_prog_act_s);
        $r_b_sesion_apr = mysqli_fetch_array($b_sesion_aprendizaje);
        $id_sesion_apr = $r_b_sesion_apr['id'];

        $b_asistencia = buscarAsistenciaBySesionAndDetalleMatricula($conexion, $id_sesion_apr, $id_det_mat);
        $r_b_asistencia = mysqli_fetch_array($b_asistencia);

        if ($r_b_asistencia['asistencia'] == "F") {
            $cont_inasistencia += 1;
        }
    }
    return $cont_inasistencia;
}
//>>>>>>>>>>>>>>>>>>>>> FIN DE FUNCION PARA CONTAR ASISTENCIA <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<




// funciones para calificaciones --------------------------------------------------------

//funcion para calcular promedio de los criterios de evaluacion
function calc_criterios($conexion, $id_evaluacion)
{
    $b_criterio_evaluacion = buscarCriterioEvaluacionByEvaluacion($conexion, $id_evaluacion);
    $suma_criterios = 0;
    $cont_crit = 0;
    while ($r_b_criterio_evaluacion = mysqli_fetch_array($b_criterio_evaluacion)) {
        if (is_numeric($r_b_criterio_evaluacion['calificacion'])) {
            $suma_criterios += $r_b_criterio_evaluacion['calificacion'];
            $cont_crit += 1;
            //$suma_criterios += (($r_b_criterio_evaluacion['ponderado']/100)*$r_b_criterio_evaluacion['calificacion']);
        }
    }
    if ($cont_crit > 0) {
        $suma_criterios = round($suma_criterios / $cont_crit);
    } else {
        $suma_criterios = round($suma_criterios);
    }
    return $suma_criterios;
}

//funcion para calcular la la evaluacion(criterio de evaluacion) por ponderado
function calc_evaluacion($conexion, $id_calificacion)
{
    $suma_evaluacion = 0;

    $b_evaluacion = buscarEvaluacionByIdCalificacion($conexion, $id_calificacion);
    while ($r_b_evaluacion = mysqli_fetch_array($b_evaluacion)) {
        $id_evaluacion = $r_b_evaluacion['id'];
        //buscamos los criterios de evaluacion
        $suma_criterios = calc_criterios($conexion, $id_evaluacion);

        if (is_numeric($r_b_evaluacion['ponderado'])) {
            $suma_evaluacion += ($r_b_evaluacion['ponderado'] / 100) * $suma_criterios;
        }
    }
    return round($suma_evaluacion);
}


//funcion para calcular la cantidad de ud desaprobadas de esrudiantes

function calc_ud_desaprobado_sin_recuperacion($conexion, $id_est, $per_select, $sede, $id_programa_sede, $id_sem, $turno, $seccion)
{

    //buscar si estudiante esta matriculado en una unidad didactica
    $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);

    $cont_ud_desaprobadas = 0;
    while ($r_bb_ud = mysqli_fetch_array($b_ud_pe_sem)) {
        $id_udd = $r_bb_ud['id'];

        $b_prog_ud = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_udd, $id_programa_sede, $per_select, $turno, $seccion);
        $r_b_prog_ud = mysqli_fetch_array($b_prog_ud);
        $id_prog = $r_b_prog_ud['id'];

        //buscar matricula de estudiante
        $b_mat_est = buscarMatriculaByEstPeriodoSede($conexion, $id_est, $per_select, $sede);
        $r_b_mat_est = mysqli_fetch_array($b_mat_est);
        $id_mat_est = $r_b_mat_est['id'];
        //buscar detalle de matricula
        $b_det_mat_est = buscarDetalleMatriculaByIdMatriculaAndProgrmacion($conexion, $id_mat_est, $id_prog);
        $r_b_det_mat_est = mysqli_fetch_array($b_det_mat_est);
        $cont_r_b_det_mat = mysqli_num_rows($b_det_mat_est);
        $id_det_mat = $r_b_det_mat_est['id'];
        if ($cont_r_b_det_mat > 0) {
            //echo "<td>SI</td>";

            //buscar las calificaciones
            $b_calificaciones = buscarCalificacionByIdDetalleMatricula($conexion, $id_det_mat);

            $suma_calificacion = 0;
            $cont_calif = 0;
            while ($r_b_calificacion = mysqli_fetch_array($b_calificaciones)) {

                $id_calificacion = $r_b_calificacion['id'];
                //buscamos las evaluaciones
                $suma_evaluacion = calc_evaluacion($conexion, $id_calificacion);

                $suma_calificacion += $suma_evaluacion;
                if ($suma_evaluacion > 0) {
                    $cont_calif += 1;
                }
            }
            if ($cont_calif > 0) {
                $calificacion = round($suma_calificacion / $cont_calif);
            } else {
                $calificacion = round($suma_calificacion);
            }
            if ($calificacion != 0) {
                $calificacion = round($calificacion);
            } else {
                $calificacion = "";
            }
            //buscamos si tiene recuperacion
            /*if ($r_b_det_mat_est['recuperacion'] != '') {
                $calificacion = $r_b_det_mat_est['recuperacion'];
            }*/

            if ($calificacion > 12) {
                //echo '<td align="center" ><font color="blue">' . $calificacion . '</font></td>';
            } else {
                //echo '<td align="center" ><font color="red">' . $calificacion . '</font></td>';
                $cont_ud_desaprobadas += 1;
            }
        } else {
            $calificacion = 0;
            //echo '<td></td>';
        }
    }
    return $cont_ud_desaprobadas;
}



// funcion para calcular si estudiante se matriculo a todas las ud del semestre (en caso de repitencia)

function calcular_mat_ud($conexion, $id_est, $per_select, $sede, $id_programa_sede, $id_sem, $turno, $seccion)
{
    //buscar si estudiante esta matriculado en una unidad didactica
    $b_ud_pe_sem = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);
    $cant_ud_sem = mysqli_num_rows($b_ud_pe_sem);

    $cant_matt = 0;

    while ($r_bb_ud = mysqli_fetch_array($b_ud_pe_sem)) {
        $id_udd = $r_bb_ud['id'];

        $b_prog_ud = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_udd, $id_programa_sede, $per_select, $turno, $seccion);
        $r_b_prog_ud = mysqli_fetch_array($b_prog_ud);
        $id_prog = $r_b_prog_ud['id'];

        //buscar matricula de estudiante
        $b_mat_est = buscarMatriculaByEstPeriodoSede($conexion, $id_est, $per_select, $sede);
        $r_b_mat_est = mysqli_fetch_array($b_mat_est);
        $id_mat_est = $r_b_mat_est['id'];
        //buscar detalle de matricula
        $b_det_mat_est = buscarDetalleMatriculaByIdMatriculaAndProgrmacion($conexion, $id_mat_est, $id_prog);
        $cont_r_b_det_mat = mysqli_num_rows($b_det_mat_est);

        if ($cont_r_b_det_mat > 0) {
            $cant_matt += 1;
        } else {
        }
    }

    if ($cant_ud_sem === $cant_matt) {
        return 1;
    }
    return 0;
}
