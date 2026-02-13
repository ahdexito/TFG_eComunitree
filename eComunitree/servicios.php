<?php
    session_start();

    include("db/db.inc");

    if (!isset($_SESSION["rol"])) {
        header("location:login.php");
        die();
    }

    // PAGINADOR
    $num_lineas = 10;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS
    $total_resultado = $conn -> query(
        "SELECT COUNT(*) AS total FROM servicios"
    );
    $total_filas = $total_resultado -> fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // OBTENER SERVICIOS
    $resultado = $conn->query(
        "SELECT * FROM servicios 
        ORDER BY id_servicio ASC
        LIMIT $num_lineas OFFSET $offset"
    );
    $servicios = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Servicios</title>
    <link rel="stylesheet" href="css/servicios/servicios.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="body-header">
        <a href="login.php" class="btn-index">
            <img src="img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-name">
                <p><?= $_SESSION["nombre"] ?> (<?= $_SESSION["vivienda"] ?>)</p>
                <p><?= ucfirst($_SESSION["rol"]) ?></p>
            </div>
            <a href="desconectar.php" title="Cerrar sesión">
                <img src="img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <main>
        <div class="services">
            <header class="services-header">
                <h2>Servicios</h2>
                <input type="search" placeholder="Buscar...">
            </header>
            <hr>

            <section class="services-body">
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
                                <?= !empty($s['telefono']) ? htmlspecialchars($s['telefono']) : "Sin información" ?>
                            </li>

                            <li>
                                <u>Email</u>: 
                                <?= !empty($s['email']) ? htmlspecialchars($s['email']) : "Sin información" ?>
                            </li>

                            <li>
                                <u>Web</u>: 
                                <?php if (!empty($s['enlace'])): ?>
                                    <a href="<?= htmlspecialchars($s['enlace']) ?>" target="_blank">
                                        <?= htmlspecialchars($s['enlace']) ?>
                                    </a>
                                <?php else: ?>
                                    Sin información
                                <?php endif; ?>
                            </li>
                        </ul>
                    </section>
                </article>
            <?php endforeach; ?>
            </section>
        </div>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="index.php">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-plus"></i>
                        Nueva incidencia
                    </a>
                </li>
                <li>
                    <a href="servicios.php">
                        <i class="fa-solid fa-phone-volume"></i>
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
                        <a href="admin/panel_control.php">
                            <i class="fa-solid fa-gear"></i>
                            Panel de control
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
    </main>
</body>
</html>