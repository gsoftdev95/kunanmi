<?php
require_once('./controladores/funciones.php');

$total = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $total += $item['cantidad'];
    }
}
echo $total;
?>