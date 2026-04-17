<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$errores = [];
$id = $_GET['id'];

$productos = detProdForAdmin($bd, $id, 'productos');    
$categorias = obtenerCategorias($bd);
$subcategorias = obtenerSubcategorias($bd);
$supracategorias = obtenerSupracategorias($bd);
$imagenes = is_string($productos['imagen']) ? json_decode($productos['imagen'], true) : $productos['imagen'];
$atributos = obtenerAtributosConValores($bd);

$descripciones = isset($productos['descripcion']) && !empty($productos['descripcion']) ? implode("\n", json_decode($productos['descripcion'], true) ?? []) : '';
$modoEmpleoVarios = isset($productos['modo_empleo']) && !empty($productos['modo_empleo']) ? implode("\n", json_decode($productos['modo_empleo'], true) ?? []) : '';
//$usos = isset($productos['descripcion']) && !empty($productos['descripcion']) ? implode("\n", json_decode($productos['descripcion'], true) ?? []) : '';


if ($_POST) {
    $valoresAtributos = [];
    if (isset($_POST['atributos'])) {
        if (is_array(reset($_POST['atributos']))) {
            $valoresAtributos = array_merge(...array_values($_POST['atributos']));
        } else {
            $valoresAtributos = $_POST['atributos'];
        }
    }

    // ✅ Imágenes actuales desde la BD
    $imagenes_actuales = is_string($productos['imagen']) 
        ? json_decode($productos['imagen'], true) 
        : $productos['imagen'];

    // Si se suben nuevas imágenes, las procesamos; si no, mantenemos las actuales
    if (isset($_FILES['avatar']) && !empty($_FILES['avatar']['name'][0])) {        
        $imagenes_nuevas = armarLaImagenProducto($_FILES); // Devuelve array de nombres de archivo
        $avatar = json_encode($imagenes_nuevas);
    } else {
        $avatar = json_encode($imagenes_actuales);
    }

    // Actualizamos el producto incluyendo el JSON de imágenes
    modificarProducto($bd, 'productos', $_POST, $avatar);

    // Eliminar atributos anteriores
    $stmt = $bd->prepare("DELETE FROM producto_atributo WHERE producto_id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Insertar nuevos atributos
    foreach ($valoresAtributos as $idValor) {
        $stmt = $bd->prepare("INSERT INTO producto_atributo (producto_id, valor_atributo_id) VALUES (:prod, :valor)");
        $stmt->bindValue(':prod', $id, PDO::PARAM_INT);
        $stmt->bindValue(':valor', $idValor, PDO::PARAM_INT);
        $stmt->execute();
    }

    // Redirigir
    header('Location: adminProductView.php?id=' . $_POST['id']);
    exit;
}
?>


<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
    
    <header>
        <?php include_once('./src/partials/navbar.php')?>
    </header>


    <main class="mainAdmin">
        <!-- Sidebar -->
        <?php include_once('./src/partials/asideAdmin.php')?>
        

        <section class="bodyManupulation">
            <section class="bodyManupulationInner">
                <h2>Modo edición</h2>

                <form action="" method="POST" enctype="multipart/form-data" class="formEditProd">
                    
                    <label for="id" class="mt-2">ID</label>
                    <input type="text" class="form-control" name="id" value="<?= $productos['id']; ?>" readonly>

                    <label for="nombreProducto" class="mt-2">Nombre del producto</label>
                    <input type="text" class="form-control" name="nombreProducto" value="<?= htmlspecialchars($productos['nombre']) ?>" required>

                    <label for="descripcionProducto" class="mt-2">Descripción del producto</label>                    
                    <textarea class="form-control" name="descripcionProducto" id="" rows="5" ><?= htmlspecialchars($descripciones) ?></textarea>

                    <label for="precioProducto" class="mt-2">Precio del producto</label>
                    <input type="text" class="form-control" name="precioProducto" value="<?= htmlspecialchars($productos['precio']) ?>" required>

                    <label for="stockProducto" class="mt-2">Stock del producto</label>
                    <input type="text" class="form-control" name="stockProducto" value="<?= htmlspecialchars($productos['stock']) ?>" required>

                    <div class="form-group mt-2">
                        <label for="categoriaProducto">Categoría</label>
                        <select id="categoriaProducto" name="categoriaProducto" class="form-control" required>
                            <option value="">Seleccione una categoría</option>  
                            <?php foreach ($categorias as $id =>  $categoria): ?>
                                <option value="<?= $id ?>" <?= $id == $productos['categoria_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="subcategoriaProducto">Subcategoría</label>
                        <select id="subcategoriaProducto" name="subcategoriaProducto" class="form-control" required>
                            <option value="">Seleccione una subcategoría</option>
                            <?php foreach ($subcategorias as $sub): ?>
                                <option value="<?= $sub['id'] ?>" data-categoria="<?= $sub['categoria_id'] ?>"
                                    <?= $sub['id'] == $productos['subcategoria_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mt-2">
                        <label for="supracategoriaProducto">Supracategoría</label>
                        <select id="supracategoriaProducto" name="supracategoriaProducto" class="form-control">
                            <option value="">Seleccione una supracategoría</option>
                            <?php foreach ($supracategorias as $supra): ?>
                                <option value="<?= $supra['id'] ?>" data-supracategoria="<?= $supra['subcategoria_id'] ?>" <?= $supra['id'] == $productos['supracategoria_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($supra['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <label for="beneficiosProducto" class="mt-2">Beneficios del producto</label>
                    <input type="text" class="form-control" name="beneficiosProducto" value="<?= htmlspecialchars($productos['beneficios']) ?>" >

                    <label for="modoEmpleo" class="mt-2">Modo de empleo</label>
                    <textarea class="form-control" name="modoEmpleo" id="" rows="5" ><?= htmlspecialchars($modoEmpleoVarios) ?></textarea>

                    <label for="ingredProducto" class="mt-2">Ingredientes</label>
                    <input type="text" class="form-control" name="ingredProducto" value="<?= htmlspecialchars($productos['ingredientes']) ?>" >

                    <label for="destacadoProducto" class="mt-2">Destacado</label>
                    <select name="destacadoProducto" class="form-control" required>
                        <option value="">¿Es destacado?</option>
                        <option value="1" <?= $productos['destacado'] == 1 ? 'selected' : '' ?>>Sí</option>
                        <option value="0" <?= $productos['destacado'] == 0 ? 'selected' : '' ?>>No</option>
                    </select>

                    <label for="avatar" class="mt-2">Actualizar imágenes (puedes subir múltiples)</label>
                    <input type="file" class="form-control" name="avatar[]" multiple >

                    <label for="estadoProducto" class="mt-2">Estado</label>
                    <select name="estadoProducto" class="form-control mb-2" required>
                        <option value="">Seleccione estado</option>
                        <option value="activo" <?= isset($productos['estado']) && $productos['estado'] === 'activo' ? 'selected' : '' ?>>activo</option>
                        <option value="inactivo" <?= isset($productos['estado']) && $productos['estado'] === 'inactivo' ? 'selected' : '' ?>>inactivo</option>

                    </select>

                    <h5 class="mt-2">Imágenes actuales:</h5>
                    <div class="d-flex flex-wrap">
                        <?php if (!empty($imagenes)): ?>
                            <?php foreach ($imagenes as $imagen): ?>
                                <img src="./src/imgBD/Productos/<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($productos['nombre']) ?>" class="me-2 w-25">
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No hay imágenes disponibles para este producto.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group containerAtributo mt-2">
                        <label>Atributos</label><br>
                        <div class="listAtributos">
                            <?php foreach ($atributos as $nombreAtributo => $valores): ?>
                                <div>
                                    <strong><?= htmlspecialchars($nombreAtributo) ?></strong><br>
                                    <div class="mt-1">
                                        <?php foreach ($valores as $valor): ?>
                                            <label>
                                                <input type="checkbox" name="atributos[]" value="<?= $valor['id'] ?>" <?= in_array($valor['id'], array_column($productos['atributosPlano'], 'id')) ? 'checked' : '' ?>>
                                                <?= htmlspecialchars($valor['valor']) ?>
                                            </label><br>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </form>
                

            </section>
        </section>
    </main>

  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectCategoria = document.getElementById('categoriaProducto');
            const selectSubcategoria = document.getElementById('subcategoriaProducto');

            const opcionesSub = Array.from(selectSubcategoria.options);

            selectCategoria.addEventListener('change', () => {
                const categoriaId = selectCategoria.value;

                selectSubcategoria.innerHTML = '<option value="">Seleccione una subcategoría</option>';
                opcionesSub.forEach(option => {
                    if (option.dataset.categoria === categoriaId) {
                        selectSubcategoria.appendChild(option);
                    }
                });
            });
        });

        
        const selectSubcategoria = document.getElementById('subcategoriaProducto');
        const selectSupracategoria = document.getElementById('supracategoriaProducto');
        const opcionesSupra = Array.from(selectSupracategoria.options);

        selectSubcategoria.addEventListener('change', () => {
            const subcategoriaId = selectSubcategoria.value;

            selectSupracategoria.innerHTML = '<option value="">Seleccione una supracategoría</option>';
            opcionesSupra.forEach(option => {
                if (option.dataset.subcategoria === subcategoriaId) {
                selectSupracategoria.appendChild(option);
                }
            });
        });
    </script>

</body>
</html>