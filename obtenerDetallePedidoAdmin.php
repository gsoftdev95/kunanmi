<?php

require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('./controladores/controlAcceso.php');

if (empty($_POST['pedido_id'])) {
    exit('Pedido no recibido');
}

$pedidoId = (int)$_POST['pedido_id'];

// El administrador puede ver cualquier pedido
$detalle = obtenerDetallePedidoAdmin($bd, $pedidoId);

if (empty($detalle)) {
    echo "<h3>Detalle del pedido</h3>";
    echo "<hr>";
    echo "<p>No se encontraron productos para este pedido.</p>";
    exit;
}

$total = 0;
?>

<h3>Detalle del pedido</h3>
<hr>

<div class="contenedorProductosPedido">

    <?php foreach ($detalle as $item): ?>

        <?php
            $total += $item['subtotal'];

            $imagenes = json_decode($item['imagen'], true);
            $primeraImagen = !empty($imagenes)
                ? $imagenes[0]
                : 'sin-imagen.png';
        ?>

        <div class="cardProductoPedido">
            <div class="imagenProductoPedido">
                <img
                    src="./src/imgBD/Productos/<?= htmlspecialchars($primeraImagen) ?>"
                    alt="<?= htmlspecialchars($item['nombre']) ?>">
            </div>

            <div class="infoProductoPedido">
                <h4><?= htmlspecialchars($item['nombre']) ?></h4>
                <div class="datoProductoPedido">
                    <strong>Cantidad:</strong>
                    <?= $item['cantidad'] ?>
                </div>
                <div class="datoProductoPedido">
                    <strong>Precio:</strong>
                    S/ <?= number_format($item['precio_unitario'],2) ?>
                </div>
                <div class="datoProductoPedido">
                    <strong>Subtotal:</strong>
                    S/ <?= number_format($item['subtotal'],2) ?>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

    <div class="totalPedidoModal">
        <span>Total del pedido</span>
        <strong>S/ <?= number_format($total,2) ?></strong>
    </div>

</div>