function toggleFiltros(e) {
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