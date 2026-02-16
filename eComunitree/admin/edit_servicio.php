<?php
    session_start();
    
    include("../db/db.inc");

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
                <h2>Actualizar Servicio</h2>
            </div>
            <hr>
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

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-plus"></i>
                        Nueva incidencia
                    </a>
                </li>
                <li>
                    <a href="../servicios.php">
                        <i class="fa-solid fa-briefcase"></i>
                        Servicios
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-solid fa-calendar-days"></i>
                        Calendario
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa-regular fa-file-lines"></i>
                        Documentación
                    </a>
                </li>
                <li>
                    <a href="panel_control.php">
                        <i class="fa-solid fa-gear"></i>
                        Panel de control
                    </a>
                </li>
            </ul>
        </aside>
    </main>
</body>
</html>