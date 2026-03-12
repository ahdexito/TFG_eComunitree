<?php
    session_start();
    include("./db/db.inc");
    include("./includes/verficar_sesion.php");
    $base = "./";

    $id_usuario = $_SESSION["id_usuario"];
    $mensaje = "";

    // Lógica para actualizar datos personales
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre'])) {
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"] ?? "";
        $email = $_POST["email"];
        $telefono = $_POST["telefono"] ?? "";
        $vivienda = $_POST["vivienda"];
        $pass_verificar = $_POST["pass_verificar"]; // Nueva variable

        // Validar email duplicado
        $sql_check = "SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?";
        $stmt_e = $conn->prepare($sql_check);
        $stmt_e->bind_param("si", $email, $id_usuario);
        $stmt_e->execute();
        
        if ($stmt_e->get_result()->num_rows > 0) {
            $mensaje = "El email ya está registrado por otro usuario.";
        } else {
            // VERIFICAR CONTRASEÑA ANTES DE ACTUALIZAR
            $stmt_p = $conn->prepare("SELECT password FROM usuarios WHERE id_usuario = ?");
            $stmt_p->bind_param("i", $id_usuario);
            $stmt_p->execute();
            $user_db = $stmt_p->get_result()->fetch_assoc();

            if (!password_verify($pass_verificar, $user_db['password'])) {
                $mensaje = "La contraseña de confirmación es incorrecta.";
            } else {
                $sql_update = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, profesional=?, vivienda=? WHERE id_usuario=?";
                // Nota: He mantenido tu estructura original, asegúrate de que el campo 'telefono' o 'profesional' coincida con tu DB. 
                // Usando los datos del POST:
                $sql_update = "UPDATE usuarios SET nombre=?, apellidos=?, email=?, telefono=?, vivienda=? WHERE id_usuario=?";
                $stmt = $conn->prepare($sql_update);
                $stmt->bind_param("sssssi", $nombre, $apellidos, $email, $telefono, $vivienda, $id_usuario);

                if ($stmt->execute()) {
                    $_SESSION["nombre"] = $nombre;
                    $_SESSION["vivienda"] = $vivienda;
                    header("location:./index.php?prof=0");
                    die();
                } else {
                    $mensaje = "Error al actualizar los datos.";
                }
            }
        }
    }

    // Cargar datos actuales
    $stmt_load = $conn->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $stmt_load->bind_param("i", $id_usuario);
    $stmt_load->execute();
    $usuario = $stmt_load->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>eComunitree | Editar Perfil</title>
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
        <a href="./index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Editar Perfil</li>
        </ul>

        <!-- ASIDE -->
        <?php include("./includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-user-pen icono-header"></i> Mis Datos</h2>
                <hr>
            </div>

            <form action="" method="POST">
                <div class="form">
                    <div class="casilla">
                        <label>Nombre</label>
                        <input type="text" name="nombre" value="<?= $usuario["nombre"] ?>" required>
                    </div>
                    <div class="casilla">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" value="<?= $usuario["apellidos"] ?>" required>
                    </div>
                    <div class="casilla">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $usuario["email"] ?>" required>
                    </div>
                    <div class="casilla">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" value="<?= $usuario["telefono"] ?>">
                    </div>
                    <div class="casilla">
                        <label>Vivienda</label>
                        <input type="text" name="vivienda" value="<?= $usuario["vivienda"] ?>" required>
                    </div>
                    <div class="casilla">
                        <label>Confirmar Contraseña</label>
                        <input type="password" name="pass_verificar" placeholder="Introduce tu contraseña" required>
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