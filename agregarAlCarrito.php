<?php
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

$idProducto = $_POST['id'] ?? null;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

if (!$idProducto) {
    http_response_code(400);
    echo "ID de producto no válido";
    exit;
}

$producto = obtenerProductoPorId($bd, $idProducto);
if (!$producto) {
    http_response_code(404);
    echo "Producto no encontrado";
    exit;
}

// Preparar datos del carrito
$itemCarrito = [
    'id' => $producto['id'],
    'nombre' => $producto['nombre'],
    'precio' => $producto['precio'],
    'imagen' => '',
    'cantidad' => $cantidad
];

// Si tiene imágenes, usar la primera
if (!empty($producto['imagen'])) {
    $imagenes = json_decode($producto['imagen'], true);
    if (is_array($imagenes) && count($imagenes) > 0) {
        $itemCarrito['imagen'] = $imagenes[0];
    }
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Si ya existe el producto, sumamos la cantidad
if (isset($_SESSION['carrito'][$idProducto])) {
    $_SESSION['carrito'][$idProducto]['cantidad'] += $cantidad;
} else {
    $_SESSION['carrito'][$idProducto] = $itemCarrito;
}

// Redirigir a la página anterior
$referer = $_SERVER['HTTP_REFERER'] ?? 'tienda.php';
header("Location: $referer");
exit;
