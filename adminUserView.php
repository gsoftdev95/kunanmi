<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$id = $_GET['id'];

$usuarios = detUserForAdmin($bd, $id, 'usuarios');
//dd($usuarios);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>

    <header>
        <?php include_once('./src/partials/navbar.php')?>
    </header>
    
    <main class="mainAdmin">
        <!-- Sidebar -->
        <?php include_once('./src/partials/asideAdmin.php')?>

        <section class="bodyManupulation">
            <section class="bodyManupulationInner">
                <h2>Informacion del Producto</h2>

                <section id="registrarProductos" class="containerView">
                    <section class="containerViewInner">
                        <h6>Id:</h5>
                        <p class="ms-5"><?= $usuarios['id'] ?></p>
                        <hr>

                        <h5>Nombre:</h5>
                        <p class="ms-5"><?= $usuarios['nombre'] ?></p>
                        <hr>

                        <h5>Apellido paterno:</h5>
                        <p class="ms-5"><?= $usuarios['apellido_paterno'] ?></p>
                        <hr>

                        <h5>Apellido materno:</h5>
                        <p class="ms-5"><?= $usuarios['apellido_materno'] ?></p>
                        <hr>

                        <h5>Email:</h5>
                        <p class="ms-5"><?= $usuarios['email'] ?></p>
                        <hr>

                        <h5>celular:</h5>
                        <p class="ms-5"><?= $usuarios['celular'] ?></p>
                        <hr>

                        <h5>Dirección:</h5>
                        <p class="ms-5"><?= $usuarios['direccion'] ?></p>
                        <hr>

                        <h5>Fecha de creación:</h5>
                        <p class="ms-5"><?= $usuarios['fecha_creacion'] ?></p>
                        <hr>


                    </section>
                </section>

            </section>
        </section>
    </main>
    
    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>

    
    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>