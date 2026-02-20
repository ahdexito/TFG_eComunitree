<?php
    session_start();
    
    include("../db/db.inc");

    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== 'admin') {
        header("location:../login.php?usu=1");
        die();
    }

    if (isset($_POST["nombre"]) && !empty($_POST["nombre"])) {
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"] ?? "";
        $email = $_POST["email"];
        $password = $_POST["password"];
        $rol = $_POST["rol"];
        $telefono = $_POST["telefono"] ?? "";
        $vivienda = $_POST["vivienda"];
        $activo = $_POST["activo"];

        // Comprobar si el email existe
        $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        if ($res->num_rows > 0) {
            header("location:gestion_usuarios.php?usu=1");
            die();
        }

        // Encriptar contraseña
        $pass_encriptada = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios (nombre, apellidos, email, password, rol, telefono, vivienda, activo)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt_insert = $conn->prepare($sql);
        $stmt_insert->bind_param("sssssssi", $nombre, $apellidos, $email, $pass_encriptada, $rol, $telefono, $vivienda, $activo);

        if ($stmt_insert->execute()) {
            header("location:gestion_usuarios.php?usu=0");
        } else {
            header("location:gestion_usuarios.php?usu=2");
        }

        $stmt_insert->close();
        $stmt_check->close();
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Usuario</title>
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
            <li>Insertar Usuario</li>
        </ul>
    </nav>
    
    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-regular fa-square-plus icono-header"></i>
                <h2>Insertar Usuario</h2>
            </div>

            <hr>

            <form action="" method="POST">
                <div class="form">
                    <div class="casilla">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" placeholder="Texto" required>
                    </div>

                    <div class="casilla">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" name="apellidos" id="apellidos" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" placeholder="Texto" required>
                    </div>

                    <div class="casilla">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" id="password" placeholder="Texto" required>
                    </div>

                    <div class="casilla">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="vivienda">Vivienda</label>
                        <input type="text" name="vivienda" id="vivienda" placeholder="Texto" required>
                    </div>

                    <div class="casilla">
                        <label for="rol">Rol</label>
                        <select name="rol" id="rol" class="seleccion" required>
                            <option value="" selected disabled>Selecciona...</option>
                            <option value="vecino">Vecino</option>
                            <option value="presidente">Presidente</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>

                    <div class="casilla">
                        <label for="activo">Activo</label>
                        <select name="activo" id="activo" class="seleccion" required>
                            <option value="1" selected>Activado</option>
                            <option value="0">Desactivado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar Usuario</button>
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