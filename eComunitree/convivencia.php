<?php
    session_start();
    include("./db/db.inc");
    include("./includes/verficar_sesion.php");
    $base = "./";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Convivencia</title>
    <link rel="stylesheet" href="css/convivencia/convivencia.css">
    <link rel="icon" href="img/logo-favicon.png" type="image/png">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- HEADER -->
    <?php include("includes/header.php"); ?>

    <!-- NAV -->
    <nav>
        <a href="./index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i></a>

        <ul class="navegacion">
            <li><i class="fa-solid fa-house"></i></li>
            <li><a href="index.php">Inicio</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
            <li>Convivencia</li>
        </ul>

        <!-- ASIDE -->
        <?php include("includes/aside.php"); ?>
    </nav>

    <!-- MAIN -->
    <main class="rules-container">
        <header class="rules-header">
            <h2><i class="fa-solid fa-clipboard-list"></i> Normas de Convivencia</h2>
            <hr class="hr-section">
        </header>

        <section class="rules-grid">
            
            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-volume-xmark"></i>
                    <h3>Descanso y Acústica</h3>
                </header>

                <div class="rule-content">
                    <ul>
                        <li><strong>Horario de máximo silencio:</strong> De 22:00 a 08:00 h en días laborables y hasta las 10:00 h en festivos.</li>
                        <li>Las celebraciones privadas deben realizarse en el interior de las viviendas, manteniendo un volumen que no trascienda a los colindantes.</li>
                        <li>Se prohíbe el uso de taladros o maquinaria ruidosa fuera del horario de 09:00 a 20:00 h (L-V).</li>
                        <li>El uso de tacones o calzado ruidoso en el interior de la vivienda debe evitarse durante las horas de descanso nocturno.</li>
                    </ul>
                </div>
            </article>

            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-shirt"></i>
                    <h3>Estética del Edificio</h3>
                </header>

                <div class="rule-content">
                    <ul>
                        <li><strong>Tendido de ropa:</strong> Debe realizarse exclusivamente en los tendederos interiores o zonas habilitadas. Prohibido colgar ropa hacia la calle.</li>
                        <li>No se permite la instalación de toldos, cerramientos o aires acondicionados sin la previa aprobación de la Junta para mantener la uniformidad.</li>
                        <li>Está prohibido sacudir alfombras o fregonas por las ventanas o balcones.</li>
                        <li>Las macetas en balcones deben contar con plato inferior para evitar el goteo a los vecinos de abajo al regar.</li>
                    </ul>
                </div>
            </article>

            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-shield-halved"></i>
                    <h3>Seguridad y Accesos</h3>
                </header>

                <div class="rule-content">
                    <ul>
                        <li><strong>Puerta del portal:</strong> Es obligatorio verificar que queda correctamente cerrada tras entrar o salir del edificio.</li>
                        <li>No se debe facilitar el acceso a personas desconocidas o que no se identifiquen correctamente como repartidores o servicios oficiales.</li>
                        <li>Las llaves de zonas comunes son personales e intransferibles; se prohíbe realizar copias para personas ajenas a la comunidad.</li>
                    </ul>
                </div>
            </article>

            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-stairs"></i>
                    <h3>Zonas Comunes y Tránsito</h3>
                </header>

                <div class="rule-content">
                    <ul>
                        <li>Los rellanos deben permanecer libres de obstáculos (bicicletas, basura, muebles) por seguridad y cumplimiento de la normativa de incendios.</li>
                        <li>El uso del ascensor por parte de menores de 12 años sin supervisión de un adulto está desaconsejado por motivos de seguridad.</li>
                        <li>En caso de manchas en el suelo por bolsas de basura o mascotas, el propietario responsable debe proceder a su limpieza inmediata.</li>
                    </ul>
                </div>
            </article>

            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-car"></i>
                    <h3>Uso del Garaje</h3>
                </header>

                <div class="rule-content">
                    <ul>
                        <li>Las plazas de garaje son para el estacionamiento de vehículos, no para el almacenamiento de trastos, neumáticos o productos inflamables.</li>
                        <li>Se prohíbe realizar reparaciones mecánicas o cambios de aceite en el interior del garaje.</li>
                        <li>La velocidad máxima de circulación en el interior es de 10 km/h.</li>
                        <li>Es obligatorio esperar a que la puerta automática se cierre por completo antes de abandonar el acceso.</li>
                    </ul>
                </div>
            </article>

            <article class="rule-card">
                <header>
                    <i class="fa-solid fa-person-swimming"></i>
                    <h3>Piscina y Jardines</h3>
                </header>
                
                <div class="rule-content">
                    <ul>
                        <li>Es obligatorio el uso de la ducha antes de entrar en el agua.</li>
                        <li>Prohibido el uso de envases de vidrio o comida en la zona de césped y borde de piscina.</li>
                        <li>El aforo máximo indicado en los carteles de la instalación debe respetarse estrictamente por motivos de salud pública.</li>
                        <li>No se permite reservar hamacas o espacio mediante la colocación de toallas si el usuario no está presente.</li>
                    </ul>
                </div>
            </article>

        </section>
    </main>

    <footer>
        <script type="module" src="./js/main.js"></script>
    </footer>
</body>
</html>