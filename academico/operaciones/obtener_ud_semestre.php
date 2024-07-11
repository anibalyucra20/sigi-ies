<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_semestre = $_POST['id_semestre'];
$cadena = '<option></option>';
$ejec_cons = buscarUnidadDidacticaByIdSemestre($conexion, $id_semestre);
$orden = 0;
while ($mostrar = mysqli_fetch_array($ejec_cons)) {
	$orden ++;
	$cadena = $cadena . '<option value=' . $mostrar['id'] . '>'.$orden.".- " . $mostrar['nombre'] . '</option>';
}
echo $cadena;
