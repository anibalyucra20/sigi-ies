<!--MODAL EDITAR-->
<div class="modal fade edit_<?php echo $res_busc_programacion['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title" id="myModalLabel" align="center">Editar Programación</h4>
            </div>
            <div class="modal-body">
                <!--INICIO CONTENIDO DE MODAL-->
                <div class="x_panel">
                    <div class="x_content">
                        <br />
                        <form role="form" action="operaciones/actualizar_programacion.php" class="form-horizontal form-label-left input_mask" method="POST">
                            <input type="hidden" name="data" value="<?php echo $res_busc_programacion['id']; ?>">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Unidad Didáctica : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input type="text" readonly class="form-control" value="<?php echo $res_b_unidad_didactica['nombre']; ?>">
                                    <br>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Docente : </label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                    <select class="form-control" id="docente" name="docente" required="required">
                                        <option value="0">Sin Docente</option>
                                        <?php
                                        $ejec_busc_doc = buscarUsuarioDocentesOrderByApellidosNombresAndSede($conexion, $id_sede_act);
                                        while ($res_busc_doc = mysqli_fetch_array($ejec_busc_doc)) {
                                            $id_doc = $res_busc_doc['id'];
                                            $doc = $res_busc_doc['apellidos_nombres'];
                                        ?>
                                            <option value="<?php echo $id_doc;
                                                            ?>" <?php if ($id_docente == $id_doc) {
                                                                    echo "selected";
                                                                } ?>><?php echo $doc; ?></option>
                                        <?php
                                        }
                                        ?>
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