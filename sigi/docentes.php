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
    $sistemas = solicitarSistemas($conexion, '', 1, 'S_SIGI');
    $sistemas = decodificar($sistemas);
?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="Content-Language" content="es-ES">
      <!-- Meta, title, CSS, favicons, etc. -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">

      <title>Docentes <?php include("../include/header_title.php"); ?></title>
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
                      <h2 align="center">Docentes</h2>
                      <button class="btn btn-success" data-toggle="modal" data-target=".registrar"><i class="fa fa-plus-square"></i> Nuevo</button>

                      <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                      <br />

                      <table id="example" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                          <tr>
                            <th>Nro</th>
                            <th>DNI</th>
                            <th>Apellidos y Nombres</th>
                            <th>Género</th>
                            <th>Sede</th>
                            <th>Programa de Estudio</th>
                            <th>Cargo</th>
                            <th>Activo</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $contr = 0;
                          $ejec_busc_doc = buscarUsuarioDocentesOrderByApellidosNombres($conexion);
                          while ($res_busc_doc = mysqli_fetch_array($ejec_busc_doc)) {
                            $contr += 1;

                            $id_sede = $res_busc_doc['id_sede'];
                            $ejec_busc_sede = buscarSedeById($conexion, $id_sede);
                            $res__busc_sede = mysqli_fetch_array($ejec_busc_sede);

                            $id_cargo = $res_busc_doc['id_rol'];
                            $ejec_busc_carg = buscarRolById($conexion, $id_cargo);
                            $res_busc_carg = mysqli_fetch_array($ejec_busc_carg);

                            $id_pe = $res_busc_doc['id_programa_estudios'];
                            $ejec_busc_pe = buscarProgramaEstudioById($conexion, $id_pe);
                            $res_busc_pe = mysqli_fetch_array($ejec_busc_pe);
                          ?>
                            <tr>
                              <td><?php echo $contr; ?></td>
                              <td><?php echo $res_busc_doc['dni']; ?></td>
                              <td><?php echo $res_busc_doc['apellidos_nombres']; ?></td>
                              <td><?php echo $res_busc_doc['genero']; ?></td>
                              <td><?php echo $res__busc_sede['nombre']; ?></td>
                              <td><?php echo $res_busc_pe['nombre']; ?></td>
                              <td><?php echo $res_busc_carg['nombre']; ?></td>
                              <td><?php if ($res_busc_doc['estado'] == 0) {
                                    echo "NO";
                                  } else {
                                    echo "SI";
                                  } ?></td>
                              <td>
                                <button class="btn btn-success" data-toggle="modal" data-target=".edit_<?php echo $res_busc_doc['id']; ?>"><i class="fa fa-pencil-square-o"></i> Editar</button>
                                <button class="btn btn-warning" data-toggle="modal" data-target=".permisos_<?php echo $res_busc_doc['id']; ?>"><i class="fa fa-key"></i> Permisos</button>
                              </td>
                            </tr>
                          <?php
                            include('include/acciones_usuarios.php');
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
                              <h4 class="modal-title" id="myModalLabel" align="center">Registrar Docente</h4>
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
                                  <form role="form" action="operaciones/registrar_docente.php" class="form-horizontal form-label-left input_mask" method="POST">
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">DNI : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" class="form-control" name="dni" required="required" maxlength="8">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Apellidos y Nombres : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" class="form-control" name="nom_ap" required="required">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Género : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="genero" name="genero" value="" required="required">
                                          <option></option>
                                          <option value="M">Masculino</option>
                                          <option value="F">Femenino</option>
                                        </select>
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Fecha de Nacimiento : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="date" class="form-control" name="fecha_nac" required="required">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Dirección : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" class="form-control" name="direccion" required="required" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Correo Electrónico : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="email" class="form-control" name="email" required="required">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Teléfono : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="Number" class="form-control" name="telefono" required="required" maxlength="15">
                                        <br>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="control-label col-md-3 col-sm-3 col-xs-12">Discapacidad : </label>
                                      <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select class="form-control" id="discapacidad" name="discapacidad" value="" required="required">
                                          <option></option>
                                          <option value="SI">SI</option>
                                          <option value="NO" selected>NO</option>
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
                                                            ?>"><?php echo $sede; ?></option>
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
                                        <select class="form-control" id="cargo" name="cargo" value="" required="required">
                                          <option></option>
                                          <?php
                                          $ejec_busc_car = buscarRol($conexion);
                                          while ($res__busc_car = mysqli_fetch_array($ejec_busc_car)) {
                                            $id_car = $res__busc_car['id'];
                                            $car = $res__busc_car['nombre'];
                                          ?>
                                            <option value="<?php echo $id_car;
                                                            ?>"><?php echo $car; ?></option>
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
                                            <option value="<?php echo $id_pe;
                                                            ?>"><?php echo $pe; ?></option>
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

                      <!-- FIN MODAL REGISTRAR-->

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
      <?php mysqli_close($conexion); ?>
    </body>

    </html>
<?php
  }
}
