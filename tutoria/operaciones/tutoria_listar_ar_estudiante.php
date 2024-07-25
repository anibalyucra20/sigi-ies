<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";

if (isset($_POST['datos'])) {
    $arr_est = $_POST['datos'];
    $cadena = '';
	foreach ($arr_est as $id) {

		$ejec_cons = buscarUsuarioById($conexion,$id);
		$mostrar=mysqli_fetch_array($ejec_cons);
		$cadena=$cadena.'<div class="checkbox"><label><input type="checkbox" name="est_tutoria" value="'.$id.'" onchange="gen_arr_tutoria();" checked>'.$mostrar['apellidos_nombres'].'</label></div>';
	}
		echo $cadena;
}else{
    echo "Aún no hay Unidades Didácticas Agregadas para la Matrícula";
}


?>