<?php

// usuarios
function buscarUsuarioById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioByDni($conexion, $dni)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE dni='$dni'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioByDniCorreo($conexion, $dni, $correo)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE dni='$dni' AND correo='$correo'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioCoordinador_sede($conexion, $sede)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol=4 AND id_sede='$sede'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioDirector_All($conexion)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol=1";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioDocentes($conexion)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol<=5";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioDocentesBySede($conexion, $sede)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol<=5 AND id_sede='$sede'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioDocentesOrderByApellidosNombres($conexion)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol<=5 ORDER BY apellidos_nombres";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioDocentesOrderByApellidosNombresAndSede($conexion, $sede)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol<=5 AND id_sede='$sede' ORDER BY apellidos_nombres";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioEstudiantesBySede($conexion, $sede)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol=6 AND id_sede='$sede'";
	return mysqli_query($conexion, $sql);
}
function buscarUsuarioEstudiantesBySedePeriodo($conexion, $sede, $periodo)
{
	$sql = "SELECT * FROM sigi_usuarios WHERE id_rol=6 AND id_sede='$sede' AND id_periodo_registro='$periodo'";
	return mysqli_query($conexion, $sql);
}

function buscarEstudiantePeById_est($conexion, $id_usu)
{
	$sql = "SELECT * FROM acad_estudiante_programa WHERE id_usuario='$id_usu'";
	return mysqli_query($conexion, $sql);
}
function buscarEstudiantePeByEst_Pe($conexion, $id_usu, $id_pe)
{
	$sql = "SELECT * FROM acad_estudiante_programa WHERE id_usuario='$id_usu' AND id_programa_estudio='$id_pe'";
	return mysqli_query($conexion, $sql);
}

//roles
function buscarRol($conexion)
{
	$sql = "SELECT * FROM sigi_roles";
	return mysqli_query($conexion, $sql);
}
function buscarRolById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_roles WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}


//Sedes
function buscarSede($conexion)
{
	$sql = "SELECT * FROM sigi_sedes";
	return mysqli_query($conexion, $sql);
}
function buscarSedeById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_sedes WHERE id=$id";
	return mysqli_query($conexion, $sql);
}


//programas de estudios
function buscarProgramaEstudio($conexion)
{
	$sql = "SELECT * FROM sigi_programa_estudios";
	return mysqli_query($conexion, $sql);
}
function buscarProgramaEstudioById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_programa_estudios WHERE id=$id";
	return mysqli_query($conexion, $sql);
}

//programas de estudios - Sedes
function buscarProgramaEstudioSede($conexion)
{
	$sql = "SELECT * FROM sigi_programa_sede";
	return mysqli_query($conexion, $sql);
}
function buscarProgramaEstudioSedeById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_programa_sede WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarProgramaEstudioSedeByIdSede($conexion, $id_sede)
{
	$sql = "SELECT * FROM sigi_programa_sede WHERE id_sede=$id_sede";
	return mysqli_query($conexion, $sql);
}
function buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede, $id_pe)
{
	$sql = "SELECT * FROM sigi_programa_sede WHERE id_sede=$id_sede AND id_programa_estudio=$id_pe";
	return mysqli_query($conexion, $sql);
}

//modulos formativos
function buscarModulosFormativos($conexion)
{
	$sql = "SELECT * FROM sigi_modulo_formativo";
	return mysqli_query($conexion, $sql);
}
function buscarModuloFormativoById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_modulo_formativo WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarModuloFormativoByIdPe($conexion, $id)
{
	$sql = "SELECT * FROM sigi_modulo_formativo WHERE id_programa_estudio=$id";
	return mysqli_query($conexion, $sql);
}

//semestre
function buscarSemestres($conexion)
{
	$sql = "SELECT * FROM sigi_semestre";
	return mysqli_query($conexion, $sql);
}
function buscarSemestreById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_semestre WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarSemestreByIdModulo_Formativo($conexion, $id)
{
	$sql = "SELECT * FROM sigi_semestre WHERE id_modulo_formativo=$id";
	return mysqli_query($conexion, $sql);
}

//unidades didacticas
function buscarUnidadDidactica($conexion)
{
	$sql = "SELECT * FROM sigi_unidad_didactica";
	return mysqli_query($conexion, $sql);
}
function buscarUnidadDidacticaById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_unidad_didactica WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarUdByName($conexion, $nombre){
	$sql = "SELECT * FROM sigi_unidad_didactica WHERE nombre='$nombre'";
	return mysqli_query($conexion, $sql);
}
function buscarUnidadDidacticaByIdSemestre($conexion, $id)
{
	$sql = "SELECT * FROM sigi_unidad_didactica WHERE id_semestre=$id";
	return mysqli_query($conexion, $sql);
}

//unidades competencias
function buscarCompetencias($conexion)
{
	$sql = "SELECT * FROM sigi_competencias";
	return mysqli_query($conexion, $sql);
}
function buscarCompetenciasById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_competencias WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarCompetenciasByIdModuloFormativo($conexion, $id)
{
	$sql = "SELECT * FROM sigi_competencias WHERE id_modulo_formativo=$id";
	return mysqli_query($conexion, $sql);
}
function buscarCompetenciasEspecialidadByIdModulo($conexion, $id){
	$sql = "SELECT * FROM sigi_competencias WHERE id_modulo_formativo='$id' AND tipo='ESPECÍFICA'";
	return mysqli_query($conexion, $sql);
}

// indicadores de logro de competencia
function buscarIndCompetencias($conexion)
{
	$sql = "SELECT * FROM sigi_ind_logro_competencia";
	return mysqli_query($conexion, $sql);
}
function buscarIndCompetenciasById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_ind_logro_competencia WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarIndCompetenciasByIdCompetencia($conexion, $id)
{
	$sql = "SELECT * FROM sigi_ind_logro_competencia WHERE id_competencia=$id";
	return mysqli_query($conexion, $sql);
}


//unidades capacidad
function buscarCapacidad($conexion)
{
	$sql = "SELECT * FROM sigi_capacidades";
	return mysqli_query($conexion, $sql);
}
function buscarCapacidadById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_capacidades WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarCapacidadByIdUd($conexion, $id)
{
	$sql = "SELECT * FROM sigi_capacidades WHERE id_unidad_didactica=$id";
	return mysqli_query($conexion, $sql);
}

// indicadores de logro de capacidad
function buscarIndCapacidad($conexion)
{
	$sql = "SELECT * FROM sigi_ind_logro_capacidad";
	return mysqli_query($conexion, $sql);
}
function buscarIndCapacidadById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_ind_logro_capacidad WHERE id=$id";
	return mysqli_query($conexion, $sql);
}
function buscarIndCapacidadByIdCapacidad($conexion, $id)
{
	$sql = "SELECT * FROM sigi_ind_logro_capacidad WHERE id_capacidad=$id";
	return mysqli_query($conexion, $sql);
}


// busquedas permisos usuarios
function buscarPermisoUsuarioByUsuarioSistema($conexion, $usuario, $sistema)
{
	$sql = "SELECT * FROM sigi_permisos_usuarios WHERE id_usuario='$usuario' AND id_sistema='$sistema'";
	return mysqli_query($conexion, $sql);
}
function buscarPermisoUsuarioByUsuarioSistemaRol($conexion, $usuario, $sistema, $rol)
{
	$sql = "SELECT * FROM sigi_permisos_usuarios WHERE id_usuario='$usuario' AND id_sistema='$sistema' AND id_rol='$rol'";
	return mysqli_query($conexion, $sql);
}


// sesiones
function buscarSesionLoginById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_sesiones WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}

// periodos academicos
function buscarPeriodoAcademico($conexion)
{
	$sql = "SELECT * FROM sigi_periodo_academico";
	return mysqli_query($conexion, $sql);
}
function buscarPeriodoAcademicoInvert($conexion)
{
	$sql = "SELECT * FROM sigi_periodo_academico ORDER BY id DESC";
	return mysqli_query($conexion, $sql);
}
function buscarPeriodoAcadById($conexion, $id)
{
	$sql = "SELECT * FROM sigi_periodo_academico WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarPresentePeriodoAcad($conexion)
{
	$sql = "SELECT * FROM sigi_periodo_academico ORDER BY id DESC LIMIT 1";
	return mysqli_query($conexion, $sql);
}


// busquedas sistemas
function buscarSistemas($conexion)
{
	$sql = "SELECT * FROM sigi_sistemas_integrados";
	return mysqli_query($conexion, $sql);
}
function buscarSistemaByCodigo($conexion, $sistema)
{
	$sql = "SELECT * FROM sigi_sistemas_integrados WHERE codigo='$sistema'";
	return mysqli_query($conexion, $sql);
}


// DATOS INSTICUCIONALES
function buscarDatosInstitucional($conexion)
{
	$sql = "SELECT * FROM sigi_datos_institucionales LIMIT 1";
	return mysqli_query($conexion, $sql);
}
// DATOS SISTEMA
function buscarDatosSistema($conexion)
{
	$sql = "SELECT * FROM sigi_datos_sistema LIMIT 1";
	return mysqli_query($conexion, $sql);
}



// --------------------------------------------------------- SISTEMA ACADEMICO ------------------------------------------------------

//PROGRAMACION DE UNIDADES DIDACTICAS

function buscarProgramacionUDById($conexion, $id)
{
	$sql = "SELECT * FROM acad_programacion_unidad_didactica WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarProgramacionUDByIdUd($conexion, $id)
{
	$sql = "SELECT * FROM acad_programacion_unidad_didactica WHERE id_unidad_didactica='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarProgramacionUDByPeriodoSede($conexion, $periodo, $programasede)
{
	$sql = "SELECT * FROM acad_programacion_unidad_didactica WHERE id_periodo_academico='$periodo' AND id_programa_sede='$programasede'";
	return mysqli_query($conexion, $sql);
}
function buscarProgramacionByUd_Peridodo_ProgramaSede($conexion, $unidad_didactica, $programa_sede, $periodo_actual){
	$sql = "SELECT * FROM acad_programacion_unidad_didactica WHERE id_unidad_didactica='$unidad_didactica' AND id_periodo_academico='$periodo_actual' AND id_programa_sede='$programa_sede'";
	return mysqli_query($conexion, $sql);
}
function buscarProgramacionByDocente_Peridodo_ProgramaSede($conexion, $docente, $programa_sede, $periodo_actual){
	$sql = "SELECT * FROM acad_programacion_unidad_didactica WHERE id_docente='$docente' AND id_periodo_academico='$periodo_actual' AND id_programa_sede='$programa_sede'";
	return mysqli_query($conexion, $sql);
}

//SILABOS
function buscarSilabosById($conexion, $id)
{
	$sql = "SELECT * FROM acad_silabos WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarSilabosByIdProgramacion($conexion, $id)
{
	$sql = "SELECT * FROM acad_silabos WHERE id_prog_unidad_didactica='$id'";
	return mysqli_query($conexion, $sql);
}

// PROGRAMACION ACTIVIDADES DE SILABO
function buscarProgActividadesSilaboById($conexion, $id){
	$sql = "SELECT * FROM acad_programacion_actividades_silabo WHERE id='$id' ORDER BY semana";
	return mysqli_query($conexion, $sql);
}
function buscarProgActividadesSilaboByIdSilabo($conexion, $id){
	$sql = "SELECT * FROM acad_programacion_actividades_silabo WHERE id_silabo='$id' ORDER BY semana";
	return mysqli_query($conexion, $sql);
}
function buscarProgActividadesSilaboByIdSilabo_16($conexion, $id){
	$sql = "SELECT * FROM acad_programacion_actividades_silabo WHERE id_silabo='$id' ORDER BY semana LIMIT 16";
	return mysqli_query($conexion, $sql);
}
function buscarProgActividadesSilaboByIdSilaboAndSemana($conexion, $idSilabo, $semana){
	$sql = "SELECT * FROM acad_programacion_actividades_silabo WHERE id_silabo='$idSilabo' AND semana='$semana'";
	return mysqli_query($conexion, $sql);
}

// SESIONES DE APRENDIZAJE
function buscarSesionAprendizajeById($conexion, $id){
	$sql = "SELECT * FROM acad_sesion_aprendizaje WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarSesionAprendizajeByIdProgActividadesSilabo($conexion, $id){
	$sql = "SELECT * FROM acad_sesion_aprendizaje WHERE id_prog_actividad_silabo='$id'";
	return mysqli_query($conexion, $sql);
}

//MOMENTOS SESION DE APRENDIZAJE
function buscarMomentosSesionAprendizajeById($conexion, $id){
	$sql = "SELECT * FROM acad_momentos_sesion_aprendizaje WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarMomentosSesionAprendizajeByIdSesion($conexion, $id){
	$sql = "SELECT * FROM acad_momentos_sesion_aprendizaje WHERE id_sesion_aprendizaje='$id'";
	return mysqli_query($conexion, $sql);
}

//ACTIVIDAD DE EVALUACION DE SESION DE APRENDIZAJE
function buscarActividadEvaluacionById($conexion, $id){
	$sql = "SELECT * FROM acad_actividad_evaluacion_sesion_aprendizaje WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarActividadEvaluacionByIdSesion($conexion, $id){
	$sql = "SELECT * FROM acad_actividad_evaluacion_sesion_aprendizaje WHERE id_sesion_aprendizaje='$id'";
	return mysqli_query($conexion, $sql);
}


// MATRICULAS

function buscarMatriculaByPeriodoSede($conexion, $periodo, $sede)
{
	$sql = "SELECT * FROM acad_matricula WHERE id_periodo_academico='$periodo' AND id_sede='$sede'";
	return mysqli_query($conexion, $sql);
}
function buscarMatriculaByEstPeriodoSede($conexion,$estudiante, $periodo, $sede)
{
	$sql = "SELECT * FROM acad_matricula WHERE id_periodo_academico='$periodo' AND id_sede='$sede' AND id_estudiante='$estudiante'";
	return mysqli_query($conexion, $sql);
}

// DETALLE DE MATRICULA
function buscarDetalleMatriculaByIdProgramacion($conexion, $id_prog){
	$sql = "SELECT * FROM acad_detalle_matricula WHERE id_programacion_ud='$id_prog'";
	return mysqli_query($conexion, $sql);
}

// CALIFICACIONES

function buscarCalificacionById($conexion, $id){
	$sql = "SELECT * FROM acad_calificacion WHERE id='$id'";
	return mysqli_query($conexion, $sql);
}
function buscarCalificacionByIdDetalleMatricula($conexion, $id_detalle){
	$sql = "SELECT * FROM acad_calificacion WHERE id_detalle_matricula = '$id_detalle' ORDER BY nro_calificacion";
	return mysqli_query($conexion, $sql);
}
function buscarCalificacionByIdDetalleMatricula_nro($conexion, $id_det_mat, $nro_calif){
	$sql = "SELECT * FROM acad_calificacion WHERE id_detalle_matricula = '$id_det_mat' AND nro_calificacion='$nro_calif' ORDER BY nro_calificacion";
	return mysqli_query($conexion, $sql);
}

// EVALUACION
function buscarEvaluacionById($conexion, $id){
	$sql = "SELECT * FROM acad_evaluacion WHERE id = '$id'";
	return mysqli_query($conexion, $sql);
}
function buscarEvaluacionByIdCalificacion($conexion, $id){
	$sql = "SELECT * FROM acad_evaluacion WHERE id_calificacion = '$id' ORDER BY id";
	return mysqli_query($conexion, $sql);
}
function buscarEvaluacionByIdCalificacion_detalle($conexion, $id, $detalle){
	$sql = "SELECT * FROM acad_evaluacion WHERE id_calificacion = '$id' AND detalle='$detalle' ORDER BY id";
	return mysqli_query($conexion, $sql);
}

// CRITERIO DE EVALUACION
function buscarCriterioEvaluacionByEvaluacion($conexion, $id){
	$sql = "SELECT * FROM acad_criterio_evaluacion WHERE id_evaluacion = '$id' ORDER BY orden";
	return mysqli_query($conexion, $sql);
}
function buscarCriterioEvaluacionByEvaluacionOrden($conexion, $id, $orden){
	$sql = "SELECT * FROM acad_criterio_evaluacion WHERE id_evaluacion = '$id' AND orden='$orden' ORDER BY orden";
	return mysqli_query($conexion, $sql);
}


// ASISTENCIAS

function buscarAsistenciaByIdSesion($conexion, $id_sesion){
	$sql = "SELECT * FROM acad_asistencia WHERE id_sesion_aprendizaje='$id_sesion'";
	return mysqli_query($conexion, $sql);
}
function buscarAsistenciaBySesionAndDetalleEstudiante($conexion, $id, $detalle_mat){
	$sql = "SELECT * FROM acad_asistencia WHERE id_sesion_aprendizaje='$id' AND id_detalle_matricula='$detalle_mat'";
	return mysqli_query($conexion, $sql);
}