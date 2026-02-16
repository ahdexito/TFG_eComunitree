<?php
    session_start();

    include("db/db.inc");

    if (!isset($_SESSION["id_usuario"]) || $_SERVER["REQUEST_METHOD"] !== "POST") {
        header("location:index.php");
        die();
    }

    $id_usuario = $_SESSION["id_usuario"];
    $id_publicacion = intval($_POST['id_publicacion']);
    $id_opcion = intval($_POST['id_opcion']);

    // Comprobar si la votación está abierta
    $sql_check = "SELECT fecha_cierre FROM votaciones WHERE id_publicacion = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_publicacion);
    $stmt_check->execute();
    $res = $stmt_check->get_result()->fetch_assoc();

    if (!$res || new DateTime() > new DateTime($res['fecha_cierre'])) {
        header("location:index.php?error=cerrada");
    }

    // Insertar el voto
    $sql_voto = 
        "INSERT INTO votos (id_usuario, id_publicacion, id_opcion)
        VALUES (?, ?, ?)";
    $stmt_voto = $conn->prepare($sql_voto);
    $stmt_voto->bind_param("iii", $id_usuario, $id_publicacion, $id_opcion);

    try {
        if ($stmt_voto->execute()) {
            header("location:index.php?vote=success");
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) {
            header("location:index.php?error=ya_votado");
        } else {
            $msg = urlencode($e->getMessage());
            header("location:index.php?error=db&detalle=$msg");
        }
    }

    $stmt_check->close();
    $stmt_voto->close();
?>