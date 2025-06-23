<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Captura la categoría de la URL
$categoriaId = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$categoriaNombre = isset($_GET['categoria_nombre']) ? $_GET['categoria_nombre'] : 'General';



if (!empty($categoriaId)) {
    $productos = obtenerProductosPorCategoria($bd, $categoriaId);
} else {
    $productos = obtenerProdTienda($bd, "productos");
}

?>

<!doctype html>
<html lang="es">
  <head>
    <?php include_once('./src/partials/head.php')?>
  </head>
  <body>    
    
    <header>    
        <section class="contTopBar">
            <div class="topBar">Hecho con amor — Productos artesanales y naturales</div>
        </section>
        <?php include_once('./src/partials/navbar.php')?>
    </header>
    

    <section class="navegacion">
        <section class="navegacionInner">
            <a href="./index.php">Inicio</a>/Tienda
        </section>
    </section>

    <section class="ContainerMainProduct">
        <section class="container-fluid ContainerMainProductInner">
            <div class="row">
                <!-- Sidebar izquierdo -->
                <aside class="productFilters col-12 col-md-3 col-lg-2 p-3" style="min-height: 100vh; background-color: #f8f9fa;">
                    <h5 class="mb-3">Filtros</h5>
                    <form>
                        <!-- PRECIO -->
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio máximo: <span id="precio-valor">S/. 100</span></label>
                            <input type="range" class="form-range" id="precio" name="precio" min="10" max="300" step="10" value="100" oninput="document.getElementById('precio-valor').textContent = 'S/. ' + this.value">
                        </div>

                        <!-- AROMA -->
                        <div class="mb-3">
                            <label class="form-label">Aroma</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aroma[]" value="lavanda" id="lavanda">
                                <label class="form-check-label" for="lavanda">Lavanda</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aroma[]" value="coco" id="coco">
                                <label class="form-check-label" for="coco">Coco</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aroma[]" value="citrico" id="citrico">
                                <label class="form-check-label" for="citrico">Cítrico</label>
                            </div>
                        </div>

                        <!-- INGREDIENTES -->
                        <div class="mb-3">
                            <label class="form-label">Ingredientes</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingredientes[]" value="aloe" id="aloe">
                                <label class="form-check-label" for="aloe">Aloe vera</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ingredientes[]" value="colageno" id="colageno">
                                <label class="form-check-label" for="colageno">Colágeno</label>
                            </div>
                        </div>

                        <!-- BENEFICIOS -->
                        <div class="mb-3">
                            <label class="form-label">Beneficios</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="beneficios[]" value="hidratante" id="hidratante">
                                <label class="form-check-label" for="hidratante">Hidratante</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="beneficios[]" value="antiedad" id="antiedad">
                                <label class="form-check-label" for="antiedad">Anti edad</label>
                            </div>
                        </div>

                        <!-- DESTACADOS -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="destacados" name="destacados">
                            <label class="form-check-label" for="destacados">
                                Solo productos destacados
                            </label>
                        </div>

                        <!-- DISPONIBLES -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="disponibles" name="disponibles">
                            <label class="form-check-label" for="disponibles">
                                Solo productos en stock
                            </label>
                        </div>

                        <!-- BOTONES -->
                        <button type="submit" class="btn btn-primary w-100 mb-2">Filtrar</button>
                        <button type="reset" class="btn btn-outline-secondary w-100">Limpiar filtros</button>
                    </form>
                    </aside>


                <!-- Contenido principal -->
                <section class="productContainer col-12 col-md-9 col-lg-10 p-4">
                    <section class="productContainerTittle">
                        <h1>
                            <?= isset($categoriaNombre) ? htmlspecialchars($categoriaNombre) : "General"; ?>
                        </h1>

                        <div>
                            ordenar
                        </div>
                    </section>
                    <section class="containerCards">
                        <?php foreach ($productos as $id => $row) { ?>
                            <div class="cardProduct" style="width: 18rem;">
                                <a href="./detalleProducto.php?id=<?php echo $row['id']; ?>">
                                    <div class="imgProdShopCont">
                                        <?php 
                                            $imagenes = json_decode($row['imagen'], true);
                                            $primeraImagen = is_array($imagenes) && count($imagenes) > 0 ? htmlspecialchars($imagenes[0]) : 'default.jpg';
                                            $segundaImagen = is_array($imagenes) && count($imagenes) > 1 ? htmlspecialchars($imagenes[1]) : 'default.jpg';
                                        ?>
                                        <img class="img-default" src="src/imgBD/Productos/<?= $primeraImagen ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" alt="<?php echo $row['nombre']; ?>">
                                        <!--<img class="img-hover" src="src/imgBD/Productos/<?= $segundaImagen ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" alt="<?php echo $row['nombre']; ?>">-->
                                        <div class="hoverShop">
                                            <a href="#" class="btn cardProductTextBut mb-2">añadir al carrito <i class="bi bi-cart"></i></a>
                                            <a href="./detalleProducto.php?id=<?= $row['id'] ?>" class="btn cardProductTextBut">ver Producto</a>
                                        </div>
                                    </div>                                    
                                </a>
                                <div class="card-body cardProductText">
                                    <div class="card-title cardProductTextTitle"><?php echo $row['nombre']; ?></div>
                                    <div class="card-text cardProductTextSub"><?php echo $row['subcategoria_nombre']; ?></div>
                                    <div class="card-text cardProductTextPrice m-0">S/. <?php echo number_format($row['precio'], 2); ?></div>                                    
                                </div>
                            </div>
                        <?php } ?>
                    </section>
                </section>
            </div>
        </section>
    </section>
    





    <footer>
      <?php include_once('./src/partials/footer.php')?>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>
