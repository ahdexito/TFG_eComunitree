<input type="checkbox" id="menu-toggle" class="menu-checkbox">
<label for="menu-toggle" class="menu-button">
    <i class="fa-solid fa-bars"></i>
</label>

<div id="aside-overlay" class="aside-overlay"></div>

<aside class="main-aside">
    <h3>Navegación</h3>
    <hr>
    <ul>
        <li><a href="<?= $base ?>index.php">
            Inicio<i class="fa-solid fa-house"></i>
        </a></li>

        <li class="has-dropdown">
            <p>Publicaciones<i class="fa-solid fa-comments"></i></p>

            <ul class="submenu">
                <li><a href="<?= $base ?>crear_incidencia.php">
                    Crear Incidencia<i class="fa-solid fa-triangle-exclamation"></i>
                </a></li>

                <?php if ($_SESSION["rol"] !== 'vecino'): ?>
                <li><a href="<?= $base ?>admin/ins_aviso.php">
                    Crear Aviso<i class="fa-solid fa-bullhorn"></i>    
                </a></li>

                <li><a href="<?= $base ?>admin/ins_votacion.php">
                    Crear Votación<i class="fa-solid fa-envelope"></i>
                </a></li>

                <li><a href="<?= $base ?>admin/gestion_incidencias.php">
                    Resolver Incidencia<i class="fa-solid fa-person-digging"></i>    
                </a></li>
                <?php endif; ?>
            </ul>
        </li>

        <li><a href="<?= $base ?>servicios.php">
            Servicios<i class="fa-solid fa-briefcase"></i>
        </a></li>

        <li><a href="<?= $base ?>convivencia.php">
            Convivencia<i class="fa-solid fa-handshake-angle"></i>
        </a></li>
        
        <?php if ($_SESSION["rol"] !== "vecino"): ?>
        <li><a href="<?= $base ?>admin/panel_control.php">
            Panel de Control<i class="fa-solid fa-gear"></i>
        </a></li>
        <?php endif; ?>

        <li class="has-dropdown">
            <p>Sesión<i class="fa-solid fa-user"></i></p>

            <ul class="submenu">
                <li><a href="<?= $base ?>editar_datos.php">
                    Mis Datos<i class="fa-solid fa-user-pen"></i> 
                </a></li>
                
                <li><a href="<?= $base ?>cambiar_password.php">
                    Cambiar Contraseña<i class="fa-solid fa-key"></i>
                </a></li>

                <li><a href="<?= $base ?>cambiar_foto.php">
                    Cambiar Foto<i class="fa-solid fa-camera"></i>
                </a></li>
                
                <li><a href="<?= $base ?>desconectar.php" class="logout" onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');">
                    Cerrar Sesión<i class="fa-solid fa-right-from-bracket"></i>
                </a></li>
            </ul>
        </li>
    </ul>
</aside>
