<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$id = $_GET['id'];

$productos = detProdForAdmin($bd, $id, 'productos');

if($_POST){
    eliminarProducto($bd,'productos',$_POST);
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
                                <input type="text" class="form-control" name="id" value="<?= $productos['id'];?>" readonly>                        

                                <label for="nombreProducto">Nombre del producto</label>
                                <input type="text" class="form-control" name="nombreProducto" value="<?= $productos['nombre'];?>" readonly>

                                <label for="precioProducto">Precio del producto</label>
                                <input type="text" class="form-control" name="precioProducto" value="<?= $productos['precio'];?>" readonly>

                                <label for="stockProducto">Stock del producto</label>
                                <input type="text" class="form-control" name="stockProducto" value="<?= $productos['stock'];?>" readonly>

                                <label for="categoriaProducto">Categoria</label>
                                <input type="text" class="form-control" name="categoriaProducto" value="<?= $productos['categoria_nombre'];?>" readonly>
                                
                                <label for="subcategoriaProducto">Subcategoría</label>
                                <input type="text" class="form-control" name="subcategoriaProducto" value="<?= $productos['subcategoria_nombre'];?>" readonly>
                                
                                <label for="supraProducto">Supra-categoria</label>
                                <input type="text" class="form-control" name="supraProducto" value="<?= $productos['supracategoria_nombre'];?>" readonly>
                                
                                <label for="destacado">Destacado</label>
                                <input type="text" class="form-control" name="destacado" value="<?= $productos['destacado'] ?'sí' : 'No';?>" readonly>
                                
                                <label for="estadoProducto">Estado del producto</label>
                                <p class="ms-5"><?= $productos['estado'] ?></p>
                                <input type="text" class="form-control" name="estadoProducto" value="<?= $productos['estado'];?>" readonly>
                                
                                <h5>Imagenes:</h5>
                                <div class="d-flex flex-wrap">
                                    <?php if (!empty($productos['imagen']) && is_array($productos['imagen'])): ?>
                                        <?php foreach ($productos['imagen'] as $imagen): ?>
                                            <figure class="m-2">
                                                <img class="" src="./src/imgBD/Productos/<?= $imagen ?>" alt="<?= $productos['nombre']; ?>" style="max-width: 150px; max-height: 150px;">
                                            </figure>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p>No hay imágenes disponibles.</p>
                                    <?php endif; ?>
                                </div>

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