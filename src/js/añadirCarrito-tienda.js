document.addEventListener('DOMContentLoaded', () => {
    // Capturamos todos los formularios de productos
    const formularios = document.querySelectorAll('.form-agregar-carrito');
    const mensaje = document.getElementById('mensaje-agregado'); // Asegúrate de tener este div en tu HTML

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
                if (mensaje) {
                    mensaje.style.display = 'block';
                    setTimeout(() => mensaje.style.display = 'none', 2500);
                }

                // Actualizar contador del carrito en el navbar
                fetch('contadorCarrito.php')
                    .then(res => res.text())
                    .then(total => {
                        const contador = document.querySelector('.cart-count');
                        if (contador) {
                            if (parseInt(total) > 0) {
                                contador.textContent = total;
                                contador.classList.remove('d-none');
                            } else {
                                contador.classList.add('d-none');
                            }
                        }
                    });
            })
            .catch(() => alert('Error al agregar al carrito'));
        });
    });
});
