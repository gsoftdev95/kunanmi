<?php

require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

$codigo = trim($_GET['codigo'] ?? '');

if ($codigo === '') {
    header('Location: libroReclamaciones.php');
    exit;
}

$sql = "SELECT 
            codigo_reclamo,
            fecha_registro,
            nombres,
            apellido_paterno,
            apellido_materno,
            correo
        FROM libro_reclamaciones
        WHERE codigo_reclamo = :codigo
        LIMIT 1";

$stmt = $bd->prepare($sql);
$stmt->execute([
    ':codigo' => $codigo
]);

$reclamo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reclamo) {
    header('Location: libroReclamaciones.php');
    exit;
}

?>

<!doctype html>
<html lang="es">

<head>
    <?php include_once('./src/partials/head.php') ?>
</head>

<body>

<header>
    <?php include_once('./src/partials/navbar.php') ?>
</header>

<main class="container my-5">

    <section class="libro-container">

        <div class="card shadow-sm text-center p-4">

            <div class="mb-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>

            <h1 class="mb-3">
                Hoja de Reclamación registrada
            </h1>

            <p>
                Su Hoja de Reclamación ha sido registrada correctamente.
            </p>

            <div class="alert alert-success mt-4">

                <h5>
                    Código de reclamo
                </h5>

                <h2 class="fw-bold">
                    <?= htmlspecialchars($reclamo['codigo_reclamo']) ?>
                </h2>

                <p class="mb-0">
                    Conserve este código para futuras consultas relacionadas
                    con su reclamación.
                </p>

            </div>

            <div class="text-start mt-4">

                <p>
                    <strong>Fecha de registro:</strong>
                    <?= date(
                        'd/m/Y H:i',
                        strtotime($reclamo['fecha_registro'])
                    ) ?>
                </p>

                <p>
                    <strong>Consumidor:</strong>
                    <?= htmlspecialchars(
                        $reclamo['nombres'] . ' ' .
                        $reclamo['apellido_paterno'] . ' ' .
                        $reclamo['apellido_materno']
                    ) ?>
                </p>

                <p>
                    <strong>Correo:</strong>
                    <?= htmlspecialchars($reclamo['correo']) ?>
                </p>

            </div>

            <div class="alert alert-secondary mt-4 text-start">

                <strong>Importante:</strong>

                <p class="mb-0 mt-2">
                    Se enviará una copia de la Hoja de Reclamación
                    al correo electrónico proporcionado.
                </p>

            </div>

            <a href="index.php" class="btn btn-success mt-3">
                Volver a Kunanmi
            </a>

        </div>

    </section>

</main>

<footer>
    <?php include_once('./src/partials/footer.php') ?>
</footer>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
    crossorigin="anonymous">
</script>

</body>
</html>