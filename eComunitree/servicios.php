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
    <link rel="icon" href="img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
<header class="body-header" id="inicio">
        <a href="index.php" class="btn-index">
            <img src="img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-info">
                <strong><?= $_SESSION["nombre"] ?></strong>
                <i><?= $_SESSION["vivienda"] ?></i>
                <i><?= ucfirst($_SESSION["rol"]) ?></i>
            </div>
            <a href="desconectar.php" title="Cerrar sesión">
                <img src="img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <nav>
        <ul>
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Servicios</li>
        </ul>
    </nav>

    <main>
        <div class="services">
            <header class="services-header">
                <h2>Servicios: <small>página <?= $pagina ?></small></h2>
                <input type="search" placeholder="Buscar...">
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

            <div class="pager">
                <?php 
                // Configuración: cuántas páginas mostrar alrededor de la actual
                $rango = 1; 
                
                // Botón Anterior
                if ($pagina > 1): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina - 1 ?>" title="Anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                // Mostrar siempre la primera página si estamos lejos de ella
                if ($pagina > ($rango + 1)) {
                    echo '<a href="?pag=1" class="num-link">1</a>';
                    if ($pagina > ($rango + 2)) echo '<span class="dots">...</span>';
                }

                // Bucle para generar los números alrededor de la página actual
                for ($i = max(1, $pagina - $rango); $i <= min($total_paginas, $pagina + $rango); $i++): 
                    if ($i == $pagina): ?>
                        <span class="num-link activo"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?pag=<?= $i ?>" class="num-link"><?= $i ?></a>
                    <?php endif; 
                endfor;

                // Mostrar siempre la última página si estamos lejos de ella
                if ($pagina < ($total_paginas - $rango)) {
                    if ($pagina < ($total_paginas - $rango - 1)) echo '<span class="dots">...</span>';
                    echo '<a href="?pag=' . $total_paginas . '" class="num-link">' . $total_paginas . '</a>';
                }
                ?>

                <?php // Botón Siguiente
                if ($pagina < $total_paginas): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina + 1 ?>" title="Siguiente">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>

            <a href="#inicio" class="btn-up">
                <i class="fa-solid fa-angles-up"></i>
            </a>
        </div>

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
                            <a href="crear_incidencia.php">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Crear Incidencia
                            </a>
                        </li>

                        <?php if ($_SESSION["rol"] !== 'vecino'): ?>
                        <li>
                            <a href="admin/ins_aviso.php">
                                <i class="fa-solid fa-bullhorn"></i>
                                Crear Aviso
                            </a>
                        </li>
                        <li>
                            <a href="admin/ins_votacion.php">
                                <i class="fa-solid fa-envelope"></i>
                                Crear Votación
                            </a>
                        </li>
                        <li>
                            <a href="admin/gestion_incidencias.php">
                                <i class="fa-solid fa-person-digging"></i>
                                Resolver Incidencia
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
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
                            Panel de Control
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
    </main>
</body>
</html>