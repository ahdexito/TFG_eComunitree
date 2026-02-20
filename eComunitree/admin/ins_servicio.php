<?php
    session_start();
    
    include("../db/db.inc");

    if (!isset($_SESSION["rol"])) {
        header("location:../login.php?usu=1");
        die();
    }
    
    elseif ($_SESSION["rol"] === 'vecino') {
        header("location:../login.php?usu=1");
        die();
    }

    // INSERTAR SERVICIO
    if (isset($_POST["nombre"])) {
        $nombre = htmlspecialchars($_POST["nombre"]);
        $activo = intval($_POST['activo']);
        $descripcion = htmlspecialchars($_POST["descripcion"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $email = htmlspecialchars($_POST["email"]);
        $enlace = htmlspecialchars($_POST["enlace"]);

        $sql = 
            "INSERT INTO servicios (nombre, descripcion, telefono, email, enlace, activo)
            VALUES ('$nombre', '$descripcion', '$telefono', '$email', '$enlace', '$activo')";

        if (mysqli_query($conn, $sql)) {
            header("location:gestion_servicios.php?serv=0");// insertado correctamente
        } 
            
        else {
            header("location:gestion_servicios.php?serv=1");// error al insertar
        }

        die();
    }    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Servicio</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="../index.php" class="btn-index">
            <img src="../img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-name">
                <p><?= $_SESSION["nombre"] ?> (<?= $_SESSION["vivienda"] ?>)</p>
                <p><?= ucfirst($_SESSION["rol"]) ?></p>
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
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_servicios.php">Gestión Servicios</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Añadir Servicio</li>
        </ul>
    </nav>
    
    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-regular fa-square-plus icono-header"></i>
                <h2>Insertar Servicio</h2>
            </div>

            <hr>

            <form method="POST">
                <div class="form">
                    <div class="casilla">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" placeholder="Texto" required>
                    </div>
                    
                    <div class="casilla">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="area-texto" placeholder="Área de texto"></textarea>
                    </div>

                    <div class="casilla">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="email">Email</label>
                        <input type="text" name="email" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="enlace">Enlace</label>
                        <input type="text" name="enlace" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="activo">Activo</label>
                        <select name="activo" id="activo" class="seleccion" required>
                            <option value="1" selected>Activado</option>
                            <option value="0">Desactivado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar Servicio</button>
            </form>
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