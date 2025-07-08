document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formFiltros');
    const contenedor = document.querySelector('.containerCards');
    const ordenarSelect = document.getElementById('ordenar');

    const aplicarFiltros = () => {
        const formData = new FormData(form);

        // Agregar categoría y subcategoría al formData si están presentes
        const categoria = form.dataset.categoria;
        const subcategoria = form.dataset.subcategoria;
        if (categoria) formData.append('categoria', categoria);
        if (subcategoria) formData.append('subcategoria', subcategoria);

        // Agregar el valor del ordenamiento
        if (ordenarSelect && ordenarSelect.value) {
            formData.append('ordenar', ordenarSelect.value);
        }

        // Hacer la petición AJAX
        fetch('filtrarProductos.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            contenedor.innerHTML = html;
        })
        .catch(error => {
            console.error('Error al filtrar productos:', error);
        });
    };

    // Ejecutar filtros al cambiar cualquier checkbox
    form.addEventListener('change', aplicarFiltros);

    // Ejecutar filtros al cambiar el select de orden
    if (ordenarSelect) {
        ordenarSelect.addEventListener('change', aplicarFiltros);
    }
});
