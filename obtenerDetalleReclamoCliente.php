<?php

require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    exit('Acceso no autorizado.');
}

$usuario_id = $_SESSION['id'];
$reclamo_id = $_POST['reclamo_id'] ?? null;

if (!$reclamo_id || !is_numeric($reclamo_id)) {
    exit('Reclamo no válido.');
}

$sql = "SELECT *
        FROM libro_reclamaciones
        WHERE id = :id
        AND usuario_id = :usuario_id
        LIMIT 1";

$stmt = $bd->prepare($sql);

$stmt->execute([
    ':id' => $reclamo_id,
    ':usuario_id' => $usuario_id
]);

$reclamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclamo) {
    exit('No se encontró el reclamo.');
}

?>

<div class="container-fluid">

    <h4 class="mb-4">
        Detalle del reclamo
    </h4>

    <div class="row">

        <div class="col-md-6 mb-3">
            <strong>Código:</strong><br>
            <?= htmlspecialchars($reclamo['codigo_reclamo']) ?>
        </div>

        <div class="col-md-6 mb-3">
            <strong>Fecha de registro:</strong><br>
            <?= date('d/m/Y H:i', strtotime($reclamo['fecha_registro'])) ?>
        </div>

        <div class="col-md-6 mb-3">
            <strong>Tipo:</strong><br>
            <?= htmlspecialchars($reclamo['tipo']) ?>
        </div>

        <div class="col-md-6 mb-3">
            <strong>Estado:</strong><br>
            <?= htmlspecialchars($reclamo['estado']) ?>
        </div>

        <div class="col-12 mb-3">
            <strong>Producto o servicio:</strong><br>
            <?= htmlspecialchars($reclamo['producto_servicio']) ?>
        </div>

        <div class="col-12 mb-3">
            <strong>Descripción:</strong><br>
            <?= nl2br(htmlspecialchars($reclamo['descripcion_bien'])) ?>
        </div>

        <div class="col-12 mb-3">
            <strong>Detalle de la reclamación:</strong><br>
            <?= nl2br(htmlspecialchars($reclamo['detalle'])) ?>
        </div>

        <div class="col-12 mb-3">
            <strong>Pedido del consumidor:</strong><br>
            <?= nl2br(htmlspecialchars($reclamo['pedido'])) ?>
        </div>

        <?php if (!empty($reclamo['respuesta'])): ?>

            <div class="col-12 mt-2">
                <div class="alert alert-success">
                    <strong>Respuesta de Kunanmi:</strong>

                    <p class="mb-1 mt-2">
                        <?= nl2br(htmlspecialchars($reclamo['respuesta'])) ?>
                    </p>

                    <?php if (!empty($reclamo['fecha_respuesta'])): ?>
                        <small class="text-muted">
                            Respondido el
                            <?= date('d/m/Y H:i', strtotime($reclamo['fecha_respuesta'])) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>

            <div class="col-12 mt-2">
                <div class="alert alert-warning">
                    Tu reclamo aún no tiene una respuesta.
                </div>
            </div>

        <?php endif; ?>

    </div>

</div>