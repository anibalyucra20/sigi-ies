<?php
include("../../include/conexion.php");
include("../../include/busquedas.php");
include("../../include/funciones.php");
include("../../include/verificar_sesion_sigi.php");

if (!verificar_sesion($conexion)) {
    $sistema = base64_encode('S_SIGI');
    echo "<script>
                  window.location.replace('../../passport/index?data=" . $sistema . "');
              </script>";
} else {
    // validamos si su rol corresponde
    $b_sesion = buscarSesionLoginById($conexion, $_SESSION['sigi_id_sesion']);
    $rb_sesion = mysqli_fetch_array($b_sesion);

    $b_usuario = buscarUsuarioById($conexion, $rb_sesion['id_usuario']);
    $rb_usuario = mysqli_fetch_array($b_usuario);
    $id_usuario = $rb_usuario['id'];

    $b_sistema = buscarSistemaByCodigo($conexion, 'S_SIGI');
    $rb_sistema = mysqli_fetch_array($b_sistema);
    $id_sistema = $rb_sistema['id'];

    $b_permiso = buscarPermisoUsuarioByUsuarioSistema($conexion, $id_usuario, $id_sistema);
    $rb_permiso = mysqli_fetch_array($b_permiso);

    if ($rb_permiso['id_rol'] != 1 || $rb_usuario['estado'] == 0) {
        echo "<script>
					alert('Error, PERMISOS NO SUFICIENTES PARA REALIZAR LA OPERACIÓN');
					window.history.back();
				</script>
			";
    } else {

        $id_sede = $_POST['id'];

        $b_programas_estudios = buscarProgramaEstudio($conexion);
        while ($rb_programas_estudios = mysqli_fetch_array($b_programas_estudios)) {
            $id_pe = $rb_programas_estudios['id'];
            $b_programa_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede, $id_pe);
            $cont_programa_sede = mysqli_num_rows($b_programa_sede);
            $rb_programa_sede = mysqli_fetch_array($b_programa_sede);
            $id_programa_Sede = $rb_programa_sede['id'];

            $hoy = date("Y-m-d");

            $dato = $_POST['pe_sede_' . $id_sede . '_' . $id_pe];
            //echo $rb_programas_estudios['nombre'];
            if ($cont_programa_sede > 0 && $dato == 0) {
                //elimnar permiso
                //echo " - eliminar <br>";
                $consulta = "DELETE FROM sigi_programa_sede WHERE id='$id_programa_Sede'";
                $ejec_consulta = mysqli_query($conexion, $consulta);
            } elseif ($cont_programa_sede == 0 && $dato == 1) {
                // registrar permiso
                //echo " - registrar <br>";
                $consulta = "INSERT INTO sigi_programa_sede (id_sede,id_programa_estudio,fecha_registro) VALUES ('$id_sede','$id_pe','$hoy')";
                $ejec_consulta = mysqli_query($conexion, $consulta);
            } else {
                // no se cambio nada
                //echo " - no hacer nada <br>";
            }
        }


        echo "<script>
        alert('Actualizado Correctamente');
        window.location= '../sedes'
    </script>
";
        mysqli_close($conexion);
    }
}
