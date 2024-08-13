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
                    <li><a href="../bolsa/"><i class="fa fa-home"></i>Inicio</a></li>
                    <li><a href="empresas"><i class="fa fa-graduation-cap"></i> Empresas </a></li>
                    <li><a href="ofertas"><i class="fa fa-graduation-cap"></i> Ofertas Laborales </a></li>
                    <li><a href="curriculum"><i class="fa fa-graduation-cap"></i> Mi Curriculum </a></li>
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
                        <li><a href="../include/cerrar_sesion_bolsa"><i class="fa fa-sign-out pull-right"></i> Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!-- /top navigation -->