<!--MODAL EDITAR-->
<div class="modal fade edit_crit_<?php echo $r_b_critt_eva['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
        </button>
        <h4 class="modal-title" id="myModalLabel" align="center">Editar Detalle de Criterio de Evaluación</h4>
      </div>
      <div class="modal-body">
        <!--INICIO CONTENIDO DE MODAL-->
        <form action="#">
          <div class="x_panel">
            <div class="x_content">
              <br />
              <input type="hidden" name="id" value="<?php echo $r_b_critt_eva['id']; ?>">
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Detalle : </label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  <?php
                  $b_eva_crit = buscarEvaluacionById($conexion, $r_b_critt_eva['id_evaluacion']);
                  $r_b_eva_crit = mysqli_fetch_array($b_eva_crit);
                  ?>
                  <input type="hidden" id="ord_crit_<?php echo $r_b_critt_eva['id']; ?>" value="<?php echo $r_b_critt_eva['orden']; ?>">
                  <input type="hidden" id="detalle_eva_<?php echo $r_b_critt_eva['id']; ?>" value="<?php echo $r_b_eva_crit['detalle']; ?>">
                  <input type="text" class="form-control" id="ndetalle_<?php echo $r_b_critt_eva['id']; ?>" required="" value="<?php echo $r_b_critt_eva['detalle']; ?>">

                  <br>
                </div>
              </div>
              <div align="center">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

                <button type="button" class="btn btn-primary" onclick="actualizarCriterio(<?php echo $r_b_critt_eva['id']; ?>)">Guardar</button>
              </div>

            </div>
          </div>
        </form>
        <!--FIN DE CONTENIDO DE MODAL-->
      </div>
    </div>
  </div>
</div>