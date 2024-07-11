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
                <h2>Usuario</h2>
            </div>
        </div>
        <!-- /menu profile quick info -->

        <br />

        <!-- sidebar menu -->
        <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
            <div class="menu_section">
                <h3>Menu de Navegación</h3>
                <ul class="nav side-menu">
                    <li><a href="../sigi/"><i class="fa fa-home"></i>Inicio</a>
                    </li>
                    <li><a><i class="fa fa-calendar"></i> Gestión IES <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li><a href="informacion">Información General</a></li>
                            <li class="sub_menu"><a href="periodo_lectivo">Periodo Lectivo</a></li>
                            <li class="sub_menu"><a href="docentes">Docentes</a>
                            <li class="sub_menu"><a href="sedes">Sedes</a>
                            <li class="sub_menu"><a href="programa_estudios">Programas de Estudio</a></li>
                            <li class="sub_menu"><a href="modulos_formativos">Módulos Formativos</a></li>
                            <li class="sub_menu"><a href="semestre">Semestre</a></li>
                            <li class="sub_menu"><a href="unidades_didacticas">Unidades Didácticas</a></li>
                            <li class="sub_menu"><a href="competencias">Competencias</a></li>
                            <li class="sub_menu"><a href="capacidades">Capacidades</a></li>
                            <li class="sub_menu"><a href="sistema">Sistema</a></li>
                        </ul>
                    </li>
                    <!--<li><a><i class="fa fa-check-square-o"></i> Reportes <span class="fa fa-chevron-down"></span></a>
                        <ul class="nav child_menu">
                            <li class="sub_menu"><a href="reporte_matricula">Reporte de Registro de Matrícula</a></li>
                            <li class="sub_menu"><a href="reporte_actas_notas">Reporte de Acta Consolidada de Notas</a></li>
                        </ul>
                    </li>-->
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
                            <a href=""> Cambiar mi contraseña</a>
                        </li>
                        <li><a href="../include/cerrar_sesion_sigi.php"><i class="fa fa-sign-out pull-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->