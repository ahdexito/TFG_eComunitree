<?php
    session_start();
    include("../db/db.inc");
    $base = "../";

    // Verificación de sesión. Si no hay usuario, fuera
    if (!isset($_SESSION["id_usuario"])) {
        header("location:../login.php?usu=1");
        die();
    }

    if (!isset($_GET['edit'])) {
        header("location:gestion_publicaciones.php");
        die();
    }

    $id_publicacion = intval($_GET['edit']);
    $id_sesion = $_SESSION["id_usuario"];
    $mensaje = "";

    // Consultar la publicación para saber quién la escribió
    $sql_check = "SELECT id_usuario, tipo FROM publicaciones WHERE id_publicacion = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_publicacion);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $datos_pub = $res_check->fetch_assoc();

    // Si la publicación no existe o el id_usuario del creador NO coincide con el de la sesión
    // se le expulsa sin cargar el formulario
    if (!$datos_pub || $datos_pub['id_usuario'] !== $id_sesion) {
        header("location:gestion_publicaciones.php?error=acceso_denegado");
        exit();
    }

    $tipo_actual = $datos_pub['tipo'];

    // A partir de aquí solo llega el autor original

    /// LÓGICA DE PROCESAMIENTO POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $contenido = htmlspecialchars($_POST["contenido"]);

        $conn->begin_transaction();
        try {
            // Actualizar tabla principal
            $sql_upd = "UPDATE publicaciones SET titulo = ?, contenido = ? WHERE id_publicacion = ?";
            $st = $conn->prepare($sql_upd);
            $st->bind_param("ssi", $titulo, $contenido, $id_publicacion);
            $st->execute();

            // Lógica específica por tipo
            if ($tipo_actual === 'incidencia') {
                $id_tipo_inc = $_POST["id_tipo_incidencia"];
                $sql_inc = "UPDATE incidencias SET id_tipo_incidencia = ? WHERE id_publicacion = ?";
                $st_inc = $conn->prepare($sql_inc);
                $st_inc->bind_param("ii", $id_tipo_inc, $id_publicacion);
                $st_inc->execute();

                // Manejo de imagen (subida y borrado físico)
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $res_foto = $conn->query("SELECT foto FROM incidencias WHERE id_publicacion = $id_publicacion");
                    $foto_vieja = $res_foto->fetch_assoc()['foto'];

                    $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
                    $nuevo_nombre = "INC_" . $id_sesion . "_" . time() . "." . $extension;
                    $destino = "../img_incidencias/" . $nuevo_nombre; // Ajuste de ruta relativa

                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
                        if ($foto_vieja && $foto_vieja != "default.jpg" && file_exists("../img_incidencias/" . $foto_vieja)) {
                            unlink("../img_incidencias/" . $foto_vieja);
                        }
                        $conn->query("UPDATE incidencias SET foto = '$nuevo_nombre' WHERE id_publicacion = $id_publicacion");
                    }
                }
            } elseif ($tipo_actual === 'votacion') {
                $fecha_c = $_POST["fecha_cierre"];
                $sql_v = "UPDATE votaciones SET fecha_cierre = ? WHERE id_publicacion = ?";
                $st_v = $conn->prepare($sql_v);
                $st_v->bind_param("si", $fecha_c, $id_publicacion);
                $st_v->execute();
            }

            $conn->commit();
            header("location:gestion_publicaciones.php?upt=0");
            exit();

        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "Error al intentar actualizar la publicación. Inténtelo de nuevo.";
        }
    }

    // Cargar datos actualizados para el formulario
    $sql_data = "SELECT p.*, i.id_tipo_incidencia, i.foto, v.fecha_cierre 
                FROM publicaciones p 
                LEFT JOIN incidencias i ON p.id_publicacion = i.id_publicacion 
                LEFT JOIN votaciones v ON p.id_publicacion = v.id_publicacion 
                WHERE p.id_publicacion = ?";
    $stmt_data = $conn->prepare($sql_data);
    $stmt_data->bind_param("i", $id_publicacion);
    $stmt_data->execute();
    $pub = $stmt_data->get_result()->fetch_assoc();

    $tipos_incidencia = $conn->query("SELECT * FROM tipos_incidencia WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Editar Publicación</title>
    <link rel="stylesheet" href="../css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?>
        <div class="msg msg-timer" id="mensaje-contenedor">
            <p><?= $mensaje ?></p>
            <button type="button" class="btn-cerrar-msg" id="btn-cerrar-js">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <?php include("../includes/header.php"); ?>
    
    <!-- NAV -->
    <nav>
        <a href="./gestion_publicaciones.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="../index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="panel_control.php">Panel Control</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li><a href="gestion_publicaciones.php">Gestión Publicaciones</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Editar Publicación</li>
        </ul>

        <!-- ASIDE -->
        <?php include("../includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-regular fa-pen-to-square"></i> Editar <?= ucfirst($tipo_actual) ?></h2>
                <hr>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tipo" value="<?= $tipo_actual ?>">

                <div class="form">
                    <div class="casilla">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($pub['titulo']) ?>" required>
                    </div>

                    <?php if($tipo_actual == 'votacion'): ?>
                    <div class="casilla">
                        <label>Fecha de cierre</label>
                        <input type="date" name="fecha_cierre" value="<?= $pub['fecha_cierre'] ?>" required>
                    </div>
                    <?php endif; ?>

                    <?php if($tipo_actual == 'incidencia'): ?>
                    <div class="casilla">
                        <label>Categoría</label>
                        <select name="id_tipo_incidencia" class="seleccion">
                            <?php foreach($tipos_incidencia as $t): ?>
                                <option value="<?= $t['id_tipo_incidencia'] ?>" <?= $t['id_tipo_incidencia'] == $pub['id_tipo_incidencia'] ? 'selected' : '' ?> >
                                    <?= $t['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="casilla">
                        <label>Imagen de la incidencia</label>
                        <div class="edit-foto-container" style="display: flex; align-items: center; gap: 20px; background: #f9f9f9; padding: 10px; border-radius: 15px;">
                            <?php 
                                $ruta_foto = (!empty($pub['foto']) && file_exists("../img_incidencias/" . $pub['foto'])) 
                                    ? "../img_incidencias/" . $pub['foto'] 
                                    : "../img_incidencias/default.jpg";
                            ?>
                            <img src="<?= $ruta_foto ?>" alt="Incidencia" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                            <div style="flex-grow: 1;">
                                <p style="font-size: 0.8em; color: #666; margin-bottom: 5px;">Sube una nueva para reemplazar:</p>
                                <input type="file" name="imagen" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="casilla">
                        <label>Contenido</label>
                        <textarea name="contenido" class="area-texto" required><?= htmlspecialchars($pub['contenido']) ?></textarea>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </form>
        </section>
    </main>

    <footer>
        <script type="module" src="../js/main.js"></script>
    </footer>
</body>
</html>