<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_carrera = $_POST['id_carrera'];

	$ejec_cons = buscarModuloFormativoByIdPe($conexion, $id_carrera);

		$cadena = '<option></option>';
		while ($mostrar=mysqli_fetch_array($ejec_cons)) {
			$cadena=$cadena.'<option value='.$mostrar['id'].'>M'.$mostrar['nro_modulo'].' - '.$mostrar['descripcion'].'</option>';
		}
		echo $cadena;

?>