<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eComunitree | Index</title>
    <link rel="stylesheet" href="css/index/index.css">
    <script src="https://kit.fontawesome.com/bc8e4b1cda.js" crossorigin="anonymous"></script>
</head>
<body>
    <header class="body-header">
        <a href="index.html" class="btn-index">
            <img src="img/logo-transparencia.png" alt="logotipo">
            <h1>eComunitree</h1>
        </a>
        <div class="user">
            <div class="user-name">
                <p>Bienvenido,</p>
                <p>Ángel</p>
            </div>
            <a href="login.html">
                <!-- <i class="fa-regular fa-circle-user usuario"></i> -->
                <img src="img_user/oscar.jpg" alt="">
            </a>
        </div>
    </header>

    <main>
        <section class="feed">
            <header class="section-header">
                <h2>Todas las publicaciones</h2>
                <div class="section-btns">
                    <a href="#" class="btn-order">
                        <i class="fa-solid fa-sort"></i>
                        <p>ORDENAR</p>
                    </a>
                    <a href="#" class="btn-filter">
                        <i class="fa-solid fa-filter"></i>
                        <p>FILTRAR</p>
                    </a>
                </div>
            </header>
            <hr>
            <article class="vote-card">
                <header class="article-header">
                    <div class="article-header-user">
                        <img src="img_user/enrique.jpg" alt="">
                        <div class="user-info">
                            <strong>Enrique</strong>
                            <p>Vicepresidente</p>
                        </div>
                    </div>
                    <div class="article-header-date">
                        <strong>7:27h<i class="fa-regular fa-clock"></i></strong>
                        <p>31/01/26<i class="fa-solid fa-calendar-days"></i></p>
                    </div>
                </header>

                <section class="article-body">
                    <header class="article-body-title">
                        <h3>
                            <i class="fa-solid fa-envelope"></i>
                            VOTACIÓN
                        </h3>
                        <h4>Nuevo color para las paredes del rellano.</h4>
                    </header>

                    <div class="article-body-content">
                        <p>
                            <i class="fa-regular fa-message"></i>
                            La pintura está empezando a desconcharse por la humedad. 
                            Los pintores vendrán la semana que viene.
                        </p>
                    </div>
                </section>

                <footer class="article-footer">
                    <strong>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        TODAVÍA NO HAS VOTADO
                    </strong>
                    <p>
                        <i class="fa-solid fa-hourglass-half"></i>
                        Expira en: <time>3 días</time>
                    </p>
                    <hr>
                    <div class="vote-btns">
                        <button>Blanco</button>
                        <button>Beige</button>
                        <button>Azul cielo</button>
                    </div>
                    <hr>
                    <button class="vote-confirm">CONFIRMAR</button>
                </footer>
            </article>

            <article class="advert-card">
                <header class="article-header">
                    <div class="article-header-user">
                        <img src="img_user/antonio.jpg" alt="">
                        <div class="user-info">
                            <strong>Antonio</strong>
                            <p>Presidente</p>
                        </div>
                    </div>
                    <div class="article-header-date">
                        <strong>20:15h<i class="fa-regular fa-clock"></i></strong>
                        <p>29/01/26<i class="fa-solid fa-calendar-days"></i></p>
                    </div>
                </header>    
                
                <section class="article-body">
                    <header class="article-body-title">
                        <h3>
                            <i class="fa-solid fa-bullhorn"></i>
                            ANUNCIO
                        </h3>
                        <h4>Corte de luz.</h4>
                    </header>

                    <div class="article-body-content">
                        <p>
                            <i class="fa-regular fa-message"></i>
                            El próximo LUNES 2 DE FEBRERO a las 9:45h se cortará el suministro 
                            de luz por labores de mantenimiento. El corte durará 
                            hasta las 13:00h aproximadamente.
                        </p>
                    </div>
                </section>
            </article>

            
            <article class="incidence-card">
                <header class="article-header">
                    <div class="article-header-user">
                        <img src="img_user/fina.jpg" alt="">
                        <div class="user-info">
                            <strong>Fina</strong>
                            <p>Vecina</p>
                        </div>
                    </div>
                    <aside class="article-header-date">
                        <strong>11:27h<i class="fa-regular fa-clock"></i></strong>
                        <p>27/01/26<i class="fa-solid fa-calendar-days"></i></p>
                    </aside>
                </header>

                <section class="article-body">
                    <header class="article-body-title">
                        <h3>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                            INCIDENCIA
                        </h3>
                        <h4>Desperfecto estético.</h4>
                        <p>Pintura de las paredes desgastada.</p>
                    </header>

                    <div class="article-body-content">
                        <img src="img_incidencias/pintura-desconchada.jpg" alt="">
                        <p>
                            <i class="fa-regular fa-message"></i>
                            Hola a todos. Me gustaría llamar la atención 
                            sobre el pésimo estado de las paredes del rellano; 
                            la pintura está totalmente desgastada y da una imagen de 
                            abandono lamentable. Solicito que se incluya su 
                            repintado urgente en la próxima junta para mantener la 
                            finca en condiciones dignas.
                        </p>
                    </div>
                </section>
            </article>
            <a href="index.html" class="btn-up">
                <i class="fa-solid fa-angles-up"></i>
            </a>
        </section>

        <input type="checkbox" id="menu-toggle" class="menu-checkbox">
        <label for="menu-toggle" class="menu-button"><i class="fa-solid fa-bars"></i></label>

        <aside class="main-aside">
            <h3>Navegación</h3>
            <hr>
            <ul>
                <li>
                    <a href="index.html">
                        <i class="fa-solid fa-house"></i>
                        Inicio
                    </a></li>
                <li>
                    <a href="#">
                    <i class="fa-solid fa-plus"></i>
                    Nueva incidencia
                    </a></li>
                <li>
                    <a href="servicios.html">
                    <i class="fa-solid fa-phone-volume"></i>
                    Servicios
                </a></li>
                <li>
                    <a href="#">
                    <i class="fa-solid fa-calendar-days"></i>
                    Calendario
                </a></li>
                <li><a href="#">
                    <i class="fa-regular fa-file-lines"></i>
                    Documentación
                </a></li>
                <li><a href="panel_control.html">
                    <i class="fa-solid fa-gear"></i>
                    Panel de control
                </a></li>
            </ul>
        </aside>
    </main>
</body>
</html>