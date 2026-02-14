<?php
    session_start();
    
    include("../db/db.inc");

    if (!isset($_SESSION["rol"])) {
        header("location:../login.php");
        die();
    }
    
    elseif ($_SESSION["rol"] == 'vecino') {
        header("location:../login.php");
        die();
    }

    if (!isset($_GET['edit'])) {
        header("location:gestion_servicios.php");
        die();
    }

    $id_servicio = intval($_GET['edit']);
    $sql = "SELECT * FROM servicios WHERE id_servicio = $id_servicio";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) === 0) {
        header("location:gestion_servicios.php");
        die();
    }

    $servicio = mysqli_fetch_assoc($res);

    if (isset($_POST['nombre'])) {
        $activo = intval($_POST['activo']);
        $nombre = htmlspecialchars($_POST['nombre']);
        $descripcion = htmlspecialchars($_POST['descripcion']);
        $telefono = htmlspecialchars($_POST['telefono']);
        $email = htmlspecialchars($_POST['email']);
        $enlace = htmlspecialchars($_POST['enlace']);

        $sql_update = 
            "UPDATE servicios SET 
            nombre = '$nombre', descripcion = '$descripcion', telefono = '$telefono', 
            email = '$email', enlace = '$enlace', activo = '$activo'
            WHERE id_servicio = '$id_servicio'";

        if(mysqli_query($conn, $sql_update)) {
            header("location:gestion_servicios.php?upt=0"); // actualizado correctamente
        } 
        else {
            header("location:gestion_servicios.php?upt=1"); // error al actualizar
        }
        die();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>

<body>
    <header class="body-header">
        <a href="../index.php" class="btn-index">
            <img src="../img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-name">
                <p><?= $_SESSION["nombre"] ?> (<?= $_SESSION["vivienda"] ?>)</p>
                <p><?= ucfirst($_SESSION["rol"]) ?></p>
            </div>
            <a href="desconectar_admin.php" title="Cerrar sesión">
                <img src="../img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-regular fa-pen-to-square icono-header"></i>
                <h2>Actualizar producto</h2>
            </div>
            <hr>
            <form method="POST" enctype="multipart/form-data">
                <div class="form">
                    <div class="casilla">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" value="<?= htmlspecialchars($servicio['nombre']) ?>" required>
                    </div>

                    <div class="casilla">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="area-texto"><?= htmlspecialchars($servicio['descripcion']) ?></textarea>
                    </div>

                    <div class="casilla">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" value="<?= htmlspecialchars($servicio['telefono']) ?>">
                    </div>

                    <div class="casilla">
                        <label for="email">Email</label>
                        <input type="text" name="email" value="<?= htmlspecialchars($servicio['email']) ?>">
                    </div>

                    <div class="casilla">
                        <label for="enlace">Enlace</label>
                        <input type="text" name="enlace" value="<?= htmlspecialchars($servicio['enlace']) ?>">
                    </div>

                    <div class="casilla">
                        <label for="activo">Activo</label>
                        <select name="activo" class="seleccion" required>
                            <option value="0" <?= $servicio['activo']==0?'selected':'' ?> >Desactivado</option>
                            <option value="1" <?= $servicio['activo']==1?'selected':'' ?> >Activado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Actualizar Servicio</button>
            </form>
        </section>
    </main>
</body>
</html>