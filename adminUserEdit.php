<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$errores = [];
$id=$_GET['id'];

$usuario=detalleUsuario($bd,$id,'usuarios'); 
if ($_POST) {
    modificarUsuarioAdmin($bd,'usuarios',$_POST);
    header('location: adminUserView.php?id=' . $_POST['id']);
    exit;
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
                <h2>Edición de usuarios</h2>

                <form action="" method="POST" enctype="multipart/form-data" class="formEditProd">
                    
                    <label for="id">ID</label>
                    <input type="text" class="form-control" name="id" value="<?= $usuario['id']; ?>" readonly>

                    <label for="nombreUsuario">Nombre del usuario</label>
                    <input type="text" class="form-control" name="nombreUsuario" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>

                    <label for="paternoUsuario">Apellido paterno</label>
                    <input type="text" class="form-control" name="paternoUsuario" value="<?= htmlspecialchars($usuario['apellido_paterno']) ?>" required>

                    <label for="maternoUsuario">Apellido materno</label>
                    <input type="text" class="form-control" name="maternoUsuario" value="<?= htmlspecialchars($usuario['apellido_materno']) ?>" required>

                    <label for="emailUsuario">Email del usuario</label>
                    <input type="text" class="form-control" name="emailUsuario" value="<?= htmlspecialchars($usuario['email']) ?>" required>

                    <label for="celularUsuario">Celular del Usuario</label>
                    <input type="text" class="form-control" name="celularUsuario" value="<?= htmlspecialchars($usuario['celular']) ?>" required>

                    <label for="direccionUsuario">Direccion del Usuario</label>
                    <input type="text" class="form-control" name="direccionUsuario" value="<?= htmlspecialchars($usuario['direccion']) ?>" required>

                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </form>
                

            </section>
        </section>
    </main>

  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectCategoria = document.getElementById('categoriaProducto');
            const selectSubcategoria = document.getElementById('subcategoriaProducto');

            const opcionesSub = Array.from(selectSubcategoria.options);

            selectCategoria.addEventListener('change', () => {
                const categoriaId = selectCategoria.value;

                selectSubcategoria.innerHTML = '<option value="">Seleccione una subcategoría</option>';
                opcionesSub.forEach(option => {
                    if (option.dataset.categoria === categoriaId) {
                        selectSubcategoria.appendChild(option);
                    }
                });
            });
        });

        
        const selectSubcategoria = document.getElementById('subcategoriaProducto');
        const selectSupracategoria = document.getElementById('supracategoriaProducto');
        const opcionesSupra = Array.from(selectSupracategoria.options);

        selectSubcategoria.addEventListener('change', () => {
            const subcategoriaId = selectSubcategoria.value;

            selectSupracategoria.innerHTML = '<option value="">Seleccione una supracategoría</option>';
            opcionesSupra.forEach(option => {
                if (option.dataset.subcategoria === subcategoriaId) {
                selectSupracategoria.appendChild(option);
                }
            });
        });
    </script>

</body>
</html>