<?php

require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

$productoId = isset($_POST['producto_id']) ? (int) $_POST['producto_id'] : 0;
$estado = $_POST['estado'] ?? '';

if ($productoId <= 0 || !in_array($estado, ['activo', 'inactivo'], true)) {
    echo json_encode([
        'success' => false,
        'message' => 'Datos inválidos.'
    ]);
    exit;
}

$stmt = $bd->prepare("
    UPDATE productos
    SET estado = :estado
    WHERE id = :id
");

$stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
$stmt->bindValue(':id', $productoId, PDO::PARAM_INT);

if ($stmt->execute()) {

    echo json_encode([
        'success' => true,
        'estado' => $estado
    ]);

} else {

    echo json_encode([
        'success' => false,
        'message' => 'No se pudo actualizar el producto.'
    ]);
}