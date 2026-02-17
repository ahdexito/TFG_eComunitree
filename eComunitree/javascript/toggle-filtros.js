function toggleFiltros(e) {
    e.preventDefault();
    const menu = document.getElementById('filter-menu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}