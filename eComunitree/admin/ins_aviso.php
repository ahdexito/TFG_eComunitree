<?php
    session_start();

    include("../db/db.inc");

    $base = "../";
    
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
            // Mensaje ins=0 enviado en email.php
            header("location:email.php");

        } catch (Exception $e) {
            $conn->rollback();
            header("location:gestion_publicaciones.php?ins=1");
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
    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./gestion_publicaciones.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_publicaciones.php">Gestión Publicaciones</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Crear Aviso</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-bullhorn icono-header"></i> Crear Aviso</h2>
                <hr>
            </div>

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
                    <i class="fa-solid fa-floppy-disk"></i> Confirmar
                </button>
            </form>
        </section>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>