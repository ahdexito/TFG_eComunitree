<header class="body-header" id="inicio">
    <a href="<?= $base ?>index.php" class="btn-index">
        <img src="<?= $base ?>img/logo-transparencia.png" alt="logotipo">
        <h1>eComunitree</h1>
    </a>
    
    <div class="user" id="user-menu-parent">
        <div class="user-info">
            <strong><?= $_SESSION["nombre"] ?></strong>
            <i><?= ucfirst($_SESSION["rol"]) ?></i>
            <i><?= $_SESSION["vivienda"] ?></i>
        </div>

        <div class="user-trigger" id="user-menu-trigger">
            <img src="<?= $base ?>img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
        </div>

        <div class="user-dropdown">
            <ul>
                <li><a href="<?= $base ?>editar_datos.php"><i class="fa-solid fa-user-pen"></i> Mis Datos</a></li>
                <li><a href="<?= $base ?>cambiar_foto.php"><i class="fa-solid fa-camera"></i> Cambiar Foto</a></li>
                <hr>
                <li>
                    <a href="<?= $base ?>desconectar.php" class="logout" onclick="return confirm('¿Estás seguro de que deseas cerrar sesión?');">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>