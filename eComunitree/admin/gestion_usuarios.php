<?php
    session_start();

    include("../db/db.inc");

    $base = "../";
    
    if (!isset($_SESSION["rol"])) {
        header("location:../login.php?usu=1");
        die();
    }
    
    elseif ($_SESSION["rol"] !== 'admin') {
        header("location:../login.php?usu=1");
        die();
    }

    // PAGINADOR
    $num_lineas = 5;
    $pagina = isset($_GET['pag']) ? max(1, intval($_GET['pag'])) : 1;
    $offset = ($pagina - 1) * $num_lineas;

    // TOTAL DE REGISTROS
    $total_resultado = $conn -> query(
        "SELECT COUNT(*) AS total FROM usuarios"
    );
    $total_filas = $total_resultado -> fetch_assoc()['total'];
    $total_paginas = ceil($total_filas / $num_lineas);

    // OBTENER USUARIOS
    $resultado = $conn->query(
        "SELECT * FROM usuarios 
        ORDER BY activo DESC, id_usuario DESC 
        LIMIT $num_lineas OFFSET $offset"
    );
    $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);

    // LÓGICA DE RANGO PARA EL HTML
    $rango = 1; // cuántas páginas mostrar a cada lado de la actual
    $inicio = max(1, $pagina - $rango);
    $fin = min($total_paginas, $pagina + $rango);

    // ELIMINAR USUARIO
    if (isset($_GET["eliminar"])) {
        $id_usuario = intval($_GET["eliminar"]);

        $stmt = $conn -> prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt -> bind_param("i", $id_usuario);
        $stmt -> execute();
        $stmt -> close();

        header("location:gestion_usuarios.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Usuarios</title>
    <link rel="stylesheet" href="../css/gestion/gestion.css">
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
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
            <li>Gestión Usuarios</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <?php
        // ALERTAS DE CREACIÓN DE USUARIO
        if (isset($_GET["usu"])) {
            if ($_GET["usu"] == 0) { // registro correcto
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Usuario insertado correctamente.</div>';
            }
            if ($_GET["usu"] == 1) { // email ya existe
                echo '<div class="alerta"><i class="fa-solid fa-circle-exclamation exclamacion"></i>
                El email ya existe en la base de datos.</div>';
            }
            if ($_GET["usu"] == 2) { // problema al insertar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al intentar insertar el usuario.</div>';
            }
        }

        // ALERTAS DE MODIFICACIÓN DE USUARIO
        if (isset($_GET["upt"])) {
            if ($_GET["upt"] == 0) { // actualización correcta
                echo '<div class="alerta"><i class="fa-solid fa-circle-check check"></i>
                Usuario actualizado correctamente.</div>';
            }
            if ($_GET["upt"] == 1) { // problema al actualizar
                echo '<div class="alerta"><i class="fa-solid fa-circle-xmark xmark"></i>
                Ha ocurrido un error al intentar actualizar el usuario.</div>';
            }
        }
    ?>

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-users icono-header"></i> Gestión de Usuarios: <small>página <?= $pagina ?></small></h2>
                <hr>
            </div>

            <ul class="acciones">
                <li>
                    <a href="ins_usuario.php" class="btn-tool">
                        <i class="fa-regular fa-square-plus"></i>
                        INSERTAR USUARIO
                    </a>
                </li>
            </ul>

            <div class="caja-overflow">
                <table>
                    <thead>
                        <tr>
                            <th>Acciones</th>
                            <th>Activo</th>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Apellidos</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Teléfono</th>
                            <th>Vivienda</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <a href="edit_usuario.php?edit=<?= $u['id_usuario'] ?>">
                                    <i class="fa-regular fa-pen-to-square icon-edit"></i>
                                </a>
                                <a href="?eliminar=<?= $u['id_usuario'] ?>" 
                                onclick="return confirm('¿Eliminar usuario?');">
                                    <i class="fa-regular fa-trash-can icon-del"></i>
                                </a>
                            </td>
                            <td>
                                <?php
                                    if ($u['activo'] == 0) echo '<i class="fa-solid fa-x"></i>'; 
                                    elseif ($u['activo'] == 1) echo '<i class="fa-solid fa-check"></i>';
                                ?>
                            </td>
                            <td>#<?= $u['id_usuario'] ?></td>
                            <td><?= htmlspecialchars($u['nombre']) ?></td>
                            <td><?= htmlspecialchars($u['apellidos']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['rol']) ?></td>
                            <td><?= htmlspecialchars($u['telefono']) ?></td>
                            <td><?= htmlspecialchars($u['vivienda']) ?></td>
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