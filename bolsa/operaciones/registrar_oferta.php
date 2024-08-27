<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_bolsa.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BOLSA');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['bolsa_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);
    $id_sesion = $rb_sesion['id'];
    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BOLSA');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../admin'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_bolsa.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../admin');
              </script>";
        } else {

            $empresa = $_POST['empresa'];
            $titulo = $_POST['titulo'];
            $detalle = $_POST['detalle'];
            $requisitos = $_POST['requisitos'];
            $fecha_cierre = $_POST['fecha_cierre'];
            $salario = $_POST['salario'];
            $ubicacion = $_POST['ubicacion'];
            $tipo_contrato = $_POST['tipo_contrato'];
            $data = base64_encode($empresa);

            /*$consultaa = "INSERT INTO bolsa_ofertas_laborales (id_empresa, titulo, detalle, requisitos, fecha_cierre,salario,ubicacion,tipo_contrato,foto) VALUES ('$empresa', '$titulo', '$detalle','$fecha_cierre','$salario','$ubicacion','$tipo_contrato','')";
            $ejec_consulta = mysqli_query($conexion, $consultaa);*/


            //if ($ejec_consulta) {
                $id = mysqli_insert_id($conexion);
                if ($_FILES['foto']['name']!='') {
                    $file_path_foto = $_FILES['archivo']['tmp_name'];
                    $directorio_foto= "../portadas/";
                    
                }

                


                /*echo "<script>
			        alert('Se realizó el registro con Éxito ');
                    window.location= '../ofertas_laborales?".$data."';
		            </script>   
		            ";*/
            /*} else {
                echo "<script>
                        alert('Error, No se pudo realizar el registro '".$m.");
                        window.history.back();
                        </script>
                    ";
                    
            }*/
        }
    }
}
