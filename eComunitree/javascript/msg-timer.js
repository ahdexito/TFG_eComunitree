// Eliminar cada mensaje de error tras 5 segundos aplicando animación de opacidad
document.addEventListener("DOMContentLoaded", function() {
    const mensajes = document.querySelectorAll('.msg-timer');

    mensajes.forEach((mensaje) => {

        setTimeout(() => {
            mensaje.style.opacity = '0';

            setTimeout(() => {
                mensaje.remove();
            }, 2000);
        }, 3000);
    });
});
