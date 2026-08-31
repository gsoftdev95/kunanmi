<?php

require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['reclamo_id'])) {
    echo '<div class="alert alert-danger">Solicitud no válida.</div>';
    exit;
}

$reclamo_id = (int) $_POST['reclamo_id'];

$stmt = $bd->prepare("SELECT * FROM libro_reclamaciones WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $reclamo_id]);

$reclamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclamo) {
    echo '<div class="alert alert-danger">No se encontró el reclamo.</div>';
    exit;
}

?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">Detalle de reclamo</h4>
            <small class="text-muted">Código: <?= htmlspecialchars($reclamo['codigo_reclamo']) ?></small>
        </div>

        <span class="badge bg-warning text-dark">
            <?= htmlspecialchars($reclamo['estado']) ?>
        </span>
    </div>

    <div class="row g-3">

        <!-- DATOS DEL CONSUMIDOR -->
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Datos del consumidor</strong>
                </div>

                <div class="card-body">

                    <p class="mb-2">
                        <strong>Documento:</strong>
                        <?= htmlspecialchars($reclamo['tipo_documento']) ?> -
                        <?= htmlspecialchars($reclamo['numero_documento']) ?>
                    </p>

                    <p class="mb-2">
                        <strong>Nombres:</strong>
                        <?= htmlspecialchars($reclamo['nombres']) ?>
                    </p>

                    <p class="mb-2">
                        <strong>Apellidos:</strong>
                        <?= htmlspecialchars($reclamo['apellido_paterno']) ?>
                        <?= htmlspecialchars($reclamo['apellido_materno'] ?? '') ?>
                    </p>

                    <p class="mb-2">
                        <strong>Domicilio:</strong>
                        <?= htmlspecialchars($reclamo['domicilio']) ?>
                    </p>

                    <p class="mb-2">
                        <strong>Teléfono:</strong>
                        <?= htmlspecialchars($reclamo['telefono'] ?? '') ?>
                    </p>

                    <p class="mb-0">
                        <strong>Correo:</strong>
                        <?= htmlspecialchars($reclamo['correo']) ?>
                    </p>

                </div>
            </div>
        </div>

        <!-- REPRESENTANTE -->
        <?php if (!empty($reclamo['representante_nombre'])): ?>

        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <strong>Representante del menor</strong>
                </div>

                <div class="card-body">

                    <p class="mb-2">
                        <strong>Nombre:</strong>
                        <?= htmlspecialchars($reclamo['representante_nombre']) ?>
                    </p>

                    <p class="mb-2">
                        <strong>Documento:</strong>
                        <?= htmlspecialchars($reclamo['representante_tipo_documento'] ?? '') ?> -
                        <?= htmlspecialchars($reclamo['representante_numero_documento'] ?? '') ?>
                    </p>

                    <p class="mb-2">
                        <strong>Domicilio:</strong>
                        <?= htmlspecialchars($reclamo['representante_domicilio'] ?? '') ?>
                    </p>

                    <p class="mb-2">
                        <strong>Teléfono:</strong>
                        <?= htmlspecialchars($reclamo['representante_telefono'] ?? '') ?>
                    </p>

                    <p class="mb-0">
                        <strong>Correo:</strong>
                        <?= htmlspecialchars($reclamo['representante_correo'] ?? '') ?>
                    </p>

                </div>
            </div>
        </div>

        <?php endif; ?>

        <!-- PRODUCTO / SERVICIO -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <strong>Identificación del bien contratado</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <strong>Tipo:</strong>
                            <?= htmlspecialchars($reclamo['tipo_bien']) ?>
                        </div>

                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <strong>Producto / Servicio:</strong>
                            <?= htmlspecialchars($reclamo['producto_servicio']) ?>
                        </div>

                        <div class="col-12 col-md-4">
                            <strong>Monto reclamado:</strong>
                            <?= $reclamo['monto_reclamado'] !== null ? 'S/ ' . number_format($reclamo['monto_reclamado'], 2) : 'No especificado' ?>
                        </div>

                    </div>

                    <hr>

                    <strong>Descripción:</strong>
                    <p class="mb-0 mt-2">
                        <?= nl2br(htmlspecialchars($reclamo['descripcion_bien'])) ?>
                    </p>

                </div>
            </div>
        </div>

        <!-- RECLAMACIÓN -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <strong>Detalle de la reclamación</strong>
                </div>

                <div class="card-body">

                    <p class="mb-3">
                        <strong>Tipo:</strong>
                        <?= htmlspecialchars($reclamo['tipo']) ?>
                    </p>

                    <strong>Detalle:</strong>
                    <div class="bg-light border rounded p-3 mt-2 mb-3 text-break">
                        <?= nl2br(htmlspecialchars($reclamo['detalle'])) ?>
                    </div>

                    <strong>Pedido del consumidor:</strong>
                    <div class="bg-light border rounded p-3 mt-2 text-break">
                        <?= nl2br(htmlspecialchars($reclamo['pedido'])) ?>
                    </div>

                </div>
            </div>
        </div>

        <!-- INFORMACIÓN FINAL -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="row">

                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <strong>Medio de respuesta:</strong><br>
                            <?= htmlspecialchars($reclamo['medio_respuesta']) ?>
                        </div>

                        <div class="col-12 col-md-4 mb-3 mb-md-0">
                            <strong>Fecha de registro:</strong><br>
                            <?= htmlspecialchars($reclamo['fecha_registro']) ?>
                        </div>

                        <div class="col-12 col-md-4">
                            <strong>Estado:</strong><br>
                            <?= htmlspecialchars($reclamo['estado']) ?>
                        </div>

                    </div>

                    <?php if (!empty($reclamo['respuesta'])): ?>

                        <hr>

                        <strong>Respuesta registrada:</strong>

                        <div class="bg-light border rounded p-3 mt-2">
                            <?= nl2br(htmlspecialchars($reclamo['respuesta'])) ?>
                        </div>

                        <?php if (!empty($reclamo['fecha_respuesta'])): ?>
                            <small class="text-muted">
                                Fecha de respuesta:
                                <?= htmlspecialchars($reclamo['fecha_respuesta']) ?>
                            </small>
                        <?php endif; ?>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>


    <!-- form de respuesta -->
    <hr>

    <h5 class="mt-4">Gestión del reclamo</h5>

    <form id="formGestionReclamoAdmin">
        <input type="hidden" name="reclamo_id" value="<?= $reclamo['id'] ?>">

        <div class="mb-3">
            <label for="estado_reclamo" class="form-label">
                <strong>Estado del reclamo</strong>
            </label>
            <select class="form-select" id="estado_reclamo" name="estado" required>
                <option value="PENDIENTE" <?= $reclamo['estado'] === 'PENDIENTE' ? 'selected' : '' ?>>Pendiente</option>
                <option value="EN_PROCESO" <?= $reclamo['estado'] === 'EN_PROCESO' ? 'selected' : '' ?>>En proceso</option>
                <option value="ATENDIDO" <?= $reclamo['estado'] === 'ATENDIDO' ? 'selected' : '' ?>>Atendido</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="respuesta_reclamo" class="form-label"> <strong>Respuesta al consumidor</strong> </label>
            <textarea class="form-control" id="respuesta_reclamo" name="respuesta" rows="5" placeholder="Ingrese la respuesta al consumidor..."><?= htmlspecialchars($reclamo['respuesta'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Guardar cambios
        </button>
    </form>
</div>