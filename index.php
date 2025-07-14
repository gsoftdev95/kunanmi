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
        <?php include_once('./src/partials/destacados.php') ?>
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
    <script src='./src/js/carruselDestacado.js'></script>

</script>

    


</body>
</html>