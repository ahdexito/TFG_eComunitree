<?php
    session_start();

    include("db/db.inc");

    if (!isset($_SESSION["rol"])) {
        header("location:login.php");
        die();
    }

    // LÓGICA DE FILTRADO
    $filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todos';
    $where_sql = "";
    $params_url = "";

    if ($filtro_tipo !== 'todos') {
        // Validamos para evitar inyecciones (solo permitimos estos 3 tipos)
        if (in_array($filtro_tipo, ['aviso', 'incidencia', 'votacion'])) {
            $where_sql = " WHERE p.tipo = '$filtro_tipo' ";
            $params_url = "&tipo=$filtro_tipo"; // Para arrastrar el filtro en los enlaces
        }
    }
    
    // PAGINADOR
    $num_lineas = 5;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS
    $total_resultado = $conn->query("SELECT COUNT(*) AS total FROM publicaciones p $where_sql");
    $total_filas = $total_resultado->fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // RENDERIZADO DEL PAGINADOR
    $url_f = "";
    $rango = 1;
    $inicio = max(1, $pagina - $rango);
    $fin = min($total_paginas, $pagina + $rango);

    // OBTENER PUBLICACIONES
    $sql_main =
        "SELECT p.*, 
            u.nombre AS autor_nombre, u.rol AS autor_rol, u.foto AS autor_foto, u.vivienda AS autor_vivienda,
            i.estado, i.foto,
            ti.nombre AS tipo_incidencia_nombre,
            v.fecha_cierre
        FROM publicaciones p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        LEFT JOIN incidencias i ON p.id_publicacion = i.id_publicacion
        LEFT JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
        LEFT JOIN votaciones v ON p.id_publicacion = v.id_publicacion
        $where_sql
        ORDER BY fecha_creacion DESC
        LIMIT $num_lineas OFFSET $offset";
    
    $resultado = $conn->query($sql_main);
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
    <header class="body-header" id="inicio">
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

    <?php if (isset($_GET['ins'])): ?>
        <div class="alerta <?php echo ($_GET['ins'] === 'ok') ? 'exito' : 'error'; ?>">
            <?php 
                if ($_GET['ins'] === 'ok') {
                    echo '<i class="fa-solid fa-circle-check"></i> Incidencia publicada correctamente.';
                } else {
                    echo '<i class="fa-solid fa-circle-exclamation"></i> Hubo un error al publicar. Inténtalo de nuevo.';
                }
            ?>
        </div>
    <?php endif; ?>

    <nav>
        <ul>
            <li>NAVEGACIÓN</li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Inicio</li>
        </ul>
    </nav>

    <main>
        <div class="feed">
            <header class="section-header">
                <h2>Últimas Publicaciones: <small>página <?= $pagina ?></small></h2>
                <div class="filter-container">
                    <div class="btn-filter" id="btn-filter">
                        <i class="fa-solid fa-filter"></i>
                        <p>FILTRAR: <br> <?= strtoupper($filtro_tipo) ?></p>
                    </div>
                    <div id="filter-menu" class="filter-menu">
                        <a href="index.php?tipo=todos">Todos</a>
                        <a href="index.php?tipo=votacion">Votaciones</a>
                        <a href="index.php?tipo=aviso">Avisos</a>
                        <a href="index.php?tipo=incidencia">Incidencias</a>
                    </div>
                </div>
            </header>

            <section class="feed-body">
            <?php foreach($publicaciones as $p): ?>

                <hr class="hr-section">

                <?php $fecha = new DateTime($p['fecha_creacion']); ?>

                <!-- ARTÍCULO DE TIPO VOTACIÓN -->
                <?php if ($p['tipo'] === 'votacion'): ?>

                    <?php 
                        $id_votacion = $p['id_publicacion'];

                        // OBTENER CONTEO DE VOTACIONES
                        $sql_opciones = 
                            "SELECT ov.*, COUNT(v.id_usuario) AS total_votos
                            FROM opciones_votacion ov
                            LEFT JOIN votos v ON ov.id_opcion = v.id_opcion
                            WHERE ov.id_publicacion = $id_votacion
                            GROUP BY ov.id_opcion";


                        $res_opciones = $conn->query($sql_opciones);
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
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>

                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                    <i class="fa-solid fa-envelope"></i>VOTACIÓN
                                </h3>
                                
                            </header>

                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= $p['titulo'] ?> </h4>
                                <p>
                                    <?= '<i class="fa-solid fa-quote-left"></i>' . $p['contenido'] . '<i class="fa-solid fa-quote-right"></i>' ?>
                                </p>
                            </div>
                        </section>

                        <form class="article-footer" method="POST" action="votar.php">
                            <input type="hidden" name="id_publicacion" value="<?= $p['id_publicacion'] ?>">

                            <strong>
                                <i class="fa-solid fa-triangle-exclamation"></i>TU VOTO CUENTA
                            </strong>
                            <p>
                                <i class="fa-solid fa-hourglass-half"></i>Expira en: <time> <?= $dias_restantes ?> </time>
                            </p>

                            <hr>
                            
                            <div class="vote-btns">
                                <?php foreach($opciones as $opc): ?>
                                    <label class="vote-option <?= ($voto_usuario == $opc['id_opcion']) ? 'selected-voto' : '' ?>">
                                        <input type="radio" name="id_opcion" value="<?= $opc['id_opcion'] ?>" 
                                            required <?= ($ya_votado || $diferencia->invert) ? 'disabled' : '' ?>
                                            <?= ($voto_usuario == $opc['id_opcion']) ? 'checked' : '' ?>>
                                        
                                        <span>
                                            <?= $opc['texto'] ?> 
                                            <small class="vote-count">
                                                <div class="vote-number"><?= $opc['total_votos'] ?> <i class="fa-solid fa-user-check"></i></div>
                                            </small>
                                        </span>
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
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>  
                        
                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                    <i class="fa-solid fa-bullhorn"></i>AVISO
                                </h3>
                            </header>

                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= $p['titulo'] ?> </h4>
                                <p>
                                    <?= '<i class="fa-solid fa-quote-left"></i>' . $p['contenido'] . '<i class="fa-solid fa-quote-right"></i>' ?>
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
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>
                            </div>

                            <div class="article-header-date">
                                <strong> <?= $fecha->format('H:i') ?> <i class="fa-regular fa-clock"></i></strong>
                                <p> <?= $fecha->format('d/m') ?> <i class="fa-solid fa-calendar-days"></i></p>
                            </div>
                        </header>

                        <section class="article-body">
                            <header class="article-body-title">
                                <h3>
                                    <i class="fa-solid fa-triangle-exclamation"></i>INCIDENCIA: <?= $p['tipo_incidencia_nombre'] ?>
                                </h3>
                            </header>

                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= $p['titulo'] ?> </h4>
                                <?php if ($p['foto'] !== 'default.jpg'): ?>
                                    <img src="img_incidencias/<?= $p['foto'] ?>" alt="Imagen de la incidencia">
                                <?php endif; ?>
                                <p>
                                    <?= '<i class="fa-solid fa-quote-left"></i>' . $p['contenido'] . '<i class="fa-solid fa-quote-right"></i>' ?>
                                </p>
                            </div>
                        </section>
                        
                        <footer class="article-footer">
                            <div class="estado">
                                <strong><i class="fa-solid fa-person-digging"></i> ESTADO: </strong>
                                <?php
                                    $mensaje_estado = match ($p['estado']) {
                                        'pendiente' => '<small style="color:khaki">PENDIENTE <i class="fa-solid fa-circle-pause"></i></small>',
                                        'en_proceso' => '<small style="color:deepskyblue">EN PROCESO <i class="fa-solid fa-clock"></i></small>',
                                        'resuelta' => '<small style="color:lightgreen">RESUELTA <i class="fa-solid fa-circle-check"></i></small>',
                                        'rechazada' => '<small style="color:tomato">RECHAZADA <i class="fa-solid fa-circle-xmark"></i></small>',
                                    };

                                    echo $mensaje_estado;
                                ?>
                            </div>
                        </footer>
                    </article>
                <?php endif; ?>

            <?php endforeach; ?>   
            </section> 

            <div class="pager">
            <?php 
                // Configuración: cuántas páginas mostrar alrededor de la actual
                $rango = 2; 
                
                // Botón Anterior
                if ($pagina > 1): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina - 1 ?><?= $params_url ?>" title="Anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php
                // Mostrar siempre la primera página si no estamos cerca de ella
                if ($pagina > ($rango + 1)) {
                    echo '<a href="?pag=1' . $params_url . '" class="num-link">1</a>';
                    if ($pagina > ($rango + 2)) echo '<span class="dots">...</span>';
                }

                // Bucle para páginas centrales
                for ($i = max(1, $pagina - $rango); $i <= min($total_paginas, $pagina + $rango); $i++): 
                    if ($i == $pagina): ?>
                        <span class="num-link activo"><?= $i ?></span>
                    <?php else: ?>
                        <a href="?pag=<?= $i ?><?= $params_url ?>" class="num-link"><?= $i ?></a>
                    <?php endif; 
                endfor;

                // Mostrar siempre la última página si no estamos cerca de ella
                if ($pagina < ($total_paginas - $rango)) {
                    if ($pagina < ($total_paginas - $rango - 1)) echo '<span class="dots">...</span>';
                    echo '<a href="?pag=' . $total_paginas . $params_url . '" class="num-link">' . $total_paginas . '</a>';
                }
                ?>

                <?php // Botón Siguiente
                if ($pagina < $total_paginas): ?>
                    <a class="pag-arrow" href="?pag=<?= $pagina + 1 ?><?= $params_url ?>" title="Siguiente">
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
                <li>
                    <a href="index.php">
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
    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>