<?php

require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

header('Content-Type: application/json; charset=utf-8');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud no válida.'
    ]);
    exit;
}


// Recibir datos
$reclamo_id = (int) ($_POST['reclamo_id'] ?? 0);
$estado = trim($_POST['estado'] ?? '');
$respuesta = trim($_POST['respuesta'] ?? '');


// Validar ID
if ($reclamo_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Reclamo no válido.'
    ]);
    exit;
}


// Validar estado
$estadosPermitidos = [
    'PENDIENTE',
    'EN_PROCESO',
    'ATENDIDO'
];

if (!in_array($estado, $estadosPermitidos, true)) {
    echo json_encode([
        'success' => false,
        'message' => 'Estado no válido.'
    ]);
    exit;
}


try {

    // Si el estado es ATENDIDO y existe una respuesta,
    // registramos la fecha de respuesta.
    if ($estado === 'ATENDIDO' && $respuesta !== '') {
        $sql = "UPDATE libro_reclamaciones
                SET estado = :estado,
                    respuesta = :respuesta,
                    fecha_respuesta = NOW()
                WHERE id = :id";
    } else {
        $sql = "UPDATE libro_reclamaciones
                SET estado = :estado,
                    respuesta = :respuesta
                WHERE id = :id";
    }


    $stmt = $bd->prepare($sql);

    $stmt->execute([
        ':estado' => $estado,
        ':respuesta' => $respuesta !== '' ? $respuesta : null,
        ':id' => $reclamo_id
    ]);


    echo json_encode([
        'success' => true,
        'message' => 'Reclamo actualizado correctamente.'
    ]);

} catch (PDOException $e) {

    error_log(
        'Error al actualizar reclamo: ' . $e->getMessage()
    );

    echo json_encode([
        'success' => false,
        'message' => 'No fue posible actualizar el reclamo.'
    ]);
}

exit;