<?php

require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

if (!isset($_SESSION['id'])) {
    exit('No autorizado');
}

if (empty($_POST['pedido_id'])) {
    exit('Pedido no recibido');
}

$pedidoId = (int) $_POST['pedido_id'];
$usuarioId = (int) $_SESSION['id'];

// Obtener el detalle del pedido
$detalle = obtenerDetallePedido($bd, $pedidoId, $usuarioId);

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

<div >    
    <?php foreach ($detalle as $item): ?>
        <?php
            $total += $item['subtotal'];

            // Obtener la primera imagen del JSON
            $imagenes = json_decode($item['imagen'], true);
            $primeraImagen = !empty($imagenes) ? $imagenes[0] : 'sin-imagen.png';
        ?>

        <div class="cardProductoPedido">
            <div class="imagenProductoPedido">
                <img
                    src="./src/imgBD/Productos/<?= htmlspecialchars($primeraImagen) ?>"
                    alt="<?= htmlspecialchars($item['nombre']) ?>">
            </div>

            <div class="infoProductoPedido">
                <h5><?= htmlspecialchars($item['nombre']) ?></h5>

                <div class="datosProductoPedido">
                    <div>
                        <span>Cantidad</span>
                        <strong><?= $item['cantidad'] ?></strong>
                    </div>
                    <div>
                        <span>Precio</span>
                        <strong>S/ <?= number_format($item['precio_unitario'],2) ?></strong>
                    </div>
                    <div>
                        <span>Subtotal</span>
                        <strong class="subtotalPedido">
                            S/ <?= number_format($item['subtotal'],2) ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="totalPedido">
        <span>Total del pedido</span>
        <strong>
            S/ <?= number_format($total,2) ?>
        </strong>
    </div>
</div>