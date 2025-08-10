<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Verifica que el cliente esté logueado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}

// Obtener los datos del usuario
$idUsuario = $_SESSION['id'];
$usuario = obtenerUsuarioPorId($bd, $idUsuario);
$pedidos = obtenerPedidosPorUsuario($bd, $idUsuario);
?>

<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
    
    <header>
        <?php include_once('./src/partials/navbar.php')?>
    </header>

    
    <main class="container mt-4 mb-5">
        <h2 class="mb-4">Mi Perfil</h2>

        <!-- Datos del usuario -->
        <div class="card mb-4">
            <div class="card-body">
            <h5 class="card-title">Información personal</h5>
            <p><strong>Nombre:</strong> <?= $usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno'] ?></p>
            <p><strong>Email:</strong> <?= $usuario['email'] ?></p>
            <p><strong>Celular:</strong> <?= $usuario['celular'] ?></p>
            <p><strong>Dirección:</strong> <?= $usuario['direccion'] ?></p>
            <p><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
            </div>
        </div>

        <!-- Pedidos del usuario -->
        <div class="card mb-4">
            <div class="card-body">
            <h5 class="card-title">Mis Pedidos</h5>
            <?php if (count($pedidos) > 0): ?>
                <div class="table-responsive">
                <table class="table table-striped text-center">
                    <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Monto Total</th>
                        <th>Ver Detalle</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pedidos as $pedido): ?>
                        <tr>
                        <td><?= $pedido['id'] ?></td>
                        <td><?= date('d/m/Y', strtotime($pedido['fecha_pedido'])) ?></td>
                        <td><?= ucfirst($pedido['descripcion_cliente']) ?></td>
                        <td>S/ <?= number_format($pedido['monto_total'], 2) ?></td>
                        <td><a href="detallePedido.php?id=<?= $pedido['id'] ?>" class="btn btn-sm btn-outline-primary">Ver</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            <?php else: ?>
                <p>No tienes pedidos registrados aún.</p>
            <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <p>Para actualización de datos comunicarse al correo correo@gmail.com</p>            </div>
            </div>
        </div>

    </main>
  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>