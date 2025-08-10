<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}
$carrito = $_SESSION['carrito'];

// Obtener datos del cliente desde la BD
$cliente = null;
if (isset($_SESSION['id'])) {
    $id_cliente = $_SESSION['id'];
    $stmt = $bd->prepare("SELECT email, nombre, apellido_paterno, celular, direccion FROM usuarios WHERE id = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
   <header>
        <?php include_once('./src/partials/navbar.php'); ?>
    </header>

    <section class="container containerCarrito">
        <h1 class="text-center">Tu carrito de compras</h1>

        <?php if (empty($carrito)) : ?>
            <section class="alertCarrito">
                <div class="alert alert-info text-center ">Tu carrito está vacío.</div>
            </section>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $total = 0;
                            foreach ($carrito as $producto) {
                                $subtotal = $producto['precio'] * $producto['cantidad'];
                                $total += $subtotal;
                        ?>
                        <tr>
                            <td><img src="./src/imgBD/Productos/<?= htmlspecialchars($producto['imagen']) ?>" width="80"></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>S/ <?= number_format($producto['precio'], 2) ?></td>
                            <td>
                                <form method="post" class="d-flex form-actualizar" data-id="<?= $producto['id'] ?>">
                                    <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" min="1"
                                        class="form-control form-control-sm me-2 input-cantidad" style="width: 80px;">
                                    <button type="submit" class="btn btn-outline-success btn-sm">Actualizar</button>
                                </form>
                            </td>
                            <td>S/ <?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a href="eliminarDelCarrito.php?id=<?= $producto['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php $_SESSION['total_carrito'] = $total; ?>
                    </tbody>
                </table>
            </div>
                              
            <?php if ($cliente): ?>
                <form action="checkout.php" method="POST">
                    <input type="hidden" name="amount" value="<?= intval($total * 100) ?>"> <!-- 60.00 soles = 6000 -->
                    <input type="hidden" name="currency" value="PEN">
                    <input type="hidden" name="orderId" value="<?= uniqid('ORD_') ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($cliente['email']) ?>">
                    <input type="hidden" name="firstName" value="<?= htmlspecialchars($cliente['nombre']) ?>">
                    <input type="hidden" name="lastName" value="<?= htmlspecialchars($cliente['apellido_paterno']) ?>">
                    <input type="hidden" name="phoneNumber" value="<?= htmlspecialchars($cliente['celular']) ?>">
                    <input type="hidden" name="identityType" value="DNI">
                    <input type="hidden" name="identityCode" value="12345678"> <!-- Puedes reemplazar si lo tienes -->
                    <input type="hidden" name="address" value="<?= htmlspecialchars($cliente['direccion']) ?>">
                    <input type="hidden" name="country" value="PE">
                    <input type="hidden" name="city" value="Lima">
                    <input type="hidden" name="state" value="Lima">
                    <input type="hidden" name="zipCode" value="15001">
                    <button type="submit" class="btn btn-primary mt-2">Proceder al pago</button>
                </form>
            <?php else: ?>
                <div class="alert alert-warning mt-4 text-center">
                    Debes iniciar sesión para proceder al pago.
                </div>
            <?php endif; ?>            
        <?php endif; ?>
    </section>

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formularios = document.querySelectorAll('.form-actualizar');

            formularios.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const id = form.getAttribute('data-id');
                    const cantidad = form.querySelector('.input-cantidad').value;

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('cantidad', cantidad);

                    fetch('actualizarCarrito.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.ok ? res.text() : Promise.reject())
                    .then(() => {
                        // Actualizar subtotal y total sin recargar (opcionalmente)
                        location.reload(); // o puedes actualizar valores en la misma página con JS
                    })
                    .catch(() => alert('Error al actualizar cantidad'));
                });
            });
        });
    </script>


</body>
</html>