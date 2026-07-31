<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

require_once "keys.example.php";

/*
echo "<pre>";
echo "SESSION ID: " . session_id() . "\n\n";
print_r($_SESSION);
echo "\nPOST:\n";
print_r($_POST);
echo "</pre>";
*/



if (empty($_POST)) {
    throw new Exception("No post data received!");
}

// Validación de firma
if (!checkHash(HMAC_SHA256)) {
    throw new Exception("Invalid signature");
}
if (!isset($_POST['kr-answer'])) {
    throw new Exception("No se recibió kr-answer.");
}
$answer = json_decode($_POST["kr-answer"], true);
if (!$answer) {
    throw new Exception("Respuesta inválida de Izipay.");
}

// Validar estado del pago
$orderStatus = $answer['orderStatus'] ?? '';
switch ($orderStatus) {
    case 'PAID':
        break;

    case 'REFUSED':
        $mensaje = "Pago rechazado.";
        $detalle = "La entidad bancaria rechazó la transacción.";
        include 'pagoError.php';
        exit;

    case 'CANCELLED':
        $mensaje = "Pago cancelado.";
        $detalle = "El pago fue cancelado por el cliente.";
        include 'pagoError.php';
        exit;

    case 'EXPIRED':
        $mensaje = "Pago expirado.";
        $detalle = "La sesión de pago expiró.";
        include 'pagoError.php';
        exit;

    default:
        $mensaje = "Pago no procesado.";
        $detalle = "Estado recibido: " . $orderStatus;
        include 'pagoError.php';
        exit;
}

// Datos del pago
$orderId = $answer['orderDetails']["orderId"];
if (empty($orderId)) {
    throw new Exception("No se recibió el Order ID de Izipay.");
}
$monto = $answer['orderDetails']["orderTotalAmount"] / 100;
$fecha = date("Y-m-d H:i:s");

// Validar monto recibido

$totalEsperado = floatval($_SESSION['total_carrito']);
$montoRecibido = floatval($answer['orderDetails']['orderTotalAmount']) / 100;

/*
echo "<pre>";
echo "Total esperado: ";
var_dump($totalEsperado);

echo "Monto recibido: ";
var_dump($montoRecibido);
echo "</pre>";
exit;
*/


if (abs($montoRecibido - $totalEsperado) > 0.01) {
    die("Error: el monto del pago no coincide con el total del carrito.");
}

$montoRecibido = floatval($answer['orderDetails']['orderTotalAmount']) / 100; //temporal

// Datos para la vista
$mensaje = "Pago procesado correctamente";
$orderStatus = $answer['orderStatus'] ?? 'Desconocido';
$moneda = $answer['orderDetails']['orderCurrency'] ?? 'PEN';
$montoSoles = number_format($monto, 2);

// Validaciones de sesión
$usuario_id = $_SESSION['id'] ?? null;
if (!$usuario_id) {
    die("No hay sesión de usuario activa.");
}
if (
    empty($_SESSION['destinatario']) ||
    empty($_SESSION['telefono_contacto']) ||
    empty($_SESSION['direccion_envio']) ||
    empty($_SESSION['distrito_envio'])
) {
    die("No se encontraron los datos de entrega.");
}

$destinatario = $_SESSION['destinatario'];
$telefono = $_SESSION['telefono_contacto'];
$direccion_envio = $_SESSION['direccion_envio'];
$distrito = $_SESSION['distrito_envio'];
$referencia = $_SESSION['referencia_envio'] ?? '';

$productos = $_SESSION['carrito'] ?? [];
if (empty($productos)) {
    die("El carrito está vacío.");
}


try {
    // Insertar transacción
    $bd->beginTransaction();

    // Verificar si el pedido ya fue registrado
    $stmt = $bd->prepare("
        SELECT id
        FROM pedidos
        WHERE order_id = ?
    ");
    $stmt->execute([$orderId]);

    if ($stmt->fetch()) {
        throw new Exception("Este pedido ya fue procesado anteriormente.");
    }

    // Insertar pedido
    $stmt = $bd->prepare("
        INSERT INTO pedidos
            (
                usuario_id,
                fecha_pedido,
                monto_total,
                destinatario,
                telefono_contacto,
                direccion_envio,
                distrito,
                referencia,
                estado_id,
                order_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ");

    $stmt->execute([
        $usuario_id,
        $fecha,
        $monto,
        $destinatario,
        $telefono,
        $direccion_envio,
        $distrito,
        $referencia,
        1,
        $orderId
    ]);
    $pedido_id = $bd->lastInsertId();

    // Procesar productos
    foreach ($productos as $producto) {
        // Verificar stock disponible
        $stmt = $bd->prepare("
            SELECT stock
            FROM productos
            WHERE id = :producto_id
        ");

        $stmt->execute([
            ':producto_id' => $producto['id']
        ]);

        $stockActual = $stmt->fetchColumn();

        if ($stockActual < $producto['cantidad']) {
            throw new Exception(
                "No hay suficiente stock para el producto: " . $producto['nombre']
            );
        }

        // Insertar detalle del pedido
        $subtotal = $producto['precio'] * $producto['cantidad'];

        $stmt = $bd->prepare("
            INSERT INTO detalle_pedido
            (pedido_id, producto_id, precio_unitario, cantidad, subtotal)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $pedido_id,
            $producto['id'],
            $producto['precio'],
            $producto['cantidad'],
            $subtotal
        ]);

        // Descontar stock
        $stmt = $bd->prepare("
            UPDATE productos
            SET stock = stock - :cantidad
            WHERE id = :producto_id
        ");
        $stmt->execute([
            ':cantidad'    => $producto['cantidad'],
            ':producto_id' => $producto['id']
        ]);

        // Verificar que el UPDATE se realizó
        if ($stmt->rowCount() === 0) {
            throw new Exception(
                "No se pudo actualizar el stock del producto."
            );
        }
    }

    // Limpiar sesión
    unset($_SESSION['carrito']);
    unset($_SESSION['total_carrito']);
    unset($_SESSION['direccion_envio']);
    unset($_SESSION['destinatario']);
    unset($_SESSION['telefono_contacto']);
    unset($_SESSION['distrito_envio']);
    unset($_SESSION['referencia_envio']);

    // Confirmar cambios
    $bd->commit();

    // Obtener datos del usuario
    $stmt = $bd->prepare("SELECT nombre, apellido_paterno, apellido_materno, email FROM usuarios WHERE id = ? ");
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Datos del pedido para el correo
    $pedido = [
        'id' => $pedido_id,
        'fecha' => $fecha,
        'direccion' => $direccion_envio,
        'total' => $monto
    ];

    // Enviar correo
    try {
        enviarCorreoCompra($usuario, $pedido, $productos);
        $correoEnviado = true;
    } catch (Throwable $e) {
        $correoEnviado = false;
    }

} catch (Throwable $e) {

    // Deshacer todos los cambios
    if ($bd->inTransaction()) {
        $bd->rollBack();
    }
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

                    <?php if ($correoEnviado): ?>
                        <div class="alert alert-success">
                            <strong>¡Tu compra fue realizada correctamente!</strong><br>
                            Hemos enviado un correo electrónico con el resumen de tu pedido.
                            Si no lo encuentras en los próximos minutos, revisa la carpeta de spam.
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <strong>¡Tu compra fue realizada correctamente!</strong><br>
                            No pudimos enviar el correo de confirmación en este momento.
                            Tu pedido ha sido registrado correctamente.
                            Si deseas confirmar el estado de tu compra, puedes comunicarte con nuestro equipo de atención al cliente.
                        </div>
                    <?php endif; ?>

                    <!-- solo para local -->
                    <details>
                        <summary><strong>Respuesta completa</strong></summary>
                        <pre><?= json_encode($answer, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); ?></pre>
                    </details>

                    <a href="tienda.php" class="btn btn-primary mt-3">
                        Volver al inicio
                    </a>
                </section>
            </div>
            <div class="col-md-3"></div>
        </div>
    </section>
</body>
</html>