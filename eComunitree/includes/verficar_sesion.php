<?php
    if (isset($_SESSION["id_usuario"])) {
        $check_activo = $conn->query("SELECT activo FROM usuarios WHERE id_usuario = " . $_SESSION['id_usuario']);
        $estado = $check_activo->fetch_assoc();
    }

    // Si no ha iniciado sesión
    else {
        header("location:login.php");
        die();
    }

    // Si está desactivado
    if ($estado['activo'] == 0) {
        session_destroy();
        header("location:login.php?usu=1");
        die();
    }
?>