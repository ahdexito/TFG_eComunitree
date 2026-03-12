<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require '../src/PHPMailer/src/Exception.php';
    require '../src/PHPMailer/src/PHPMailer.php';
    require '../src/PHPMailer/src/SMTP.php';

    session_start();
    include("../db/db.inc");

    // --- COMENTAMOS LA SEGURIDAD TEMPORALMENTE PARA VER EL ERROR ---
    /*
    if (!isset($_SESSION["id_usuario"])) {
        die("Error: No hay sesión de usuario activa.");
    }
    */

    $mail = new PHPMailer(true);

    try {
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // ACTIVAMOS DEBUG
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ecomunitree@gmail.com';
        $mail->Password = 'wytp fdgw yggr pyqp'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom('ecomunitree@gmail.com', 'eComunitree');
        $mail->addAddress('ecomunitree@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = 'Prueba de Diagnóstico';
        $mail->Body = "Si ves esto, la conexión SMTP funciona.";

        echo "Iniciando envío...<br>";
        $mail->send();
        echo "<b>¡ÉXITO! El correo se envió correctamente.</b>";

    } catch (Exception $e) {
        echo "<b>FALLO EN EL ENVÍO:</b><br>";
        echo "Detalle del error: {$mail->ErrorInfo}";
    }
?>