<?php
    session_start();
    include("./db/db.inc");
    $mensaje = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $nombre = trim($_POST["nombre"]);
        $apellidos = trim($_POST["apellidos"]);
        $vivienda = trim($_POST["vivienda"]);
        $email = trim($_POST["email"]);
        $password_input = $_POST["password"];
        $password_again = $_POST["passagain"];
        $rol_predeterminado = 'vecino';

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS
        if (!empty($nombre) && !empty($vivienda) && !empty($email) && !empty($password_input) && !empty($password_again)) {
            
            // VERIFICAR QUE DOBLE CAMPO PASSWORD COINCIDE
            if ($password_input !== $password_again) {
                $mensaje = "Las contraseñas no coinciden.";
            }

            // VALIDAR FORMATO DE EMAIL
            elseif (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                
                // VERIFICAR SI EMAIL EXISTE
                $stmt_check = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = ?");
                $stmt_check->bind_param("s", $email);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    $mensaje = "Este correo electrónico ya está registrado.";
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
                        $nuevo_id = $conn->insert_id;

                        // Iniciar sesión con los datos del formulario
                        $_SESSION["id_usuario"] = $nuevo_id;
                        $_SESSION["nombre"] = $nombre;
                        $_SESSION["email"] = $email;
                        $_SESSION["rol"] = $rol_predeterminado;
                        $_SESSION["vivienda"] = $vivienda;
                        $_SESSION["foto"] = 'default.jpg';

                        // Redirigir directamente al index
                        header("Location:index.php");
                        die();
                    } 
                    else $mensaje = "Hubo un error al guardar los datos. Inténtalo de nuevo.";
                    $stmt_insert->close();
                }
                $stmt_check->close();
            } 
            else $mensaje = "El formato del correo electrónico no es válido.";
        } 
        else $mensaje = "Por favor, rellena todos los campos obligatorios.";
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
    <?php if ($mensaje): ?>
        <div class="msg msg-timer" id="mensaje-contenedor">
            <p><i class="fa-solid fa-bell"></i> <?= $mensaje ?></p>
            <button type="button" class="btn-cerrar-msg" id="btn-cerrar-js">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <header>
        <img src="./img/logo-transparencia.png" alt="logotipo">
        <h1>eComunitree</h1>
    </header>

    <h2>Gestiona tu comunidad de forma fácil y digital.</h2>

    <main>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <h3>Introduce tus datos</h3>

            <hr>

            <div class="casilla">
                <label for="nombre">Nombre</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre" required>
            </div>
            
            <div class="casilla">
                <label for="apellidos">Apellidos</label>
                <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos">
            </div>
            
            <div class="casilla">
                <label for="vivienda">Vivienda</label>
                <input type="text" name="vivienda" id="vivienda" placeholder="Vivienda (Ej: 1ºC)" required>
            </div>
            
            <div class="casilla">
                <label for="email">Correo Electrónico</label>
                <input type="email" name="email" id="email" placeholder="Correo electrónico" required>
            </div>

            <hr>
            
            <div class="casilla">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="password" placeholder="Contraseña" required>
            </div>
            
            <div class="casilla">
                <label for="passagain">Repite Contraseña</label>
                <input type="password" name="passagain" id="passagain" class="password" placeholder="Repite contraseña" required>
            </div>

            <hr>

            <div class="register-footer">
                <a href="login.php">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <button type="submit">Confirmar y entrar</button>
            </div>
        </form>
    </main>
    
    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>