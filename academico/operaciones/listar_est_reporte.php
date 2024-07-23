<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_acad.php");

session_start();

$dni_es = $_POST['dni_es'];
$na_es = $_POST['na_es'];
$pe_es = $_POST['pe_es'];

$id_periodo_act = $_SESSION['acad_periodo'];
$b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
$rb_periodo_act = mysqli_fetch_array($b_periodo_act);

$id_sede_act = $_SESSION['acad_sede'];
$b_sede_act = buscarSedeById($conexion, $id_sede_act);
$rb_sede_act = mysqli_fetch_array($b_sede_act);

$cadena = '<table  class="table table-striped table-bordered" style="width:100%">
<thead>
  <tr>
    <th>Nro</th>
    <th>DNI</th>
    <th>Apellidos y Nombres</th>
    <th>Semestre</th>
    <th>Acciones</th>
  </tr>
</thead>
<tbody>
';
function b_semestre($conexion, $id_semm)
{
    $b_sem = buscarSemestreById($conexion, $id_semm);
    $r_b_sem = mysqli_fetch_array($b_sem);
    return $r_b_sem['descripcion'];
}
$cont = 0;
$cont_t = strlen($dni_es);
if ($cont_t > 0) {
    $b_estby_dni = buscarUsuarioByDni($conexion, $dni_es);
    while ($r_b_est = mysqli_fetch_array($b_estby_dni)) {
        $id_est = $r_b_est['id'];
        $b_matriculaa = buscarMatriculaByEstPeriodoSedePe($conexion, $id_est, $id_periodo_act, $id_sede_act, $pe_es);
        $rb_matricula = mysqli_fetch_array($b_matriculaa);
        if (mysqli_num_rows($b_matriculaa) > 0) {
            $sem = b_semestre($conexion, $rb_matricula['id_semestre']);
            $cont += 1;
            $cadena = $cadena . '<tr><td>' . $cont . '</td><td>' . $r_b_est['dni'] . '</td><td>' . $r_b_est['apellidos_nombres'] . '</td><td>' . $sem . '</td><td>
            <form role="form" action="reporte_individual" method="POST">
            <input type="hidden" name="id" value="' . $r_b_est['id'] . '">
            <button type="submit" class="btn btn-success">Ver Reporte</button>
            </form>
            </td></tr>';
        } else {
            $cadena .= '';
        }
    }
} else {
    $b_est_by_nom_ap = buscarUsuarioByApellidosNombres_like($conexion, $na_es);
    while ($r_b_est = mysqli_fetch_array($b_est_by_nom_ap)) {
        $id_est = $r_b_est['id'];
        $b_matriculaa = buscarMatriculaByEstPeriodoSedePe($conexion, $id_est, $id_periodo_act, $id_sede_act, $pe_es);
        $rb_matricula = mysqli_fetch_array($b_matriculaa);
        if (mysqli_num_rows($b_matriculaa) > 0) {
            $sem = b_semestre($conexion, $rb_matricula['id_semestre']);
            $cont += 1;
            $cadena = $cadena . '<tr><td>' . $cont . '</td><td>' . $r_b_est['dni'] . '</td><td>' . $r_b_est['apellidos_nombres'] . '</td><td>' . $sem . '</td><td>
        <form role="form" action="reporte_individual" method="POST">
        <input type="hidden" name="id" value="' . $r_b_est['id'] . '">
        <button type="submit" class="btn btn-success">Ver Reporte</button>
        </form>
        </td></tr>';
        } else {
            $cadena .= '';
        }
    }
}

$cadena = $cadena . '</tbody></table>';
echo $cadena;
