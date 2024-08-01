<?php
include "../../include/conexion.php";
include "../../include/busquedas.php";
include "../../include/funciones.php";
session_start();
$id_sem = $_POST['id_sem'];
$id_pe = $_POST['id_pe'];
$id_turno = $_POST['id_turno'];
$id_seccion = $_POST['id_seccion'];
$id_sede = $_SESSION['acad_sede'];

$b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede, $id_pe);
$rb_pe_sede = mysqli_fetch_array($b_pe_sede);
$id_pe_sede = $rb_pe_sede['id'];

$ejec_cons = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);

$cadena = '<div class="checkbox">
		<label>
		  <input type="checkbox" onchange="select_all();" id="all_check"> <b> SELECCIONAR TODAS LAS UNIDADES DIDÁCTICAS *</b>
		</label>
		</div>';

while ($mostrar = mysqli_fetch_array($ejec_cons)) {
  $id_unidad_didactica = $mostrar["id"];
  $busc_progr = buscarProgramacionByUd_Peridodo_ProgramaSedeTurnoSeccion($conexion, $id_unidad_didactica, $id_pe_sede, $_SESSION['acad_periodo'], $id_turno, $id_seccion);
  $cont = mysqli_num_rows($busc_progr);
  if ($cont > 0) {
    while ($res_prog = mysqli_fetch_array($busc_progr)) {
      $b_usuario = buscarUsuarioById($conexion, $res_prog['id_docente']);
      $rb_usuario = mysqli_fetch_array($b_usuario);
    $cadena = $cadena . '<div class="checkbox"><label><input type="checkbox" name="unidades_didacticas" onchange="gen_arr_uds();" value="' . $res_prog["id"] . '">' . $mostrar["nombre"] . ' - '.$rb_usuario['apellidos_nombres'].'</label></div>';
    }
    
  }
}
echo $cadena;
