<?php
require_once "keys.example.php"; // Aquí tomará correctamente $_POST[]
$formToken = formToken(); // esta función usa las variables enviadas por POST
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pago Izipay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css">
    <script type="text/javascript"
        src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
        kr-public-key="<?= PUBLIC_KEY ?>"
        kr-post-url-success="result.php"
        kr-language="es-Es">
    </script>
    <link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
    <script type="text/javascript" src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>
</head>
<body>
    <div class="container py-5">
        <h3 class="mb-4">Finaliza tu pago</h3>
        <div class="kr-embedded" kr-form-token="<?= $formToken ?>"></div>
    </div>
</body>
</html>
