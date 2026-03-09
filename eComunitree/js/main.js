import { 
    iniciarTempoMsj, 
    agregarOpcionVoto, 
    toggleFiltros, 
    initAsideDropdowns, 
    initAside, 
    initUserMenu } 
from "./modules/ui.js";

// Inicializar los mensajes y el menú lateral
iniciarTempoMsj();
initAsideDropdowns();
initAside();
initUserMenu();

const btnOpcion = document.getElementById('btn-agregar-opcion');
if (btnOpcion) btnOpcion.addEventListener('click', agregarOpcionVoto);

const btnFiltros = document.getElementById('btn-filter');
if (btnFiltros) btnFiltros.addEventListener('click', toggleFiltros);