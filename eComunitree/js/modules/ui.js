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
    
    const menu = document.getElementById('filter-menu');
    const btn = e.currentTarget;

    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
        btn.classList.add('active');
    } else {
        menu.style.display = 'none';
        btn.classList.remove('active');
    }
}

export function iniciarTempoMsj() {
    const mensajes = document.querySelectorAll('.msg-timer');
    mensajes.forEach((mensaje) => {
        setTimeout(() => {
            mensaje.style.opacity = '0';
            setTimeout(() => mensaje.remove(), 2000);
        }, 3000);
    });
}

export function initAsideDropdowns() {
    const dropdowns = document.querySelectorAll('.has-dropdown');

    dropdowns.forEach(dropdown => {
        const trigger = dropdown.querySelector('p');

        if (trigger) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Si ya está abierto, se cierra. Si no, se abre
                const wasActive = dropdown.classList.contains('is-active');
                
                // Cerrar todos los dropdowns primero
                dropdowns.forEach(d => d.classList.remove('is-active'));

                // Si no estaba activo, se activa
                if (!wasActive) {
                    dropdown.classList.add('is-active');
                }
            });
        }
    });
}