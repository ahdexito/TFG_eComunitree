export function agregarOpcionVoto() {
    const contenedor = document.getElementById('contenedor-opciones');
    if (!contenedor) return;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'opciones[]';
    input.placeholder = 'Nueva opción';
    input.classList.add('input-voto');
    contenedor.appendChild(input);
}


export function toggleFiltros(e) {
    e.preventDefault();
    
    // Obtenemos el menú
    const menu = document.getElementById('filter-menu');
    
    // Obtenemos el botón (el elemento que tiene el onclick)
    const btn = e.currentTarget;

    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        btn.classList.add('active'); // Activa la apariencia de hover
    } else {
        menu.style.display = 'none';
        btn.classList.remove('active'); // Desactiva la apariencia de hover
    }
}


// Eliminar cada mensaje de error tras 5 segundos aplicando animación de opacidad
export function iniciarTempoMsj() {
    const mensajes = document.querySelectorAll('.msg-timer');
    mensajes.forEach((mensaje) => {
        setTimeout(() => {
            mensaje.style.opacity = '0';
            setTimeout(() => mensaje.remove(), 2000);
        }, 3000);
    });
}