<?php
session_start();
include("./db/db.inc");
$base = "./";

if (!isset($_SESSION["id_usuario"])) {
    header("location:./login.php");
    die();
}

$id_usuario = $_SESSION["id_usuario"];
$mensaje = "";

// Lógica para BORRAR FOTO (Restablecer a default.jpg)
if (isset($_POST['borrar_foto'])) {
    if ($_SESSION["foto"] != "default.jpg") {
        $archivo_viejo = "./img_user/" . $_SESSION["foto"];
        if (file_exists($archivo_viejo)) {
            unlink($archivo_viejo);
        }
        
        $sql = "UPDATE usuarios SET foto = 'default.jpg' WHERE id_usuario = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_usuario);
        
        if ($stmt->execute()) {
            $_SESSION["foto"] = "default.jpg";
            $mensaje = "Foto eliminada. Se ha restablecido la imagen predeterminada.";
        }
        $stmt->close();
    } else {
        $mensaje = "Ya tienes la foto predeterminada.";
    }
}

// Lógica para SUBIR NUEVA FOTO
if (isset($_FILES['nueva_foto']) && $_FILES['nueva_foto']['error'] === UPLOAD_ERR_OK) {
    $archivo = $_FILES['nueva_foto'];
    $tipo = $archivo['type'];
    $ruta_temporal = $archivo['tmp_name'];
    
    $permitidos = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    
    if (in_array($tipo, $permitidos)) {
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nuevo_nombre = "user_" . $id_usuario . "_" . time() . "." . $extension;
        $destino = "./img_user/" . $nuevo_nombre;

        if (move_uploaded_file($ruta_temporal, $destino)) {
            // Borrar anterior si no es la default
            if ($_SESSION["foto"] != "default.jpg" && file_exists("./img_user/" . $_SESSION["foto"])) {
                unlink("./img_user/" . $_SESSION["foto"]);
            }

            $sql = "UPDATE usuarios SET foto = ? WHERE id_usuario = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $nuevo_nombre, $id_usuario);
            
            if ($stmt->execute()) {
                $_SESSION["foto"] = $nuevo_nombre;
                $mensaje = "Foto actualizada con éxito.";
            }
            $stmt->close();
        }
    } else {
        $mensaje = "Formato no permitido.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar Foto | eComunitree</title>
    <link rel="stylesheet" href="./css/insert_edit/insert_edit.css">
    <link rel="icon" href="./img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php include("./includes/header.php"); ?>

    <nav>
        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Foto de Perfil</li>
        </ul>

        <?php include("./includes/aside.php"); ?>
    </nav>
    
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-solid fa-camera icono-header"></i> Modificar Foto de Perfil</h2>

                <hr>
            </div>

            <?php if ($mensaje): ?>
                <p style="color: white; background: #333; padding: 10px; border-radius: 5px; margin-bottom: 20px;"><?= $mensaje ?></p>
            <?php endif; ?>

            <div class="current-img">
                <p>Imagen actual:</p>
                <img src="./img_user/<?= $_SESSION['foto'] ?>" alt="Actual" style="width: 150px; height: 150px; border-radius: 100px; border: 4px solid white; object-fit: cover; box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
            </div>

            <form action="" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
                <div class="form">
                    <div class="casilla" style="grid-column: 1 / -1;">
                        <label for="nueva_foto">Subir Nueva Imagen</label>
                        <input type="file" name="nueva_foto" id="nueva_foto" class="imagen" accept="image/*" required>
                    </div>
                </div>
                <button type="submit" class="guardar">
                    <i class="fa-solid fa-upload"></i> Actualizar Foto
                </button>
            </form>

            <?php if ($_SESSION["foto"] != "default.jpg"): ?>
            <form action="" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar tu foto de perfil?');">
                <input type="hidden" name="borrar_foto" value="1">
                <button type="submit" class="guardar" style="margin-top: 0;">
                    <i class="fa-solid fa-trash-can"></i> Eliminar Foto
                </button>
            </form>
            <?php endif; ?>
            
        </section>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>