<!--MODAL EDITAR-->
<div class="modal fade edit_<?php echo $res_busc_doc['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel" align="center">Editar Docente</h4>
            </div>
            <div class="modal-body">
                <!--INICIO CONTENIDO DE MODAL-->
                <div class="x_panel">


                    <div class="x_content">
                        <br />
                        <form role="form" action="operaciones/actualizar_usuario.php" class="form-horizontal form-label-left input_mask" method="POST">
                            <input type="hidden" name="id" value="<?php echo $res_busc_doc['id']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="number" class="form-control" name="dni" required="required" maxlength="8" value="<?php echo $res_busc_doc['dni']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Apellidos y Nombres : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="nom_ap" required="required" value="<?php echo $res_busc_doc['apellidos_nombres']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Género : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="genero" name="genero" required="required">
                                        <option></option>
                                        <option value="M" <?php if ($res_busc_doc['genero'] == 'M') {
                                                                echo "selected";
                                                            } ?>>Masculino</option>
                                        <option value="F" <?php if ($res_busc_doc['genero'] == 'F') {
                                                                echo "selected";
                                                            } ?>>Femenino</option>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Nacimiento : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="date" class="form-control" name="fecha_nac" required="required" value="<?php echo $res_busc_doc['fecha_nac']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Dirección : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="direccion" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $res_busc_doc['direccion']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Correo Electrónico : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="email" class="form-control" name="email" required="required" value="<?php echo $res_busc_doc['correo']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Teléfono : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="Number" class="form-control" name="telefono" required="required" maxlength="15" value="<?php echo $res_busc_doc['telefono']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discapacidad : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="discapacidad" name="discapacidad" value="" required="required">
                                        <option></option>
                                        <option value="SI" <?php if ($res_busc_doc['discapacidad'] == 'SI') {
                                                                echo "selected";
                                                            } ?>>SI</option>
                                        <option value="NO" <?php if ($res_busc_doc['discapacidad'] == 'NO') {
                                                                echo "selected";
                                                            } ?>>NO</option>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Sede : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="sede" name="sede" value="" required="required">
                                        <option></option>
                                        <?php
                                        $ejec_busc_sede = buscarSede($conexion);
                                        while ($res_busc_sede = mysqli_fetch_array($ejec_busc_sede)) {
                                            $id_sede = $res_busc_sede['id'];
                                            $sede = $res_busc_sede['nombre'];
                                        ?>
                                            <option value="<?php echo $id_sede;
                                                            ?>" <?php if ($id_sede == $res_busc_doc['id_sede']) {
                                                                    echo "selected";
                                                                } ?>><?php echo $sede; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Cargo : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="cargo" name="cargo" required="required">
                                        <option></option>
                                        <?php
                                        $ejec_busc_car = buscarRol($conexion);
                                        while ($res__busc_car = mysqli_fetch_array($ejec_busc_car)) {
                                            $id_car = $res__busc_car['id'];
                                            $car = $res__busc_car['nombre'];
                                        ?>
                                            <option value="<?php echo $id_car;
                                                            ?>" <?php if ($id_car == $res_busc_doc['id_rol']) {
                                                                    echo "selected";
                                                                } ?>><?php echo $car; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="pe" name="pe" value="" required="required">
                                        <option></option>
                                        <?php
                                        $b_busc_car = buscarProgramaEstudio($conexion);
                                        while ($res_b_busc_car = mysqli_fetch_array($b_busc_car)) {
                                            $id_pe = $res_b_busc_car['id'];
                                            $pe = $res_b_busc_car['nombre'];
                                        ?>
                                            <option value="<?php echo $id_pe; ?>" <?php if ($id_pe == $res_busc_doc['id_programa_estudios']) {
                                                                                        echo "selected";
                                                                                    } ?>><?php echo $pe; ?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Activo : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="estado" name="estado" value="" required="required">
                                        <option></option>
                                        <option value="1" <?php if ($res_busc_doc['estado'] == 1) {
                                                                echo "selected";
                                                            } ?>>SI</option>
                                        <option value="0" <?php if ($res_busc_doc['estado'] == 0) {
                                                                echo "selected";
                                                            } ?>>NO</option>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div align="center">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--FIN DE CONTENIDO DE MODAL-->
            </div>
        </div>
    </div>
</div>
<!--FIN MODAL EDITAR-->


<!--MODAL PERMISOS-->
<div class="modal fade permisos_<?php echo $res_busc_doc['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel" align="center">Editar Docente</h4>
            </div>
            <div class="modal-body">
                <!--INICIO CONTENIDO DE MODAL-->
                <div class="x_panel">


                    <div class="x_content">
                        <br />
                        <form role="form" action="operaciones/actualizar_permisos_usuarios.php" class="form-horizontal form-label-left input_mask" method="POST">
                            <input type="hidden" name="id" value="<?php echo $res_busc_doc['id']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="number" class="form-control" value="<?php echo $res_busc_doc['dni']; ?>" readonly>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Apellidos y Nombres : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" value="<?php echo $res_busc_doc['apellidos_nombres']; ?>" readonly>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">SISTEMAS </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                </div>
                            </div>
                            :
                            <br>
                            <br>
                            <?php
                            $b_sistemas = buscarSistemas($conexion);
                            while ($rb_sistemas = mysqli_fetch_array($b_sistemas)) {
                                $cod_sistema = $rb_sistemas['codigo'];
                                if (in_array($cod_sistema, $sistemas)) {
                                    $usu_id = $res_busc_doc['id'];
                                    $sis_id = $rb_sistemas['id'];
                                    $b_permisos_sis = buscarPermisoUsuarioByUsuarioSistema($conexion, $usu_id, $sis_id);
                                    $cont_permisos_usu = mysqli_num_rows($b_permisos_sis);
                                    $rb_permisos_sis = mysqli_fetch_array($b_permisos_sis);
                            ?>
                                    <div class="form-group row">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12"><?php echo $rb_sistemas['nombre']; ?> : </label>
                                        <div class="col-md-2 col-sm-2 col-xs-6">
                                            <select class="form-control" name="sistema_<?php echo $rb_sistemas['id'] . "_" . $res_busc_doc['id']; ?>">
                                                <option value="1" <?php if ($cont_permisos_usu > 0) {
                                                                        echo "selected";
                                                                    } ?>>Si</option>
                                                <option value="0" <?php if ($cont_permisos_usu == 0) {
                                                                        echo "selected";
                                                                    } ?>>No</option>
                                            </select>
                                            <br>
                                        </div>
                                        <label class="control-label col-md-1 col-sm-1 col-xs-6">Rol : </label>
                                        <div class="col-md-4 col-sm-4 col-xs-6">
                                            <select class="form-control" name="rolsistema_<?php echo $rb_sistemas['id'] . "_" . $res_busc_doc['id']; ?>">
                                            <option value="0">Seleccione</option>
                                                <?php
                                                $b_roles = buscarRol($conexion);
                                                while ($rb_roles = mysqli_fetch_array($b_roles)) {
                                                ?>
                                                    <option value="<?php echo $rb_roles['id']; ?>" <?php if ($cont_permisos_usu > 0 && $rb_roles['id']== $rb_permisos_sis['id_rol']) {
                                                                            echo "selected";
                                                                        } ?>><?php echo $rb_roles['nombre']; ?></option>

                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                            <div align="center">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!--FIN DE CONTENIDO DE MODAL-->
            </div>
        </div>
    </div>
</div>
<!--FIN MODAL PERMISOS-->