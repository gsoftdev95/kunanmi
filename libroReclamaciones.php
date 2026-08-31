<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
?>

<!doctype html>
<html lang="es">

<head>
    <?php include_once('./src/partials/head.php') ?>
</head>

<body>

    <header>
        <?php include_once('./src/partials/navbar.php') ?>
    </header>

    <main class="container my-5">

        <section class="libro-container">

            <!-- ENCABEZADO -->
            <div class="libro-header text-center">
                <img src="./imagenes/libroRec.png" alt="Libro de Reclamaciones" class="img-fluid mb-3" style="max-width: 150px;" >
                <h1>Libro de Reclamaciones</h1>
                <p> Conforme a lo establecido en el Código de Protección y Defensa del Consumidor</p>
            </div>

            <!-- DATOS DEL PROVEEDOR -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Datos del proveedor</strong>
                </div>
                <div class="card-body">
                    <p class="mb-1">
                        <strong>Razón social / Nombre comercial:</strong> KUNANMI
                    </p>
                    <p class="mb-1">
                        <strong>RUC:</strong> 20615749461
                    </p>
                    <p class="mb-1">
                        <strong>Domicilio:</strong>
                        Av. 28 de Julio 462, Int. 210, Miraflores, Lima, Perú
                    </p>
                    <p class="mb-0">
                        <strong>Correo electrónico:</strong>
                        Kunanmipe@gmail.com
                    </p>
                    <p class="mb-0">
                        <strong>Fecha:</strong>
                        <?= date('d/m/Y') ?>
                    </p>
                </div>
            </div>

            <!-- FORMULARIO -->
            <form action="guardar_reclamo.php" method="POST" class="border p-4 rounded bg-light">

                <!-- IDENTIFICACIÓN DEL CONSUMIDOR -->
                <h4 class="mb-3">
                    1. Identificación del consumidor reclamante
                </h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tipo_documento" class="form-label"> Tipo de documento </label>
                        <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                            <option value="">Seleccione</option>
                            <option value="DNI">DNI</option>
                            <option value="CE">Carné de Extranjería</option>
                            <option value="PASAPORTE">Pasaporte</option>
                        </select>
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="numero_documento" class="form-label"> Número de documento </label>
                        <input type="text" class="form-control" id="numero_documento" name="numero_documento" maxlength="20" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nombres" class="form-label"> Nombres </label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required >
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_paterno" class="form-label"> Apellido paterno </label>
                        <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_materno" class="form-label"> Apellido materno </label>
                        <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="domicilio" class="form-label"> Domicilio </label>
                    <input type="text" class="form-control" id="domicilio" name="domicilio" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label"> Teléfono </label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="correo" class="form-label"> Correo electrónico </label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                </div>
                
                <!-- REPRESENTANTE DEL MENOR -->
                <div class="card px-3">
                    <div class="mt-4 mb-3">
                        <h5>Datos del padre, madre o representante</h5>
                        <small class="text-muted"> Completar únicamente si el consumidor reclamante es menor de edad. </small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="representante_nombre" class="form-label"> Nombres y apellidos </label>
                            <input type="text" class="form-control" id="representante_nombre" name="representante_nombre" maxlength="150" >
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="representante_domicilio" class="form-label"> Domicilio </label>
                            <input type="text" class="form-control" id="representante_domicilio" name="representante_domicilio" maxlength="255" >
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="representante_tipo_documento" class="form-label"> Tipo de documento </label>
                            <select class="form-select" id="representante_tipo_documento" name="representante_tipo_documento" >
                                <option value="">Seleccione</option>
                                <option value="DNI">DNI</option>
                                <option value="CE">Carné de Extranjería</option>
                                <option value="PASAPORTE">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="representante_numero_documento" class="form-label">
                                Número de documento
                            </label>
                            <input type="text" class="form-control" id="representante_numero_documento" name="representante_numero_documento" maxlength="30" >
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="representante_telefono" class="form-label">
                                Teléfono
                            </label>
                            <input type="text" class="form-control" id="representante_telefono" name="representante_telefono" maxlength="30" >
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="representante_correo" class="form-label">
                            Correo electrónico
                        </label>
                        <input type="email" class="form-control" id="representante_correo" name="representante_correo" maxlength="150" >
                    </div>
                </div>
                

                <!-- IDENTIFICACIÓN DEL BIEN -->
                <h4 class="mt-4 mb-3"> 2. Identificación del bien contratado </h4>
                <div class="mb-3">
                    <label class="form-label"> Tipo de bien contratado </label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo_bien" id="producto" value="Producto" required >
                            <label class="form-check-label" for="producto" > Producto </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipo_bien" id="servicio" value="Servicio" >
                            <label class="form-check-label" for="servicio" > Servicio </label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bien_descripcion" class="form-label"> Producto o servicio </label>
                    <input type="text" class="form-control" id="bien_descripcion" name="bien_descripcion" required >
                </div>

                <div class="mb-3">
                    <label for="monto_reclamado" class="form-label"> Monto reclamado (S/) </label>
                    <input type="number" class="form-control" id="monto_reclamado" name="monto_reclamado" min="0" step="0.01" >
                </div>

                <div class="mb-3">
                    <label for="descripcion_bien" class="form-label"> Descripción del producto o servicio </label>
                    <textarea class="form-control" id="descripcion_bien" name="descripcion_bien" rows="3" required ></textarea>
                </div>

                <!-- RECLAMACIÓN -->
                <h4 class="mt-4 mb-3">
                    3. Detalle de la reclamación y pedido del consumidor
                </h4>
                <div class="mb-3">
                    <label for="tipo" class="form-label"> Tipo de solicitud </label>
                    <select class="form-select" id="tipo" name="tipo" required >
                        <option value=""> Seleccione </option>
                        <option value="RECLAMO">Reclamo</option>
                        <option value="QUEJA">Queja</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="detalle" class="form-label"> Detalle de la reclamación o queja </label>
                    <textarea class="form-control" id="detalle" name="detalle" rows="5" required ></textarea>
                </div>

                <div class="mb-3">
                    <label for="pedido" class="form-label"> Pedido del consumidor </label>
                    <textarea class="form-control" id="pedido" name="pedido" rows="4" required ></textarea>
                </div>

                <!-- MEDIO DE RESPUESTA -->
                <div class="mb-3">
                    <label for="medio_respuesta" class="form-label"> Medio para recibir la respuesta </label>
                    <select class="form-select" id="medio_respuesta" name="medio_respuesta" required >
                        <option value=""> Seleccione </option>
                        <option value="correo"> Correo electrónico </option>
                        <option value="domicilio"> Domicilio </option>
                    </select>
                </div>

                <!-- DECLARACIÓN -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="declaracion" name="declaracion" value="1" required >
                    <label class="form-check-label" for="declaracion" >
                        Declaro que los datos consignados en esta Hoja de Reclamación
                        son verdaderos y correctos.
                    </label>
                </div>

                <!-- INFORMACIÓN LEGAL -->
                <div class="alert alert-secondary mt-4" role="alert" style="font-size: 0.9rem; line-height: 1.5;" >
                    <p>
                        <strong>RECLAMO:</strong>
                        Disconformidad relacionada con los productos o servicios.
                    </p>
                    <p>
                        <strong>QUEJA:</strong>
                        Disconformidad no relacionada con los productos o servicios,
                        o malestar o descontento respecto a la atención al público.
                    </p>
                    <p>
                        La formulación del reclamo no impide acudir a otras vías de
                        solución de controversias ni constituye un requisito previo
                        para interponer una denuncia ante el INDECOPI.
                    </p>
                    <p class="mb-0">
                        Kunanmi deberá dar respuesta al reclamo o queja en un plazo
                        máximo de <strong>15 días hábiles improrrogables</strong>.
                    </p>
                </div>

                <!-- BOTÓN -->
                <button type="submit" class="btn btn-danger w-100" >
                    Registrar Hoja de Reclamación
                </button>
            </form>


            <!-- AVISO INFORMATIVO -->
            <div class="mt-4 text-muted" style="font-size: 0.9rem;">
                <p>
                    Una vez registrada la Hoja de Reclamación, se generará una
                    constancia con el número correspondiente y se enviará una copia
                    al correo electrónico proporcionado por el consumidor.
                </p>
            </div>
        </section>
    </main>


    <footer>
        <?php include_once('./src/partials/footer.php') ?>
    </footer>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO"
        crossorigin="anonymous">
    </script>

</body>
</html>