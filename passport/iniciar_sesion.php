<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
$usuario = $_POST['usuario'];
$pass = $_POST['password'];
$sistema = base64_decode($_POST['sistema']);


$ejec_busc = buscarUsuarioByDni($conexion, $usuario);
$res_busc = mysqli_fetch_array($ejec_busc);
$cont = mysqli_num_rows($ejec_busc);

//verificar si se tiene permiso del super admin en el sistema
$sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
$sistemas = decodificar($sistemas);

if (in_array($sistema, $sistemas)) {

	if (($cont == 1) && (password_verify($pass, $res_busc['password']))) {
		$id_usuario = $res_busc['id'];
		$id_sede = $res_busc['id_sede'];
		$cargo_usuario = $res_busc['id_rol'];
		$buscar_periodo = buscarPresentePeriodoAcad($conexion);
		$res_b_periodo = mysqli_fetch_array($buscar_periodo);
		$presente_periodo = $res_b_periodo['id'];
		if ($res_busc['estado'] != 1) {
			echo "<script>
                alert('Error, Usted no se encuentra activo en el sistema, Por Favor Contacte con el Administrador');
                window.location.replace('../login/');
    		</script>";
		} else {
			// GENERAR SESION - SEGUN EL TIPO DE SISTEMA
			if ($cargo_usuario != 0) {
				//obtener id de sistema
				$b_sistema = buscarSistemaByCodigo($conexion, $sistema);
				$rb_sistema = mysqli_fetch_array($b_sistema);
				$id_sistema = $rb_sistema['id'];

				//verificar si este usuario tiene acceso al sistema que esta ingresando
				$b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
				$cont_permiso = mysqli_num_rows($b_permiso);

				if ($cont_permiso > 0) {
					session_start();
					$llave = generar_llave();
					//registramos sesion
					$id_sesion = reg_sesion($conexion, $id_usuario, $llave, $id_sistema);
					$token = password_hash($llave, PASSWORD_DEFAULT);
					if ($id_sesion != 0) {
						sleep(1);
						switch ($sistema) {
							case 'S_SIGI':
								//se genera las variables de sesion
								$_SESSION['sigi_id_sesion'] = $id_sesion;
								$_SESSION['sigi_periodo'] = $presente_periodo;
								$_SESSION['sigi_token'] = $token;
								$_SESSION['sigi_sede'] = $id_sede;
								echo "<script> window.location.replace('../sigi/'); </script>";
								break;
							case 'S_ACAD':
								//se genera las variables de sesion
								$_SESSION['acad_id_sesion'] = $id_sesion;
								$_SESSION['acad_periodo'] = $presente_periodo;
								$_SESSION['acad_token'] = $token;
								$_SESSION['acad_sede'] = $id_sede;
								echo "<script> window.location.replace('../academico/'); </script>";
								break;
							case 'S_TUTORIA':
								//se genera las variables de sesion
								$_SESSION['tutoria_id_sesion'] = $id_sesion;
								$_SESSION['tutoria_periodo'] = $presente_periodo;
								$_SESSION['tutoria_token'] = $token;
								$_SESSION['tutoria_sede'] = $id_sede;
								echo "<script> window.location.replace('../tutoria/'); </script>";
								break;
							case 'S_BIBLIOTECA':
								//se genera las variables de sesion
								$_SESSION['biblioteca_id_sesion'] = $id_sesion;
								$_SESSION['biblioteca_periodo'] = $presente_periodo;
								$_SESSION['biblioteca_token'] = $token;
								$_SESSION['biblioteca_sede'] = $id_sede;
								echo "<script> window.location.replace('../biblioteca/'); </script>";
								break;
							case 'S_ADMISION':
								//se genera las variables de sesion
								$_SESSION['admision_id_sesion'] = $id_sesion;
								$_SESSION['admision_periodo'] = $presente_periodo;
								$_SESSION['admision_token'] = $token;
								$_SESSION['admision_sede'] = $id_sede;
								echo "<script> window.location.replace('../admision/'); </script>";
								break;
							default:
								# code...
								break;
						}
					} else {
						echo "<script>
                		alert('Error al Iniciar Sesión. Intente Nuevamente');
						window.location.replace('index?data=" . $_POST['sistema'] . "');
    					</script>";
					}
				} else {
					echo "<script>
                		alert('Error, Ud No cuenta con el permiso para acceder a este sistema');
						window.location.replace('index?data=" . $_POST['sistema'] . "');
    					</script>";
				}
			} else {
				echo "<script>
                alert('Error, Usted no Cuenta con los permisos necesarios para acceder al sistema');
				window.location.replace('index?data=" . $_POST['sistema'] . "');
    		</script>";
			}
		}
	} else {
		echo "<script>
                alert('Usuario o Contraseña incorrecto');
				window.location.replace('index?data=" . $_POST['sistema'] . "');
    		</script>";
	}
} else {
	echo "<script>
			alert('Error 232, Ud No cuenta con el permiso para acceder a este sistema ');
			window.location.replace('index?data=" . $_POST['sistema'] . "');
			</script>";
}

mysqli_close($conexion);
