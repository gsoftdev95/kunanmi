<?php

require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Verificar usuario
$usuario_id = $_SESSION['id'] ?? null;

if (!$usuario_id) {
    die('Debes iniciar sesión para realizar el pedido.');
}

// Obtener carrito
$productos = $_SESSION['carrito'] ?? [];
$total = floatval($_SESSION['total_carrito'] ?? 0);

if (empty($productos)) {
    die('El carrito está vacío.');
}

if ($total <= 0) {
    die('El monto de la compra no es válido.');
}

// Obtener datos de entrega
$destinatario = trim($_SESSION['destinatario'] ?? '');
$telefono = trim($_SESSION['telefono_contacto'] ?? '');
$direccion = trim($_SESSION['direccion_envio'] ?? '');
$distrito = trim($_SESSION['distrito_envio'] ?? '');
$referencia = trim($_SESSION['referencia_envio'] ?? '');

if (
    $destinatario === '' ||
    $telefono === '' ||
    $direccion === '' ||
    $distrito === ''
) {
    die('No se encontraron todos los datos de entrega.');
}


// Generar identificador del pedido
$orderId = 'KUN-' . date('YmdHis') . '-' . strtoupper(
    substr(bin2hex(random_bytes(3)), 0, 6)
);

$fecha = date('Y-m-d H:i:s');

try {

    $bd->beginTransaction();

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
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $usuario_id,
        $fecha,
        $total,
        $destinatario,
        $telefono,
        $direccion,
        $distrito,
        $referencia,
        1,
        $orderId
    ]);

    $pedido_id = $bd->lastInsertId();


    // Insertar productos del pedido
    foreach ($productos as $producto) {

        $subtotal = $producto['precio'] * $producto['cantidad'];

        $stmt = $bd->prepare("
            INSERT INTO detalle_pedido
            (
                pedido_id,
                producto_id,
                precio_unitario,
                cantidad,
                subtotal
            )
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $pedido_id,
            $producto['id'],
            $producto['precio'],
            $producto['cantidad'],
            $subtotal
        ]);
    }


    // Confirmar transacción
    $bd->commit();


    // Limpiar carrito y datos temporales
    unset($_SESSION['carrito']);
    unset($_SESSION['total_carrito']);
    unset($_SESSION['direccion_envio']);
    unset($_SESSION['destinatario']);
    unset($_SESSION['telefono_contacto']);
    unset($_SESSION['distrito_envio']);
    unset($_SESSION['referencia_envio']);


    // Aquí continuaremos con WhatsApp
    // echo "Pedido registrado correctamente. ID: " . htmlspecialchars($orderId);
    // ruta a wsp
    $mensaje = "Hola Kunanmi, quiero confirmar mi pedido.\n\n";
    $mensaje .= "Pedido: " . $orderId . "\n\n";

    foreach ($productos as $producto) {
        $subtotal = $producto['precio'] * $producto['cantidad'];

        $mensaje .= "- " . $producto['nombre']
            . " x" . $producto['cantidad']
            . " — S/ " . number_format($subtotal, 2) . "\n";
    }

    $mensaje .= "\nTotal: S/ " . number_format($total, 2);
    $mensaje .= "\n\nDatos de entrega:";
    $mensaje .= "\nNombre: " . $destinatario;
    $mensaje .= "\nTeléfono: " . $telefono;
    $mensaje .= "\nDirección: " . $direccion;
    $mensaje .= "\nDistrito: " . $distrito;

    if ($referencia !== '') {
        $mensaje .= "\nReferencia: " . $referencia;
    }

    $numeroWhatsApp = '51910263511';

    header(
        'Location: https://wa.me/' . $numeroWhatsApp . '?text=' . urlencode($mensaje)
    );
    exit;


} catch (Throwable $e) {

    if ($bd->inTransaction()) {
        $bd->rollBack();
    }

    error_log(
        'Error al registrar pedido por WhatsApp: ' . $e->getMessage()
    );

    die('No fue posible registrar el pedido.');
}

