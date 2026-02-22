import { iniciarTempoMsj, agregarOpcionVoto, toggleFiltros } from "./modules/ui.js";

iniciarTempoMsj();

const btnOpcion = document.getElementById('btn-agregar-opcion');
if (btnOpcion) btnOpcion.addEventListener('click', agregarOpcionVoto);

const btnFiltros = document.getElementById('btn-filter');
if (btnFiltros) btnFiltros.addEventListener('click', toggleFiltros);