<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_modulo = $_POST['id_modulo'];

	$ejec_cons = buscarCompetenciasByIdModuloFormativo($conexion, $id_modulo);

		$cadena = '<option></option>';
		while ($mostrar=mysqli_fetch_array($ejec_cons)) {
			$cadena=$cadena.'<option value='.$mostrar['id'].'>'.$mostrar['codigo'].' - '.$mostrar['descripcion'].'</option>';
		}
		echo $cadena;

?>