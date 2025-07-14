
document.addEventListener('DOMContentLoaded', () => {
    // Capturamos todos los formularios de productos
    const formularios = document.querySelectorAll('.form-agregar-carrito');

    formularios.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('agregarAlCarrito.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.ok ? res.text() : Promise.reject())
            .then(() => {
                // Crear un mensaje de éxito temporal
                const btn = this.querySelector('button');
                const mensaje = document.createElement('div');
                mensaje.textContent = "¡Añadido al carrito!";
                mensaje.style.color = "green";
                mensaje.style.marginTop = "5px";
                btn.after(mensaje);
                setTimeout(() => mensaje.remove(), 2000);
            })
            .catch(() => alert('Error al agregar al carrito'));
        });
    });
});
