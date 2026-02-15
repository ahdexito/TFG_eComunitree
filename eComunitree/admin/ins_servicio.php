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

    if (isset($_POST["nombre"])) {
        $nombre = htmlspecialchars($_POST["nombre"]);
        $activo = intval($_POST['activo']);
        $descripcion = htmlspecialchars($_POST["descripcion"]);
        $telefono = htmlspecialchars($_POST["telefono"]);
        $email = htmlspecialchars($_POST["email"]);
        $enlace = htmlspecialchars($_POST["enlace"]);

        $sql = 
            "INSERT INTO servicios (nombre, descripcion, telefono, email, enlace, activo)
            VALUES ('$nombre', '$descripcion', '$telefono', '$email', '$enlace', '$activo')";

        if (mysqli_query($conn, $sql)) {
            header("location:gestion_servicios.php?serv=0");// insertado correctamente
        } 
            
        else {
            header("location:gestion_servicios.php?serv=1");// error al insertar
        }

        die();
    }    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insertar Servicio</title>
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
                <i class="fa-regular fa-square-plus icono-header"></i>
                <h2>Insertar Servicio</h2>
            </div>

            <hr>

            <form method="POST">
                <div class="form">
                    <div class="casilla">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" placeholder="Texto" required>
                    </div>
                    
                    <div class="casilla">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="area-texto" placeholder="Área de texto"></textarea>
                    </div>

                    <div class="casilla">
                        <label for="telefono">Teléfono</label>
                        <input type="text" name="telefono" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="email">Email</label>
                        <input type="text" name="email" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="enlace">Enlace</label>
                        <input type="text" name="enlace" placeholder="Texto">
                    </div>

                    <div class="casilla">
                        <label for="activo">Activo</label>
                        <select name="activo" id="activo" class="seleccion" required>
                            <option value="1" selected>Activado</option>
                            <option value="0">Desactivado</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar Servicio</button>
            </form>
        </section>
    </main>
</body>
</html>