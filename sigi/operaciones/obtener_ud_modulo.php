<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_modulo = $_POST['id_modulo'];
$cadena = '<option></option>';
	$b_sem = buscarSemestreByIdModulo_Formativo($conexion, $id_modulo);
	while ($rb_sem = mysqli_fetch_array($b_sem)) {
		$id_sem = $rb_sem['id'];
		$ejec_cons = buscarUnidadDidacticaByIdSemestre($conexion, $id_sem);

		while ($mostrar=mysqli_fetch_array($ejec_cons)) {
			$cadena=$cadena.'<option value='.$mostrar['id'].'>- '.$mostrar['nombre'].'</option>';
		}
	}
		echo $cadena;
?>