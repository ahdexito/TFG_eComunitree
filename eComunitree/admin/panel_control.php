<?php
    session_start();
    
    if (!isset($_SESSION["rol"])) {
        header("location:../index.php?usu=1");
        die();
    }

    elseif ($_SESSION["rol"] == 'vecino') {
        header("location:../index.php?usu=1");
        die();
    } 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Panel Control</title>
    <link rel="stylesheet" href="../css/panel_control/panel_control.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="../index.php" class="btn-index">
            <img src="../img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-info">
                <strong><?= $_SESSION["nombre"] ?></strong>
                <i><?= $_SESSION["vivienda"] ?></i>
                <i><?= ucfirst($_SESSION["rol"]) ?></i>
            </div>
            <a href="desconectar_admin.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <nav>
        <ul>
            <li>NAVEGACIÓN</li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Panel Control</li>
        </ul>
    </nav>

    <main>
        <section class="feed">
            <header class="section-header">
                <h2>Panel de control</h2>
            </header>

            <hr class="hr-section">

            <article>
                <a href="gestion_publicaciones.php" class="boton"><i class="fa-solid fa-comments"></i> PUBLICACIONES</a>

                <a href="gestion_servicios.php" class="boton"><i class="fa-solid fa-briefcase"></i> SERVICIOS</a>

                <a href="gestion_documentacion.php" class="boton"><i class="fa-regular fa-file-lines"></i> DOCUMENTACIÓN</a>
            <?php if ($_SESSION["rol"] === 'admin'): ?>
                <a href="gestion_usuarios.php" class="boton"><i class="fa-solid fa-users"></i> USUARIOS</a>
            <?php endif; ?>
            </article>
        </section>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                </li>
                <li class="has-dropdown">
                    <p>
                        <i class="fa-solid fa-comments"></i>
                        Publicaciones
                    </p>
                    <ul class="submenu">
                        <li>
                            <a href="../crear_incidencia.php">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Crear Incidencia
                            </a>
                        </li>

                        <?php if ($_SESSION["rol"] !== 'vecino'): ?>
                        <li>
                            <a href="ins_aviso.php">
                                <i class="fa-solid fa-bullhorn"></i>
                                Crear Aviso
                            </a>
                        </li>
                        <li>
                            <a href="ins_votacion.php">
                                <i class="fa-solid fa-envelope"></i>
                                Crear Votación
                            </a>
                        </li>
                        <li>
                            <a href="gestion_incidencias.php">
                                <i class="fa-solid fa-person-digging"></i>
                                Resolver Incidencia
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li>
                    <a href="../servicios.php">
                        <i class="fa-solid fa-briefcase"></i>
                        Servicios
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-calendar-days"></i>
                        Calendario
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-regular fa-file-lines"></i>
                        Documentación
                    </a>
                </li>
                <?php if ($_SESSION["rol"] !== "vecino"): ?>
                    <li>
                        <a href="panel_control.php">
                            <i class="fa-solid fa-gear"></i>
                            Panel de Control
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
    </main>
</body>
</html>