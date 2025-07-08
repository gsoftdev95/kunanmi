<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Inicializamos
$categoriaNombre = 'General';
$productos = [];

if (isset($_GET['subcategoria'])) {
    $subcategoriaId = $_GET['subcategoria'];

    // Traer productos por subcategoría
    $productos = obtenerProductosPorSubcategoria($bd, $subcategoriaId);

    // Obtener nombre de la categoría a la que pertenece la subcategoría
    $stmt = $bd->prepare("SELECT sc.nombre AS sub_nombre, c.nombre AS cat_nombre 
                          FROM subcategorias sc 
                          JOIN categorias c ON sc.categoria_id = c.id 
                          WHERE sc.id = ?");
    $stmt->execute([$subcategoriaId]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        $categoriaNombre = $res['cat_nombre']; // o $res['sub_nombre'] si prefieres el nombre de la subcategoría
    }

} elseif (isset($_GET['categoria'])) {
    $categoriaId = $_GET['categoria'];

    // Traer productos por categoría
    $productos = obtenerProductosPorCategoria($bd, $categoriaId);

    // Obtener nombre de la categoría
    $stmt = $bd->prepare("SELECT nombre FROM categorias WHERE id = ?");
    $stmt->execute([$categoriaId]);
    $res = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($res) {
        $categoriaNombre = $res['nombre'];
    }
} else {
    // General (todos)
    $productos = obtenerProdTienda($bd, "productos");
}

//Filtros dinamicos
// Obtener todos los atributos y sus valores
$atributos = [];
$sql = "SELECT a.nombre AS atributo_nombre, va.valor 
        FROM atributos a 
        JOIN valores_atributos va ON a.id = va.id_atributo 
        ORDER BY a.nombre, va.valor";
$stmt = $bd->prepare($sql);
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar en array agrupado por atributo
foreach ($resultado as $row) {
    $atributos[$row['atributo_nombre']][] = $row['valor'];
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
            <a href="./index.php">Inicio</a> / <a href="./tienda.php">Tienda</a> 
        </section>
    </section>

    <section class="ContainerMainProduct">
        <section class="container-fluid ContainerMainProductInner">
            <div class="row">
                <!-- Sidebar izquierdo -->
                <aside class="productFilters col-12 col-md-3 col-lg-2 p-3" style="min-height: 100vh; background-color: #f8f9fa;">
                    <h5 class="mb-3">Filtros</h5>
                    <!-- ATRIBUTOS DINÁMICOS EN ACORDEÓN -->
                    <form id="formFiltros" data-categoria="<?= isset($_GET['categoria']) ? $_GET['categoria'] : '' ?>" data-subcategoria="<?= isset($_GET['subcategoria']) ? $_GET['subcategoria'] : '' ?>">
                        <div class="accordion mb-3" id="accordionFiltros">
                            <?php 
                            $i = 0;
                            foreach ($atributos as $nombreAtributo => $valores): 
                                $idAcordeon = 'collapse' . $i;
                            ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $i ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $idAcordeon ?>" aria-expanded="false" aria-controls="<?= $idAcordeon ?>">
                                        <?= ucfirst($nombreAtributo) ?>
                                    </button>
                                </h2>
                                <div id="<?= $idAcordeon ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $i ?>" data-bs-parent="#accordionFiltros">
                                    <div class="accordion-body">
                                        <?php foreach ($valores as $valor): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="<?= $nombreAtributo ?>[]" value="<?= htmlspecialchars($valor) ?>" id="<?= $nombreAtributo . '_' . $valor ?>">
                                                <label class="form-check-label" for="<?= $nombreAtributo . '_' . $valor ?>">
                                                    <?= htmlspecialchars(ucfirst($valor)) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            $i++; 
                            endforeach; 
                            ?>
                        </div>
                    </form>
                </aside>


                <!-- Contenido principal -->
                <section class="productContainer col-12 col-md-9 col-lg-10 p-4">
                    <section class="productContainerTittle">
                        <h1>
                            <?= $categoriaNombre; ?>
                        </h1>

                        <div class="mb-3 d-flex justify-content-end">
                            <label for="ordenar" class="me-2">Ordenar por:</label>
                            <select class="form-select w-auto" id="ordenar" name="ordenar">
                                <option value="">-- Seleccionar --</option>
                                <option value="precio_asc">Precio: menor a mayor</option>
                                <option value="precio_desc">Precio: mayor a menor</option>
                                <option value="nombre_asc">Nombre: A-Z</option>
                                <option value="nombre_desc">Nombre: Z-A</option>
                                <option value="reciente">Más recientes</option>
                            </select>
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
    

    <!--ajaxTienda-->
    <script src="./src/js/ajaxtienda.js"></script>

  </body>
</html>

intento2 de Tienda
1
2
3