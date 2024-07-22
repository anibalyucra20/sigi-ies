<!--MODAL EDITAR-->
<div class="modal fade edit_eva<?php echo $r_b_evaluacion['id']; ?>" tabindex="-1" role="dialog" aria-hidden="true">
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

              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Detalle : </label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  <?php
                  ?>
                  <input type="text" class="form-control" required="" value="<?php echo $r_b_evaluacion['detalle']; ?>" readonly>

                  <br>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label col-md-3 col-sm-3 col-xs-12">Ponderado (peso) : </label>
                <div class="col-md-9 col-sm-9 col-xs-12">
                  <input type="number" id="peso_evav_<?php echo $r_b_evaluacion['id']; ?>" value="<?php echo $r_b_evaluacion['ponderado']; ?>" min="0" max="100">%
                  <br>
                </div>
              </div>
              <div align="center">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="actualizarEvaluacion(<?php echo $r_b_evaluacion['id']; ?>)">Guardar</button>
              </div>

            </div>
          </div>
        </form>
        <!--FIN DE CONTENIDO DE MODAL-->
      </div>
    </div>
  </div>
</div>