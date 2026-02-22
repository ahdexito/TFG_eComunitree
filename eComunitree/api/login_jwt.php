<?php
    require_once '../vendor/autoload.php';
    require_once '../db/db.inc';
    use Firebase\JWT\JWT;

    header('Content-Type: application/json');

    // Recibir los datos
    $json = file_get_contents('php://input');
    $data = json_decoDe($json, true);

    $email_input = $data['email'] ?? '';
    $password_input = $data['password'] ?? '';

    if (!$email_input || !$password_input) {
        echo json_encode(["error" => "Faltan credenciales"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id_usuario, nombre, email, password, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email_input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($usuario = $result->fetch_assoc()) {
        if (password_verify($password_input, $usuario["password"])) {
            $key = "123456";
            $payload = [
                "iat" => time(),
                "exp" => time() + (60 * 60 * 24),
                "id_usuario" => $usuario["id_usuario"],
                "nombre" => $usuario["nombre"],
                "rol" => $usuario["rol"]
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            echo json_encode([
                "success" => true,
                "token" => $jwt,
                "nombre" => $usuario["nombre"]
            ]);
        }

        else {
            http_response_code(401);
            echo json_encode(["error" => "Password incorrecto"]);
        }
    }

    else {
        http_response_code(401);
        echo json_encode(["error" => "Usuario no encontrado"]);
    }
