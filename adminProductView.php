<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$id = $_GET['id'];

$productos = detProdForAdmin($bd, $id, 'productos');
if (!$productos) {
    header('Location: administrador.php');
    exit;
}
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
                        <section class="ViewDetailsAdminA">
                            <div class="containerInputDetailsAdmin">
                                <h5>Id:</h5>
                                <p class=""><?= $productos['id'] ?></p>
                            </div>                        
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Nombre:</h5>
                                <p class=""><?= $productos['nombre'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Descripción:</h5>
                                <ul class="">
                                    <?php
                                        if (!empty($productos['descripcion'])) {
                                            // Decodificar JSON a array
                                            $descripcionArray = json_decode($productos['descripcion'], true);

                                            if (is_array($descripcionArray)) {
                                                foreach ($descripcionArray as $descripcion) {
                                                    echo "<li>" . htmlspecialchars(trim($descripcion)) . "</li>";
                                                }
                                            } else {
                                                echo "<p>Error al leer las descripcion.</p>";
                                            }
                                        } else {        
                                            echo "<p>No hay descripcion del producto.</p>";
                                        }
                                    ?>
                                </ul>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Precio:</h5>
                                <p class=""><?= $productos['precio'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Stock:</h5>
                                <p class=""><?= $productos['stock'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Categoria:</h5>
                                <p class=""><?= $productos['categoria_nombre'] ?></p>                        
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Sub-categoria:</h5>
                                <p class=""><?= $productos['subcategoria_nombre'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Supra-categoria:</h5>
                                <p class=""><?= $productos['supracategoria_nombre'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Fecha de creación:</h5>
                                <p class=""><?= $productos['fecha_creacion'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Beneficios:</h5>
                                <?php
                                    $beneficios = $productos['beneficios'] ?? '';

                                    if (trim($beneficios) !== '') {
                                        $beneficiosArray = json_decode($beneficios, true);

                                        if (json_last_error() === JSON_ERROR_NONE && is_array($beneficiosArray)) {
                                            if (!empty($beneficiosArray)) {
                                                echo '<ul>';
                                                foreach ($beneficiosArray as $beneficio) {
                                                    if (is_string($beneficio) || is_numeric($beneficio)) {
                                                        echo '<li>' . htmlspecialchars(trim((string) $beneficio), ENT_QUOTES, 'UTF-8') . '</li>';
                                                    }
                                                }
                                                echo '</ul>';
                                            } else {
                                                echo '<p>No hay beneficios registrados.</p>';
                                            }
                                        } else {
                                            // Compatibilidad con productos antiguos que guardan texto normal.
                                            echo '<p>' . nl2br(htmlspecialchars($beneficios, ENT_QUOTES, 'UTF-8')) . '</p>';
                                        }
                                    } else {
                                        echo '<p>No hay beneficios registrados.</p>';
                                    }
                                ?>
                            </div>
                            <hr>
                        </section>

                        <section class="ViewDetailsAdminB">
                            <div class="containerInputDetailsAdmin">
                                <h5>Modo de empleo:</h5>
                                <ul class="">
                                    <?php
                                        if (!empty($productos['modo_empleo'])) {
                                            // Decodificar JSON a array
                                            $modoEmpleoArray = json_decode($productos['modo_empleo'], true);

                                            if (is_array($modoEmpleoArray)) {
                                                foreach ($modoEmpleoArray as $empleo) {
                                                    echo "<li>" . htmlspecialchars(trim($empleo)) . "</li>";
                                                }
                                            } else {
                                                echo "<p>Error al leer el modo de empleo .</p>";
                                            }
                                        } else {        
                                            echo "<p>No hay modo de empleo del producto.</p>";
                                        }
                                    ?>
                                </ul>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Ingredientes:</h5>
                                <p class=""><?= $productos['ingredientes'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Destacado:</h5>
                                <p class=""><?= $productos['destacado'] ?'sí' : 'No' ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Estado:</h5>
                                <p class=""><?= $productos['estado'] ?></p>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Atributos:</h5>
                                <?php if (!empty($productos['atributos'])): ?>
                                    <ul class=" ulAtributoDetail">
                                        <?php foreach ($productos['atributos'] as $nombreAtributo => $valores): ?>
                                            <li><strong><?= htmlspecialchars($nombreAtributo) ?>:</strong> <?= implode(', ', array_map('htmlspecialchars', $valores)) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="">Sin atributos asignados.</p>
                                <?php endif; ?>
                            </div>
                            <hr>

                            <div class="containerInputDetailsAdmin">
                                <h5>Imagenes:</h5>
                                <div class="d-flex flex-wrap mb-5">
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
                            </div>

                        </section>                        
                    </section>

                    <div class="containerbSecond">
                        <a href="adminProductEdit.php?id=<?= $productos['id']; ?>"  class="bSecond">Editar producto</a>
                        <a href="adminProductAdd.php"  class="bSecond2">Agregar otro producto</a>
                        <a href="administrador.php"  class="bSecond2">Regresar</a>
                    </div>
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