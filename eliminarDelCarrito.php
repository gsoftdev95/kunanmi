<?php
require_once('./controladores/funciones.php');

$id = $_GET['id'] ?? null;

if ($id && isset($_SESSION['carrito'][$id])) {
    unset($_SESSION['carrito'][$id]);
}

header('Location: carrito.php');
exit;
?>