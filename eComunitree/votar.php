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
        // Votación cerrada
        header("location:index.php?vote=2");
    }

    // Insertar el voto
    $sql_voto = 
        "INSERT INTO votos (id_usuario, id_publicacion, id_opcion)
        VALUES (?, ?, ?)";
    $stmt_voto = $conn->prepare($sql_voto);
    $stmt_voto->bind_param("iii", $id_usuario, $id_publicacion, $id_opcion);

    try {
        if ($stmt_voto->execute()) {
            // Votación realizada correctamente
            header("location:index.php?vote=0");
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() === 1062) {
            // Votación ya votada
            header("location:index.php?vote=1");
        } else {
            // Error inesperado
            header("location:index.php?vote=3");
        }
    }

    $stmt_check->close();
    $stmt_voto->close();
?>