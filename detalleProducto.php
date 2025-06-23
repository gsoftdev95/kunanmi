<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');


$idProducto = isset($_GET['id']) ? $_GET['id'] : null;
if (!$idProducto) {
    echo "Producto no encontrado.";
    exit;
}

$producto = obtenerProductoPorId($bd, $idProducto);
if (!$producto) {
    echo "Producto no encontrado.";
    exit;
}

$opiniones = obtenerOpiniones($bd);

$imagenes = !empty($producto['imagen']) ? json_decode($producto['imagen'],true) : [];
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

        <section class="navegacion">
            <section class="navegacionInner">
                <a href="./index.php">Inicio</a> / <a href="./tienda.php">Tienda</a>/Producto
            </section>
        </section>

        <section class="detalleProducto">
            <section class="detalleProductoInner">
                <section class="containerProducto">                    
                    <div id="carouselExampleIndicators" class="carousel slide containerImg">
                        <div class="carousel-indicators">
                            <?php if (!empty($imagenes) && is_array($imagenes)): ?>
                                <?php foreach ($imagenes as $index => $imagen): ?>
                                    <button 
                                        type="button" 
                                        data-bs-target="#carouselExampleIndicators" 
                                        data-bs-slide-to="<?= $index ?>" 
                                        class="<?= $index === 0 ? 'active' : '' ?>" 
                                        aria-current="<?= $index === 0 ? 'true' : 'false' ?>" 
                                        aria-label="Slide <?= $index + 1 ?>">
                                    </button>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <div class="carousel-inner">
                            <?php if (!empty($imagenes) && is_array($imagenes)): ?>
                                <?php foreach ($imagenes as $index => $imagen): ?>
                                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                                        <img src="./src/imgBD/Productos/<?= htmlspecialchars($imagen) ?>" class="d-block imgDetProd" alt="Imagen <?= $index + 1 ?>">
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="carousel-item active">
                                    <img src="./src/imgBD/Productos/default.jpg" class="d-block imgDetProd" alt="Imagen por defecto">
                                </div>
                            <?php endif; ?>
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                    
                    <section class="infoProduct">
                        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>
                        <h4>S/. <?= htmlspecialchars($producto['precio']) ?></h4>
                        <hr>
                        <div class="beneficios">Descripción</div>
                        <div class="beneficiosP"><?= htmlspecialchars($producto['descripcion']) ?></div>
                        <div class="contador">
                        <button type="button" class="btn-decrementar">−</button>
                        <input type="text" class="input-cantidad" value="1" readonly>
                        <button type="button" class="btn-incrementar">+</button>
                        </div>
                    
                        <a href="#" class="btn btn-agregar-carrito">Añadir al carrito <i class="bi bi-cart"></i></a>

                    </section>
                </section>
            
                <div class="tabsdetailsProducts">
                    <ul class="nav nav-tabs" id="infoTab" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#beneficios">Beneficios</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#uso">Modo de empleo</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ingredientes">Ingredientes</button>
                        </li>
                    </ul>
                    <div class="tab-content p-3 border">
                        <div class="tab-pane fade show active" id="beneficios"><?= htmlspecialchars($producto['beneficios']) ?></div>
                        <div class="tab-pane fade" id="uso"><?= htmlspecialchars($producto['modo_empleo']) ?></div>
                        <div class="tab-pane fade" id="ingredientes"><?= htmlspecialchars($producto['ingredientes']) ?></div>
                    </div>
                </div>

            </section>        
        </section>

        <section class="OpinionProducto">
            <section class="OpinionProductoInner">
                <h4>Opiniones de clientes</h4>
                <?php if (count($opiniones) > 0): ?>
                    <?php foreach ($opiniones as $op): ?>
                        <div class="opinion">
                            <strong><?= htmlspecialchars($op['nombre']) ?> <?= htmlspecialchars($op['apellido_paterno']) ?></strong>
                            <p><?= htmlspecialchars($op['opinion']) ?></p>
                            <small><?= htmlspecialchars($op['fecha']) ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aún no hay opiniones para este producto.</p>
                <?php endif; ?> 
            </section>            
        </section>
                    
        <?php if (isset($_SESSION['id'])): ?>
        <section class="formOpinion">
            <section class="formOpinionInner my-5">
                <h4 class="mb-4">Deja tu opinión</h4>
                <form id="form-opinion" method="POST">
                    <input type="hidden" name="usuario_id" value="<?= $_SESSION['id'] ?>">

                    <textarea name="opinion" required class="form-control" rows="4" placeholder="Escribe tu opinión..."></textarea>

                    <div class="rating my-3">
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="valoracion" id="estrella<?= $i ?>" value="<?= $i ?>" required>
                            <label for="estrella<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>

                    <button type="submit" class="btn btn-primary">Enviar</button>

                    <div id="popup-exito" style="display: none; color: green; margin-top: 10px;">
                        ¡Gracias por tu opinión!
                    </div>
                </form>
            </section>
        </section>
        <?php else: ?>
            <p style="text-align:center; margin: 2rem 0;">Debes <a href="login.php">iniciar sesión</a> para dejar una opinión.</p>
        <?php endif; ?>




        <section class="highlights">
            <section class="highlightsInner">
                <div class="highlightsTitle">Productos destacados</div>
                <div class="highlightsSwiper swiper mySwiper">
                    <div class="swiper-wrapper">
                        <!-- Card 1 -->
                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>

                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>
                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>
                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>
                        <div class="swiper-slide cardHL">
                            <img src="./src/img/velas.png" alt="">                    
                            <div class="card-body cardProductText">
                                <h4 class="card-title cardProductTextTitle">Nombre del producto 1</h4>
                                <p class="card-text cardProductTextSub">categoría</p>
                                <p class="card-text cardProductTextPrice">S/ 25.00</p>
                                <a href="#" class="btn cardProductTextBut">añadir al carrito <i class="bi bi-cart"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Controles -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </section>      
        </section>

        

        <footer>
            <?php include_once('./src/partials/footer.php')?>
        </footer>





        <!--Boostrap-->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

        <!--Script de incremento de cantidad-->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const btnIncrementar = document.querySelector('.btn-incrementar');
                const btnDecrementar = document.querySelector('.btn-decrementar');
                const inputCantidad = document.querySelector('.input-cantidad');

                btnIncrementar.addEventListener('click', () => {
                inputCantidad.value = parseInt(inputCantidad.value) + 1;
                });

                btnDecrementar.addEventListener('click', () => {
                if (parseInt(inputCantidad.value) > 1) {
                    inputCantidad.value = parseInt(inputCantidad.value) - 1;
                }
                });
            });
        </script>


        <!--scrip para destacados-->
        <script>
            const swiper = new Swiper('.highlightsSwiper', {
                slidesPerView: 4,          // Cuántas se ven al mismo tiempo
                slidesPerGroup: 1,         // Cuántas se deslizan a la vez
                spaceBetween: 10,          // Espacio entre tarjetas
                navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                0: { slidesPerView: 1 },
                576: { slidesPerView: 2 },
                768: { slidesPerView: 3 },
                992: { slidesPerView: 4 }
                },
            });
        </script>

        <!--formulario opiniones-->
        <script>
        document.getElementById('form-opinion')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('guardar_opinion.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.ok ? res.text() : Promise.reject())
            .then(() => {
                const popup = document.getElementById('popup-exito');
                popup.style.display = 'block';
                setTimeout(() => popup.style.display = 'none', 3000);
                document.getElementById('form-opinion').reset();
            })
            .catch(() => alert('Error al enviar tu opinión'));
        });
        </script>






    </body>
</html>