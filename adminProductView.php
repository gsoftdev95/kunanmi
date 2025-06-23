<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$id = $_GET['id'];

$productos = detProdForAdmin($bd, $id, 'productos');
//dd($productos);

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
                        <p class="ms-5"><?= $productos['id'] ?></p>
                        <hr>

                        <h5>Nombre:</h5>
                        <p class="ms-5"><?= $productos['nombre'] ?></p>
                        <hr>

                        <h5>Descripción:</h5>
                        <p class="ms-5"><?= $productos['descripcion'] ?></p>
                        <hr>

                        <h5>Precio:</h5>
                        <p class="ms-5"><?= $productos['precio'] ?></p>
                        <hr>

                        <h5>Stock:</h5>
                        <p class="ms-5"><?= $productos['stock'] ?></p>
                        <hr>

                        <h5>Categoria:</h5>
                        <p class="ms-5"><?= $productos['categoria_nombre'] ?></p>
                        <hr>

                        <h5>Sub-categoria:</h5>
                        <p class="ms-5"><?= $productos['subcategoria_nombre'] ?></p>
                        <hr>

                        <h5>Supra-categoria:</h5>
                        <p class="ms-5"><?= $productos['supracategoria_nombre'] ?></p>
                        <hr>

                        <h5>Fecha de creación:</h5>
                        <p class="ms-5"><?= $productos['fecha_creacion'] ?></p>
                        <hr>

                        <h5>Beneficios:</h5>
                        <p class="ms-5"><?= $productos['beneficios'] ?></p>
                        <hr>

                        <h5>Modo de empleo:</h5>
                        <p class="ms-5"><?= $productos['modo_empleo'] ?></p>
                        <hr>

                        <h5>Ingredientes:</h5>
                        <p class="ms-5"><?= $productos['ingredientes'] ?></p>
                        <hr>

                        <h5>Destacado:</h5>
                        <p class="ms-5"><?= $productos['destacado'] ?'sí' : 'No' ?></p>
                        <hr>

                        <h5>Estado:</h5>
                        <p class="ms-5"><?= $productos['estado'] ?></p>
                        <hr>  

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