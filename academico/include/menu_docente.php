<div class="col-md-3 left_col menu_fixed">
    <div class="left_col scroll-view">
        <div class="clearfix"></div>
        <!-- menu profile quick info -->
        <div class="profile clearfix">
            <div class="profile_pic">
                <img src="../images/logo.png" alt="..." class="img-circle profile_img">
            </div>
            <div class="profile_info">
                <span>Bienvenido,</span>
            </div>
        </div>
        <!-- /menu profile quick info -->
        <br />
        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Menu de Navegación</h3>
                <ul class="nav side-menu">
                    <li><a href="../academico/"><i class="fa fa-home"></i>Inicio</a></li>
                    <li><a><i class="fa fa-calendar"></i> Planificacion <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="programacion">Programacion de Clases</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-check-square-o"></i> Matrículas <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="matriculas">Registro de Matrícula</a></li>
                            <li class="sub_menu"><a href="licencias">Licencias</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-graduation-cap"></i> Estudiantes <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="estudiantes">Relación de Estudiantes</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-pencil-square-o"></i> Unidades Didácticas <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="unidades_didacticas">Mis Unidades Didácticas</a></li>
                            <li class="sub_menu"><a href="pe_unidades_didacticas">Todas las Unidades Didácticas</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-book"></i> Evaluación <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="calificaciones_unidades_didacticas">Registro de Evaluación</a></li>
                        </ul>
                    </li>
                    <li><a><i class="fa fa-book"></i> Reportes <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="reportes_coordinador">Reportes de Coordinador</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- top navigation -->
<div class="top_nav">
    <div class="nav_menu">
        <nav>
            <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
            </div>

            <ul class="nav navbar-nav navbar-right">
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <img src="../images/user.png" alt=""><?php echo $rb_usuario['apellidos_nombres']; ?>
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <li>
                            <a href="../passport/enviar_correo"> Cambiar mi contraseña</a>
                        </li>
                        <li><a href="../include/cerrar_sesion_acad"><i class="fa fa-sign-out pull-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <?php
                        $busc_per_id = buscarPeriodoAcadById($conexion, $_SESSION['acad_periodo']);
                        $res_busc_per_id = mysqli_fetch_array($busc_per_id);
                        echo $res_busc_per_id['nombre']; ?>
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <?php
                        $buscar_periodos = buscarPeriodoAcademicoInvert($conexion);
                        while ($res_busc_periodos = mysqli_fetch_array($buscar_periodos)) {
                        ?>
                            <li><a href="operaciones/actualizar_sesion_periodo?dato=<?php echo $res_busc_periodos['id']; ?>"><?php if ($res_busc_periodos['id'] == $_SESSION['acad_periodo']) {
                                                                                                                                    echo "<b>";
                                                                                                                                } ?><?php echo $res_busc_periodos['nombre']; ?><?php if ($res_busc_periodos['id'] == $_SESSION['acad_periodo']) {
                                                                                                                                                                                        echo "</b>";
                                                                                                                                                                                    } ?></a></li>
                        <?php
                        }
                        ?>
                    </ul>
                </li>
                <li class="">
                    <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <?php
                        $busc_sede_id = buscarSedeById($conexion, $_SESSION['acad_sede']);
                        $res_busc_sede_id = mysqli_fetch_array($busc_sede_id);
                        echo $res_busc_sede_id['nombre']; ?>
                        <span class=" fa fa-angle-down"></span>
                    </a>
                    <ul class="dropdown-menu dropdown-usermenu pull-right">
                        <?php
                        $b_sedes_menu = buscarSede($conexion);
                        while ($rb_sede_menu = mysqli_fetch_array($b_sedes_menu)) {
                        ?>
                            <li><a href="operaciones/actualizar_sesion_sedes?dato=<?php echo $rb_sede_menu['id']; ?>"><?php if ($rb_sede_menu['id'] == $_SESSION['acad_sede']) {
                                                                                                                            echo "<b>";
                                                                                                                        } ?><?php echo $rb_sede_menu['nombre']; ?><?php if ($rb_sede_menu['id'] == $_SESSION['acad_sede']) {
                                                                                                                                                                                    echo "</b>";
                                                                                                                                                                                } ?></a></li>
                        <?php } ?>
                    </ul>
                </li>


            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->