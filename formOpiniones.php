<?php
require_once('./src/partials/conexionBD.php');

$nombre = $_POST['nombre'];
$apellido_p = $_POST['apellido_p'];
$apellido_m = $_POST['apellido_m'];
$opinion = $_POST['opinion'];
$producto_id = $_POST['producto_id'];

$sql = "INSERT INTO opiniones (usuario_id, producto_id, opinion, fecha)
        VALUES (NULL, ?, ?, NOW())";

$stmt = $bd->prepare($sql);
$stmt->execute([$producto_id, $opinion]);

header("Location: producto.php?id=" . $producto_id);
exit;
?>

<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
    
    <header>
        <?php include_once('./src/partials/navbar.php')?>
    </header>



    <section>
        <form action="guardar_opinion.php" method="POST">
            <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>"> 
            <label>Nombre:</label>
            <input type="text" name="nombre" required>

            <label>Apellido Paterno:</label>
            <input type="text" name="apellido_p" required>

            <label>Apellido Materno:</label>
            <input type="text" name="apellido_m" required>

            <label>Tu opinión:</label>
            <textarea name="opinion" required></textarea>

            <button type="submit">Enviar opinión</button>
        </form>

    </section>
  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>