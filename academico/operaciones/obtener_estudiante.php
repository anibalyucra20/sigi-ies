<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $NIT = htmlspecialchars(trim($_POST["dni_est"]));

    // Codigo para buscar en tu base de datos acá


    include '../../include/conexion.php';
    include "../../include/busquedas.php";
    include "../../include/funciones.php";

    $resultado = buscarUsuarioByDni($conexion,$NIT);
    $dato = mysqli_fetch_array($resultado);
    $cont = mysqli_num_rows($resultado);


$id_est = $dato['id'];
$nombre = $dato['apellidos_nombres'];

if ($cont>0) {
    echo json_encode([
        'id_est' => $id_est,
        'nombre' => $nombre
     ]);
}else{
    echo json_encode([
        'nombre' => ""
     ]);
     // verificar codigo  ALERT--------------------------------------
}

} else {
    echo "<p>No se encontro el nombre en la DB!!</p>";
}
?>