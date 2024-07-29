<?php
include '../../include/conexion.php';
include "../../include/busquedas.php";
include "../../include/funciones.php";


$id_pe = $_POST['id_pe'];
$id_sem = $_POST['id_sem'];

$cadena = '<option value="TODOS">TODOS</option>';

$b_mf = buscarModuloFormativoByIdPe($conexion, $id_pe);
while ($rb_mf = mysqli_fetch_array($b_mf)) {
        $id_modulo = $rb_mf['id'];

        $ejec_cons = buscarSemestreByIdModulo_Formativo($conexion, $id_modulo);
        while ($mostrar = mysqli_fetch_array($ejec_cons)) {
                $oo = '';
                if ($id_sem == $mostrar['id']) {
                        $oo = 'selected';
                }
                $cadena = $cadena . '<option value="' . $mostrar['id'] . '" '.$oo.' >' . $mostrar['descripcion'] . '</option>';
        }
}


echo $cadena;
