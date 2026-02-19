<?php
    session_start();
    include("db/db.inc");

    $error_msg = "";
    $success_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $nombre = trim($_POST["nombre"]);
        $apellidos = trim($_POST["apellidos"]);
        $vivienda = trim($_POST["vivienda"]);
        $email = trim($_POST["email"]);
        $password_input = $_POST["password"];
        $rol_predeterminado = 'vecino';

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        if (!empty($nombre) && !empty($vivienda) && !empty($email) && !empty($password_input)) {
            
            // VALIDAR FORMATO DE EMAIL
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                
                // VERIFICAR SI EMAIL EXISTE
                $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
                $stmt_check->bind_param("s", $email);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    $error_msg = "Este correo electrónico ya está registrado.";
                } else {
                    // CIFRADO DE CONTRASEÑA
                    $password_hashed = password_hash($password_input, PASSWORD_BCRYPT);

                    // INSERCIÓN EN LA BASE DE DATOS
                    $stmt_insert = $conn->prepare(
                        "INSERT INTO usuarios (nombre, apellidos, email, password, rol, vivienda) 
                        VALUES (?, ?, ?, ?, ?, ?)"
                    );
                    
                    $stmt_insert->bind_param("ssssss", $nombre, $apellidos, $email, $password_hashed, $rol_predeterminado, $vivienda);

                    if ($stmt_insert->execute()) {
                        $success_msg = "Registro completado con éxito. Ya puedes iniciar sesión.";
                    } else {
                        $error_msg = "Hubo un error al guardar los datos. Inténtalo de nuevo.";
                    }
                    $stmt_insert->close();
                }
                $stmt_check->close();
            } else {
                $error_msg = "El formato del correo electrónico no es válido.";
            }
        } else {
            $error_msg = "Por favor, rellena todos los campos obligatorios.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login/login.css">
    <title>eComunitree | Registro</title>
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <img src="./img/logo-transparencia.png" alt="logotipo">
        <h1>eComunitree</h1>
    </header>

    <h2>Gestiona tu comunidad de forma fácil y digital.</h2>

    <?php if (!empty($error_msg)): ?>
        <div class='error msg-timer' style="background: #ff000021; border-left: 5px solid red; padding: 10px; width: 80%; max-width: 400px; margin: 10px auto;">
            <i class='fa-solid fa-triangle-exclamation'></i> <?php echo $error_msg; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success_msg)): ?>
        <div class='success msg-timer' style="background: #00ff0021; border-left: 5px solid green; padding: 10px; width: 80%; max-width: 400px; margin: 10px auto;">
            <i class='fa-solid fa-circle-check'></i> <?php echo $success_msg; ?>
        </div>
    <?php endif; ?>

    <main>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h3>Introduce tus datos</h3>
            <hr>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
            <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos">
            <input type="text" name="vivienda" id="vivienda" placeholder="Vivienda (Ej: 1ºC)" required>
            <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
            <input type="password" name="password" id="password" placeholder="Contraseña" required>
            <hr>
            <div class="register-footer">
                <a href="login.php">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <button type="submit">Registrarme</button>
            </div>
        </form>
    </main>
    
    <footer>
        <script src="javascript/msg-timer.js"></script>
    </footer>
</body>
</html>