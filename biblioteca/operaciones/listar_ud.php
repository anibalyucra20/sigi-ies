<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";

$id_sem = $_POST['id_sem'];
$id_ud = $_POST['id_ud'];

$ejec_cons = buscarUnidadDidacticaByIdSemestre($conexion,$id_sem);

$cadena = '<option value="TODOS">TODOS</option>';
$info = "";
while ($mostrar = mysqli_fetch_array($ejec_cons)) {

        if ($id_ud==$mostrar['id']) {
                $info = "selected";
        }else {
                $info = "";
        }
        $cadena = $cadena . '<option value="' . $mostrar['id'] . '" ' . $info . '>' . $mostrar['nombre'] . '</option>';
}
echo $cadena;
