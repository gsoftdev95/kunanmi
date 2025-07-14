<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

if (!isset($_SESSION['id'])) {
    $_SESSION['url_redireccion'] = $_SERVER['REQUEST_URI'];
    header('Location: login.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Obtener productos del carrito
$carrito = $_SESSION['carrito'];
?>

<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
   <header>
        <section class="contTopBar">
            <div class="topBar">Hecho con amor — Productos artesanales y naturales</div>
        </section>
        <?php include_once('./src/partials/navbar.php'); ?>
    </header>

    <section class="container py-5">
        <h1 class="text-center mb-4">Tu carrito de compras</h1>

        <?php if (empty($carrito)) : ?>
            <div class="alert alert-info text-center">Tu carrito está vacío.</div>
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
                                <form method="post" action="actualizarCarrito.php" class="d-flex">
                                    <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                                    <input type="number" name="cantidad" value="<?= $producto['cantidad'] ?>" min="1" class="form-control form-control-sm me-2" style="width: 80px;">
                                    <button class="btn btn-outline-success btn-sm" type="submit">Actualizar</button>
                                </form>
                            </td>
                            <td>S/ <?= number_format($subtotal, 2) ?></td>
                            <td>
                                <a href="eliminarDelCarrito.php?id=<?= $producto['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="text-end">
                <h4>Total: S/ <?= number_format($total, 2) ?></h4>
                <a href="checkout.php" class="btn btn-primary mt-2">Proceder al pago</a>
            </div>
        <?php endif; ?>
    </section>

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>