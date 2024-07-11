<!--MODAL EDITAR-->
<div class="modal fade edit_<?php echo $rb_est_programa['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <form role="form" action="operaciones/actualizar_estudiante.php" class="form-horizontal form-label-left input_mask" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="<?php echo $res_busc_est['id']; ?>">
                            <input type="hidden" name="dni_a" value="<?php echo $res_busc_est['dni']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Dni : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="number" class="form-control" name="dni" required="" maxlength="8" value="<?php echo $res_busc_est['dni']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Apellidos y Nombres : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="ap_nom" required="" value="<?php echo $res_busc_est['apellidos_nombres']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Género : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="genero" name="genero" value="" required="required">
                                        <option></option>
                                        <option value="M" <?php if ("M" == $res_busc_est['genero']) :
                                                                echo 'selected';
                                                            endif ?>>Masculino</option>
                                        <option value="F" <?php if ("F" == $res_busc_est['genero']) :
                                                                echo 'selected';
                                                            endif ?>>Femenino</option>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Nacimiento : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="date" class="form-control" name="fecha_nac" required="" value="<?php echo $res_busc_est['fecha_nac']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Dirección : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="direccion" required="required" value="<?php echo $res_busc_est['direccion']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Correo Electrónico : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="email" class="form-control" name="email" required="required" value="<?php echo $res_busc_est['correo']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Teléfono : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="number" class="form-control" name="telefono" required="" maxlength="15" value="<?php echo $res_busc_est['telefono']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="carrera" name="carrera" value="" required="required">
                                        <option></option>
                                        <?php
                                        $ejec_busc_carr = buscarProgramaEstudio($conexion);
                                        while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                                            $id_carr = $res__busc_carr['id'];
                                            $b_pe_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede_act, $id_carr);
                                            $cont_pe_sede = mysqli_num_rows($b_pe_sede);
                                            if ($cont_pe_sede > 0) {
                                                $carr = $res__busc_carr['nombre'];
                                        ?>
                                                <option value="<?php echo $id_carr;
                                                                ?>" <?php if ($rb_est_programa['id_programa_estudio'] == $id_carr) {
                                                                    echo "selected";
                                                                } ?>><?php echo $carr; ?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Discapacidad : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" name="discapacidad" value="" required="required">
                                        <option></option>
                                        <option value="SI" <?php if ("SI" == $res_busc_est['discapacidad']) :
                                                                echo 'selected';
                                                            endif ?>>SI</option>
                                        <option value="NO" <?php if ("NO" == $res_busc_est['discapacidad']) :
                                                                echo 'selected';
                                                            endif ?>>NO</option>
                                    </select>
                                    <br>
                                </div>
                            </div>


                            <div align="center">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-success">Guardar</button>
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