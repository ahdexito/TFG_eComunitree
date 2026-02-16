<?php
    session_start();

    include("db/db.inc");

    if (!isset($_SESSION["rol"])) {
        header("location:login.php");
        die();
    }
    
    // PAGINADOR
    $num_lineas = 8;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS
    $total_resultado = $conn -> query(
        "SELECT COUNT(*) AS total FROM publicaciones"
    );
    $total_filas = $total_resultado -> fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // RENDERIZADO DEL PAGINADOR
    $url_f = "";
    $rango = 1;
    $inicio = max(1, $pagina - $rango);
    $fin = min($total_paginas, $pagina + $rango);

    // OBTENER PUBLICACIONES
    $resultado = $conn->query(
        "SELECT p.*, 
            u.nombre AS autor_nombre, u.rol AS autor_rol, u.foto AS autor_foto,
            i.subtitulo, i.estado, i.prioridad,
            ti.nombre AS tipo_incidencia_nombre,
            v.fecha_cierre
        FROM publicaciones p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        LEFT JOIN incidencias i ON p.id_publicacion = i.id_publicacion
        LEFT JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
        LEFT JOIN votaciones v ON p.id_publicacion = v.id_publicacion
        ORDER BY fecha_creacion DESC
        LIMIT $num_lineas OFFSET $offset"
    );
    $publicaciones = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Index</title>
    <link rel="stylesheet" href="css/index/index.css">
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
        <section class="feed">
            <header class="section-header">
                <h2>Todas las Publicaciones</h2>
                <div class="section-btns">
                    <a href="#" class="btn-order">
                        <i class="fa-solid fa-sort"></i>
                        <p>ORDENAR</p>
                    </a>
                    <a href="#" class="btn-filter">
                        <i class="fa-solid fa-filter"></i>
                        <p>FILTRAR</p>
                    </a>
                </div>
            </header>

            <hr>

            <?php foreach($publicaciones as $p): ?>
                <?php $fecha = new DateTime($p['fecha_creacion']); ?>

                <!-- ARTÍCULO DE TIPO VOTACIÓN -->
                <?php if ($p['tipo'] === 'votacion'): ?>

                    <?php 
                        // Query para obtener las opciones de votación
                        $opciones = [];

                        $id_votacion = $p['id_publicacion'];
                        $res_opciones = $conn->query(
                            "SELECT * FROM opciones_votacion 
                            WHERE id_publicacion = $id_votacion"
                        );
                        $opciones = $res_opciones->fetch_all(MYSQLI_ASSOC);

                        // Calcular tiempo restante de cierre
                        $fecha_cierre = new DateTime($p['fecha_cierre']);
                        $hoy = new DateTime();
                        $diferencia = $hoy->diff($fecha_cierre);
                        $dias_restantes = $diferencia->invert ? "Finalizada" : $diferencia->format('%a días');

                        // Verificar si el usuario ya ha votado
                        $id_usuario = $_SESSION['id_usuario'];
                        $check_voto = $conn->query(
                            "SELECT id_opcion FROM votos
                            WHERE id_usuario = $id_usuario
                            AND id_publicacion = $id_votacion"
                        );
                        $ya_votado = ($check_voto->num_rows > 0);
                        $voto_usuario = $check_voto->fetch_assoc()['id_opcion'] ?? null;
                    ?>

                    <article class="vote-card">
                        <header class="article-header">
                            <div class="article-header-user">
                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m/Y') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>

                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                    <i class="fa-solid fa-envelope"></i>
                                    VOTACIÓN
                                </h3>
                                <h4> <?= $p['titulo'] ?> </h4>
                            </header>

                            <div class="article-body-content">
                                <p>
                                    <i class="fa-regular fa-message"></i>
                                    <?= $p['contenido'] ?>
                                </p>
                            </div>

                            <form class="article-footer" method="POST" action="votar.php">
                                <input type="hidden" name="id_publicacion" value="<?= $p['id_publicacion'] ?>">

                                <strong>
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    TU VOTO CUENTA
                                </strong>
                                <p>
                                    <i class="fa-solid fa-hourglass-half"></i>
                                    Expira en: <time> <?= $dias_restantes ?> </time>
                                </p>

                                <hr>
                                
                                <div class="vote-btns">
                                    <?php foreach($opciones as $opc): ?>
                                        <label class="vote-option">
                                            <input type="radio" name="id_opcion" value="<?= $opc['id_opcion'] ?>" required>
                                            <span><?= $opc['texto'] ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>

                                <hr>

                                <?php if (!$diferencia->invert): ?>
                                    <?php if ($ya_votado): ?>
                                        <div class="voted-msg">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Ya has participado en esta votación.
                                        </div>
                                    <?php else: ?>
                                        <button type="submit" class="vote-confirm">CONFIRMAR VOTO</button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <button class="vote-confirm disabled" disabled>VOTACIÓN CERRADA</button>
                                <?php endif; ?>
                            </form>
                        </section>
                    </article>
                <?php endif; ?>


                <!-- ARTÍCULO DE TIPO AVISO -->
                <?php if ($p['tipo'] === 'aviso'): ?>
                    <article class="advert-card">
                        <header class="article-header">
                            <div class="article-header-user">
                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m/Y') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>  
                        
                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                    <i class="fa-solid fa-bullhorn"></i>
                                    ANUNCIO
                                </h3>
                                <h4> <?= $p['titulo'] ?> </h4>
                            </header>

                            <div class="article-body-content">
                                <p>
                                    <i class="fa-regular fa-message"></i>
                                    <?= $p['contenido'] ?>
                                </p>
                            </div>
                        </section>
                    </article>
                <?php endif; ?>


                <!-- ARTÍCULO DE TIPO INCIDENCIA -->
                <?php if ($p['tipo'] === 'incidencia'): ?>
                    <article class="incidence-card">
                        <header class="article-header">
                            <div class="article-header-user">
                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m/Y') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>

                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                    INCIDENCIA: <?= $p['tipo_incidencia_nombre'] ?>
                                </h3>
                                <h4> <?= $p['titulo'] ?> </h4>
                            </header>

                            <div class="article-body-content">
                                <img src="img_incidencias/pintura-desconchada.jpg" alt="">
                                <p>
                                    <i class="fa-regular fa-message"></i>
                                    <?= $p['contenido'] ?>
                                </p>
                            </div>
                        </section>
                    </article>
                <?php endif; ?>

            <?php endforeach; ?>    

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
            
            <a href="index.php" class="btn-up">
                <i class="fa-solid fa-angles-up"></i>
            </a>
        </section>

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