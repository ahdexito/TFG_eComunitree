<?php
    session_start();

    include("./db/db.inc");

    $base = "./";

    // Verificación de sesión. Si no hay usuario, fuera
    if (!isset($_SESSION["id_usuario"])) {
        header("location:./login.php");
        die();
    }

    if (!isset($_GET['edit'])) {
        header("location:gestion_publicaciones.php");
        die();
    }

    $id_publicacion = intval($_GET['edit']);
    $id_sesion = $_SESSION["id_usuario"];

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

    // A partir de aquí solo llega el autor original

    // Obtener el resto de datos para el formulario
    $sql = "SELECT p.*, i.estado, i.id_tipo_incidencia, v.fecha_cierre 
            FROM publicaciones p 
            LEFT JOIN incidencias i ON p.id_publicacion = i.id_publicacion
            LEFT JOIN votaciones v ON p.id_publicacion = v.id_publicacion
            WHERE p.id_publicacion = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_publicacion);
    $stmt->execute();
    $publicacion = $stmt->get_result()->fetch_assoc();

    // LÓGICA DE PROCESAMIENTO POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['titulo'])) {
        $titulo = $_POST["titulo"];
        $contenido = $_POST["contenido"];
        $tipo = $_POST["tipo"];

        $conn->begin_transaction();
        try {
            $sql_upd = "UPDATE publicaciones SET titulo = ?, contenido = ? WHERE id_publicacion = ?";
            $st = $conn->prepare($sql_upd);
            $st->bind_param("ssi", $titulo, $contenido, $id_publicacion);
            $st->execute();

            // Lógica por tipo
            if ($tipo === 'incidencia') {
                $sql_inc = "UPDATE incidencias SET id_tipo_incidencia = ? WHERE id_publicacion = ?";
                $st_inc = $conn->prepare($sql_inc);
                $st_inc->bind_param("ii", $_POST['id_tipo_incidencia'], $id_publicacion);
                $st_inc->execute();
            }

            $conn->commit();
            header("location:gestion_publicaciones.php?upt=0");
        } catch (Exception $e) {
            $conn->rollback();
            header("location:gestion_publicaciones.php?upt=1");
        }
        die();
    }

    $tipos_incidencia = $conn->query("SELECT * FROM tipos_incidencia WHERE activo = 1")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Publicación</title>
    <link rel="stylesheet" href="./css/insert_edit/insert_edit.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <?php include("./includes/header.php"); ?>
    
    <!-- NAV -->
    <nav>
        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="./index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Editar Publicación</li>
        </ul>

        <!-- ASIDE -->
        <?php include("./includes/aside.php"); ?>
    </nav>
    
    <!-- MAIN -->
    <main>
        <section class="panel-control">
            <div class="section-header">
                <h2><i class="fa-regular fa-pen-to-square"></i> Editar mi <?= ucfirst($publicacion['tipo']) ?></h2>
                <hr>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="tipo" value="<?= $publicacion['tipo'] ?>">
                <div class="form">
                    <div class="casilla">
                        <label>Título</label>
                        <input type="text" name="titulo" value="<?= htmlspecialchars($publicacion['titulo']) ?>" required>
                    </div>
                    <div class="casilla">
                        <label>Contenido</label>
                        <textarea name="contenido" class="area-texto" required><?= htmlspecialchars($publicacion['contenido']) ?></textarea>
                    </div>
                    
                    <?php if($publicacion['tipo'] == 'incidencia'): ?>
                    <div class="casilla">
                        <label>Categoría</label>
                        <select name="id_tipo_incidencia">
                            <?php foreach($tipos_incidencia as $t): ?>
                                <option value="<?= $t['id_tipo_incidencia'] ?>" <?= $t['id_tipo_incidencia'] == $publicacion['id_tipo_incidencia'] ? 'selected' : '' ?>>
                                    <?= $t['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="guardar">Actualizar</button>
            </form>
        </section>
    </main>
</body>
</html>