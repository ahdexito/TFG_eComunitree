<?php
    session_start();

    include("../db/db.inc");
    
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

    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-solid fa-users icono-header"></i>
                <h2>Gestión de Usuarios</h2>
            </div>

            <hr>

            <a href="ins_usuario.php" class="boton-insertar">
                <i class="fa-regular fa-square-plus"></i>
                Insertar Usuario
            </a>

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
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <a href="?eliminar=<?= $u['id_usuario'] ?>" 
                                onclick="return confirm('¿Eliminar usuario?');">
                                    <i class="fa-regular fa-trash-can"></i>
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
        </section>
    </main>

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
                </a></li>
            <li>
                <a href="#">
                <i class="fa-solid fa-plus"></i>
                Nueva incidencia
                </a></li>
            <li>
                <a href="../servicios.php">
                <i class="fa-solid fa-briefcase"></i>
                Servicios
            </a></li>
            <li>
                <a href="#">
                <i class="fa-solid fa-calendar-days"></i>
                Calendario
            </a></li>
            <li><a href="#">
                <i class="fa-regular fa-file-lines"></i>
                Documentación
            </a></li>
            <li><a href="panel_control.php">
                <i class="fa-solid fa-gear"></i>
                Panel de control
            </a></li>
        </ul>
    </aside>
</body>
</html>