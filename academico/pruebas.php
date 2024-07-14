<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
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

    $id_sesion = base64_decode($_GET['data']);
    $b_sesion = buscarSesionAprendizajeById($conexion, $id_sesion);
    $r_b_sesion = mysqli_fetch_array($b_sesion);
    $id_prog_act = $r_b_sesion['id_programacion_actividad_silabo'];
    // buscamos datos de la programacion de actividades
    $b_prog_act = buscarProgActividadesSilaboById($conexion, $id_prog_act);
    $r_b_prog_act = mysqli_fetch_array($b_prog_act);
    $id_silabo = $r_b_prog_act['id_silabo'];
    // buscamos datos de silabo
    $b_silabo = buscarSilabosById($conexion, $id_silabo);
    $r_b_silabo = mysqli_fetch_array($b_silabo);
    $id_prog = $r_b_silabo['id_programacion_unidad_didactica'];
    //buscamos datos de la programacion de unidad didactica
    $b_prog = buscarProgramacionUDById($conexion, $id_prog);
    $res_b_prog = mysqli_fetch_array($b_prog);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../academico/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
    } else {

?>

        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <th colspan="2" style="text-align: center;"><b>I. INFORMACIÓN GENERAL</b></th>
            </tr>
            <tr>
                <td style="width: 30%;">Docente a cargo</td>
                <td style="width: 70%;">: Tec. YUCRA CURO ANIBAL</td>
            </tr>
            <tr>
                <td>Periodo Académico</td>
                <td>: 2024-I</td>
            </tr>
            <tr>
                <td>Programa de Estudios </td>
                <td>: DISEÑO Y PROGRAMACIÓN WEB</td>
            </tr>
            <tr>
                <td>Competencia para la
                    empleabilidad</td>
                <td>: Tecnologías de la Información(UD/T).- Manejar herramientas informáticas de
                    las TIC para buscar y analizar información, comunicarse y realizar
                    procedimientos o tareas vinculados al área profesional, de acuerdo con los
                    requerimientos de su entorno laboral.</td>
            </tr>
            <tr>
                <td>Módulo</td>
                <td>: ANALISIS Y DISEÑO DE SISTEMAS</td>
            </tr>
            <tr>
                <td>Unidad didáctica</td>
                <td>: APLICACIONES EN INTERNET</td>
            </tr>
            <tr>
                <td>Capacidad</td>
                <td>: Utilizar aplicaciones y herramientas informáticas para la búsqueda,
                    comunicación y análisis de información de manera responsable y
                    considerando los principios éticos. </td>
            </tr>
            <tr>
                <td>Tema o Actividad</td>
                <td>: Introducción e identificación de la industria 4.0.</td>
            </tr>
            <tr>
                <td>Actividades de tipo </td>
                <td>: TEORICO-PRÁCTICO</td>
            </tr>
            <tr>
                <td>Tipo de sesión</td>
                <td>: Presencial</td>
            </tr>
            <tr>
                <td>Fecha de desarrollo</td>
                <td>: 2024-04-02</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <th colspan="2" style="text-align: center;"><b>II. PLANIFICACIÓN DEL APRENDIZAJE</b></th>
            </tr>
            <tr>
                <td style="width: 30%;">Indicador(es) de logro de
                    competencia a la que se
                    vincula.</td>
                <td style="width: 70%;">: Utiliza herramientas de ofimática y especializadas para responder a los
                    requerimientos del entorno laboral, de manera ética, eficiente y responsable.</td>
            </tr>
            <tr>
                <td>Indicador(es) de logro de
                    capacidad vinculados a la
                    sesión.</td>
                <td>: Utiliza aplicaciones de internet para la búsqueda de la información,
                    aplicando criterios para la selección de información y el respeto a la propiedad
                    intelectual</td>
            </tr>
            <tr>
                <td>Logro de la sesión </td>
                <td>: Introducción e identificación de la industria 4.0.</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <td colspan="4" style="text-align: center;"><b>III. SECUENCIA DIDÁCTICA</b></td>
            </tr>
            <tr style="background-color: #CCCCCC;">
                <td style="width: 15%; text-align: center;"><b>Momento</b></td>
                <td style="width: 50%; text-align: center;"><b>Estrategías y Actividades</b></td>
                <td style="width: 28%; text-align: center;"><b>Recursos Didácticas</b></td>
                <td style="width: 7%; text-align: center;"><b>Tiempo</b></td>
            </tr>
            <tr>
                <td style="text-align: center;">Inicio</td>
                <td>* Estrategía:
                    Actividad focal introductoria,Declaración de
                    objetivos,Discusión guiada
                    * Actividades:
                    El estudiante visualiza un ejemplo e identifica la industria
                    4.0,El estudiante responde la pregunta de ¿Qué es una
                    industria 4.0?</td>
                <td>Material audiovisual</td>
                <td style="text-align: center;">20</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <td colspan="5" style="text-align: center;"><b>IV. ACTIVIDADES DE EVALUACIÓN</b></td>
            </tr>
            <tr style="background-color: #CCCCCC;">
                <td style="width: 35%; text-align: center;"><b>Indicadores de logro de la sesión</b></td>
                <td style="width: 15%; text-align: center;"><b>Técnicas</b></td>
                <td style="width: 27%; text-align: center;"><b>Instrumentos</b></td>
                <td style="width: 15%; text-align: center;"><b>Peso o Porcentaje</b></td>
                <td style="width: 8%; text-align: center;"><b>Momento</b></td>
            </tr>
            <tr>
                <td>Introducción e identificación de la
                    industria 4.0.</td>
                <td>Observacione
                    s</td>
                <td>Listas de
                    control,Hoja de
                    evaluación</td>
                <td style="text-align: center;">70</td>
                <td style="text-align: center;">Desarrollo</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <td colspan="2" style="text-align: center;"><b>V. BIBLIOGRAFÍA</b></td>
            </tr>
            <tr style="background-color: #CCCCCC;">
                <td style="text-align: center;"><b>Para el docente</b></td>
                <td style="text-align: center;"><b>Para el Estudiante</b></td>
            </tr>
            <tr style="background-color: #CCCCCC;">
                <td style="text-align: center;"><b>Obligatoria</b></td>
                <td style="text-align: center;"><b>Obligatoria</b></td>
            </tr>
            <tr>
                <td>Acceso y uso de entornos virtuales de aprendizaje:
                    Guía para el estudiante. Córdova M. & Loya J. (2019).</td>
                <td>Acceso y uso de entornos virtuales de aprendizaje:
                    Guía para el estudiante. Córdova M. & Loya J. (2019).</td>
            </tr>
            <tr style="background-color: #CCCCCC;">
                <td style="text-align: center;"><b>Opcional</b></td>
                <td style="text-align: center;"><b>Opcional</b></td>
            </tr>
            <tr>
                <td>La industria 4.0 en la sociedad digital. Marge Books.
                    Garrel A. y Guilera L. (2019).</td>
                <td>La industria 4.0 en la sociedad digital. Marge Books.
                    Garrel A. y Guilera L. (2019).</td>
            </tr>
        </table>
        <br>
        <table style="width: 100%;" border="1" cellspacing="0" cellpadding="3">
            <tr style="background-color: #CCCCCC;">
                <td style="text-align: center;"><b>VI. ANEXOS</b></td>
            </tr>
            <tr>
                <td>dfsdgsdhgsdgsdgsdg</td>
            </tr>

        </table>




<?php

    }
}
