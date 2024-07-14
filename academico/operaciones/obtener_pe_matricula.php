<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_est = $_POST['id'];
$b_est_pe = buscarEstudiantePeById_est($conexion, $id_est);
$cadena = '<option></option>';

while ($rb_est_pe = mysqli_fetch_array($b_est_pe)) {

    $ejec_cons = buscarProgramaEstudioById($conexion, $rb_est_pe['id_programa_estudio']);
    while ($mostrar = mysqli_fetch_array($ejec_cons)) {
        $cadena = $cadena . '<option value=' . $mostrar['id'] . '>' . $mostrar['nombre'] . '</option>';
    }
}


echo $cadena;
