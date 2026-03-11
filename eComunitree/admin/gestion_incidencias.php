<?php
    session_start();

    include("../db/db.inc");

    $base = "../";
    
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] === 'vecino') {
        header("location:../login.php?usu=1");
        die();
    }

    // LÓGICA ACTUALIZACIÓN DE ESTADO
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cambiar_estado'], $_POST['id_publicacion'])) {
        $nuevo_estado = $_POST['cambiar_estado'];
        $id_p = intval($_POST['id_publicacion']);
        
        $stmt = $conn->prepare("UPDATE incidencias SET estado = ? WHERE id_publicacion = ?");
        $stmt->bind_param("si", $nuevo_estado, $id_p);
        
        // Determinar el resultado para el mensaje
        $res = $stmt->execute() ? "0" : "1";
        $stmt->close();

        // Construir la URL manteniendo el filtro y la página, pero añadiendo el mensaje
        $url_actual = "gestion_incidencias.php" . (isset($_SERVER['QUERY_STRING']) ? "?" . $_SERVER['QUERY_STRING'] : "?");
        header("Location: " . $url_actual . "&upt=" . $res);
        exit();
    }

    // LÓGICA DE FILTRADO
    $filtro_estado = isset($_GET['estado']) ? $_GET['estado'] : 'todas';
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

    $mensaje = "";
    if (isset($_GET['upt'])) {
        $mensaje = ($_GET['upt'] === '0') 
            ? "Estado de la incidencia actualizado correctamente." 
            : "Error al actualizar el estado.";
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>eComunitree | Gestión Incidencias</title>
    <link rel="stylesheet" href="../css/gestion/gestion.css">
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?>
        <span class="msg"><?= $mensaje ?></span>
    <?php endif; ?>

    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./gestion_publicaciones.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_publicaciones.php">Gestión Publicaciones</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Incidencias</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-triangle-exclamation"></i> Gestión de Incidencias: <small>página <?= $pagina ?></small></h2>

                <div class="filter-container">
                    <div class="btn-filter" id="btn-filter-toggle"">
                        <i class="fa-solid fa-filter"></i>
                        <p><?= strtoupper($filtro_estado) ?></p>
                    </div>
                    <div id="filter-menu" class="filter-menu">
                        <a href="gestion_incidencias.php?estado=todos">Todas</a>
                        <a href="gestion_incidencias.php?estado=pendiente">Pendientes</a>
                        <a href="gestion_incidencias.php?estado=en_proceso">En Proceso</a>
                        <a href="gestion_incidencias.php?estado=resuelta">Resueltas</a>
                    </div>
                </div>

                <hr>
            </div>

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

            <!-- PAGER -->
            <?php include("../includes/pager.php"); ?>
        </section>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>