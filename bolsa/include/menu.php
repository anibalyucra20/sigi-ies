<header id="page-topbar">
    <div class="navbar-header">
        <!-- LOGO -->
        <div class="navbar-brand-box d-flex align-items-left">
        <a href="admin" class="logo">
                <img src="../images/logo.png" alt="" width="90px">
                <?php
                $b_datos = buscarDatosSistema($conexion);
                $r_b_datos = mysqli_fetch_array($b_datos);
                ?>
                <span>
                    Bolsa Laboral <?php  echo $r_b_datos['nombre_corto']; ?>
                </span>
            </a>
            <button type="button" class="btn btn-sm mr-2 font-size-16 d-lg-none header-item waves-effect waves-light" data-toggle="collapse" data-target="#topnav-menu-content">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown d-inline-block ml-2">
                <button type="button" class="btn header-item waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="../images/user.png" alt="Header Avatar">
                    <span class="d-none d-sm-inline-block ml-1"><?php echo $rb_usuario['apellidos_nombres']; ?></span>
                    <i class="mdi mdi-chevron-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="../perfil">
                        <span>Mi perfil</span>
                    </a>
                    <a class="dropdown-item d-flex align-items-center justify-content-between" href="../include/cerrar_sesion_bolsa.php">
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>

        </div>
    </div>
</header>
<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="admin">
                            <i class="fas fa-home"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ofertas_laborales">
                            <i class="fas fa-book"></i>Ofertas Laborales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios">
                            <i class="fas fa-users"></i>Usuarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="empresas">
                            <i class="fas fa-home"></i>Empresas
                        </a>
                    </li>
                    
                </ul>
            </div>
        </nav>
    </div>
</div>