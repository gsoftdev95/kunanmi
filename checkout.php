<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');

// $configCulqi = require_once './config/culqi.php'; comentado temporalmente

// Guardar datos de entrega en sesión
$_SESSION['destinatario'] = trim($_POST['destinatario'] ?? '');
$_SESSION['telefono_contacto'] = trim($_POST['telefono'] ?? '');
$_SESSION['direccion_envio'] = trim($_POST['direccion'] ?? '');
$_SESSION['distrito_envio'] = trim($_POST['distrito'] ?? '');
$_SESSION['referencia_envio'] = trim($_POST['referencia'] ?? '');


// Obtener carrito y total
$productos = $_SESSION['carrito'] ?? [];
$total = floatval($_SESSION['total_carrito'] ?? 0);

// Validar que exista un carrito
if (empty($productos)) {
    die('El carrito está vacío.');
}

if ($total <= 0) {
    die('El monto de la compra no es válido.');
}

// Llave pública de Culqi para el frontend
// $culqiPublicKey = $configCulqi['public_key'];

?>

<!DOCTYPE html>
<html>

<head>
<title>Form Token</title>
<link rel='stylesheet' href='css/style.css' />
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/journal/bootstrap.min.css"
    integrity="sha384-QDSPDoVOoSWz2ypaRUidLmLYl4RyoBWI44iA5agn6jHegBxZkNqgm2eHb6yZ5bYs" crossorigin="anonymous" /> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar bg-primary" style="background-color: #17b598!important;">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand mb-1"><img src="./src/img/logokunanmi.png" alt="Logo Kunanmi" width="80"></a>
        </div>
    </nav>

    <section class="container">
        <div class="row">
        <div class="col-md-3"></div>
        <div class="center-column col-md-6">
            <section class="payment-form">
                <!-- comentado temporal
                <div class="row card-header">
                    <li class="list-group-item w-100">
                    Pago con tarjeta de crédito/débito
                    <img src="" alt="Tarjetas aceptadas" style="width: 200px;">
                    </li>            
                </div> -->
                <div class="row card-header">
                    <li class="list-group-item w-100">
                        Actualmente estamos habilitando nuestro sistema de pagos. Puedes solicitar tu pedido directamente por WhatsApp.                        
                    </li>            
                </div>

                <div id="micuentawebstd_rest_wrapper" class="mt-3">
                    <div class="card mb-4">
                        <div class="card-header">
                            <strong>Resumen de tu compra</strong>
                        </div>
                        <ul class="list-group list-group-flush">
                            <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($producto['nombre']) ?> x <?= $producto['cantidad'] ?>
                                <span>S/ <?= number_format($producto['precio'] * $producto['cantidad'], 2) ?></span>
                                </li>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item text-muted">No hay productos cargados</li>
                            <?php endif; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Total:</strong>
                                <strong>S/ <?= number_format($total, 2) ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>

                <form action="registrarPedidoWhatsApp.php" method="POST">
                    <button type="submit" class="btn btn-success w-100 mt-3">
                        Solicitar pedido por WhatsApp
                    </button>
                </form>
            
            </section>
        </div>
        <div class="col-md-3"></div>
        </div>
    </section>

</body>
</html>