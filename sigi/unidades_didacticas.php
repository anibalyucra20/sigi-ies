<?php
include("../include/conexion.php");
include("../include/busquedas.php");
include("../include/funciones.php");
include("../include/verificar_sesion_sigi.php");

if (!verificar_sesion($conexion)) {
  $sistema = base64_encode('S_SIGI');
  echo "<script>
                  window.location.replace('../passport/index?data=" . $sistema . "');
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
    echo "<center><h1>PERMISOS NO SUFICIENTES PARA ACCEDER A LA PÁGINA SOLICITADA</h1><br>
    <a href='../sigi/'>Regresar</a><br>
    <a href='../include/cerrar_sesion_sigi.php'>Cerrar Sesión</a><br>
    </center>";
  } else {
    # code...
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="Content-Language" content="es-ES">
      <!-- Meta, title, CSS, favicons, etc. -->
      <meta http-equiv="Content-Type" content="text/html" charset="UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title>unidades didácticas<?php include("../include/header_title.php"); ?></title>
      <!--icono en el titulo-->
      <link rel="shortcut icon" href="../images/favicon.ico">
      <!-- Bootstrap -->
      <link href="../plantilla/Gentella/vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
      <!-- Font Awesome -->
      <link href="../plantilla/Gentella/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
      <!-- NProgress -->
      <link href="../plantilla/Gentella/vendors/nprogress/nprogress.css" rel="stylesheet">
      <!-- iCheck -->
      <link href="../plantilla/Gentella/vendors/iCheck/skins/flat/green.css" rel="stylesheet">
      <!-- Datatables -->
      <link href="../plantilla/Gentella/vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
      <link href="../plantilla/Gentella/vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
      <link href="../plantilla/Gentella/vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
      <link href="../plantilla/Gentella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
      <link href="../plantilla/Gentella/vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

      <!-- Custom Theme Style -->
      <link href="../plantilla/Gentella/build/css/custom.min.css" rel="stylesheet">
      <!-- Script obtenido desde CDN jquery -->
      <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    </head>

    <body class="nav-md">
      <div class="container body">
        <div class="main_container">
          <!--menu-->
          <?php
          include("include/menu_director.php"); ?>

          <!-- page content -->
          <div class="right_col" role="main">
            <div class="">

              <div class="clearfix"></div>
              <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="x_panel">
                    <div class="">
                      <h2 align="center">Unidades Didácticas</h2>
                      <button class="btn btn-success" data-toggle="modal" data-target=".registrar"><i class="fa fa-plus-square"></i> Nuevo</button>

                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <br />

                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>Identificador</th>
                            <th>Programa de Estudios</th>
                            <th>Módulo</th>
                            <th>Semestre</th>
                            <th>Unidad Didactica</th>
                            <th>Créditos</th>
                            <th>Horas</th>
                            <th>Tipo</th>
                            <th>Orden</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $cont =0;
                          $ejec_busc_ud = buscarUnidadDidactica($conexion);
                          while ($res_busc_ud = mysqli_fetch_array($ejec_busc_ud)) {
                            $cont++;
                            $data = base64_encode($res_busc_ud['id']);
                          ?>
                            <tr>
                              <td><?php echo $cont; ?></td>
                              <?php
                              $id_semestre = $res_busc_ud['id_semestre'];
                              $ejec_busc_semestre = buscarSemestreById($conexion, $id_semestre);
                              $res_busc_semestre = mysqli_fetch_array($ejec_busc_semestre);

                              $id_modulo = $res_busc_semestre['id_modulo_formativo'];
                              $ejec_busc_modulo = buscarModuloFormativoById($conexion, $id_modulo);
                              $res_busc_modulo = mysqli_fetch_array($ejec_busc_modulo);


                              $id_carrera = $res_busc_modulo['id_programa_estudio'];
                              $ejec_busc_carrera = buscarProgramaEstudioById($conexion, $id_carrera);
                              $res_busc_carrera = mysqli_fetch_array($ejec_busc_carrera);
                              ?>
                              <td><?php echo $res_busc_carrera['nombre']; ?></td>
                              <td><?php echo "M" . $res_busc_modulo['nro_modulo']; ?></td>
                              <td><?php echo $res_busc_semestre['descripcion']; ?></td>
                              <td><?php echo $res_busc_ud['nombre']; ?></td>
                              <td><?php echo $res_busc_ud['creditos']; ?></td>
                              <td><?php echo $res_busc_ud['horas']; ?></td>
                              <td><?php echo $res_busc_ud['tipo']; ?></td>
                              <td><?php echo $res_busc_ud['orden']; ?></td>
                              <td>
                                <a class="btn btn-success" href="editar_unidaddidactica.php?id=<?php echo $data; ?>"><i class="fa fa-pencil-square-o"></i> Editar</a>
                              </td>
                            </tr>
                          <?php
                          };
                          ?>

                        </tbody>
                      </table>
                      <!--MODAL REGISTRAR-->
                      <div class="modal fade registrar" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">

                            <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                              </button>
                              <h4 class="modal-title" id="myModalLabel" align="center">Registrar Unidad Didáctica</h4>
                            </div>
                            <div class="modal-body">
                              <!--INICIO CONTENIDO DE MODAL-->
                              <div class="x_panel">

                                <div class="" align="center">
                                  <h2></h2>
                                  <div class="clearfix"></div>
                                </div>
                                <div class="x_content">
                                  <br />
                                  <form role="form" action="operaciones/registrar_unidad_didactica.php" class="form-horizontal form-label-left input_mask" method="POST">
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Unidad Didáctica : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" class="form-control" name="ud" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Programa de Estudios : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="carrera_m" name="carrera_m" value="" required="required">
                                          <option></option>
                                          <?php
                                          $ejec_busc_carr = buscarProgramaEstudio($conexion);
                                          while ($res__busc_carr = mysqli_fetch_array($ejec_busc_carr)) {
                                            $id_carr = $res__busc_carr['id'];
                                            $carr = $res__busc_carr['nombre'];
                                          ?>
                                            <option value="<?php echo $id_carr;
                                                            ?>"><?php echo $carr; ?></option>
                                          <?php
                                          }
                                          ?>
                                        </select>
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Módulo Formativo : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="modulo" name="modulo" value="" required="required">
                                          <!--las opciones se cargan con ajax y javascript  dependiendo de la carrera elegida,verificar en la parte final-->
                                        </select>
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Semestre : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="semestre" name="semestre" value="" required="required">

                                        </select>
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Créditos : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="number" class="form-control" name="creditos" required="required">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Horas (Semestral): </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="number" class="form-control" name="horas" required="required">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Tipo : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="tipo" name="tipo" value="" required="required">
                                          <option value=""></option>
                                          <option value="ESPECIALIDAD">ESPECIALIDAD</option>
                                          <option value="EMPLEABILIDAD">EMPLEABILIDAD</option>
                                        </select>
                                        <br>
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


                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- /page content -->

          <!-- footer content -->
          <?php
          include("../include/footer.php");
          ?>
          <!-- /footer content -->
        </div>
      </div>

      <!-- jQuery -->
      <script src="../plantilla/Gentella/vendors/jquery/dist/jquery.min.js"></script>
      <!-- Bootstrap -->
      <script src="../plantilla/Gentella/vendors/bootstrap/dist/js/bootstrap.min.js"></script>
      <!-- FastClick -->
      <script src="../plantilla/Gentella/vendors/fastclick/lib/fastclick.js"></script>
      <!-- NProgress -->
      <script src="../plantilla/Gentella/vendors/nprogress/nprogress.js"></script>
      <!-- iCheck -->
      <script src="../plantilla/Gentella/vendors/iCheck/icheck.min.js"></script>
      <!-- Datatables -->
      <script src="../plantilla/Gentella/vendors/datatables.net/js/jquery.dataTables.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
      <script src="../plantilla/Gentella/vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
      <script src="../plantilla/Gentella/vendors/jszip/dist/jszip.min.js"></script>
      <script src="../plantilla/Gentella/vendors/pdfmake/build/pdfmake.min.js"></script>
      <script src="../plantilla/Gentella/vendors/pdfmake/build/vfs_fonts.js"></script>

      <!-- Custom Theme Scripts -->
      <script src="../plantilla/Gentella/build/js/custom.min.js"></script>
      <script>
        $(document).ready(function() {
          $('#example').DataTable({
            "language": {
              "processing": "Procesando...",
              "lengthMenu": "Mostrar _MENU_ registros",
              "zeroRecords": "No se encontraron resultados",
              "emptyTable": "Ningún dato disponible en esta tabla",
              "sInfo": "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
              "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
              "infoFiltered": "(filtrado de un total de _MAX_ registros)",
              "search": "Buscar:",
              "infoThousands": ",",
              "loadingRecords": "Cargando...",
              "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": "Siguiente",
                "previous": "Anterior"
              },
            }
          });

        });
      </script>
      <!--script para obtener los modulos dependiendo de la carrera que seleccione-->
      <script type="text/javascript">
        $(document).ready(function() {
          recargarlista();
          $('#carrera_m').change(function() {
            recargarlista();
          });
          $('#modulo').change(function() {
            recargarsemestre();
          });
        })
      </script>
      <script type="text/javascript">
        function recargarlista() {
          $.ajax({
            type: "POST",
            url: "operaciones/obtener_modulos.php",
            data: "id_carrera=" + $('#carrera_m').val(),
            success: function(r) {
              $('#modulo').html(r);
            }
          });
          recargarsemestre();
        }
      </script>
      <script type="text/javascript">
        function recargarsemestre() {
          $.ajax({
            type: "POST",
            url: "operaciones/obtener_semestres.php",
            data: "modulo=" + $('#modulo').val(),
            success: function(r) {
              $('#semestre').html(r);
            }
          });
        }
      </script>

      <?php mysqli_close($conexion); ?>
    </body>

    </html>
<?php }
}
