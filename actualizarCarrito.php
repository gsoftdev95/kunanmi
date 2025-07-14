<?php
require_once('./controladores/funciones.php');

$id = $_POST['id'] ?? null;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

if (!$id || $cantidad < 1) {
    header('Location: carrito.php');
    exit;
}

// Si el producto está en el carrito, actualiza su cantidad
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad'] = $cantidad;
}

header('Location: carrito.php');
exit;
?>