<?php
    session_start();

    $base = "../";
    
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
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="../index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Panel Control</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <section class="feed">
            <header class="section-header">
                <h2><i class="fa-solid fa-gear"></i> Panel de Control</h2>
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
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>