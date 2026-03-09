<?php
    session_start();
    
    include("../db/db.inc");

    $base = "../";

    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] == 'vecino') {
        header("location:../login.php?usu=1");
        die();
    }

    if (!isset($_GET['edit'])) {
        header("location:gestion_servicios.php");
        die();
    }

    $id_servicio = intval($_GET['edit']);

    if (isset($_POST['nombre'])) {
        $activo = intval($_POST['activo']);
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $enlace = $_POST['enlace'];

        $sql_update = "UPDATE servicios SET nombre = ?, descripcion = ?, telefono = ?, email = ?, enlace = ?, activo = ? WHERE id_servicio = ?";
        
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("sssssii", $nombre, $descripcion, $telefono, $email, $enlace, $activo, $id_servicio);

        if ($stmt->execute()) {
            header("location:gestion_servicios.php?upt=0");
        } else {
            header("location:gestion_servicios.php?upt=1");
        }
        
        $stmt->close();
        die();
    }

    $sql_check = "SELECT * FROM servicios WHERE id_servicio = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_servicio);
    $stmt_check->execute();
    $res = $stmt_check->get_result();

    if ($res->num_rows === 0) {
        header("location:gestion_servicios.php");
        die();
    }

    $servicio = $res->fetch_assoc();
    $stmt_check->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Servicio</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
    <link rel="icon" href="../img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_servicios.php">Gestión Servicios</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Actualizar Servicio</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-regular fa-pen-to-square icono-header"></i> Actualizar Servicio</h2>
                <hr>
            </div>
            
            <form method="POST">
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

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>