<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../src/PHPMailer/src/Exception.php';
    require '../src/PHPMailer/src/PHPMailer.php';
    require '../src/PHPMailer/src/SMTP.php';

    session_start();
    include("../db/db.inc");

    if (!isset($_SESSION["id_usuario"])) {
        header("location:../login.php?usu=1");
        exit();
    }

    // VERIFICACIÓN DE ACCESO
    if (!isset($_SESSION['proceso_envio']) || $_SESSION['proceso_envio'] !== true) {
        // Si no viene de crear una publicación, lo mandamos al index
        header("location:../login.php");
        exit();
    }

    $sql_publicaciones = $conn->query("SELECT titulo, contenido, tipo FROM publicaciones ORDER BY id_publicacion DESC LIMIT 1");

    if (!$sql_publicaciones || $sql_publicaciones->num_rows === 0) {
        die("Error: No se encontró ninguna publicación.");
    }

    $publicacion = $sql_publicaciones->fetch_assoc();
    $titulo_publi = $publicacion['titulo'];
    $contenido_publi = $publicacion['contenido'];
    $tipo_publi = $publicacion['tipo'];

    $sql_usuarios = $conn->query("SELECT email FROM usuarios");
    $usuarios = $sql_usuarios->fetch_all(MYSQLI_ASSOC);

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
        $mail->addAddress('ecomunitree@gmail.com', 'Comunidad eComunitree');

        foreach($usuarios as $usuario) {
            $email_vecino = $usuario['email'];
            
            // SOLO añade el correo si contiene un dominio real
            // O simplemente asegúrate de que no sea un correo de dominio inventado
            if (filter_var($email_vecino, FILTER_VALIDATE_EMAIL) && !strpos($email_vecino, 'ecomunitree.com')) {
                $mail->addBCC($email_vecino);
            }
        }

        $mail->isHTML(true);
        $mail->Subject = 'Nueva Publicación: ' . $titulo_publi;
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; border: 1px solid #ddd; padding: 20px; border-radius: 10px; max-width: 600px;'>
                <h1 style='color: #2c3e50;'>¡Hola, vecino/a!</h1>
                <p>Se ha añadido una nueva publicación de tipo <strong>$tipo_publi</strong> en el tablón:</p>
                <div style='background-color: #f9f9f9; padding: 15px; border-left: 5px solid #4042be; margin: 20px 0;'>
                    <h2 style='margin-top: 0;'>$titulo_publi</h2>
                    <p style='color: #555;'>" . nl2br(htmlspecialchars(substr($contenido_publi, 0, 200))) . "...</p>
                </div>
                <div style='text-align: center;'>
                    <a http://localhost/ecomunitree/' style='background-color: #4042be; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;'>Ver publicación completa</a>
                </div>
            </div>";

        $mail->send();
        unset($_SESSION['proceso_envio']);

        if ($tipo_publi === "incidencia") header("location:../index.php?ins=0");
        else header("location:gestion_publicaciones.php?ins=0");
        exit();

    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
?>