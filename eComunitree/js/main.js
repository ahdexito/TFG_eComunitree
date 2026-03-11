import { 
    iniciarTempoMsj, 
    agregarOpcionVoto, 
    initFiltros, 
    initAsideDropdowns, 
    initAside, 
    initUserMenu } 
from "./modules/ui.js";

// Inicializar los mensajes y el menú lateral
document.addEventListener('DOMContentLoaded', () => {
    initFiltros();
    iniciarTempoMsj();
    initAsideDropdowns();
    initAside();
    initUserMenu();
});


const btnOpcion = document.getElementById('btn-agregar-opcion');
if (btnOpcion) btnOpcion.addEventListener('click', agregarOpcionVoto);

const btnFiltros = document.getElementById('btn-filter');
if (btnFiltros) btnFiltros.addEventListener('click', toggleFiltros);