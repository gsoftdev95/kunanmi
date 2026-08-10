<?php

require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('./controladores/controlAcceso.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Método no permitido.'
    ]);
    exit;
}

$pedidoId = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;
$telefonoContacto = isset($_POST['telefono_contacto']) ? (int)$_POST['telefono_contacto'] : 0;
$direccionEnvio = isset($_POST['direccion_envio']) ? (int)$_POST['direccion_envio'] : 0;

if ($pedidoId <= 0) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'Datos inválidos.'
    ]);
    exit;
}

$ok = actualizarEstadoPedidoAdmin($bd, $pedidoId, $estadoId);

echo json_encode([
    'success' => $ok
]);