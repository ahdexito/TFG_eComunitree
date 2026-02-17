function agregarOpcion() {
    const contenedor = document.getElementById('contenedor-opciones');
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'opciones[]';
    input.placeholder = 'Nueva opción';
    input.style.marginBottom = '10px';
    input.style.display = 'block';
    input.style.width = '100%';
    contenedor.appendChild(input);
}