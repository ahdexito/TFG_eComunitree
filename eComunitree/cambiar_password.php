<?php
    session_start();
    include("./db/db.inc");
    include("./includes/verficar_sesion.php");
    $base = "./";

    $id_usuario = $_SESSION["id_usuario"];
    $mensaje = "";

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pass_actual'])) {
        $pass_actual = $_POST["pass_actual"];
        $pass_nueva = $_POST["pass_nueva"];
        $pass_confirm = $_POST["pass_confirm"];

        // Obtener contraseña hash de la DB
        $stmt_p = $conn->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
        $stmt_p->bind_param("i", $id_usuario);
        $stmt_p->execute();
        $user_db = $stmt_p->get_result()->fetch_assoc();

        if (!password_verify($pass_actual, $user_db['password'])) {
            $mensaje = "La contraseña actual no es correcta.";
        } elseif (empty($pass_nueva) || $pass_nueva !== $pass_confirm) {
            $mensaje = "Las nuevas contraseñas no coinciden o están vacías.";
        } else {
            $pass_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
            $stmt_u = $conn->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
            $stmt_u->bind_param("si", $pass_hash, $id_usuario);
            
            if ($stmt_u->execute()) {
                header("location:./index.php?prof=0");
                die();
            } else {
                $mensaje = "Error al actualizar la contraseña.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguridad | eComunitree</title>
    <link rel="stylesheet" href="./css/insert_edit/insert_edit.css">
    <link rel="icon" href="./img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?> 
        <span class="msg"><?= $mensaje ?></span> 
    <?php endif; ?>

    <!-- HEADER -->
    <?php include("./includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./editar_datos.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Cambiar Contraseña</li>
        </ul>

        <!-- ASIDE -->
        <?php include("./includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-key icono-header"></i> Cambiar Contraseña</h2>
                <hr>
            </div>

            <form action="" method="POST">
                <div class="form">
                    <div class="casilla">
                        <label>Contraseña Actual</label>
                        <input type="password" name="pass_actual" required>
                    </div>
                    <div class="casilla">
                        <label>Nueva Contraseña</label>
                        <input type="password" name="pass_nueva" required>
                    </div>
                    <div class="casilla">
                        <label>Confirmar Nueva Contraseña</label>
                        <input type="password" name="pass_confirm" required>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </form>
        </section>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>