<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$id = $_GET['id'];

$usuarios = detUserForAdmin($bd, $id, 'usuarios');

if($_POST){
    eliminarUsuario($bd,'usuarios',$_POST);
    header('location:administrador.php');
}

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


    <main class="mainAdmin">
        <!-- Sidebar -->
        <?php include_once('./src/partials/asideAdmin.php')?>

        <section class="bodyManupulation">
            <section class="bodyManupulationInner">
                <h2>Eliminar Producto</h2>

                <section id="registrarProductos" class="containerView">
                    <section class="containerViewInner">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="id">ID</label>
                                <input type="text" class="form-control mb-3" name="id" value="<?= $usuarios['id'];?>" readonly>                        

                                <label for="nombreUsuario">Nombre del usuario</label>
                                <input type="text" class="form-control mb-3" name="nombreUsuario" value="<?= $usuarios['nombre'];?>" readonly>

                                <label for="apPaterno">Apellido paterno</label>
                                <input type="text" class="form-control mb-3" name="apPaterno" value="<?= $usuarios['apellido_paterno'];?>" readonly>

                                <label for="apMaterno">Apellido materno</label>
                                <input type="text" class="form-control mb-3" name="apMaterno" value="<?= $usuarios['apellido_materno'];?>" readonly>

                                <label for="emailUsuario">Email del usuario</label>
                                <input type="text" class="form-control mb-3" name="emailUsuario" value="<?= $usuarios['email'];?>" readonly>
                                
                                <label for="celularUsuario">Celular</label>
                                <input type="text" class="form-control mb-3" name="celularUsuario" value="<?= $usuarios['celular'];?>" readonly>
                                
                                <label for="direccionUsuario">Direccion del usuario</label>
                                <input type="text" class="form-control mb-3" name="direccionUsuario" value="<?= $usuarios['direccion'];?>" readonly>

                                <button class="btnEliminateP">eliminar</button>    
                            </div>
                        </form>
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