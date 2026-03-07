<?php
    session_start();

    include("db/db.inc");

    $error_msg = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // Validación de formato de email
        if(isset($_POST["email"]) && !empty($_POST["email"]) && filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            
            // Validación de presencia de contraseña
            if(isset($_POST["password"]) && !empty($_POST["password"])) {
                
                // Limpieza de datos (email) y cifrado (password)
                $email_input = trim($_POST["email"]);
                $password_input = $_POST["password"];

                // Preparación de consulta segura contra SQL Injection
                $stmt = $conn->prepare(
                    "SELECT id_usuario, nombre, email, password, rol, vivienda, foto 
                    FROM usuarios 
                    WHERE email = ?"
                    );

                $stmt->bind_param("s", $email_input);
                $stmt->execute();
                $result = $stmt->get_result();

                // Verificación de existencia del usuario
                if ($usuario = $result->fetch_assoc()) {

                    if (password_verify($password_input, $usuario["password"])) {

                        $_SESSION["id_usuario"] = $usuario["id_usuario"];
                        $_SESSION["nombre"] = $usuario["nombre"];
                        $_SESSION["email"] = $usuario["email"];
                        $_SESSION["rol"] = $usuario["rol"];
                        $_SESSION["vivienda"] = $usuario["vivienda"];
                        $_SESSION["foto"] = $usuario["foto"];

                        // LÓGICA DE RECUÉRDAME
                        if (isset($_POST["check"])) {
                            // Si el checkbox está marcado, guardar el email por 30 días
                            setcookie("user_email", $email_input, time() + (86400 * 30), "/");
                        } else {
                            // Si no está marcado, borrar la cookie
                            if (isset($_COOKIE["user_email"])) {
                                setcookie("user_email", "", time() - 3600, "/");
                            }
                        }

                        header("location:./index.php");
                        die();
                    } else {
                        // Contraseña incorrecta
                        $error_msg = "El email y/o la contraseña NO coinciden.";
                    }
                } else {
                    // Email no encontrado
                    $error_msg = "El email y/o la contraseña NO coinciden.";
                }

                $stmt->close();
            } else {
                $error_msg = "Error en el campo 'contraseña'.";
            }
        } else {
            if (isset($_POST["email"])) {
                $error_msg = "El email NO es válido.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login/login.css">
    <link rel="icon" href="img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
    <title>eComunitree | Login</title>
</head>
<body>
    <header>
        <img src="./img/logo-transparencia.png" alt="logotipo">
        <h1>eComunitree</h1>
    </header>

    <h2>Gestiona tu comunidad de forma fácil y digital.</h2>

    <!-- Imprimir errores de procesamiento de usuario -->
    <?php if (!empty($error_msg)): ?>
            <div class='error msg-timer'>
                <i class='fa-solid fa-triangle-exclamation'></i><?php echo $error_msg; ?>
            </div>
    <?php endif; ?>

    <?php if (isset($_GET["usu"]) && $_GET["usu"] == 1): ?>
        <div class='error msg-timer'>
            <i class='fa-solid fa-triangle-exclamation'></i> 
            No tienes permiso para acceder a la página introducida. Inicia sesión con una cuenta válida o contacta con tu administrador.
        </div>
    <?php endif; ?>

    <main>
        <form method="POST">
            <h3>¡Hola, vecino/a!</h3>

            <hr>

            <?php 
                $email_value = isset($_COOKIE["user_email"]) ? $_COOKIE["user_email"] : ""; 
                $check_status = isset($_COOKIE["user_email"]) ? "checked" : "";
            ?>

            <input type="email" name="email" id="email" placeholder="Correo electrónico" value="<?php echo $email_value; ?>">

            <input type="password" name="password" id="password" class="password" placeholder="Contraseña">

            <div class="recuerdame">
                <input type="checkbox" name="check" class="check" id="check" <?php echo $check_status; ?> >
                <label for="check">Recuérdame</label>
            </div>

            <hr>

            <button type="submit">Iniciar sesión</button>
        </form>
        
        <section class="registrarse">
            <h3>¿Eres nuevo/a?</h3>
            <hr>
            <p>Comunícate, vota, reporta incidencias y mantente al día desde un único lugar.</p>
            <hr>
            <a href="register.php"><button>Registrarme</button></a>
        </section>
    </main>
    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>