<?php
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Recuperar filtros
$categoria = $_POST['categoria'] ?? null;
$subcategoria = $_POST['subcategoria'] ?? null;
$orden = $_POST['ordenar'] ?? null;

$atributosFiltrados = $_POST;
unset($atributosFiltrados['categoria'], $atributosFiltrados['subcategoria'], $atributosFiltrados['ordenar']);

$sql = "SELECT p.*, s.nombre AS subcategoria_nombre 
        FROM productos p 
        LEFT JOIN subcategorias s ON p.subcategoria_id = s.id 
        WHERE 1";

$parametros = [];

// Filtros por categoría/subcategoría
if ($subcategoria) {
    $sql .= " AND p.subcategoria_id = ?";
    $parametros[] = $subcategoria;
} elseif ($categoria) {
    $sql .= " AND p.subcategoria_id IN (
        SELECT id FROM subcategorias WHERE categoria_id = ?
    )";
    $parametros[] = $categoria;
}

// Filtros por atributos
if (!empty($atributosFiltrados)) {
    foreach ($atributosFiltrados as $nombreAtributo => $valores) {
        if (!is_array($valores)) continue;

        $placeholders = implode(',', array_fill(0, count($valores), '?'));

        $sql .= " AND p.id IN (
            SELECT pa.producto_id 
            FROM producto_atributo pa 
            JOIN valores_atributos va ON pa.valor_atributo_id = va.id 
            JOIN atributos a ON va.id_atributo = a.id 
            WHERE a.nombre = ? AND va.valor IN ($placeholders)
        )";

        $parametros[] = $nombreAtributo;
        foreach ($valores as $valor) {
            $parametros[] = $valor;
        }
    }
}

// ORDENAMIENTO antes de ejecutar
switch ($orden) {
    case 'precio_asc':
        $sql .= " ORDER BY p.precio ASC";
        break;
    case 'precio_desc':
        $sql .= " ORDER BY p.precio DESC";
        break;
    case 'nombre_asc':
        $sql .= " ORDER BY p.nombre ASC";
        break;
    case 'nombre_desc':
        $sql .= " ORDER BY p.nombre DESC";
        break;
    case 'reciente':
        $sql .= " ORDER BY p.fecha_creacion DESC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

// Ejecutar consulta
$stmt = $bd->prepare($sql);
$stmt->execute($parametros);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Renderizar resultados
if (count($productos) === 0) {
    echo '<div class="alert alert-warning">No se encontraron productos con esos filtros.</div>';
} else {
    foreach ($productos as $row) {
        $imagenes = json_decode($row['imagen'], true);
        $primeraImagen = is_array($imagenes) && count($imagenes) > 0 ? htmlspecialchars($imagenes[0]) : 'default.jpg';
        ?>
        <div class="cardProduct" style="width: 18rem;">
            <a href="./detalleProducto.php?id=<?= $row['id']; ?>">
                <div class="imgProdShopCont">
                    <img class="img-default" src="src/imgBD/Productos/<?= $primeraImagen ?>" alt="<?= htmlspecialchars($row['nombre']) ?>">
                    <div class="hoverShop">
                        <a href="#" class="btn cardProductTextBut mb-2">añadir al carrito <i class="bi bi-cart"></i></a>
                        <a href="./detalleProducto.php?id=<?= $row['id'] ?>" class="btn cardProductTextBut">ver Producto</a>
                    </div>
                </div>
            </a>
            <div class="card-body cardProductText">
                <div class="card-title cardProductTextTitle"><?= $row['nombre']; ?></div>
                <div class="card-text cardProductTextSub"><?= $row['subcategoria_nombre']; ?></div>
                <div class="card-text cardProductTextPrice m-0">S/. <?= number_format($row['precio'], 2); ?></div>
            </div>
        </div>
        <?php
    }
}
