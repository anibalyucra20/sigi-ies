<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";

if (isset($_POST['datos'])) {
    $arr_uds = $_POST['datos'];
    $cadena = '';
	foreach ($arr_uds as $id) {
		$ejec_cons = buscarProgramacionUDById($conexion, $id);
		$mostrar=mysqli_fetch_array($ejec_cons);
		$id_ud = $mostrar['id_unidad_didactica'];
		$busc_ud = buscarUnidadDidacticaById($conexion, $id_ud);
		$res_ud = mysqli_fetch_array($busc_ud);
		$cadena=$cadena.'<div class="checkbox"><label><input type="checkbox" name="uds_matri" value="'.$id.'" onchange="gen_arr_mat();" checked>'.$res_ud['nombre'].'</label></div>';
	}
		echo $cadena;
}else{
    echo "Aún no hay Unidades Didácticas Agregadas para la Matrícula";
}


?>