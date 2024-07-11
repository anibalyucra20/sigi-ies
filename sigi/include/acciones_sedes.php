<!--MODAL EDITAR-->
<div class="modal fade edit_<?php echo $res_busc_sede['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel" align="center">Editar Sede</h4>
            </div>
            <div class="modal-body">
                <!--INICIO CONTENIDO DE MODAL-->
                <div class="x_panel">


                    <div class="x_content">
                        <br />
                        <form role="form" action="operaciones/actualizar_sede.php" class="form-horizontal form-label-left input_mask" method="POST">
                            <input type="hidden" name="id" value="<?php echo $res_busc_sede['id']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Cód. Modular : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="cod_modular" required="required" maxlength="8" value="<?php echo $res_busc_sede['cod_modular']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="nombre" required="required" value="<?php echo $res_busc_sede['nombre']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Departamento : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="departamento" required="required" value="<?php echo $res_busc_sede['departamento']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Provincia : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="provincia" required="required" value="<?php echo $res_busc_sede['provincia']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Distrito : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="distrito" required="required" value="<?php echo $res_busc_sede['distrito']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Dirección : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" name="direccion" required="required" value="<?php echo $res_busc_sede['direccion']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Teléfono : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="Number" class="form-control" name="telefono" required="required" maxlength="15" value="<?php echo $res_busc_sede['telefono']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Correo Electrónico : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="email" class="form-control" name="email" required="required" value="<?php echo $res_busc_sede['correo']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Responsable : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="responsable" name="responsable">
                                        <option value="0">Sin Responsable</option>
                                        <?php
                                        $ejec_busc_docentes = buscarUsuarioDocentesOrderByApellidosNombres($conexion);
                                        while ($res_busc_docentes = mysqli_fetch_array($ejec_busc_docentes)) {
                                            $id_doc = $res_busc_docentes['id'];
                                            $docente = $res_busc_docentes['apellidos_nombres'];
                                        ?>
                                            <option value="<?php echo $id_doc;
                                                            ?>" <?php if ($id_doc == $res_busc_sede['responsable']) {
                                                                    echo "selected";
                                                                } ?>><?php echo $docente; ?></option>
                                        <?php
                                        }
                                        ?>
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

<!--MODAL PROGRAMAS-->
<div class="modal fade programas_<?php echo $res_busc_sede['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel" align="center">Programas de Estudio por Sede</h4>
            </div>
            <div class="modal-body">
                <!--INICIO CONTENIDO DE MODAL-->
                <div class="x_panel">


                    <div class="x_content">
                        <br />
                        <form role="form" action="operaciones/actualizar_sede_programaestudio.php" class="form-horizontal form-label-left input_mask" method="POST">
                            <input type="hidden" name="id" value="<?php echo $res_busc_sede['id']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Cód. Modular : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" maxlength="8" value="<?php echo $res_busc_sede['cod_modular']; ?>" readonly>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Nombre : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" class="form-control" value="<?php echo $res_busc_sede['nombre']; ?>" readonly>
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-6 col-sm-6 col-xs-12">Programas de Estudio para Sede </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                </div>
                            </div>
                            :
                            <br>
                            <br>
                            <?php
                            $b_programas_estudios = buscarProgramaEstudio($conexion);
                            while ($rb_programas_estudios = mysqli_fetch_array($b_programas_estudios)) {
                                $id_pe = $rb_programas_estudios['id'];
                                $b_programa_sede = buscarProgramaEstudioSedeByIdSedePe($conexion, $id_sede, $id_pe);
                                $cont_programa_sede = mysqli_num_rows($b_programa_sede);
                                $rb_programa_sede = mysqli_fetch_array($b_programa_sede);
                            ?>
                                <div class="form-group row">
                                    <label class="control-label col-md-6 col-sm-6 col-xs-6"><?php echo $rb_programas_estudios['nombre']; ?> : </label>
                                    <div class="col-md-2 col-sm-2 col-xs-6">
                                        <select class="form-control" name="pe_sede_<?php echo $id_sede . "_" . $id_pe; ?>">
                                            <option value="1" <?php if ($cont_programa_sede > 0) {
                                                                    echo "selected";
                                                                } ?>>Si</option>
                                            <option value="0" <?php if ($cont_programa_sede == 0) {
                                                                    echo "selected";
                                                                } ?>>No</option>
                                        </select>
                                        <br>
                                    </div>
                                </div>
                            <?php } ?>
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
<!--FIN MODAL PROGRAMAS-->