<?php
    session_start();
    include("../db/db.inc");
    
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] === 'vecino') {
        header("location:../index.php");
        die();
    } 

    if (isset($_POST["titulo"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $contenido = htmlspecialchars($_POST["descripcion"]);
        $fecha_cierre = $_POST["fecha_cierre"];
        $opciones = $_POST["opciones"]; // Array de textos
        $id_usuario = $_SESSION["id_usuario"];
        $tipo_publicacion = 'votacion';

        $conn->begin_transaction();

        try {
            // 1. Insertar en la tabla padre (publicaciones)
            $sql1 = "INSERT INTO publicaciones (id_usuario, tipo, titulo, contenido) VALUES (?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("isss", $id_usuario, $tipo_publicacion, $titulo, $contenido);
            $stmt1->execute();

            $id_pub = $conn->insert_id;

            // 2. Insertar en la tabla votaciones
            $sql2 = "INSERT INTO votaciones (id_publicacion, fecha_cierre) VALUES (?, ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("is", $id_pub, $fecha_cierre);
            $stmt2->execute();

            // 3. Insertar las opciones de votación
            $sql3 = "INSERT INTO opciones_votacion (id_publicacion, texto) VALUES (?, ?)";
            $stmt3 = $conn->prepare($sql3);

            foreach ($opciones as $texto_opcion) {
                if (!empty(trim($texto_opcion))) {
                    $texto_limpio = htmlspecialchars($texto_opcion);
                    $stmt3->bind_param("is", $id_pub, $texto_limpio);
                    $stmt3->execute();
                }
            }

            $conn->commit();
            header("location:gestion_publicaciones.php?publi=0");

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
    <title>eComunitree | Crear Votación</title>
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
            <a href="desconectar.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil">
            </a>
        </div>
    </header>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-solid fa-check-to-slot icono-header"></i>
                <h2>Crear Votación</h2>
            </div>

            <hr>

            <form method="POST">
                <div class="form">
                    <div class="casilla">
                        <label for="titulo">Título de la votación</label>
                        <input type="text" name="titulo" id="titulo" placeholder="Ej: Renovación de la pintura" required>
                    </div>

                    <div class="casilla">
                        <label for="fecha_cierre">Fecha de cierre</label>
                        <input type="date" name="fecha_cierre" id="fecha_cierre" min="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="casilla">
                        <label for="descripcion">Descripción detallada</label>
                        <textarea name="descripcion" id="descripcion" class="area-texto" placeholder="Explica los detalles de la votación..." required></textarea>
                    </div>

                    <div class="casilla">
                        <label>Opciones de respuesta</label>
                        <div id="contenedor-opciones">
                            <input type="text" name="opciones[]" placeholder="Opción 1" required style="margin-bottom: 10px;">
                            <input type="text" name="opciones[]" placeholder="Opción 2" required style="margin-bottom: 10px;">
                        </div>
                        <button type="button" onclick="agregarOpcion()" class="btn-filter" style="width: fit-content; padding: 5px 15px;">
                            <i class="fa-solid fa-plus"></i> Añadir opción
                        </button>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Publicar Votación
                </button>
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
                <li>
                    <a href="../crear_incidencia.php">
                        <i class="fa-solid fa-plus"></i>
                        Nueva incidencia
                    </a>
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
                <li>
                    <a href="panel_control.php">
                        <i class="fa-solid fa-gear"></i>
                        Panel de control
                    </a>
                </li>
            </ul>
        </aside>
    </main>

    <script src="../javascript/agregar-opcion-voto.js"></script>
</body>
</html>