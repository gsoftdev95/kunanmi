<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

require_once "keys.example.php";


if (empty($_POST)) {
  throw new Exception("No post data received!");
}

// Validación de firma
if (!checkHash(HMAC_SHA256)) {
  throw new Exception("Invalid signature");
}

$answer = json_decode($_POST["kr-answer"], true);

// Datos del pedido
$orderId = $answer['orderDetails']["orderId"];
$monto = $answer['orderDetails']["orderTotalAmount"] / 100;
$fecha = date("Y-m-d H:i:s");

// Nuevas variables para mostrar en la vista
$mensaje = "Pago procesado correctamente";
$orderStatus = $answer['orderStatus'] ?? 'Desconocido';
$moneda = $answer['orderDetails']['orderCurrency'] ?? 'PEN';
$montoSoles = number_format($monto, 2);


// Aquí debes tener el ID del usuario logueado
$usuario_id = $_SESSION['id'] ?? null;
$direccion_envio = $_SESSION['direccion_envio'] ?? 'Sin dirección'; // Puedes capturarla del checkout también

// Validación previa
if (!$usuario_id) {
  die("No hay sesión de usuario activa.");
}

try {

  // Insertar en la tabla pedidos
  $stmt = $bd->prepare("INSERT INTO pedidos (usuario_id, fecha_pedido, monto_total, direccion_envio, estado_id) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$usuario_id, $fecha, $monto, $direccion_envio, 1]); // estado_id 1 = pendiente o pagado según tu lógica
  $pedido_id = $bd->lastInsertId();

  // Insertar productos del carrito
  $productos = $_SESSION['carrito'] ?? [];

  foreach ($productos as $producto) {
    $subtotal = $producto['precio'] * $producto['cantidad'];
    $stmt = $bd->prepare("INSERT INTO detalle_pedido (pedido_id, producto_id, precio_unitario, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
      $pedido_id,
      $producto['id'],
      $producto['precio'],
      $producto['cantidad'],
      $subtotal
    ]);
  }

  // Vaciar carrito
  unset($_SESSION['carrito']);

} catch (PDOException $e) {
  die("Error al guardar el pedido: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Resultado de pago</title>
  <link rel='stylesheet' href='css/style.css' />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/journal/bootstrap.min.css" />
</head>
<body>
<nav class="navbar bg-primary" style="background-color: #FF2D46!important;">
  <div class="container-fluid">
      <a href="/" class="navbar-brand mb-1"><img src="https://iziweb001b.s3.amazonaws.com/webresources/img/logo.png" width="80"></a>
  </div>
</nav>

<section class="container mt-5">
  <div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
      <section class="result-form">
        <h2><?= $mensaje ?></h2>
        <hr>
        <p><strong>Estado:</strong> <?= $orderStatus ?></p>
        <p><strong>Monto:</strong> <?= $moneda ?> <?= $montoSoles ?></p>
        <p><strong>Order-id:</strong> <?= $orderId ?></p>
        <hr>
        <details>
          <summary><strong>Respuesta completa</strong></summary>
          <pre><?= json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
        </details>
        <a href="tienda.php" class="btn btn-primary mt-3">Volver al inicio</a>
      </section>
    </div>
    <div class="col-md-3"></div>
  </div>
</section>
</body>
</html>
