<?php
    session_start();

    include("../db/db.inc");
    
    if (!isset($_SESSION["rol"])) {
        header("location:../login.php?usu=1");
        die();
    }
    
    elseif ($_SESSION["rol"] === 'vecino') {
        header("location:../login.php?usu=1");
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
    $num_lineas = 8;
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
    $resultado = $conn->query(
        "SELECT p.id_publicacion, p.id_usuario, p.tipo, p.titulo, p.fecha_creacion, 
        u.nombre AS autor
        FROM publicaciones p
        INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
        $where_sql
        ORDER BY id_publicacion DESC
        LIMIT $num_lineas OFFSET $offset"
    );
    $publicaciones = $resultado->fetch_all(MYSQLI_ASSOC);

    // ELIMINAR PUBLICACIONES
    if (isset($_GET["eliminar"])) {
        $id_publicacion = intval($_GET["eliminar"]);

        $stmt = $conn -> prepare("DELETE FROM publicaciones WHERE id_publicacion = ?");
        $stmt -> bind_param("i", $id_publicacion);
        $stmt -> execute();
        $stmt -> close();

        header("location:gestion_publicaciones.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Panel Control</title>
    <link rel="stylesheet" href="../css/gestion/gestion.css">
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
            <a href="desconectar_admin.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <nav>
        <ul>
            <li>NAVEGACIÓN</li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Gestión Publicaciones</li>
        </ul>
    </nav>

    <?php
        // ALERTAS DE CREACIÓN DE PUBLICACIÓN
        if (isset($_GET["publi"])) {
            if ($_GET["publi"] == 0) { // inserción correcta
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Publicación añadida correctamente.</div>';
            }
            if ($_GET["publi"] == 1) { // problema al insertar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al añadir la publicación.</div>';
            }
        }

        // ALERTAS DE MODIFICACIÓN DE PUBLICACIÓN
        if (isset($_GET["upt"])) {
            if ($_GET["upt"] == 0) { // actualización correcta
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Publicación actualizada correctamente.</div>';
            }
            if ($_GET["upt"] == 1) { // problema al actualizar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al actualizar la publicación.</div>';
            }
        }
    ?>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-shop icono-header"></i> Gestión de Publicaciones</h2>

                <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div class="filter-container">
                    <div class="btn-filter" id="btn-filter">
                        <i class="fa-solid fa-filter"></i>
                        <p>FILTRAR: <?= strtoupper($filtro_tipo) ?></p>
                    </div>
                    <div id="filter-menu" class="filter-menu" style="display: none;">
                        <a href="gestion_publicaciones.php?tipo=todos">Todos</a>
                        <a href="gestion_publicaciones.php?tipo=aviso">Avisos</a>
                        <a href="gestion_publicaciones.php?tipo=incidencia">Incidencias</a>
                        <a href="gestion_publicaciones.php?tipo=votacion">Votaciones</a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <hr>
            
            <ul class="acciones">
                <li>
                    <a href="ins_aviso.php" class="boton-insertar">
                        <i class="fa-solid fa-circle-plus"></i>
                        CREAR AVISO
                    </a>
                </li>
                <li>
                    <a href="ins_votacion.php" class="boton-insertar">
                        <i class="fa-solid fa-circle-plus"></i>
                        CREAR VOTACIÓN
                    </a>
                </li>
                <li>
                    <a href="gestion_incidencias.php" class="boton-insertar">
                        <i class="fa-solid fa-rotate"></i>
                        ACTUALIZAR INCIDENCIA
                    </a>
                </li>
            </ul>

            <div class="caja-overflow">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>ID</th>
                            <th>Autor</th>
                            <th>Tipo</th>
                            <th>Título</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($publicaciones as $p): ?>
                        <tr>
                            <td>
                                <a href="edit_publicacion.php?edit=<?= $p['id_publicacion'] ?>">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <a href="?eliminar=<?= $p['id_publicacion'] ?>" 
                                onclick="return confirm('¿Eliminar publicación?');">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                            <td>#<?= $p['id_publicacion'] ?></td>
                            <td> <?= htmlspecialchars($p['autor']) ?> </td>
                            <td> <?= htmlspecialchars($p['tipo']) ?> </td>
                            <td> <?= htmlspecialchars($p['titulo']) ?> </td>
                            <td> <?= htmlspecialchars($p['fecha_creacion']) ?> </td>                            
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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
                <li class="has-dropdown">
                    <p>
                        <i class="fa-solid fa-comments"></i>
                        Publicaciones
                    </p>
                    <ul class="submenu">
                        <li>
                            <a href="../crear_incidencia.php">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                Crear Incidencia
                            </a>
                        </li>

                        <?php if ($_SESSION["rol"] !== 'vecino'): ?>
                        <li>
                            <a href="ins_aviso.php">
                                <i class="fa-solid fa-bullhorn"></i>
                                Crear Aviso
                            </a>
                        </li>
                        <li>
                            <a href="ins_votacion.php">
                                <i class="fa-solid fa-envelope"></i>
                                Crear Votación
                            </a>
                        </li>
                        <li>
                            <a href="gestion_incidencias.php">
                                <i class="fa-solid fa-person-digging"></i>
                                Resolver Incidencia
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
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
                <?php if ($_SESSION["rol"] !== "vecino"): ?>
                    <li>
                        <a href="panel_control.php">
                            <i class="fa-solid fa-gear"></i>
                            Panel de Control
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>