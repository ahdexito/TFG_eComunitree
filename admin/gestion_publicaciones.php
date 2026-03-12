<?php
    session_start();

    include("../db/db.inc");

    $base = "../";
    
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] === 'vecino') {
        header("location:../login.php?usu=1");
        die();
    }

    $mensaje = "";

    // GESTIÓN DE MENSAJES
    if (isset($_GET['del']) && $_GET['del'] == 0) {
        $mensaje = "Publicación eliminada correctamente.";
    } 
    elseif (isset($_GET["ins"])) {
        $mensaje = ($_GET["ins"] == 0) ? "Publicación añadida correctamente." : "Error al añadir la publicación.";
    } 
    elseif (isset($_GET["upt"])) {
        $mensaje = ($_GET["upt"] == 0) ? "Publicación actualizada correctamente." : "Error al actualizar la publicación.";
    }
    elseif (isset($_GET['error']) && $_GET['error'] == 'acceso_denegado') {
        $mensaje = "No tienes permiso para realizar esta acción.";
    }

    // LÓGICA DE FILTRADO
    $filtro_tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

    $texto_boton = match($filtro_tipo) {
        'aviso' => 'AVISOS',
        'incidencia' => 'INCIDENCIAS',
        'votacion' => 'VOTACIONES',
        default => 'TODAS',
    };

    $where_sql = "";
    $params_url = "";

    if ($filtro_tipo !== 'todos') {
        // Validar para evitar inyecciones (solo permite estos 3 tipos)
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

        header("location:gestion_publicaciones.php?del=0");
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
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?>
        <div class="msg msg-timer" id="mensaje-contenedor">
            <p><?= $mensaje ?></p>
            <button type="button" class="btn-cerrar-msg" id="btn-cerrar-js">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./panel_control.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Gestión Publicaciones</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-comments"></i> Gestión de Publicaciones: <small>página <?= $pagina ?></small></h2>

                <?php if ($_SESSION['rol'] === 'admin'): ?>
                <div class="filter-container">
                    <div class="btn-filter" id="btn-filter-toggle">
                        <i class="fa-solid fa-filter"></i>
                        <p><?= $texto_boton ?></p>
                    </div>
                    <div id="filter-menu" class="filter-menu">
                        <a href="gestion_publicaciones.php?tipo=todos">Todas</a>
                        <a href="gestion_publicaciones.php?tipo=votacion">Votaciones</a>
                        <a href="gestion_publicaciones.php?tipo=aviso">Avisos</a>
                        <a href="gestion_publicaciones.php?tipo=incidencia">Incidencias</a>
                    </div>
                </div>
                <?php endif; ?>

                <hr>
            </div>
            
            <ul class="acciones">
                <li>
                    <a href="ins_aviso.php" class="btn-tool">
                        <i class="fa-solid fa-circle-plus"></i>
                        CREAR AVISO
                    </a>
                </li>
                <li>
                    <a href="ins_votacion.php" class="btn-tool">
                        <i class="fa-solid fa-circle-plus"></i>
                        CREAR VOTACIÓN
                    </a>
                </li>
                <li>
                    <a href="gestion_incidencias.php" class="btn-tool">
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
                                <a href="edit_publicacion_admin.php?edit=<?= $p['id_publicacion'] ?>">
                                    <i class="fa-regular fa-pen-to-square icon-edit"></i>
                                </a>

                                <a href="?eliminar=<?= $p['id_publicacion'] ?>" 
                                onclick="return confirm('¿Eliminar publicación?');">
                                    <i class="fa-regular fa-trash-can icon-del"></i>
                                </a>
                            </td>
                            
                            <td>#<?= $p['id_publicacion'] ?></td>
                            <td><?= htmlspecialchars($p['autor']) ?></td>
                            
                            <?php
                                $tipo = htmlspecialchars($p['tipo']);
                                echo match ($tipo) {
                                    'incidencia' => "<td class='incidencia'><p>Incidencia</p></td>",
                                    'aviso'      => "<td class='aviso'><p>Aviso</p></td>",
                                    'votacion'   => "<td class='votacion'><p>Votación</p></td>",
                                };
                            ?>
                            
                            <td><?= htmlspecialchars($p['titulo']) ?></td>
                            <td><?= htmlspecialchars($p['fecha_creacion']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- PAGINADOR -->
            <?php include("../includes/pager.php"); ?>
        </section>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>