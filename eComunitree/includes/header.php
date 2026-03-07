<header class="body-header" id="inicio">
    <a href="<?= $base ?>index.php" class="btn-index">
        <img src="<?= $base ?>img/logo-transparencia.png" alt="logotipo">
        <h1>eComunitree</h1>
    </a>
    <div class="user">
        <div class="user-info">
            <strong><?= $_SESSION["nombre"] ?></strong>
            <i><?= $_SESSION["vivienda"] ?></i>
            <i><?= ucfirst($_SESSION["rol"]) ?></i>
        </div>
        <a href="<?= $base ?>desconectar.php" title="Cerrar sesión">
            <img src="<?= $base ?>img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
        </a>
    </div>
</header>