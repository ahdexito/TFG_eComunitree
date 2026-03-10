<?php
    session_start();
    include("db/db.inc");

    $base = "./";

    if (!isset($_SESSION["rol"])) {
        header("location:login.php");
        die();
    }

    // BUSCADOR. Capturar el término si existe
    $buscar = isset($_GET['s']) ? $conn->real_escape_string($_GET['s']) : '';
    $where = "";
    $params_url = "";

    if (!empty($buscar)) {
        // Filtrar por nombre o descripción
        $where = " WHERE nombre LIKE '%$buscar%' OR descripcion LIKE '%$buscar%' ";
        $params_url = "&s=" . urlencode($buscar);
    }

    // PAGINADOR
    $num_lineas = 12;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS CON FILTRO
    $total_resultado = $conn->query("SELECT COUNT(*) AS total FROM servicios $where");
    $total_filas = $total_resultado->fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // OBTENER SERVICIOS CON FILTRO
    $sql = "SELECT * FROM servicios $where ORDER BY id_servicio ASC LIMIT $num_lineas OFFSET $offset";
    $resultado = $conn->query($sql);
    $servicios = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Servicios</title>
    <link rel="stylesheet" href="css/servicios/servicios.css">
    <link rel="icon" href="img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <?php include("includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Servicios</li>
        </ul>

        <!-- ASIDE -->
        <?php include("includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <div class="services">
            <header class="services-header">
                <h2><i class="fa-solid fa-briefcase"></i> Servicios: <small>página <?= $pagina ?></small></h2>

                <form action="" method="GET" class="search-form">
                    <input type="search" name="s" placeholder="Buscar..." value="<?= htmlspecialchars($buscar) ?>">
                    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button> 
                </form>
            </header>

            <section class="services-body">

            <hr class="hr-section">

            <?php foreach ($servicios as $s): ?>
                <article>
                    <header class="article-header">
                        <h3> <?= htmlspecialchars($s['nombre']) ?> </h3>
                        <p> <?= htmlspecialchars($s['descripcion']) ?> </p>
                    </header>

                    <section class="article-body">
                        <ul>
                            <li>
                                <u>Teléfono</u>: 
                                <?= !empty($s['telefono']) ? '<p>' . htmlspecialchars($s['telefono']) . '</p>' : "<p>Sin información</p>" ?>
                            </li>

                            <li>
                                <u>Email</u>: 
                                <?= !empty($s['email']) ? '<p>' . htmlspecialchars($s['email']) . '</p>' : "<p>Sin información</p>" ?>
                            </li>

                            <li>
                                <u>Web</u>: 
                                <?php if (!empty($s['enlace'])): ?>
                                    <a href="<?= htmlspecialchars($s['enlace']) ?>" target="_blank">
                                        <p><?= htmlspecialchars($s['enlace']) ?></p>
                                    </a>
                                <?php else: ?>
                                    <p>Sin información</p>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </section>
                </article>
            <?php endforeach; ?>

            </section>

            <!-- PAGINADOR -->
            <?php include("includes/pager.php"); ?>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>