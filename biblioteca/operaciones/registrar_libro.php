<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_biblioteca.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_BIBLIO');

    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['biblioteca_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);
    $id_sesion = $rb_sesion['id'];
    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_BIBLIO');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $contar_permiso = mysqli_num_rows($b_permiso);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($contar_permiso == 0 || $rb_usuario['estado'] == 0) {
        echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../admin'>Regresar</a><br>
    <a href='../../include/cerrar_sesion_biblioteca.php'>Cerrar Sesión</a><br>
    </center>";
    } else {
        if ($rb_permiso['id_rol'] != 1) {
            echo "<script>
                  alert('Error Usted no cuenta con permiso para acceder a esta página');
                  window.location.replace('../../biblioteca/');
              </script>";
        } else {

            $titulo = $_POST['titulo'];
            $autor = $_POST['autor'];
            $editorial = $_POST['editorial'];
            $edicion = $_POST['edicion'];
            $tomo = $_POST['tomo'];
            $categoria = $_POST['categoria'];
            $isbn = $_POST['isbn'];
            $temas_relacionados = $_POST['temas_relacionados'];
            $id_programa_estudio = $_POST['id_programa'];
            $id_semestre = $_POST['id_semestre'];
            $id_unidad_didactica = $_POST['id_unidad_didactica'];






            $hoy = date("Y-m-d H:i:s");
            $nombre_archivos = $hoy . "_" . $titulo . "_" . $autor;


            function contar_paginas($filePath)
            {
                if (!file_exists($filePath))
                    return 0;
                if (!$fp = @fopen($filePath, "r"))
                    return 0;
                $i = 0;
                $type = "/Contents";
                while (!feof($fp)) {
                    $line = fgets($fp, 255);
                    $x = explode($type, $line);
                    if (count($x) > 1) {
                        $i++;
                    }
                }
                fclose($fp);
                return (int) $i;
            }
            $cant_paginas =  contar_paginas($_FILES['archivo']['tmp_name']);



            $consulta = "INSERT INTO biblioteca_libros (titulo, autor, editorial, edicion, tomo, tipo_libro, isbn, paginas, temas_relacionados,id_programa_estudio, id_semestre, id_unidad_didactica, portada, libro, id_sesion) VALUES ('$titulo', '$autor', '$editorial', '$edicion', '$tomo', '$categoria','$isbn', '$cant_paginas', '$temas_relacionados','$id_programa_estudio','$id_semestre','$id_unidad_didactica', '', '','$id_sesion')";
            if (mysqli_query($conexion, $consulta)) {
                // id del ultimo registro
                $id = mysqli_insert_id($conexion);

                $file_path_portada = $_FILES['portada']['tmp_name'];
                $file_path_libro = $_FILES['archivo']['tmp_name'];

                $directorio_portada = "../portadas/";
                $directorio_libro = "../archivos/";

                // subir portada
                $resultado = "";
                $tipoArchivo = strtolower(pathinfo($_FILES["portada"]["name"], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES["portada"]["tmp_name"], $directorio_portada . $id . "." . $tipoArchivo)) {
                    $resultado .= "";
                    $portada= $id . "." . $tipoArchivo;
                    $consulta = "UPDATE biblioteca_libros SET portada='$portada' WHERE id='$id'";
                    mysqli_query($conexion, $consulta);
                } else {
                    $resultado .= "Error al subir portada";
                }
                // subir libro
                $tipoArchivo = strtolower(pathinfo($_FILES["archivo"]["name"], PATHINFO_EXTENSION));
                if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $directorio_libro . $id . "." . $tipoArchivo)) {
                    $resultado .= "";
                    $libro= $id . "." . $tipoArchivo;
                    $consulta = "UPDATE biblioteca_libros SET libro='$libro' WHERE id='$id'";
                    mysqli_query($conexion, $consulta);
                } else {
                    $resultado .= "Error al subir archivo";
                }

                echo "<script>
			        alert('Se realizó el registro con Éxito " . $resultado . "');
                    window.location= '../libros';
		            </script>
		            ";
            } else {
                echo "<script>
        alert('Error, No se pudo realizar el registro');
        window.history.back();
    </script>
    ";
            }
        }
    }
}
