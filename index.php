<?php
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

// Traer productos destacados
$destacados = obtenerProductosDestacados($bd);

$testimonios = obtenerTestimonios($bd);

$opiniones = obtenerOpiniones($bd);
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
        <img src="./src/img/portadaCliente2.png" alt="imagen de portada" loading="lazy">
        <div class="bodyCoverText">
            <div class="bodyCoverTextSlogan">​La esencia de la naturaleza en tu piel y tu hogar.</div>
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
            <div class="textReview">
                <h2>KUNANMI</h2>
                <p>En <strong>Kunanmi</strong>, creemos en el poder de lo natural. Nuestra pasión por el cuidado personal y el respeto por el medio ambiente nos llevó a crear una línea de productos artesanales, elaborados a mano con ingredientes conscientes y de alta calidad. Cada vela, jabón o crema que producimos es el resultado de un proceso cuidadoso, ético y lleno de propósito. Nos inspira el bienestar real: ese que se logra cuando cuidamos de nuestro cuerpo, nuestro hogar y nuestro entorno.</p>
            </div>        
        </div>
    </section>

    <section class="containerComment">        
        <section class="containerCommentInner">
            <div class="commentTitle">Testimonios</div>
            <div class="commentBox">
                <?php if(count($testimonios) > 0): ?>
                    <?php foreach ($testimonios as $tes): ?>
                        <div class="card cardComment">
                            
                            <h5 class="card-title"><?= htmlspecialchars($tes['nombre'])?> <?= htmlspecialchars($tes['apellido_paterno']) ?></h5>

                            <div class="valoracion">
                                <?php
                                $valoracion = isset($tes['valoracion']) ? (int)$tes['valoracion'] : 0;
                                for ($i = 1; $i <= 5; $i++):
                                    if ($i <= $valoracion):
                                        echo '<span class="estrella">&#9733;</span>'; // estrella llena
                                    else:
                                        echo '<span class="estrella vacia">&#9734;</span>'; // estrella vacía
                                    endif;
                                endfor;
                                ?>
                            </div>

                            <p class="cardCommentOpinion"><?= htmlspecialchars($tes['opinion']) ?></p>
                        
                            <ul class="list-group list-group-flush" >
                                <li class="list-group-item" style="background-color: transparent;font-size: 0.8rem;">
                                    <?= date("d/m/Y", strtotime($tes['fecha'])) ?>
                                </li>
                            </ul>        
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p> Aú no hay testimonios de los clientes</p>
                <?php endif ?>
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
    
    <!--animación iconos del index-->   
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const elementos = document.querySelectorAll(".CategoryIcon1");

            const opciones = {
                root: null,
                threshold: 0.5 // 50% del elemento visible
            };

            const observer = new IntersectionObserver((entradas) => {
                entradas.forEach(entrada => {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add("animate");
                        // Si no quieres que se repita la animación al hacer scroll:
                        observer.unobserve(entrada.target);
                    }
                });
            }, opciones);

            elementos.forEach(el => observer.observe(el));
        });
    </script>

    <!--animación del review-->   
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const elementos = document.querySelectorAll(".textReview")
            const opciones = {root: null, threshold: 0.5};

            const observer = new IntersectionObserver((entradas) => {
                entradas.forEach(entrada => {
                    if (entrada.isIntersecting) {
                        entrada.target.classList.add("animate2");
                        
                        observer.unobserve(entrada.target);
                    }
                });
            }, opciones);

            elementos.forEach(el => observer.observe(el));

        });

    </script>


</script>

    


</body>
</html>