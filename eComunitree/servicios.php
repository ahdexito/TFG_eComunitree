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

    // RENDERIZADO DEL PAGINADOR
    $url_f = "";
    $rango = 1;
    $inicio = max(1, $pagina - $rango);
    $fin = min($total_paginas, $pagina + $rango);

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
        <a href="index.php" class="btn-index">
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
                <h2>Servicios: <small>Página <?= $pagina ?></small></h2>
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

            <div class="pager">
                <!-- FLECHA IZQUIERDA -->
                <?php if ($pagina > 1): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina - 1 ?>"><i class="fa-solid fa-angle-left"></i></a>
                <?php else: ?>
                    <span class="pag-arrow disabled"><i class="fa-solid fa-angle-left"></i></span>
                <?php endif; ?>
                
                <!-- PÁGINA LÍMITE IZQUIERDA -->
                <?php if ($pagina == 1): ?>
                    <span class="limite disabled">1</span>
                <?php else: ?>
                    <a href="?pag=1" class="limite">1</a>
                <?php endif; ?>

                <!-- PÁGINA ACTUAL -->
                <span class="activo">
                    <?= $pagina ?>
                </span>

                <!-- PÁGINA LÍMITE DERECHA -->
                <?php if ($total_paginas > 1): ?>
                    <?php if ($pagina == $total_paginas): ?>
                        <span class="limite disabled"><?= $total_paginas ?></span>
                    <?php else: ?>
                        <a href="?pag=<?= $total_paginas ?>" class="limite"><?= $total_paginas ?></a>
                    <?php endif; ?>
                <?php else: ?>
                    <span class="limite disabled">1</span>
                <?php endif; ?>

                <!-- FLECHA DERECHA -->
                <?php if ($pagina < $total_paginas): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina + 1 ?>"><i class="fa-solid fa-angle-right"></i></a>
                <?php else: ?>
                    <span class="pag-arrow disabled"><i class="fa-solid fa-angle-right"></i></span>
                <?php endif; ?>
            </div>
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