<input type="checkbox" id="menu-toggle" class="menu-checkbox">
<label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

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

        <li><a href="#">
            Calendario<i class="fa-solid fa-calendar-days"></i>
        </a></li>

        <li><a href="#">
            Documentación<i class="fa-regular fa-file-lines"></i>
        </a></li>
        
        <?php if ($_SESSION["rol"] !== "vecino"): ?>
        <li><a href="<?= $base ?>admin/panel_control.php">
            Panel de Control<i class="fa-solid fa-gear"></i>
        </a></li>
        <?php endif; ?>
    </ul>
</aside>
