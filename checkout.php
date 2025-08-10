<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');

require_once "keys.example.php";

$total = isset($_SESSION['total_carrito']) ? floatval($_SESSION['total_carrito']) : 0;
$amountInCents = intval($total * 100);


$formToken = formToken();

$productos = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];
?>


<!DOCTYPE html>
<html>

<head>
  <title>Form Token</title>
  <link rel='stylesheet' href='css/style.css' />
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Bootstrap -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/journal/bootstrap.min.css"
      integrity="sha384-QDSPDoVOoSWz2ypaRUidLmLYl4RyoBWI44iA5agn6jHegBxZkNqgm2eHb6yZ5bYs" crossorigin="anonymous" /> 
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <!-- Libreria JS de la pasarela, debe incluir la clave pública REEMPLAZAR -->
  <script type="text/javascript"
          src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
          kr-public-key="88053210:testpublickey_dCjlTRaikHpm9sryZllbtWKYPAveQZ9IGz2eiKZyP29nw" 
          kr-post-url-success="result.php" kr-language="es-Es"> 
  </script>

  <!-- Estilos de la pasarela de pagos -->
  <link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.css">
  <script type="text/javascript" src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>
</head>
<body>
  <nav class="navbar bg-primary" style="background-color: #FF2D46!important;">
    <div class="container-fluid">
        <a href="/" class="navbar-brand mb-1"><img src="https://iziweb001b.s3.amazonaws.com/webresources/img/logo.png" width="80"></a>
    </div>
  </nav>
  <section class="container">
    <div class="row">
      <div class="col-md-3"></div>
      <div class="center-column col-md-6">
        <section class="payment-form">
          <div class="row">
            <li>Pago con tarjeta de crédito/débito</li>
            <img src="https://github.com/izipay-pe/Imagenes/blob/main/logo_tarjetas_aceptadas/logo-tarjetas-aceptadas-351x42.png?raw=true" alt="Tarjetas aceptadas" style="width: 200px;">
          </div>
          <hr>
          <div id="micuentawebstd_rest_wrapper">
            <div class="card mb-4">
              <div class="card-header">
                <strong>Resumen de tu compra</strong>
              </div>
              <ul class="list-group list-group-flush">
                <?php if (!empty($productos)): ?>
                  <?php foreach ($productos as $producto): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <?= htmlspecialchars($producto['nombre']) ?> x <?= $producto['cantidad'] ?>
                      <span>S/ <?= number_format($producto['precio'] * $producto['cantidad'], 2) ?></span>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">No hay productos cargados</li>
                <?php endif; ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <strong>Total:</strong>
                  <strong>S/ <?= number_format($total, 2) ?></strong>
                </li>
              </ul>
            </div>
            
            <!-- HTML para incrustar pasarela de pagos -->
            <div class="kr-embedded" kr-form-token="<?= $formToken; ?>"></div>
            <!---->
          </div>
        </section>
      </div>
      <div class="col-md-3"></div>
    </div>
  </section>
</body>
</html>
