<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$categorias = obtenerCategorias($bd) ?? [];
$subcategorias = obtenerSubcategorias($bd);
$supracategorias = obtenerSupracategorias($bd) ?? [];
$atributos = obtenerAtributosConValores($bd);

$errores = [];
if ($_POST) {
    $nombreProducto = $_POST['nombreProducto'];
    $descripcionProducto = $_POST['descripcionProducto'];
    $precioProducto = $_POST['precioProducto'];
    $stockProducto = $_POST['stockProducto'];
    $categoriaProducto  = $_POST['categoriaProducto'];
    $subcategoriaProducto = $_POST['subcategoriaProducto'];
    $supracategoriaProducto = $_POST['supracategoriaProducto'];
    $destacado = $_POST['destacado'];
    $beneficiosProducto = $_POST['beneficiosProducto'];
    $modoEmpleoProducto = $_POST['modoEmpleoProducto'];
    $ingredientesProducto = $_POST['ingredientesProducto'];
    $estadoProducto = $_POST['estadoProducto'];
    $valoresAtributos = [];
    if (isset($_POST['atributos'])) {
        if (is_array(reset($_POST['atributos']))) {
            // Es un array de arrays → aplanar con array_merge
            $valoresAtributos = array_merge(...array_values($_POST['atributos']));
        } else {
            // Es un array plano → usar directamente
            $valoresAtributos = $_POST['atributos'];
        }
    }


    $errores = array_merge($errores, validarProducto($_POST, $_FILES));

    if (count($errores) === 0) {
        $avatar = armarLaImagenProducto($_FILES);
        require_once('src/partials/conexionBD.php');
        $idProducto = guardarProducto($bd, 'productos', $_POST, $avatar);

        foreach ($valoresAtributos as $idValor) {
            $stmt = $bd->prepare("INSERT INTO producto_atributo (producto_id, valor_atributo_id) VALUES (:prod, :valor)");
            $stmt->bindValue(':prod', $idProducto, PDO::PARAM_INT);
            $stmt->bindValue(':valor', $idValor, PDO::PARAM_INT);
            $stmt->execute();
        }

        header('Location: adminProductView.php?id=' . $idProducto);
        exit;
    }
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
            <h2>Registrar producto</h2>

            <section id="registrarProductos" class=" containerAdd">
                <section class="containerAddInner">
                    <?php if (count($errores) > 0) : ?>
                    <ul class="alert alert-danger">
                        <?php foreach ($errores as $error) : ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    <form action="" method="POST"  enctype="multipart/form-data">                
                        <div class="form-group">
                            <label for="nombreProducto">Nombre del producto</label>
                            <input type="text" class="form-control" name="nombreProducto" placeholder="Nombre del producto" value="<?= isset($nombreProducto) ? $nombreProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="descripcionProducto">Descripción</label>
                            <input type="text" class="form-control" name="descripcionProducto" placeholder="Descripcion" value="<?= isset($descripcionProducto) ? $descripcionProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="precioProducto">Precio</label>
                            <input type="text" class="form-control" name="precioProducto" placeholder="precio... Ejm: 24.90" value="<?= isset($precioProducto) ? $precioProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="stockProducto">Stock</label>
                            <input type="text" class="form-control" name="stockProducto" placeholder="stock... Ejm: 250" value="<?= isset($stockProducto) ? $stockProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="categoriaProducto">Categoría</label>
                            <select id="categoriaProducto" name="categoriaProducto" class="form-control" required>
                                <option value="">Seleccione una categoría</option>
                                <?php foreach ($categorias as $id => $cat): ?>           
                                <option value="<?= $id ?>"><?= $cat['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="subcategoriaProducto">Subcategoría</label>
                            <select id="subcategoriaProducto" name="subcategoriaProducto" class="form-control" required>
                                <option value="">Seleccione una subcategoría</option>
                                <?php foreach ($subcategorias as $sub): ?>
                                    <option value="<?= $sub['id'] ?>" data-categoria="<?= $sub['categoria_id'] ?>"><?= $sub['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="supracategoriaProducto">Supracategoría</label>
                            <select id="supracategoriaProducto" name="supracategoriaProducto" class="form-control">
                                <option value="">Seleccione una supracategoría</option>
                                <?php foreach ($supracategorias as $supra): ?>
                                <option value="<?= $supra['id'] ?>" data-subcategoria="<?= $supra['subcategoria_id'] ?>"><?= $supra['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="formFile">imagen del producto</label>
                            <input type="file" class="form-control" id="formFile" name="avatar[]" multiple>
                        </div>
                        <div class="form-group">
                            <label for="beneficiosProducto">Beneficios</label>
                            <input type="text" class="form-control" name="beneficiosProducto" placeholder="Beneficios" value="<?= isset($beneficiosProducto) ? $beneficiosProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="modoEmpleoProducto">Modo de empleo</label>
                            <input type="text" class="form-control" name="modoEmpleoProducto" placeholder="Modo de empleo" value="<?= isset($modoEmpleoProducto) ? $modoEmpleoProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="ingredientesProducto">Ingredientes</label>
                            <input type="text" class="form-control" name="ingredientesProducto" placeholder="Ingredientes" value="<?= isset($ingredientesProducto) ? $ingredientesProducto:''; ?>">
                        </div>
                        <div class="form-group">
                            <label>¿Es destacado?</label>
                            <select class="form-control" name="destacado">
                                <option value="0" <?= (isset($destacado) && $destacado == "0") ? 'selected' : '' ?>>No</option>
                                <option value="1" <?= (isset($destacado) && $destacado == "1") ? 'selected' : '' ?>>Sí</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="estadoProducto">Estado del producto</label>
                            <select class="form-control" name="estadoProducto" id="estadoProducto" required>
                                <option value="">Seleccione el estado</option>
                                <option value="activo" <?= (isset($estadoProducto) && $estadoProducto == "activo") ? 'selected' : '' ?>>activo</option>
                                <option value="inactivo" <?= (isset($estadoProducto) && $estadoProducto == "inactivo") ? 'selected' : '' ?>>inactivo</option>
                            </select>
                        </div>

                        <div class="form-group containerAtributo">
                            <label>Atributos</label><br>
                            <div class="listAtributos">
                                <?php foreach ($atributos as $nombreAtributo => $valores): ?>
                                    <div>
                                        <strong><?= htmlspecialchars($nombreAtributo) ?></strong><br>
                                        <?php foreach ($valores as $valor): ?>
                                            <label>
                                                <input type="checkbox" name="atributos[]" value="<?= $valor['id'] ?>">
                                                <?= htmlspecialchars($valor['valor']) ?>
                                            </label><br>
                                        <?php endforeach; ?>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>
                            </div>                            
                        </div>                        

                        <button type="submit" class="btn btn-primary">Guardar producto</button>

                    </form>
                </section>                    
            </section>
        </section>
    </section>
</main>


<footer>
    <?php include_once('./src/partials/footer.php')?>
</footer>





<!--Boostrap-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

<!--select dinamico categoria, subcategoria y supracategoria-->
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