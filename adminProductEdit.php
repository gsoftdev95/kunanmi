<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: administrador.php');
    exit;
}

$id = $_GET['id'];

$productos = detProdForAdmin($bd, $id, 'productos');

// Validar producto existente
if (!$productos) {
    header('Location: administrador.php');
    exit;
} 
$categorias = obtenerCategorias($bd);
$subcategorias = obtenerSubcategorias($bd);
$supracategorias = obtenerSupracategorias($bd);
$imagenes = is_string($productos['imagen']) ? json_decode($productos['imagen'], true) : $productos['imagen'];
$atributos = obtenerAtributosConValores($bd);

$descripciones = isset($productos['descripcion']) && !empty($productos['descripcion']) ? implode("\n", json_decode($productos['descripcion'], true) ?? []) : '';
$modoEmpleoVarios = isset($productos['modo_empleo']) && !empty($productos['modo_empleo']) ? implode("\n", json_decode($productos['modo_empleo'], true) ?? []) : '';
$beneficiosVarios = !empty($productos['beneficios'])
    ? implode("\n", json_decode($productos['beneficios'], true) ?? [])
    : '';
//$usos = isset($productos['descripcion']) && !empty($productos['descripcion']) ? implode("\n", json_decode($productos['descripcion'], true) ?? []) : '';

$errorSubida = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Detectar si la petición superó post_max_size
    $convertirBytes = function ($valor) {
        $unidad = strtolower(substr(trim($valor), -1));
        $bytes = (float) $valor;

        switch ($unidad) {
            case 'g':
                $bytes *= 1024;
                // continúa a multiplicar por 1024
            case 'm':
                $bytes *= 1024;
                // continúa a multiplicar por 1024
            case 'k':
                $bytes *= 1024;
        }

        return (int) $bytes;
    };

    $limitePost = $convertirBytes(ini_get('post_max_size'));
    $tamanoPeticion = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);

    if (
        empty($_POST) &&
        $tamanoPeticion > 0 &&
        $limitePost > 0 &&
        $tamanoPeticion > $limitePost
    ) {
        $errorSubida = 'La petición supera el límite total permitido por el servidor. Reduce el tamaño de las imágenes e inténtalo nuevamente.';
    }

    // Detectar errores individuales de las imágenes
    if (
        !$errorSubida &&
        isset($_FILES['avatar']['error']) &&
        is_array($_FILES['avatar']['error'])
    ) {
        foreach ($_FILES['avatar']['error'] as $error) {

            if ($error === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            if ($error === UPLOAD_ERR_OK) {
                continue;
            }

            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $errorSubida = 'Una imagen supera el tamaño máximo permitido por el servidor. Reduce su peso e inténtalo nuevamente.';
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $errorSubida = 'Una imagen se subió parcialmente. Inténtalo nuevamente.';
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                    $errorSubida = 'El servidor no pudo procesar una imagen. Inténtalo nuevamente o contacta al administrador.';
                    break;

                default:
                    $errorSubida = 'Ocurrió un error al subir una imagen.';
                    break;
            }

            break;
        }
    }
}


if ($_POST && !$errorSubida) {
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
        $imagenes_nuevas = armarLaImagenProducto($_FILES);

        if (empty($imagenes_nuevas)) {
            $errorSubida = 'No se pudo guardar ninguna imagen. Verifica el archivo e inténtalo nuevamente.';
        } else {
            $avatar = json_encode($imagenes_nuevas);
        }
    } else {
        $avatar = json_encode($imagenes_actuales);
    }

    $beneficiosTexto = trim($_POST['beneficiosProducto'] ?? '');

$beneficiosArray = array_values(
    array_filter(
        array_map('trim', explode("\n", $beneficiosTexto)),
        fn($beneficio) => $beneficio !== ''
    )
);

$_POST['beneficios'] = json_encode(
    $beneficiosArray,
    JSON_UNESCAPED_UNICODE
);

    if (!$errorSubida) {
        modificarProducto($bd, 'productos', $_POST, $avatar);

        // Aquí continúa la eliminación e inserción de atributos
    }

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
                <h2>Editar Producto</h2>

                <?php if (!empty($errorSubida)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error al actualizar el producto:</strong>
                        <?= htmlspecialchars($errorSubida, ENT_QUOTES, 'UTF-8') ?>

                        <button type="button" class="btn-close"
                                data-bs-dismiss="alert"
                                aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" enctype="multipart/form-data" class="formEditProd">
                    
                    <div class="containerInput">
                        <label for="id" class=" ">ID</label>
                        <input type="text" class="form-control" name="id" value="<?= $productos['id']; ?>" readonly>
                    </div>
                    
                    <div class="containerInput">
                        <label for="nombreProducto" class=" ">Nombre del producto</label>
                        <input type="text" class="form-control" name="nombreProducto" value="<?= htmlspecialchars($productos['nombre']) ?>" required>
                    </div>
                    
                    <div class="containerInput">
                        <label for="descripcionProducto" class=" ">Descripción del producto</label>                    
                        <textarea class="form-control" name="descripcionProducto" id="" rows="5" ><?= htmlspecialchars($descripciones) ?></textarea>
                    </div>

                    <div class="containerInput">
                        <label for="precioProducto" class=" ">Precio del producto</label>
                        <input type="text" class="form-control" name="precioProducto" value="<?= htmlspecialchars($productos['precio']) ?>" required>
                    </div>

                    <div class="containerInput">
                        <label for="stockProducto" class=" ">Stock del producto</label>
                        <input type="text" class="form-control" name="stockProducto" value="<?= htmlspecialchars($productos['stock']) ?>" required>
                    </div>
                    
                    <div class="containerInput">
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
                    
                    <div class="containerInput">
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

                    <div class="form-group  ">
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

                    <!-- <div class="containerInput">
                        <label for="beneficiosProducto" class=" ">Beneficios del producto</label>
                        <input type="text" class="form-control" name="beneficiosProducto" value="<?= htmlspecialchars($productos['beneficios']) ?>" >
                    </div> -->
                    <div class="containerInput">
                        <label for="beneficiosProducto" class=" ">Beneficios del producto</label>                   
                        <textarea class="form-control" name="beneficiosProducto" id="beneficiosProducto" rows="8" ><?= htmlspecialchars($beneficiosVarios) ?></textarea>
                    </div>

                    <div class="containerInput">
                        <label for="modoEmpleo" class=" ">Modo de empleo</label>
                        <textarea class="form-control" name="modoEmpleo" id="" rows="5" ><?= htmlspecialchars($modoEmpleoVarios) ?></textarea>
                    </div>

                    <div class="containerInput">
                        <label for="ingredProducto" class=" ">Ingredientes</label>
                        <input type="text" class="form-control" name="ingredProducto" value="<?= htmlspecialchars($productos['ingredientes']) ?>" >
                    </div>

                    <div class="containerInput">
                        <label for="destacadoProducto" class=" ">Destacado</label>
                        <select name="destacadoProducto" class="form-control mb-3" required>
                            <option value="">¿Es destacado?</option>
                            <option value="1" <?= $productos['destacado'] == 1 ? 'selected' : '' ?>>Sí</option>
                            <option value="0" <?= $productos['destacado'] == 0 ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>

                    <div class="containerInput">
                        <label for="avatar" class=" ">Actualizar imágenes (puedes subir múltiples)</label>
                        <input type="file" class="form-control mb-0" name="avatar[]" multiple >
                        <small class="text-muted d-block mt-1">
                            Tamaño máximo: <strong>5 MB (5120 KB)</strong> por imagen.
                            Formatos recomendados: JPG, PNG o WebP.
                        </small>
                    </div>

                    <div class="containerInput">
                        <label class="">Imágenes actuales:</label>
                        <div class="d-flex flex-wrap">
                            <?php if (!empty($imagenes)): ?>
                                <?php foreach ($imagenes as $imagen): ?>
                                    <img src="./src/imgBD/Productos/<?= htmlspecialchars($imagen) ?>" alt="<?= htmlspecialchars($productos['nombre']) ?>" class="me-2 w-25">
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>No hay imágenes disponibles para este producto.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="containerInput">
                        <label for="estadoProducto" class=" ">Estado</label>
                        <select name="estadoProducto" class="form-control mb-2" required>
                            <option value="">Seleccione estado</option>
                            <option value="activo" <?= isset($productos['estado']) && $productos['estado'] === 'activo' ? 'selected' : '' ?>>activo</option>
                            <option value="inactivo" <?= isset($productos['estado']) && $productos['estado'] === 'inactivo' ? 'selected' : '' ?>>inactivo</option>
                        </select>
                    </div>

                    <div class="containerAtributo ">
                        <label>Atributos</label><br>
                        <div class="listAtributos">
                            <?php foreach ($atributos as $nombreAtributo => $valores): ?>
                                <div>
                                    <strong><?= htmlspecialchars($nombreAtributo) ?></strong><br>
                                    <div class="mt-1">
                                        <?php foreach ($valores as $valor): ?>
                                            <label class="itemsatributo">
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
                    <a href="administrador.php"  class="btn bSecond2">Regresar</a>
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