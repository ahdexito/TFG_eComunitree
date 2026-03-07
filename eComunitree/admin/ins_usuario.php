<?php
    session_start();
    
    include("../db/db.inc");

    $base = "../";

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
            <li><a href="gestion_usuarios.php">Gestión Usuarios</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Insertar Usuario</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-regular fa-square-plus icono-header"></i> Insertar Usuario</h2>
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
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>