<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

if (!isset($_SESSION['id'])) {
    http_response_code(403); // Prohibido si no está logueado
    exit;
}

$usuario_id = $_SESSION['id'];
$opinion = $_POST['opinion'] ?? null;
$valoracion = $_POST['valoracion'] ?? null;

if (!$opinion || !$valoracion) {
    http_response_code(400); // Solicitud incorrecta
    echo "Datos incompletos";
    exit;
}

$sql = "INSERT INTO opiniones (usuario_id, opinion, valoracion, fecha)
        VALUES (?, ?, ?, NOW())";

$stmt = $bd->prepare($sql);
$stmt->execute([$usuario_id, $opinion, $valoracion]);

echo "ok"; // Usado por el fetch() de JavaScript para mostrar el popup
?>
