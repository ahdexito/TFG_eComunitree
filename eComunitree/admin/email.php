<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    require './PHPMailer/src/Exception.php';
    require './PHPMailer/src/PHPMailer.php';
    require './PHPMailer/src/SMTP.php';

    include("../db/db.inc");

    // OBTENER USUARIOS
    $resultado = $conn->query("SELECT * FROM usuarios ");
    $usuarios = $resultado->fetch_all(MYSQLI_ASSOC);

    foreach($usuarios as $usuario) {
        //Creo una instancia; con `true` activamos excepciones
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ecomunitree@gmail.com';
            $mail->Password = 'wytp fdgw yggr pyqp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('ecomunitree@gmail.com', 'eComunitree');
            $mail->addAddress('destinatario@direccion_email');

            $mail->isHTML(true);
            $mail->Subject = 'Nueva Publicación';
            $mail->Body = '<h1>Hola, </h1><p>Se ha añadido una nueva publicación 
                en el tablón de tu comunidad. Accede para consultar las novedades.</p>';

            $mail->send();

            echo 'Correo enviado correctamente';

            header("location:gestion_publicaciones.php?publi=0");

        } catch (Exception $e) {
            echo 'Error al enviar el correo: ' . $mail->ErrorInfo;
        }
    }
?>