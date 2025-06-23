<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');


$usuario = obtenerUsuarioPorId($bd, $_SESSION['id']);

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

    
    <main class="container">
        <section class="perfil-container">
            <h2 class="text-center mb-4">Mi Perfil</h2>
            
            <div class="perfil-item">
                <strong>Nombre:</strong>
                <span><?= htmlspecialchars($usuario['nombre']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Apellido paterno:</strong>
                <span><?= htmlspecialchars($usuario['apellido_paterno']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Apellido materno:</strong>
                <span><?= htmlspecialchars($usuario['apellido_materno']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Email:</strong>
                <span><?= htmlspecialchars($usuario['email']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Teléfono:</strong>
                <span><?= htmlspecialchars($usuario['telefono']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Dirección:</strong>
                <span><?= htmlspecialchars($usuario['direccion']) ?></span>
            </div>

            <div class="perfil-item">
                <strong>Perfil:</strong>
                <span>
                    <?= $usuario['perfil'] == 9 ? 'Administrador' : 'Usuario' ?>
                </span>
            </div>

            <div class="perfil-item">
                <strong>Fecha de creación:</strong>
                <span><?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?></span>
            </div>

            <div class="text-center mt-4">
                <a href="editarPerfil.php?id=<?= $usuario['id'] ?>" class="btn btn-primary">Editar Perfil</a>
            </div>
        </section>
    </main>
  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>