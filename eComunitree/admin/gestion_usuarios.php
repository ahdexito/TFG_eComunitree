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
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="../index.php" class="btn-index">
            <img src="../img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-info">
                <strong><?= $_SESSION["nombre"] ?></strong>
                <i><?= $_SESSION["vivienda"] ?></i>
                <i><?= ucfirst($_SESSION["rol"]) ?></i>
            </div>
            <a href="desconectar_admin.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>
    
    <nav>
        <ul>
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Gestión Usuarios</li>
        </ul>
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

    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-users icono-header"></i> Gestión de Usuarios: <small>página <?= $pagina ?></small></h2>
            </div>

            <hr>

            <ul class="acciones">
                <li>
                    <a href="ins_usuario.php" class="btn-tool">
                        <i class="fa-regular fa-square-plus"></i>
                        Insertar Usuario
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
    </main>

    <input type="checkbox" id="menu-toggle" class="menu-checkbox">
    <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

    <aside class="main-aside">
        <h3>Navegación</h3>
        <hr>
        <ul>
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
</body>
</html>