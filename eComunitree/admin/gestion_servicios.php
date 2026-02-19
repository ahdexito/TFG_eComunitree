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

    // PAGINADOR
    $num_lineas = 8;
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
        ORDER BY activo DESC, id_servicio DESC
        LIMIT $num_lineas OFFSET $offset"
    );
    $servicios = $resultado->fetch_all(MYSQLI_ASSOC);

    // ELIMINAR SERVICIOS
    if (isset($_GET["eliminar"])) {
        $id_servicio = intval($_GET["eliminar"]);

        $stmt = $conn -> prepare("DELETE FROM servicios WHERE id_servicio = ?");
        $stmt -> bind_param("i", $id_servicio);
        $stmt -> execute();
        $stmt -> close();

        header("location:gestion_servicios.php");
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

    <?php
        // ALERTAS DE CREACIÓN DE SERVICIO
        if (isset($_GET["serv"])) {
            if ($_GET["serv"] == 0) { // inserción correcta
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Servicio añadido correctamente.</div>';
            }
            if ($_GET["serv"] == 1) { // problema al insertar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al añadir el servicio.</div>';
            }
        }

        // ALERTAS DE MODIFICACIÓN DE SERVICIO
        if (isset($_GET["upt"])) {
            if ($_GET["upt"] == 0) { // actualización correcta
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Servicio actualizado correctamente.</div>';
            }
            if ($_GET["upt"] == 1) { // problema al actualizar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al actualizar el servicio.</div>';
            }
        }
    ?>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-solid fa-shop icono-header"></i>
                <h2>Gestión de Servicios</h2>
            </div>

            <hr>

            <a href="ins_servicio.php" class="boton-insertar">
                <i class="fa-regular fa-square-plus"></i>
                Añadir Servicio
            </a>

            <div class="caja-overflow">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>ID</th>
                            <th>Activo</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Enlace</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($servicios as $s): ?>
                        <tr>
                            <td>
                                <a href="edit_servicio.php?edit=<?= $s['id_servicio'] ?>">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>

                                <a href="?eliminar=<?= $s['id_servicio'] ?>" 
                                onclick="return confirm('¿Eliminar servicio?');">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                            <td>#<?= $s['id_servicio'] ?></td>
                            <td>
                                <?php
                                    if ($s['activo'] == 0) echo '<i class="fa-solid fa-x"></i>'; 
                                    elseif ($s['activo'] == 1) echo '<i class="fa-solid fa-check"></i>';
                                ?>
                            </td>
                            <td> <?= htmlspecialchars($s['nombre']) ?> </td>
                            <td> <?= htmlspecialchars($s['descripcion']) ?> </td>
                            <td> <?= htmlspecialchars($s['telefono']) ?> </td>
                            <td> <?= htmlspecialchars($s['email']) ?> </td>                            
                            <td> <?= htmlspecialchars($s['enlace']) ?> </td>
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
                <li>
                    <a href="../crear_incidencia.php">
                        <i class="fa-solid fa-plus"></i>
                        Nueva incidencia
                    </a>
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
                <li>
                    <a href="panel_control.php">
                        <i class="fa-solid fa-gear"></i>
                        Panel de control
                    </a>
                </li>
            </ul>
        </aside>
    </main>
</body>
</html>