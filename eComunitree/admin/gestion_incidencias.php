<?php
    session_start();
    include("../db/db.inc");
    
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] === 'vecino') {
        header("location:../login.php?usu=1");
        die();
    }

    // LÓGICA ACTUALIZACIÓN DE ESTADO
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_estado'], $_POST['id_publicacion'])) {
        $nuevo_estado = $_POST['cambiar_estado'];
        $id_p = intval($_POST['id_publicacion']);
        
        // Preparamos la consulta para evitar inyección SQL
        $stmt = $conn->prepare("UPDATE incidencias SET estado = ? WHERE id_publicacion = ?");
        $stmt->bind_param("si", $nuevo_estado, $id_p);
        
        if ($stmt->execute()) {
            // Recargamos para evitar reenvío de formulario y reflejar cambios
            $url_actual = "gestion_incidencias.php" . (isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "");
            header("Location: " . $url_actual);
            exit();
        }
        $stmt->close();
    }

    // LÓGICA DE FILTRADO
    $filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todos';
    $where_sql = "";
    $params_url = "";

    if ($filtro_estado !== 'todos') {
        $estados_validos = ['pendiente', 'en_proceso', 'resuelta', 'rechazada'];
        if (in_array($filtro_estado, $estados_validos)) {
            $where_sql = " WHERE i.estado = '$filtro_estado' ";
            $params_url = "&estado=$filtro_estado";
        }
    }

    // PAGINADOR
    $num_lineas = 8;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS
    $total_resultado = $conn->query("SELECT COUNT(*) AS total FROM incidencias i $where_sql");
    $total_filas = $total_resultado->fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // OBTENER INCIDENCIAS CON SUS DETALLES
    $sql = "SELECT p.id_publicacion, p.titulo, p.fecha_creacion, 
                u.nombre AS autor, u.vivienda,
                i.estado, i.foto,
                ti.nombre AS categoria
            FROM publicaciones p
            INNER JOIN incidencias i ON p.id_publicacion = i.id_publicacion
            INNER JOIN usuarios u ON p.id_usuario = u.id_usuario
            INNER JOIN tipos_incidencia ti ON i.id_tipo_incidencia = ti.id_tipo_incidencia
            $where_sql
            ORDER BY p.fecha_creacion DESC
            LIMIT $num_lineas OFFSET $offset";

    $resultado = $conn->query($sql);
    $incidencias = $resultado->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>eComunitree | Gestión Incidencias</title>
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
            <a href="desconectar.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil">
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
            <li><a href="gestion_publicaciones.php">Gestión Publicaciones</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Incidencias</li>
        </ul>
    </nav>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-triangle-exclamation"></i> Gestión de Incidencias</h2>

                <div class="filter-container">
                    <div class="btn-filter" onclick="toggleFiltros(event)">
                        <i class="fa-solid fa-filter"></i>
                        <p>ESTADO: <?= strtoupper($filtro_estado) ?></p>
                    </div>
                    <div id="filter-menu" class="filter-menu" style="display: none;">
                        <a href="gestion_incidencias.php?estado=todos">Todos</a>
                        <a href="gestion_incidencias.php?estado=pendiente">Pendientes</a>
                        <a href="gestion_incidencias.php?estado=en_proceso">En Proceso</a>
                        <a href="gestion_incidencias.php?estado=resuelta">Resueltas</a>
                    </div>
                </div>
            </div>

            <hr>

            <div class="caja-overflow">
                <table>
                    <thead>
                        <tr>
                            <th>Estado</th>
                            <th>Categoría</th>
                            <th>Título</th>
                            <th>Vecino</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidencias as $inc): 
                            // Clase CSS según el estado para dar color
                            $clase_estado = "est-" . str_replace('_', '-', $inc['estado']);
                        ?>
                        <tr>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="id_publicacion" value="<?= $inc['id_publicacion'] ?>">
                                    <select name="cambiar_estado" 
                                            class="select-estado <?= $clase_estado ?>" 
                                            onchange="this.form.submit()">
                                        <?php
                                        $estados = [
                                            'pendiente' => 'PENDIENTE',
                                            'en_proceso' => 'EN PROCESO',
                                            'resuelta' => 'RESUELTA',
                                            'rechazada' => 'RECHAZADA'
                                        ];
                                        foreach ($estados as $val => $texto): ?>
                                            <option value="<?= $val ?>" <?= ($inc['estado'] == $val) ? 'selected' : '' ?>>
                                                <?= $texto ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                            <td><?= htmlspecialchars($inc['categoria']) ?></td>
                            <td><?= htmlspecialchars($inc['titulo']) ?></td>
                            <td><?= htmlspecialchars($inc['autor']) ?> (<?= $inc['vivienda'] ?>)</td>
                            <td><?= date("d/m/Y", strtotime($inc['fecha_creacion'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

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