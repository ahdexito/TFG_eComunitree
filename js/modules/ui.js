export function agregarOpcionVoto() {
    const contenedor = document.getElementById('contenedor-opciones');
    if (!contenedor) return;

    const div = document.createElement('div');
    div.className = 'opcion-item';
    div.style.display = 'flex';
    div.style.gap = '10px';

    div.innerHTML = `
        <input type="text" name="opciones[]" placeholder="Nueva opción" class="input-voto">
        <button type="button" class="btn-borrar">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;

    div.querySelector('.btn-borrar').onclick = () => div.remove();
    contenedor.appendChild(div);
}

export function initFiltros() {
    const btn = document.getElementById('btn-filter-toggle');
    const menu = document.getElementById('filter-menu');

    if (btn && menu) {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const isOpen = menu.style.display === 'block';
            
            if (!isOpen) {
                menu.style.display = 'block';
                btn.classList.add('active');
            } else {
                menu.style.display = 'none';
                btn.classList.remove('active');
            }
        });

        document.addEventListener('click', (e) => {
            if (menu.style.display === 'block') {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.style.display = 'none';
                    btn.classList.remove('active');
                }
            }
        });
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

export function initAside() {
    const overlay = document.getElementById('aside-overlay');
    const checkbox = document.getElementById('menu-toggle');

    if (overlay && checkbox) {
        overlay.addEventListener('click', () => {
            checkbox.checked = false;
        });
    }
}

export function initUserMenu() {
    const userContainer = document.getElementById('user-menu-parent');
    const trigger = document.getElementById('user-menu-trigger');

    if (trigger && userContainer) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            userContainer.classList.toggle('is-active');
        });

        // Cerrar al hacer click fuera
        document.addEventListener('click', (e) => {
            if (!userContainer.contains(e.target)) {
                userContainer.classList.remove('is-active');
            }
        });
    }
}