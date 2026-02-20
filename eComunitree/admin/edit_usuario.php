<?php
    session_start();
    
    include("../db/db.inc");

    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== 'admin') {
        header("location:../login.php?usu=1");
        die();
    }

    if (!isset($_GET['edit'])) {
        header("location:gestion_usuarios.php");
        die();
    }

    $id_usuario = intval($_GET['edit']);

    if (isset($_POST['nombre'])) {
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"] ?? "";
        $email = $_POST["email"];
        $rol = $_POST["rol"];
        $telefono = $_POST["telefono"] ?? "";
        $vivienda = $_POST["vivienda"];
        $activo = intval($_POST["activo"]);

        $sql_update = "UPDATE usuarios SET nombre = ?, apellidos = ?, email = ?, rol = ?, telefono = ?, vivienda = ?, activo = ? WHERE id_usuario = ?";
        
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssssssii", $nombre, $apellidos, $email, $rol, $telefono, $vivienda, $activo, $id_usuario);

        if ($stmt->execute()) {
            header("location:gestion_usuarios.php?upt=0");
        } else {
            header("location:gestion_usuarios.php?upt=1");
        }
        
        $stmt->close();
        die();
    }

    $sql_check = "SELECT * FROM usuarios WHERE id_usuario = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_usuario);
    $stmt_check->execute();
    $res = $stmt_check->get_result();

    if ($res->num_rows === 0) {
        header("location:gestion_usuarios.php");
        die();
    }

    $usuario = $res->fetch_assoc();
    $stmt_check->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
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
            <li><a href="gestion_usuarios.php">Gestión Usuarios</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Editar Usuario</li>
        </ul>
    </nav>
    
    <main>
        <?php
            $id = intval($_GET["edit"]);
            $sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) > 0) {
                $usuario = mysqli_fetch_assoc($res);
            }
            else {
                header("location:gestion_usuarios.php");
                die();
            }
        ?>

        <section class="panel-control">
            <div class="section-header">
                <i class="fa-regular fa-pen-to-square icono-header"></i>
                <h2>Editar Usuario</h2>
            </div>

            <hr>

            <form action="" method="POST">
                <div class="form">
                    <input type="hidden" name="id_usuario" value="<?= $id_usuario; ?>">
                    <input type="hidden" name="accion" value="editar">

                    <div class="casilla">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" value="<?= $usuario["nombre"] ?>" required>
                    </div>

                    <div class="casilla">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" value="<?= $usuario["apellidos"] ?>" required>
                    </div>

                    <div class="casilla">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" value="<?= $usuario["email"] ?>" required>
                    </div>

                    <div class="casilla">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" value="<?= $usuario["telefono"] ?>">
                    </div>

                    <div class="casilla">
                        <label for="vivienda">Vivienda</label>
                        <input type="text" name="vivienda" id="vivienda" value="<?= $usuario["vivienda"] ?>" required>
                    </div>

                    <!-- EVITAR QUE UN ADMIN SE QUITE PERMISOS A SÍ MISMO -->
                    <?php if ($_SESSION["id_usuario"] != $id_usuario): ?>
                    <div class="casilla">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol" class="seleccion" required>
                            <?php
                                $rol = $usuario["rol"];

                                if ($rol=='vecino') {
                                    print'
                                    <option value="vecino" selected>Vecino</option>
                                    <option value="presidente">Presidente</option>
                                    <option value="admin">Administrador</option>';
                                }
                                else if ($rol=='presidente') {
                                    print'
                                    <option value="vecino">Vecino</option>
                                    <option value="presidente" selected>Presidente</option>
                                    <option value="admin">Administrador</option>';
                                }
                                else if ($rol=='admin') {
                                    print'
                                    <option value="vecino">Vecino</option>
                                    <option value="presidente">Presidente</option>
                                    <option value="admin" selected>Administrador</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <div class="casilla">
                        <label for="activo">Activo</label>
                        <select name="activo" class="seleccion" required>
                            <option value="0" <?= $usuario['activo']==0?'selected':'' ?> >Desactivado</option>
                            <option value="1" <?= $usuario['activo']==1?'selected':'' ?> >Activado</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Actualizar Usuario</button>
            </form>
        </section>

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
</body>
</html>