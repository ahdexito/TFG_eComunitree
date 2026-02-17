<?php
    session_start();

    include("db/db.inc");
    
    if (!isset($_SESSION["rol"])) {
        header("location:../index.php");
        die();
    } 

    // OBTENER TIPOS DE INCIDENCIAS
    $resultado = $conn->query(
        "SELECT * FROM tipos_incidencia 
        WHERE activo = TRUE
        ORDER BY id_tipo_incidencia ASC"
    );
    $tipos_incidencia = $resultado->fetch_all(MYSQLI_ASSOC);

    // LÓGICA DE INSERCIÓN
    if (isset($_POST["titulo"]) && $_SERVER["REQUEST_METHOD"] == "POST") {
        $titulo = htmlspecialchars($_POST["titulo"]);
        $id_tipo = $_POST["tipo_incidencia"];
        $contenido = htmlspecialchars($_POST["descripcion"]);
        $id_usuario = $_SESSION["id_usuario"];
        $tipo_publicacion = 'incidencia';

        // PROCESAR IMAGEN
        $nombre_imagen = "default.jpg";

        if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === 0) {
            $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
            // Nombre único: INC_USERID_TIME.EXT
            $nombre_imagen = "INC_" . $id_usuario . "_" . time() . "." . $extension;
            
            if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], "img_incidencias/" . $nombre_imagen)) {
                $nombre_imagen = "default.jpg"; // Si falla la subida volver al default
            }
        }

        // INICIAR TRANSACCIÓN
        $conn->begin_transaction();

        try {
            // INSERTAR EN PUBLICACIONES
            $sql1 = 
                "INSERT INTO publicaciones (id_usuario, tipo, titulo, contenido)
                VALUES (?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("isss", $id_usuario, $tipo_publicacion, $titulo, $contenido);
            $stmt1->execute();

            $id_publicacion = $conn->insert_id;

            // INSERTAR EN INCIDENCIAS
            $sql2 = 
                "INSERT INTO incidencias (id_publicacion, id_tipo_incidencia, estado, foto)
                VALUES (?, ?, 'pendiente', ?)";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("iis", $id_publicacion, $id_tipo, $nombre_imagen);
            $stmt2->execute();

            $conn->commit();
            header("location:index.php?ins=ok");

        } catch (Exception $e) {
            $conn->rollback();
            if ($nombre_imagen !== "default.jpg" && file_exists("img_incidencias/" . $nombre_imagen)) {
                unlink("img_incidencias/" . $nombre_imagen);
            }
            header("location:index.php?ins=error");
        }
    }    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Index</title>
    <link rel="stylesheet" href="css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="index.php" class="btn-index">
            <img src="img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-name">
                <p><?= $_SESSION["nombre"] ?> (<?= $_SESSION["vivienda"] ?>)</p>
                <p><?= ucfirst($_SESSION["rol"]) ?></p>
            </div>
            <a href="desconectar.php" title="Cerrar sesión">
                <img src="img_user/<?= $_SESSION["foto"]; ?>" alt="Perfil de <?= $_SESSION['nombre']; ?>">
            </a>
        </div>
    </header>

    <main>
        <section class="panel-control">
            <div class="section-header">
                <i class="fa-regular fa-square-plus icono-header"></i>
                <h2>Crear Incidencia</h2>
            </div>

            <hr>

            <form method="POST" enctype="multipart/form-data">
                <div class="form">
                    <div class="casilla">
                        <label for="tipo">Tipo de Incidencia</label>
                        <select name="tipo_incidencia" id="tipo" class="seleccion" required>
                            <option value="" selected disabled>Selecciona una opción...</option>
                            <?php foreach ($tipos_incidencia as $ti): ?>
                                <option value="<?= $ti['id_tipo_incidencia'] ?>">
                                    [<?= strtoupper($ti['nombre']) ?>]: <?= $ti['descripcion'] ?> 
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="casilla">
                        <label for="titulo">Título de la incidencia</label>
                        <input type="text" name="titulo" placeholder="Ej: Bombilla fundida portal" required>
                    </div>

                    <div class="casilla">
                        <label for="imagen">Imagen (Opcional)</label>
                        <input type="file" name="imagen" id="imagen" accept="image/*" class="imagen">
                    </div>

                    <div class="casilla">
                        <label for="descripcion">Descripción detallada</label>
                        <textarea name="descripcion" class="area-texto" placeholder="Explica brevemente el problema..." required></textarea>
                    </div>
                </div>

                <button type="submit" class="guardar">
                    <i class="fa-solid fa-floppy-disk"></i> Publicar Incidencia
                </button>
            </form>
        </section>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="index.php">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a>
                </li>
                <li>
                    <a href="crear_incidencia.php">
                        <i class="fa-solid fa-plus"></i>
                        Nueva incidencia
                    </a>
                </li>
                <li>
                    <a href="servicios.php">
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
                <?php if ($_SESSION["rol"] !== "vecino"): ?>
                    <li>
                        <a href="admin/panel_control.php">
                            <i class="fa-solid fa-gear"></i>
                            Panel de control
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </aside>
    </main>
</body>
</html>