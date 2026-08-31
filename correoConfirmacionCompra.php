<div class="email-wrapper">

    <div class="email-container">

        <!-- ENCABEZADO -->
        <div class="email-header">
            <h1 style="color:#f9f9f9; margin:0; font-size:28px;">
                Kunanmi
            </h1>
        </div>

        <!-- CONTENIDO -->
        <div class="email-content">
            <h2 class="email-title">
                ¡Gracias por tu compra!
            </h2>

            <p class="email-text">
                Hola
                <strong><?= htmlspecialchars($usuario['nombre']) ?></strong>,
                hemos recibido correctamente tu pedido.

            </p>

            <hr class="email-divider">

            <!-- INFORMACIÓN DEL PEDIDO -->
            <table class="order-info">
                <tr>
                    <td>
                        <strong>Número de pedido:</strong>
                    </td>
                    <td>
                        #<?= htmlspecialchars($pedido['id']) ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Fecha:</strong>
                    </td>
                    <td>
                        <?= htmlspecialchars($pedido['fecha']) ?>
                    </td>
                </tr>

                <tr>
                    <td>
                        <strong>Dirección:</strong>
                    </td>
                    <td>
                        <?= htmlspecialchars($pedido['direccion']) ?>
                    </td>
                </tr>
            </table>


            <!-- PRODUCTOS -->
            <table class="products-table">
                <thead>
                    <tr>
                        <th>
                            Producto
                        </th>
                        <th class="center">
                            Cantidad
                        </th>
                        <th class="right">
                            Precio
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($producto['nombre']) ?>
                            </td>
                            <td class="center">
                                <?= htmlspecialchars($producto['cantidad']) ?>
                            </td>
                            <td class="right">
                                S/
                                <?= number_format($producto['precio'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- TOTAL -->
            <div class="order-total">
                Total:
                S/ <?= number_format($pedido['total'], 2) ?>
            </div>

            <!-- BOTÓN -->
            <div class="order-button-container">
                <a href="<?= htmlspecialchars($urlPedido) ?>" class="order-button" >
                    Ver mi pedido
                </a>
            </div>

            <hr class="email-divider">

            <p class="email-text">
                Puedes revisar el estado de tu pedido
                iniciando sesión en Kunanmi.
            </p>

            <p class="email-text">
                Gracias por confiar en nosotros. ❤️
            </p>
        </div>


        <!-- FOOTER -->
        <div class="email-footer">

            <p>
                © <?= date('Y') ?> Kunanmi
            </p>

            <p>
                Este correo fue enviado automáticamente.
                Por favor, no respondas a este mensaje.
            </p>

        </div>

    </div>

</div>