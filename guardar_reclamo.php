<?php

require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Solo permitir solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: libroReclamaciones.php');
    exit;
}


// 1. RECIBIR Y LIMPIAR DATOS

$tipo_documento = trim($_POST['tipo_documento'] ?? '');
$numero_documento = trim($_POST['numero_documento'] ?? '');

$nombres = trim($_POST['nombres'] ?? '');
$apellido_paterno = trim($_POST['apellido_paterno'] ?? '');
$apellido_materno = trim($_POST['apellido_materno'] ?? '');

$domicilio = trim($_POST['domicilio'] ?? '');

$representante_nombre = trim($_POST['representante_nombre'] ?? '');
$representante_domicilio = trim($_POST['representante_domicilio'] ?? '');
$representante_tipo_documento = trim($_POST['representante_tipo_documento'] ?? '');
$representante_numero_documento = trim($_POST['representante_numero_documento'] ?? '');
$representante_telefono = trim($_POST['representante_telefono'] ?? '');
$representante_correo = trim($_POST['representante_correo'] ?? '');

$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

$tipo_bien = trim($_POST['tipo_bien'] ?? '');
$bien_descripcion = trim($_POST['bien_descripcion'] ?? '');
$monto_reclamado = trim($_POST['monto_reclamado'] ?? '');
$descripcion_bien = trim($_POST['descripcion_bien'] ?? '');

$tipo = trim($_POST['tipo'] ?? '');
$detalle = trim($_POST['detalle'] ?? '');
$pedido = trim($_POST['pedido'] ?? '');

$medio_respuesta = trim($_POST['medio_respuesta'] ?? '');

$declaracion = isset($_POST['declaracion']) ? 1 : 0;

$usuario_id = $_SESSION['id'] ?? null;


// 2. VALIDACIONES BÁSICAS

$errores = [];

// Datos del consumidor
if (!in_array($tipo_documento, ['DNI', 'CE', 'PASAPORTE'])) {
    $errores[] = 'Tipo de documento inválido.';
}

if ($numero_documento === '') {
    $errores[] = 'Debe ingresar el número de documento.';
}

if ($nombres === '') {
    $errores[] = 'Debe ingresar sus nombres.';
}

if ($apellido_paterno === '') {
    $errores[] = 'Debe ingresar su apellido paterno.';
}

if ($apellido_materno === '') {
    $errores[] = 'Debe ingresar su apellido materno.';
}

if ($domicilio === '') {
    $errores[] = 'Debe ingresar su domicilio.';
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $errores[] = 'El correo electrónico no es válido.';
}

if ($telefono === '') {
    $errores[] = 'Debe ingresar su teléfono.';
}


// Producto / servicio
if (!in_array($tipo_bien, ['Producto', 'Servicio'])) {
    $errores[] = 'Debe seleccionar el tipo de bien contratado.';
}

if ($bien_descripcion === '') {
    $errores[] = 'Debe indicar el producto o servicio.';
}

if ($descripcion_bien === '') {
    $errores[] = 'Debe ingresar la descripción del producto o servicio.';
}


// Reclamo
if (!in_array($tipo, ['RECLAMO', 'QUEJA'])) {
    $errores[] = 'Debe seleccionar Reclamo o Queja.';
}

if ($detalle === '') {
    $errores[] = 'Debe ingresar el detalle de la reclamación o queja.';
}

if ($pedido === '') {
    $errores[] = 'Debe ingresar el pedido del consumidor.';
}


// Medio de respuesta
if (!in_array($medio_respuesta, ['correo', 'domicilio'])) {
    $errores[] = 'Debe seleccionar el medio de respuesta.';
}


// Declaración
if ($declaracion !== 1) {
    $errores[] = 'Debe aceptar la declaración de veracidad de los datos.';
}


// 3. VALIDAR MONTO

if ($monto_reclamado !== '') {
    if (!is_numeric($monto_reclamado) || $monto_reclamado < 0) {
        $errores[] = 'El monto reclamado no es válido.';
    }
} else {
    $monto_reclamado = null;
}

// 4. DATOS DEL REPRESENTANTE
// Estos campos son opcionales.
// Si el consumidor es menor de edad, deberán completarse.

// ======================================================
// 4. DATOS DEL REPRESENTANTE
// ======================================================

// Si se ha ingresado cualquier dato del representante,
// se considera que el bloque debe estar completo.

$hayRepresentante =
    $representante_nombre !== '' ||
    $representante_domicilio !== '' ||
    $representante_tipo_documento !== '' ||
    $representante_numero_documento !== '' ||
    $representante_telefono !== '' ||
    $representante_correo !== '';

if ($hayRepresentante) {
    if ($representante_nombre === '') {
        $errores[] = 'Debe ingresar los nombres y apellidos del representante.';
    }
    if ($representante_domicilio === '') {
        $errores[] = 'Debe ingresar el domicilio del representante.';
    }
    if (!in_array( $representante_tipo_documento, ['DNI', 'CE', 'PASAPORTE']  )) {
        $errores[] = 'Debe seleccionar un tipo de documento válido para el representante.';
    }
    if ($representante_numero_documento === '') {
        $errores[] = 'Debe ingresar el número de documento del representante.';
    }
    if ($representante_telefono === '') {
        $errores[] = 'Debe ingresar el teléfono del representante.';
    }
    if ($representante_correo === '') {
        $errores[] = 'Debe ingresar el correo del representante.';
    } elseif (!filter_var($representante_correo, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'El correo del representante no es válido.';
    }
}


// 5. SI EXISTEN ERRORES

if (!empty($errores)) {

    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head>';
    echo '<body>';

    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger">';
    echo '<h4>Se encontraron los siguientes errores:</h4>';
    echo '<ul>';

    foreach ($errores as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }

    echo '</ul>';
    echo '<a href="javascript:history.back()" class="btn btn-secondary">';
    echo 'Volver al formulario';
    echo '</a>';
    echo '</div>';
    echo '</div>';

    echo '</body>';
    echo '</html>';

    exit;
}


// 6. GENERAR CÓDIGO ÚNICO DEL RECLAMO
// Ejemplo: KUN-20260824-000123

$fechaCodigo = date('Ymd');

try {
    // Generamos una parte aleatoria para evitar códigos repetidos
    $codigo_reclamo = 'KUN-' . $fechaCodigo . '-' . strtoupper(
        substr(bin2hex(random_bytes(4)), 0, 6)
    );


    // 7. INSERTAR RECLAMO
    $sql = "INSERT INTO libro_reclamaciones (
                usuario_id,
                codigo_reclamo,
                tipo_documento,
                numero_documento,
                nombres,
                apellido_paterno,
                apellido_materno,
                domicilio,

                representante_nombre,
                representante_domicilio,
                representante_tipo_documento,
                representante_numero_documento,
                representante_telefono,
                representante_correo,

                correo,
                telefono,

                producto_servicio,
                tipo_bien,
                monto_reclamado,
                descripcion_bien,

                tipo,
                detalle,
                pedido,

                medio_respuesta,
                declaracion,

                estado
            ) VALUES (
                :usuario_id,
                :codigo_reclamo,
                :tipo_documento,
                :numero_documento,
                :nombres,
                :apellido_paterno,
                :apellido_materno,
                :domicilio,

                :representante_nombre,
                :representante_domicilio,
                :representante_tipo_documento,
                :representante_numero_documento,
                :representante_telefono,
                :representante_correo,

                :correo,
                :telefono,

                :producto_servicio,
                :tipo_bien,
                :monto_reclamado,
                :descripcion_bien,

                :tipo,
                :detalle,
                :pedido,

                :medio_respuesta,
                :declaracion,

                'PENDIENTE'
            )";

    $stmt = $bd->prepare($sql);

    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':codigo_reclamo' => $codigo_reclamo,

        ':tipo_documento' => $tipo_documento,
        ':numero_documento' => $numero_documento,

        ':nombres' => $nombres,
        ':apellido_paterno' => $apellido_paterno,
        ':apellido_materno' => $apellido_materno,

        ':domicilio' => $domicilio,

        ':representante_nombre' => $representante_nombre ?: null,
        ':representante_domicilio' => $representante_domicilio ?: null,
        ':representante_tipo_documento' => $representante_tipo_documento ?: null,
        ':representante_numero_documento' => $representante_numero_documento ?: null,
        ':representante_telefono' => $representante_telefono ?: null,
        ':representante_correo' => $representante_correo ?: null,

        ':correo' => $correo,
        ':telefono' => $telefono,

        ':producto_servicio' => $bien_descripcion,
        ':tipo_bien' => $tipo_bien,
        ':monto_reclamado' => $monto_reclamado,
        ':descripcion_bien' => $descripcion_bien,

        ':tipo' => $tipo,
        ':detalle' => $detalle,
        ':pedido' => $pedido,

        ':medio_respuesta' => $medio_respuesta,
        ':declaracion' => $declaracion
    ]);


    // 8. OBTENER ID GENERADO

    $id_reclamo = $bd->lastInsertId();


    // 9. REDIRIGIR A CONFIRMACIÓN
    header(
        'Location: confirmacion_reclamo.php?codigo=' .
        urlencode($codigo_reclamo)
    );

    exit;


} catch (PDOException $e) {

    // Registrar el error real en el servidor
    error_log(
        'Error al registrar Libro de Reclamaciones: ' .
        $e->getMessage()
    );

    // Mostrar mensaje amigable al usuario
    echo '<!DOCTYPE html>';
    echo '<html lang="es">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Error</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '</head>';
    echo '<body>';

    echo '<div class="container mt-5">';
    echo '<div class="alert alert-danger text-center">';
    echo '<h4>No fue posible registrar la Hoja de Reclamación.</h4>';
    echo '<p>Por favor, inténtelo nuevamente más tarde.</p>';
    echo '<a href="javascript:history.back()" class="btn btn-secondary">';
    echo 'Volver';
    echo '</a>';
    echo '</div>';
    echo '</div>';

    echo '</body>';
    echo '</html>';

    exit;
}
?>