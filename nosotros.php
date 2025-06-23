<!doctype html>
<html lang="es">
<head>
    <?php include_once('./src/partials/head.php')?>
</head>
<body>    
    
    <header>
        <?php include_once('./src/partials/navbar.php')?>
    </header>

    <section class="nosotros container py-5">
        <div class="row align-items-center">
            <div class=" col-md-6 mb-4 mb-md-0">
                <img src="./src/img/equipo.png" alt="Equipo de trabajo artesanal" class="imgNosotros img-fluid rounded">
            </div>
            <div class="col-md-6">
                <h2 class="TitleNosotros mb-3">Sobre Nosotros</h2>
                <p>
                    En <strong>Kunanmi</strong>, creemos en el poder de lo natural. Nuestra pasión por el cuidado personal y el respeto por el medio ambiente nos llevó a crear una línea de productos artesanales, elaborados a mano con ingredientes conscientes y de alta calidad.
                </p>
                <p>
                    Cada vela, jabón o crema que producimos es el resultado de un proceso cuidadoso, ético y lleno de propósito. Nos inspira el bienestar real: ese que se logra cuando cuidamos de nuestro cuerpo, nuestro hogar y nuestro entorno.
                </p>
            </div>
        </div>

        <div class="containerFilosofia row text-center">
            <div class="col-md-4">
                <img src="./src/iconos/flaticon/natural.png" alt="Icono natural" class="mb-2" style="width: 60px;">
                <h5>Ingredientes Naturales</h5>
                <p>Usamos materias primas naturales y sostenibles.</p>
            </div>
            <div class="col-md-4">
                <img src="./src/iconos/flaticon/mano.png" alt="Icono hecho a mano" class="mb-2" style="width: 60px;">
                <h5>Hecho a Mano</h5>
                <p>Todo es producido artesanalmente con dedicación.</p>
            </div>
            <div class="col-md-4">
                <img src="./src/iconos/flaticon/bio.png" alt="Icono ecológico" class="mb-2" style="width: 60px;">
                <h5>Eco-Conscientes</h5>
                <p>Minimizamos el impacto ambiental en cada etapa.</p>
            </div>
        </div>

        <div class="containerSloganNosotros text-center">
            <blockquote class="blockquote">
                <p class="mb-0">“Creamos bienestar, cuidando de ti y del planeta”</p>
            </blockquote>
        </div>
    </section>





  

    

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>





    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

</body>
</html>