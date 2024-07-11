<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_acad.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_ACAD');
    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['acad_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_ACAD');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    $id_periodo_act = $_SESSION['acad_periodo'];
    $b_periodo_act = buscarPeriodoAcadById($conexion, $id_periodo_act);
    $rb_periodo_act = mysqli_fetch_array($b_periodo_act);

    $id_sede_act = $_SESSION['acad_sede'];
    $b_sede_act = buscarSedeById($conexion, $id_sede_act);
    $rb_sede_act = mysqli_fetch_array($b_sede_act);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {
        $hoy = date("Y-m-d");
        if ($rb_periodo_act['fecha_fin'] >= $hoy) {

            //buscamos los sistemas disponibles para registrar permisos para estudiantes
            $sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
            $sistemas = decodificar($sistemas);

            $dni = $_POST['dni'];
            $nom_ap = $_POST['nom_ap'];
            $genero = $_POST['genero'];
            $fecha_nac = $_POST['fecha_nac'];
            $direccion = $_POST['direccion'];
            $email = $_POST['email'];
            $telefono = $_POST['telefono'];

            $carrera = $_POST['carrera'];
            $discapacidad = $_POST['discapacidad'];

            //verificar si el estudiante ya esta registrado

            $rol = 6; //6 es estudiante


            // ----------------------------------  funcion para verificar y actualizar registros de estudiantes en carreras




            $busc_est_car = "SELECT * FROM sigi_usuarios WHERE dni='$dni'";
            $ejec_busc_est_car = mysqli_query($conexion, $busc_est_car);
            $conteo = mysqli_num_rows($ejec_busc_est_car);
            $rb_busc_est_carr = mysqli_fetch_array($ejec_busc_est_car);
            $id_esttt = $rb_busc_est_carr['id'];
            if ($conteo > 0) {
                $actualizar = "UPDATE sigi_usuarios SET apellidos_nombres='$nom_ap', genero='$genero', fecha_nac='$fecha_nac', direccion='$direccion', correo='$email', telefono='$telefono', id_programa_estudios='$carrera', discapacidad='$discapacidad', id_rol='$rol',id_sede='$id_sede_act', estado=1 WHERE dni=$dni";
                $ejecutar_actualizar = mysqli_query($conexion, $actualizar);

                $accion = registrar_usuario_pe($conexion, $id_esttt, $carrera, $id_periodo_act);

                echo "<script>
			alert('El estudiante, se registró correctamente');
            window.location= '../estudiantes'
				</script>
			";
            } else {
                $insertar = "INSERT INTO sigi_usuarios (dni, apellidos_nombres, genero, fecha_nac, direccion, correo, telefono, id_periodo_registro, id_programa_estudios, discapacidad,id_rol,id_sede,estado, password, reset_password, token_password) VALUES ('$dni','$nom_ap','$genero', '$fecha_nac', '$direccion', '$email', '$telefono', '$id_periodo_act', '$carrera', '$discapacidad','$rol','$id_sede_act',1, ' ', 0, ' ')";
                $ejecutar_insetar = mysqli_query($conexion, $insertar);


                //registrar permisos para sistemas
                $id_estudiante = mysqli_insert_id($conexion);

                foreach ($sistemas as $key => $value) {
                    if ($value == 'S_SIGI' || $value == 'S_ADMISION') {
                        // EN ESTOS SISTEMAS NO SE REGISTRARÁ LOS PERMISOS PARA EL ESTUDIANTE
                    } else {
                        $b_sistema = buscarSistemaByCodigo($conexion, $value);
                        $rb_sistema = mysqli_fetch_array($b_sistema);
                        $id_sistema = $rb_sistema['id'];

                        // registramos permiso
                        $insetar_permiso = "INSERT INTO sigi_permisos_usuarios (id_usuario,id_sistema,id_rol) VALUES ('$id_estudiante','$id_sistema','$rol')";
                        $ejecutar_insetar_permiso = mysqli_query($conexion, $insetar_permiso);
                    }
                }

                $accion = registrar_usuario_pe($conexion, $id_estudiante, $carrera, $id_periodo_act);

                if ($ejecutar_insetar) {
                    echo "<script>
                alert('Registro Existoso');
                window.location= '../estudiantes'
    			</script>";
                } else {
                    echo "<script>
			alert('Error al registrar estudiante, por favor verifique sus datos');
			window.history.back();
				</script>
			";
                };
            };





            mysqli_close($conexion);
        } else {
            echo "<script>
                alert('Error, No puede Registrar Estudiantes Fuera de Periodo');
                window.location= '../estudiantes'
    			</script>";
        }
    }
}
