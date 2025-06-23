<?php
require_once('./src/partials/conexionBD.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPedido = $_POST['id_pedido'] ?? null;
    $estado = $_POST['estado'] ?? '';

    if ($idPedido && in_array($estado, ['pendiente', 'en proceso', 'enviado', 'entregado', 'completado'])) {
        $stmt = $bd->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $idPedido, PDO::PARAM_INT);
        $stmt->execute();
    }
}

header("Location: administrador.php#pedidos");
exit;
