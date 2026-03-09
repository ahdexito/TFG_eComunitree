<?php
    session_start();

    include("../db/db.inc");

    $base = "../";
    
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
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Gestión Servicios</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

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

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-shop icono-header"></i> Gestión de Servicios: <small>página <?= $pagina ?></small></h2>
                <hr>
            </div>

            <ul class="acciones">
                <li>
                    <a href="ins_servicio.php" class="btn-tool">
                        <i class="fa-regular fa-square-plus"></i>
                        AÑADIR SERVICIO
                    </a>
                </li>
            </ul>
            

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
                                    <i class="fa-regular fa-pen-to-square icon-edit"></i>
                                </a>

                                <a href="?eliminar=<?= $s['id_servicio'] ?>" 
                                onclick="return confirm('¿Eliminar servicio?');">
                                    <i class="fa-regular fa-trash-can icon-del"></i>
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

            <!-- PAGINADOR -->
            <?php include("../includes/pager.php"); ?>
        </section>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>