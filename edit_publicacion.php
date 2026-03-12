<?php
    session_start();
    include("./db/db.inc");
    include("./includes/verficar_sesion.php");
    $base = "./";

    $id_publicacion = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
    $id_sesion = $_SESSION["id_usuario"];
    $mensaje = "";

    if ($id_publicacion === 0) {
        header("location:./index.php");
        die();
    }

    // Verificar autoría y obtener tipo de publicación
    $sql_check = "SELECT id_usuario, tipo FROM publicaciones WHERE id_publicacion = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id_publicacion);
    $stmt_check->execute();
    $res_check = $stmt_check->get_result();
    $datos_pub = $res_check->fetch_assoc();

    if (!$datos_pub || $datos_pub['id_usuario'] !== $id_sesion) {
        header("location:index.php?error=acceso_denegado");
        exit();
    }

    $tipo_actual = $datos_pub['tipo'];

    // LÓGICA DE PROCESAMIENTO
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $contenido = htmlspecialchars($_POST["descripcion"]); 

        $conn->begin_transaction();
        try {
            // Actualizar tabla padre
            $sql_upd = "UPDATE publicaciones SET titulo = ?, contenido = ? WHERE id_publicacion = ?";
            $st = $conn->prepare($sql_upd);
            $st->bind_param("ssi", $titulo, $contenido, $id_publicacion);
            $st->execute();

            // Lógica específica según tipo
            if ($tipo_actual === 'incidencia') {
                $id_tipo_inc = $_POST["tipo_incidencia"];
                
                // Actualizar tipo de incidencia
                $sql_inc = "UPDATE incidencias SET id_tipo_incidencia = ? WHERE id_publicacion = ?";
                $st_inc = $conn->prepare($sql_inc);
                $st_inc->bind_param("ii", $id_tipo_inc, $id_publicacion);
                $st_inc->execute();

                // MANEJO DE IMAGEN CON BORRADO FÍSICO
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    // Obtener la foto vieja de la base de datos antes de hacer nada
                    $res_foto = $conn->query("SELECT foto FROM incidencias WHERE id_publicacion = $id_publicacion");
                    $foto_vieja = $res_foto->fetch_assoc()['foto'];

                    $archivo = $_FILES['imagen'];
                    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
                    $nuevo_nombre = "INC_" . $id_sesion . "_" . time() . "." . $extension;
                    $destino = "img_incidencias/" . $nuevo_nombre;

                    if (move_uploaded_file($archivo['tmp_name'], $destino)) {
                        // Si la subida tuvo éxito, borrar la anterior si no es la default
                        if ($foto_vieja && $foto_vieja != "default.jpg" && file_exists("img_incidencias/" . $foto_vieja)) {
                            unlink("img_incidencias/" . $foto_vieja);
                        }

                        // Actualizar el nombre en la base de datos
                        $sql_f = "UPDATE incidencias SET foto = ? WHERE id_publicacion = ?";
                        $st_f = $conn->prepare($sql_f);
                        $st_f->bind_param("si", $nuevo_nombre, $id_publicacion);
                        $st_f->execute();
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
            header("location:index.php?upt=0");
            die();

        } catch (Exception $e) {
            $conn->rollback();
            $mensaje = "Error al intentar actualizar la publicación. Inténtelo de nuevo.";
        }
    }

    // Cargar datos para el formulario
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
    <title>eComunitree | Editar <?= ucfirst($tipo_actual) ?></title>
    <link rel="stylesheet" href="css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <?php if ($mensaje): ?>
        <div class="msg msg-timer" id="mensaje-contenedor">
            <p><i class="fa-solid fa-bell"></i> <?= $mensaje ?></p>
            <button type="button" class="btn-cerrar-msg" id="btn-cerrar-js">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    <?php endif; ?>

    <!-- HEADER -->
    <?php include("includes/header.php"); ?>
    
    <!-- NAV -->
    <nav>
        <a href="./index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Editar <?= ucfirst($tipo_actual) ?></li>
        </ul>

        <!-- ASIDE -->
        <?php include("includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-regular fa-pen-to-square"></i> Editar <?= ucfirst($tipo_actual) ?></h2>
                <hr>
            </div>

            <?php if($mensaje): ?><p class="error"><?= $mensaje ?></p><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form">
                    
                    <div class="casilla">
                        <label for="titulo">Título</label>
                        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($pub['titulo']) ?>" required>
                    </div>

                    <?php if($tipo_actual == 'votacion'): ?>
                    <div class="casilla">
                        <label for="fecha_cierre">Fecha de cierre</label>
                        <input type="date" name="fecha_cierre" id="fecha_cierre" value="<?= $pub['fecha_cierre'] ?>" required>
                    </div>
                    <?php endif; ?>

                    <?php if($tipo_actual == 'incidencia'): ?>
                    <div class="casilla">
                        <label for="tipo">Tipo de Incidencia</label>
                        <select name="tipo_incidencia" id="tipo" class="seleccion" required>
                            <?php foreach ($tipos_incidencia as $ti): ?>
                                <option value="<?= $ti['id_tipo_incidencia'] ?>" <?= ($ti['id_tipo_incidencia'] == $pub['id_tipo_incidencia']) ? 'selected' : '' ?> >
                                    [<?= strtoupper($ti['nombre']) ?>]: <?= $ti['descripcion'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="casilla">
                        <label>Imagen actual de la incidencia</label>
                        <div class="edit-foto-container" style="display: flex; align-items: center; gap: 20px; background: #f9f9f9; padding: 10px; border-radius: 15px;">
                            <?php 
                                $ruta_foto = (!empty($pub['foto']) && file_exists("img_incidencias/" . $pub['foto'])) 
                                    ? "img_incidencias/" . $pub['foto'] 
                                    : "img_incidencias/default.jpg";
                            ?>
                            <img src="<?= $ruta_foto ?>" alt="Incidencia" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                            
                            <div style="flex-grow: 1;">
                                <p style="font-size: 0.85em; color: #555; margin-bottom: 5px;">Selecciona una nueva imagen para reemplazar la anterior:</p>
                                <input type="file" name="imagen" accept="image/*" class="imagen">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="casilla">
                        <label for="descripcion">Descripción detallada</label>
                        <textarea name="descripcion" id="descripcion" class="area-texto" required><?= htmlspecialchars($pub['contenido']) ?></textarea>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Guardar Cambios
                </button>
            </form>
        </section>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>