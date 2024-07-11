<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_modulo = $_POST['modulo'];

	$ejec_cons = buscarSemestreByIdModulo_Formativo($conexion, $id_modulo);

		$cadena = '<option></option>';
		while ($mostrar=mysqli_fetch_array($ejec_cons)) {
			$cadena=$cadena.'<option value='.$mostrar['id'].'>'.$mostrar['descripcion'].'</option>';
		}
		echo $cadena;

?>