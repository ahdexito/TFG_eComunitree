<?php
    session_start();
    include("../db/db.inc");
    
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] === 'vecino') {
        header("location:index.php");
        die();
    } 

    if (isset($_POST["titulo"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $contenido = htmlspecialchars($_POST["descripcion"]);
        $id_usuario = $_SESSION["id_usuario"];
        $tipo_publicacion = 'aviso';

        $conn->begin_transaction();

        try {
            // Insertar en la tabla padre (publicaciones)
            $sql1 = "INSERT INTO publicaciones (id_usuario, tipo, titulo, contenido) VALUES (?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("isss", $id_usuario, $tipo_publicacion, $titulo, $contenido);
            $stmt1->execute();

            $id_publicacion = $conn->insert_id;

            // Insertar en la tabla hija (avisos)
            $sql2 = "INSERT INTO avisos (id_publicacion) VALUES (?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $id_publicacion);
            $stmt2->execute();

            $conn->commit();
            header("location:email.php");

        } catch (Exception $e) {
            $conn->rollback();
            header("location:gestion_publicaciones.php?publi=1");
        }
        exit();
    }    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Panel Control</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
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
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_publicaciones.php">Gestión Publicaciones</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Crear Aviso</li>
        </ul>
    </nav>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-bullhorn icono-header"></i> Crear Aviso</h2>
            </div>

            <hr>

            <form method="POST">
                <div class="form">
                    <div class="casilla">
                        <label for="titulo">Título del aviso</label>
                        <input type="text" name="titulo" id="titulo" placeholder="Ej: Próxima reunión de vecinos" required>
                    </div>

                    <div class="casilla">
                        <label for="descripcion">Contenido detallado</label>
                        <textarea name="descripcion" id="descripcion" class="area-texto" placeholder="Escribe aquí el cuerpo del aviso..." required></textarea>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Publicar Aviso
                </button>
            </form>
        </section>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
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