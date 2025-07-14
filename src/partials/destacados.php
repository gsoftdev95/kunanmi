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