<?php
    session_start();
    include("./db/db.inc");
    include("./includes/verficar_sesion.php");
    $base = "./";

    // GESTIÓN DE MENSAJES
    $mensaje = "";

    if (isset($_GET['del']) && $_GET['del'] == 0) {
        $mensaje = "Publicación eliminada correctamente.";
    } 
    elseif (isset($_GET['ins'])) {
        $mensaje = ($_GET['ins'] == 0) ? "Publicación añadida con éxito." : "Error al añadir la publicación.";
    } 
    elseif (isset($_GET["upt"])) {
        $mensaje = ($_GET["upt"] == 0) ? "Publicación actualizada correctamente." : "Error al actualizar la publicación.";
    } 
    elseif (isset($_GET["prof"]) && $_GET["prof"] == 0) $mensaje = "Datos actualizados correctamente.";

    elseif (isset($_GET['error'])) {
        $mensaje = ($_GET['error'] == 'acceso_denegado') ? "No tienes permisos para realizar esta acción." : "";
    }
    elseif (isset($_GET["vote"])) {
        $get_vote = (string)$_GET["vote"];

        $mensaje = match ($get_vote) {
            '0' => "Votación realizada correctamente.",
            '1' => "Ya has votado en esta publicación.",
            '2' => "Esta votación ya se ha cerrado, no puedes votar.",
            '3' => "Ha sucedido un error inesperado al realizar la votación. Inténtalo de nuevo.",
            default => ""
        };
    }

    // LÓGICA DE FILTRADO
    $filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

    [$nombre_boton, $icono_filtro] = match($filtro_tipo) {
        'aviso' => ['AVISOS', 'fa-bullhorn'],
        'incidencia' => ['INCIDENCIAS', 'fa-triangle-exclamation'],
        'votacion' => ['VOTACIONES', 'fa-envelope'],
        default => ['TODAS', 'fa-comments'],
    };

    $where_sql = "";
    $params_url = "";

    if ($filtro_tipo !== 'todas') {
        // Validamos para evitar inyecciones (solo permitimos estos 3 tipos)
        if (in_array($filtro_tipo, ['aviso', 'incidencia', 'votacion'])) {
            $where_sql = " WHERE p.tipo = '$filtro_tipo' ";
            $params_url = "&tipo=$filtro_tipo"; // Para arrastrar el filtro en los enlaces
        }
    }
    
    // PAGINADOR
    $num_lineas = 10;
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

    // ELIMINAR PUBLICACIONES
    if (isset($_GET["eliminar"])) {
        $id_publicacion = intval($_GET["eliminar"]);
        $stmt = $conn -> prepare("DELETE FROM publicaciones WHERE id_publicacion = ?");
        $stmt -> bind_param("i", $id_publicacion);
        $stmt -> execute();
        $stmt -> close();

        header("location:index.php?del=0");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Inicio</title>
    <link rel="stylesheet" href="css/index/index.css">
    <link rel="icon" href="img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?>
        <div class="msg msg-timer" id="mensaje-contenedor">
            <p><i class="fa-solid fa-bell"></i> <?= $mensaje ?></p>
            <button type="button" class="btn-cerrar-msg" id="btn-cerrar-js">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>
                
    <!-- HEADER -->
    <?php include("includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
        </ul>

        <!-- ASIDE -->
        <?php include("includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <div class="feed">
            <header class="section-header">
                <h2>
                    <i class="fa-solid fa-comments"></i>
                    Últimas Publicaciones: <small>página <?= $pagina ?></small>
                </h2>
                
                <div class="filter-container">
                    <div class="btn-filter" id="btn-filter-toggle">
                        <i class="fa-solid fa-filter"></i>
                        <p><?= $nombre_boton ?></p> 
                    </div>
                    <div id="filter-menu" class="filter-menu">
                        <a href="index.php?tipo=todas">Todas</a>
                        <a href="index.php?tipo=votacion">Votaciones</a>
                        <a href="index.php?tipo=aviso">Avisos</a>
                        <a href="index.php?tipo=incidencia">Incidencias</a>
                    </div>
                </div>

                <hr class="hr-section">
            </header>

            <section class="feed-body">

            <?php foreach($publicaciones as $p): ?>

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

                        if ($diferencia->invert) {
                            $tiempo_texto = "Finalizada";
                        } else {
                            // Si quedan 0 días, mostrar horas y minutos
                            if ($diferencia->days < 1) {
                                $tiempo_texto = $diferencia->format('%h h %i min');
                            } else {
                                // Si queda 1 día o más, mostrar solo los días
                                $tiempo_texto = $diferencia->format('%a días');
                            }
                        }

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
                            <h3><i class="fa-solid fa-envelope"></i>VOTACIÓN</h3>

                            <div class="article-header-user">
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>
                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                            </div>
                        </header>

                        <section class="article-body">
                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= $p['titulo'] ?> </h4>

                                <p><?= $p['contenido'] ?></p>
                            </div>

                            <div class="article-header-date">
                                <p><i class="fa-regular fa-clock"></i><?= $fecha->format('H:i') ?></p>
                                <p><i class="fa-solid fa-calendar-days"></i><?= $fecha->format('d/m') ?></p>
                            </div>
                        </section>

                        <form class="article-footer" method="POST" action="votar.php">
                            <input type="hidden" name="id_publicacion" value="<?= $p['id_publicacion'] ?>">

                            <strong>
                                <i class="fa-solid fa-triangle-exclamation"></i>TU VOTO CUENTA
                            </strong>
                            <p>
                                <i class="fa-solid fa-hourglass-half"></i>Expira en: <time> <?= $tiempo_texto ?> </time>
                            </p>
                            
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

                        <?php include("./includes/edit_del_publi.php"); ?>

                    </article>
                <?php endif; ?>


                <!-- ARTÍCULO DE TIPO AVISO -->
                <?php if ($p['tipo'] === 'aviso'): ?>
                    <article class="advert-card">
                        <header class="article-header">
                            <h3><i class="fa-solid fa-bullhorn"></i>AVISO</h3>
                            
                            <div class="article-header-user">                                
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>

                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                            </div>
                        </header>  
                        
                        <section class="article-body">
                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= $p['titulo'] ?> </h4>

                                <p><?= $p['contenido'] ?></p>
                            </div>

                            <div class="article-header-date">
                                <p><i class="fa-regular fa-clock"></i><?= $fecha->format('H:i') ?></p>
                                <p><i class="fa-solid fa-calendar-days"></i><?= $fecha->format('d/m') ?></p>
                            </div>
                        </section>

                        <?php include("./includes/edit_del_publi.php"); ?>

                    </article>
                <?php endif; ?>


                <!-- ARTÍCULO DE TIPO INCIDENCIA -->
                <?php if ($p['tipo'] === 'incidencia'): ?>
                    <article class="incidence-card">
                        <header class="article-header">
                            <h3><i class="fa-solid fa-triangle-exclamation"></i>INCIDENCIA</h3>

                            <div class="article-header-user">
                                <div class="user-info">
                                    <strong> <?= $p['autor_nombre'] ?> </strong>
                                    <p> <?= ucfirst($p['autor_rol']) ?> </p>
                                    <p> <?= $p['autor_vivienda'] ?> </p>
                                </div>

                                <img src="img_user/<?= $p['autor_foto'] ?>" alt="Foto de <?= $p['autor_nombre'] ?>">
                            </div>
                        </header>

                        <section class="article-body">                                
                            <div class="article-body-content">
                                <h4><i class="fa-solid fa-star-of-life"></i> <?= strtoupper($p['tipo_incidencia_nombre']) . ": " . $p['titulo'] ?></h4>

                                <p><?= $p['contenido'] ?></p>

                                <?php if ($p['foto'] !== 'default.jpg'): ?>
                                    <img src="img_incidencias/<?= $p['foto'] ?>" alt="Imagen de la incidencia">
                                <?php endif; ?>
                            </div>

                            <div class="article-header-date">
                                <p><i class="fa-regular fa-clock"></i><?= $fecha->format('H:i') ?></p>
                                <p><i class="fa-solid fa-calendar-days"></i><?= $fecha->format('d/m') ?></p>
                            </div>
                        </section>
                        
                        <footer class="article-footer">
                            <div class="estado">
                                <strong>- ESTADO: </strong>
                                
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

                        <?php include("./includes/edit_del_publi.php"); ?>

                    </article>
                <?php endif; ?>

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