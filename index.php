<?php
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Traer productos destacados
$destacados = obtenerProductosDestacados($bd);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>
   
    <header>    
        <section class="contTopBar">
            <div class="topBar">Hecho con amor — Productos artesanales y naturales</div>
        </section>
        <?php include_once('./src/partials/navbar.php')?>
    </header>

    <div class=bodyCover>
        <img src="./src/img/portada.jpg" alt="imagen de portada" width="100%" loading="lazy">
        <div class="bodyCoverText">
            <div class="bodyCoverTextSlogan">Bienestar natural en cada vela</div>
            <a href="./tienda.php"><div class="bodyCoverTextButtom"> Ver tienda <i class="bi bi-bag"></i></div></a>
        </div>
    </div>

    <section class="highlights">
        <section class="highlightsInner">
            <div class="highlightsTitle">Productos destacados</div>
            <div class="highlightsSwiper swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($destacados as $row): ?>
                    <div class="swiper-slide cardProduct">
                        <a href="./detalleProducto.php?id=<?= $row['id']; ?>">
                            <div class="imgProdShopCont">
                                <?php 
                                    $imagenes = json_decode($row['imagen'], true);
                                    $primeraImagen = is_array($imagenes) && count($imagenes) > 0 ? htmlspecialchars($imagenes[0]) : 'default.jpg';
                                ?>
                                <img class="img-default" src="src/imgBD/Productos/<?= $primeraImagen ?>" alt="<?= htmlspecialchars($row['nombre']) ?>">
                                <div class="hoverShop">
                                    <a href="#" class="btn cardProductTextBut mb-2">añadir al carrito <i class="bi bi-cart"></i></a>
                                    <a href="./detalleProducto.php?id=<?= $row['id'] ?>" class="btn cardProductTextBut">ver Producto</a>
                                </div>
                            </div>                                    
                        </a>
                        <div class="card-body cardProductText">
                            <div class="card-title cardProductTextTitle"><?= $row['nombre']; ?></div>
                            <div class="card-text cardProductTextSub"><?= $row['subcategoria_nombre']; ?></div>
                            <div class="card-text cardProductTextPrice m-0">S/. <?= number_format($row['precio'], 2); ?></div>                                    
                        </div>
                    </div>
                    <?php endforeach; ?>                    
                </div>

                <!-- Controles -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </section>      
    </section>

    <section class="categoryIconContainer">
        <section class="categoryIconCards">            
            <section class="CategoryIcon1">
                <i class="bi bi-brush"></i>
                <div>Artesanal</div>
            </section>
            <section class="CategoryIcon1">
                <i class="bi bi-leaf"></i>
                <div>Natural </div>
            </section>        
            <section class="CategoryIcon1">
                <i class="bi bi-heart"></i>
                <div>Bienestar</div>
            </section>            
        </section>
    </section>

    <section class="review">
        <div class=containerReview>
            <img src="./src/img/reseña.jpg" alt="">
            <div>
                <h2>KUNANMI</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste tenetur nam repellat nobis! Nemo consequatur exercitationem consectetur totam modi molestias excepturi voluptas, dignissimos iure quibusdam quas officia veritatis ipsum reiciendis maxime a ducimus. Mollitia sunt minus culpa, nisi beatae veritatis porro quaerat possimus, totam doloribus nesciunt aliquam facere quos accusantium!</p>
            </div>        
        </div>
    </section>

    <section class="containerComment row col-sm-6 mb-3 mb-sm-0">
        <div>Testimonios</div>
        <section class="containerCommentInner">
            <div class="card cardComment " style="width: 22rem;">
                <div class="card-body">
                    <h5 class="card-title">Nombre Apellido </h5>
                    <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam nemo rem ullam, quo ipsam voluptate dolores ex corporis? Minima, repellendus.</p>
                </div>
                <ul class="list-group list-group-flush ">
                    <li class="list-group-item  " style="background-color: transparent;">01/02/2025</li>
                </ul>        
            </div>
            <div class="card cardComment" style="width: 22rem;">
                <div class="card-body">
                    <h5 class="card-title">Nombre Apellido </h5>
                    <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam nemo rem ullam, quo ipsam voluptate dolores ex corporis? Minima, repellendus.</p>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item " style="background-color: transparent;">01/02/2025</li>
                </ul>        
            </div>
            <div class="card cardComment" style="width: 22rem;">
                <div class="card-body">
                    <h5 class="card-title">Nombre Apellido </h5>
                    <p class="card-text">Lorem ipsum dolor sit amet consectetur adipisicing elit. Laboriosam nemo rem ullam, quo ipsam voluptate dolores ex corporis? Minima, repellendus.</p>
                </div>
                <ul class="list-group list-group-flush" >
                    <li class="list-group-item" style="background-color: transparent;font-size: 0.8rem;">01/02/2025</li>
                </ul>        
            </div>
        </section>      
    </section>

    <section class="containerSolgan">
        <section class="containerSolganInner">
            <div class="slg1">Lorem ipsum dolor sit amet..</div>
            <div class="slg2">Lorem ipsum dolor sit amet.</div>
        </section>
    </section>

    <section class="containerGalery">
        <section class="containerGaleryInner">
            <section class="containerGaleryGrid">
                <div class="imgkunanmi1">img1</div>
                <div class="imgkunanmi2">img2</div>
                <div class="imgkunanmi3">img3</div>
                <div class="imgkunanmi4">img4</div>
                <div class="imgkunanmi5">img5</div>
                <div class="imgkunanmi6">img6</div>
            </section>   
        </section>         
    </section>

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>

    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    <!--carrusel destacado-->                
    <script>
        const swiper = new Swiper('.highlightsSwiper', {
            loop: true, // 🔁 Activa el loop infinito

            slidesPerView: 'auto',
            spaceBetween: 10, //Espacio entre cada tarjeta
            slidesPerGroup: 1, //Se desliza de una tarjeta en una

            navigation: {
                nextEl: '.swiper-button-next', //
                prevEl: '.swiper-button-prev', //
            },

            //Ajustes responsive
            breakpoints: {
                0: {
                    slidesPerView: 1.2,
                },
                450: {
                    slidesPerView: 2.5,
                },
                768: {
                    slidesPerView: 3.5,
                },
                1024: {
                    slidesPerView: 4.5,
                },
                1400: {
                    slidesPerView: 5.5,
                }
            }
        });
    </script>

</script>

    


</body>
</html>