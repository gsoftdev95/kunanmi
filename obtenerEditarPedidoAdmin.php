<?php

require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('./controladores/controlAcceso.php');

if (empty($_POST['pedido_id'])) {
    exit('Pedido no recibido');
}

$pedidoId = (int)$_POST['pedido_id'];

// Información general del pedido
$pedido = obtenerPedidoPorId($bd, $pedidoId);

// Productos del pedido
$detalle = obtenerDetallePedidoAdmin($bd, $pedidoId);

// Estados disponibles
$estados = obtenerEstadosPedido($bd);

if (!$pedido) {
    exit("<p>Pedido no encontrado.</p>");
}

$total = 0;
?>

<h3>Editar pedido</h3>
<hr>

<form id="formEditarPedidoAdmin">

    <input
        type="hidden"
        name="pedido_id"
        value="<?= $pedido['id'] ?>">

    <div class="mb-3">
        <strong>Cliente:</strong>
        <?= htmlspecialchars($pedido['nombre']) ?>
        <?= htmlspecialchars($pedido['apellido_paterno']) ?>
        <?= htmlspecialchars($pedido['apellido_materno']) ?>
    </div>

    <div class="mb-3">
        <strong>Correo:</strong>
        <?= htmlspecialchars($pedido['email']) ?>
    </div>

    <div class="mb-3">
        <label class="form-label">
            Teléfono de contacto
        </label>

        <input
            type="text"
            class="form-control"
            name="telefono_contacto"
            value="<?= htmlspecialchars($pedido['telefono_contacto']) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">
            Dirección de envío
        </label>

        <textarea
            class="form-control"
            name="direccion_envio"
            rows="3"><?= htmlspecialchars($pedido['direccion_envio']) ?></textarea>
    </div>

    <hr>

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

                <h5><?= htmlspecialchars($item['nombre']) ?></h5>

                <div class="datoProductoPedido">
                    <strong>Cantidad:</strong>
                    <?= $item['cantidad'] ?>
                </div>

                <div class="datoProductoPedido">
                    <strong>Precio:</strong>
                    S/ <?= number_format($item['precio_unitario'], 2) ?>
                </div>

                <div class="datoProductoPedido">
                    <strong>Subtotal:</strong>
                    S/ <?= number_format($item['subtotal'], 2) ?>
                </div>

            </div>

        </div>

    <?php endforeach; ?>

    <hr>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <strong>Total:</strong>
        <strong class="text-success">
            S/ <?= number_format($total, 2) ?>
        </strong>
    </div>

    <hr>

    <div class="text-end">
        <button
            type="submit"
            class="btn btn-success">

            Guardar cambios

        </button>
    </div>

</form>