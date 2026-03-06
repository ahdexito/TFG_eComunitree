<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../src/PHPMailer/src/Exception.php';
    require '../src/PHPMailer/src/PHPMailer.php';
    require '../src/PHPMailer/src/SMTP.php';

    include("../db/db.inc");

    // OBTENER ÚLTIMA PUBLICACIÓN (Con verificación de errores)
    $sql_publicaciones = $conn->query("SELECT titulo, contenido, tipo FROM publicaciones ORDER BY id_publicacion DESC LIMIT 1");

    if (!$sql_publicaciones || $sql_publicaciones->num_rows === 0) {
        die("Error: No se encontró ninguna publicación para enviar.");
    }

    $publicacion = $sql_publicaciones->fetch_assoc();
    $titulo_publi = $publicacion['titulo'];
    $contenido_publi = $publicacion['contenido'];
    $tipo_publi = $publicacion['tipo'];

    // OBTENER USUARIOS
    $sql_usuarios = $conn->query("SELECT nombre, email FROM usuarios");
    $usuarios = $sql_usuarios->fetch_all(MYSQLI_ASSOC);

    // CONFIGURACIÓN DE PHPMAILER (Fuera del bucle para mayor velocidad)
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ecomunitree@gmail.com';
        $mail->Password = 'wytp fdgw yggr pyqp'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('ecomunitree@gmail.com', 'eComunitree');
        $mail->isHTML(true);
        $mail->Subject = 'Nueva Publicación: ' . $titulo_publi;

        // BUCLE DE ENVÍO
        foreach($usuarios as $usuario) {
            try {
                $mail->addAddress($usuario['email'], $usuario['nombre']);

                $mail->Body = "
                    <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px; max-width: 600px;'>
                        <h1 style='color: #2c3e50;'>¡Hola, " . htmlspecialchars($usuario['nombre']) . "!</h1>
                        <p>Se ha añadido una nueva publicación de tipo <strong>$tipo_publi</strong> en el tablón:</p>
                        
                        <div style='background-color: #f9f9f9; padding: 15px; border-left: 5px solid #4CAF50; margin: 20px 0;'>
                            <h2 style='margin-top: 0;'>$titulo_publi</h2>
                            <p style='color: #555;'>" . nl2br(htmlspecialchars(substr($contenido_publi, 0, 200))) . "...</p>
                        </div>
                        
                        <p>Haz clic en el botón para leer el contenido completo en la plataforma:</p>
                        <div style='text-align: center;'>
                            <a href='https://bit.ly/4tKjZF2' 
                                style='background-color: #4CAF50; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>
                                Ver publicación completa
                            </a>
                        </div>
                    </div>";

                $mail->send();
                $mail->clearAddresses(); // Limpiar destinatario para la siguiente vuelta

            } catch (Exception $e) {
                // Si falla un usuario, el script sigue con los demás
                error_log("Error enviando a {$usuario['email']}: {$mail->ErrorInfo}");
            }
        }

        // Redirigir al finalizar todos los envíos
        header("location:gestion_publicaciones.php?publi=0");
        exit();

    } catch (Exception $e) {
        echo "Error crítico de configuración: {$mail->ErrorInfo}";
    }
?>