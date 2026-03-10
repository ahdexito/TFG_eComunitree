<?php
    session_start();

    include("./db/db.inc");
    
    $base = "./";

    if (!isset($_SESSION["id_usuario"])) {
        header("location:./login.php");
        die();
    }

    $id_usuario = $_SESSION["id_usuario"];
    $error = "";

    if (isset($_POST['nombre'])) {
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"] ?? "";
        $email = $_POST["email"];
        $telefono = $_POST["telefono"] ?? "";
        $vivienda = $_POST["vivienda"];
        
        $pass_actual = $_POST["pass_actual"];
        $pass_nueva = $_POST["pass_nueva"];
        $pass_confirm = $_POST["pass_confirm"];

        // VALIDAR SI EL EMAIL YA EXISTE EN OTRO USUARIO
        $sql_check_email = "SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?";
        $stmt_e = $conn->prepare($sql_check_email);
        $stmt_e->bind_param("si", $email, $id_usuario);
        $stmt_e->execute();
        if ($stmt_e->get_result()->num_rows > 0) {
            $error = "El email ya está registrado por otro usuario.";
        } else {
            // OBTENER PASS ACTUAL PARA VALIDAR
            $sql_pass = "SELECT password FROM usuarios WHERE id_usuario = ?";
            $stmt_p = $conn->prepare($sql_pass);
            $stmt_p->bind_param("i", $id_usuario);
            $stmt_p->execute();
            $user_db = $stmt_p->get_result()->fetch_assoc();

            if (!password_verify($pass_actual, $user_db['password'])) {
                $error = "La contraseña actual no es correcta.";
            } else {
                if (!empty($pass_nueva)) {
                    if ($pass_nueva !== $pass_confirm) {
                        $error = "Las nuevas contraseñas no coinciden.";
                    } else {
                        $pass_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
                        $sql_update = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, vivienda=?, password=? WHERE id_usuario=?";
                        $stmt = $conn->prepare($sql_update);
                        $stmt->bind_param("ssssssi", $nombre, $apellidos, $email, $telefono, $vivienda, $pass_hash, $id_usuario);
                    }
                } else {
                    $sql_update = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, vivienda=? WHERE id_usuario=?";
                    $stmt = $conn->prepare($sql_update);
                    $stmt->bind_param("sssssi", $nombre, $apellidos, $email, $telefono, $vivienda, $id_usuario);
                }

                if (empty($error)) {
                    try {
                        if ($stmt->execute()) {
                            $_SESSION["nombre"] = $nombre;
                            header("location:./index.php?upt=0");
                            die();
                        }
                    } catch (mysqli_sql_exception $e) {
                        $error = "Error: El email ya está en uso.";
                    }
                }
            }
        }
    }

    // Cargar datos actuales para el formulario
    $stmt_check = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt_check->bind_param("i", $id_usuario);
    $stmt_check->execute();
    $usuario = $stmt_check->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | eComunitree</title>
    <link rel="stylesheet" href="./css/insert_edit/insert_edit.css">
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("./includes/header.php"); ?>

    <nav>
        <a href="./index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Mi Perfil</li>
        </ul>

        <?php include("./includes/aside.php"); ?>
    </nav>
    
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-user icono-header"></i> Editar Datos</h2>
                <hr>
            </div>

            <?php if ($error): ?>
                <p style="color: tomato; font-weight: bold; margin: 10px 0;"><?= $error ?></p>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="form">
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

                    <div class="casilla">
                        <label for="pass_actual">Contraseña Actual (Requerida)</label>
                        <input type="password" name="pass_actual" id="pass_actual" required>
                    </div>

                    <div class="casilla">
                        <label for="pass_nueva">Nueva Contraseña (Opcional)</label>
                        <input type="password" name="pass_nueva" id="pass_nueva">
                    </div>

                    <div class="casilla">
                        <label for="pass_confirm">Confirmar Nueva Contraseña</label>
                        <input type="password" name="pass_confirm" id="pass_confirm">
                    </div>
                </div>
                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar Cambios</button>
            </form>
        </section>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>