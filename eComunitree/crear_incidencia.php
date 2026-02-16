<?php
    session_start();

    include("db/db.inc");
    
    if (!isset($_SESSION["rol"])) {
        header("location:../index.php?usu=1");
        die();
    } 

    // OBTENER TIPOS DE INCIDENCIAS
    $resultado = $conn->query(
        "SELECT * FROM tipos_incidencia 
        ORDER BY id_tipo_incidencia DESC"
    );
    $tipos_incidencia = $resultado->fetch_all(MYSQLI_ASSOC);

    // INSERTAR PUBLICACIÓN
    if (isset($_POST["titulo"]) && isset($_POST["contenido"])) {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $tipo = $_POST["tipo"];
        $contenido = htmlspecialchars($_POST["contenido"]);
        $id_usuario = $_SESSION["id_usuario"];
    }    
?>

