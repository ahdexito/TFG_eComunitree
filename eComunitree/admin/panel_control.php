<?php
    session_start();
    
    if (!isset($_SESSION["rol"])) {
        header("location:../index.php");
        die();
    }

    elseif ($_SESSION["rol"] == 'vecino') {
        header("location:../index.php");
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Panel Control</title>
    <link rel="stylesheet" href="css/index/index.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="index.html" class="btn-index">
            <img src="img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <a href="#"><i class="fa-regular fa-circle-user usuario"></i></a>
        </div>
    </header>

    <main>
        <section class="feed">
            <header class="feed-header">
                <h2>Panel de control</h2>
            </header>

            <hr>

            <article>
                <h3>PUBLICACIONES</h3>
                <a href="">Crear Aviso</a>
                <a href="">Crear Votación</a>
                <a href="">Cambiar Estado Incidencia</a>

                <!-- administrador -->
                <?php if ($_SESSION["rol"] == 'admin'): ?>

                <a href="">Gestionar Publicaciones</a>

                <?php endif; ?>
            </article>

            <article>
                <h3>SERVICIOS</h3>
                <a href="">Añadir Servicio</a>
                <a href="">Editar Servicio</a>
                <a href="">Eliminar Servicio</a>
            </article>

            <article>
                <h3>DOCUMENTACIÓN</h3>
                <a href="">Añadir Documentación</a>

                <!-- administrador -->
                <?php if ($_SESSION["rol"] == 'admin'): ?>

                <a href="">Eliminar documentación</a>

                <?php endif; ?>
            </article>

            <!-- administrador -->
            <?php if ($_SESSION["rol"] == 'admin'): ?>
            <article>
                <h3>USUARIOS</h3>
                <a href="">Crear Usuario</a>
                <a href="">Editar Usuario</a>
                <a href="">Eliminar Usuario</a>
                </ul>
            </article>
            <?php endif; ?>
        </section>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="index.html">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a></li>
                <li>
                    <a href="#">
                    <i class="fa-solid fa-plus"></i>
                    Nueva incidencia
                    </a></li>
                <li>
                    <a href="servicios.html">
                    <i class="fa-solid fa-phone-volume"></i>
                    Servicios
                </a></li>
                <li>
                    <a href="#">
                    <i class="fa-solid fa-calendar-days"></i>
                    Calendario
                </a></li>
                <li><a href="#">
                    <i class="fa-regular fa-file-lines"></i>
                    Documentación
                </a></li>
                <li><a href="panel_control.html">
                    <i class="fa-solid fa-gear"></i>
                    Panel de control
                </a></li>
            </ul>
        </aside>
    </main>
</body>
</html>