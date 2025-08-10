<?php
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');


if (!isset($_SESSION['id'])) {
    $_SESSION['url_redireccion'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}
// Proteger acceso
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Validar carrito
$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    header("Location: carrito.php");
    exit;
}

// Obtener total del carrito
$total = $_SESSION['total_carrito'] ?? 0.0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php'); ?>
</head>
<body>
    <?php include_once('./src/partials/navbar.php'); ?>

    <section class="container my-5">
        <h2>Confirmar pedido</h2>

        <!-- 🛒 Resumen del carrito -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($carrito as $producto): ?>
                        <tr>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>S/ <?= number_format($producto['precio'], 2) ?></td>
                            <td><?= $producto['cantidad'] ?></td>
                            <td>S/ <?= number_format($producto['precio'] * $producto['cantidad'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <h4 class="text-end">Total: <strong>S/ <?= number_format($total, 2) ?></strong></h4>
        </div>

        <!-- 📝 Formulario de dirección de envío -->
        <form action="procesarPedido.php" method="POST">
            <div class="mb-3">
                <label for="direccion" class="form-label mt-5">Dirección de envío</label>
                <textarea name="direccion" id="direccion" rows="3" class="form-control" placeholder="Coloque su dirección y referencias" required></textarea>

                <label for="direccion" class="form-label mt-5">Datos adicionales</label>
                <textarea name="direccion" id="direccion" rows="3" class="form-control" placeholder="Aquí puede añadir datos o referencia para tomar en consideración con respecto al pedido" required></textarea>
            </div>

            <!-- Aquí irá el botón de PayPal -->
            <div id="paypal-button-container"></div>

            <!-- Campo oculto para la dirección y monto -->
            <input type="hidden" name="monto_total" value="<?= $total ?>">
            <input type="hidden" name="paypal_id" id="paypal_id"> <!-- Lo completa JS -->
        </form>
    </section>

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>




    <!-- ✅ PayPal SDK -->
    <!-- ID DE DESARROLLO-- DEBE CAMBIARSE AL DE PRODUCCION(CLIENTE) -->
    <script src="https://www.paypal.com/sdk/js?client-id=AWwB0tl1bxY00jfi6UKsgm-_Ruo-Ohip5HyPpV-_TgEO7xJgZNqhiR_BDMHMrXzAnB5c9w8kebs3-fwz&currency=PEN"></script>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= number_format($total, 2, '.', '') ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Guardar el ID de PayPal y enviar el formulario
                    document.getElementById('paypal_id').value = details.id;
                    document.querySelector('form').submit();
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
