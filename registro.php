<?php
ob_start();
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');

$errores = [];
if ($_POST) {
    $nombre = $_POST['nombre'];    
    $apellidoPaterno = $_POST['apellidoPaterno'];
    $apellidoMaterno = $_POST['apellidoMaterno'];
    $correo = $_POST['email'];
    $celular = $_POST['celular'];
    $direccion = $_POST['direccion'];
    $contraseña = $_POST['password'];

    // Validación del usuario
    $errores = validarUsuario($_POST, $_FILES);
    var_dump($errores);
    if (count($errores) === 0) {
        require_once('./src/partials/conexionBD.php');
        
        // Guardar al usuario
        guardarUsuario($bd, 'usuarios', $_POST);
        //enviarCorreo($_POST);
        header('location: login.php');
        exit();
    }
}
ob_end_flush();
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

    <section class="registro-container">
      <h2>Crear una cuenta</h2>
      <?php if(count($errores)>0) :?>
        <ul class="alert alert-danger">
            <?php foreach ($errores as $key => $error) : ?>
                <li><?= $error?></li>
            <?php endforeach;?>
        </ul>
      <?php endif; ?>

      <form action="" method="POST" class="registro-form">
        <div class="form-group">
          <label for="nombre">Nombre:</label>
          <input type="text" name="nombre" id="nombre" placeholder="Coloque su nombre" value="<?= isset($nombre)?$nombre : '';?>"  required>
        </div>

        <div class="form-group">
          <label for="apellidoPaterno">Apellido paterno:</label>
          <input type="text" name="apellidoPaterno" id="apellidoPaterno" placeholder="coloque su apellido paterno" value="<?= isset($apellidoPaterno)? $apellidoPaterno: '' ?>" required>
        </div>

        <div class="form-group">
          <label for="apellidoMaterno">Apellido materno:</label>
          <input type="text" name="apellidoMaterno" id="apellidoMaterno" placeholder="coloque su apellido materno" value="<?= isset($apellidoMaterno)? $apellidoMaterno:'' ?>" required>
        </div>

        <div class="form-group">
          <label for="celular">Teléfono:</label>
          <input type="tel" name="celular" id="celular" placeholder="Ejm: 987654321" required maxlength="9" pattern="\d{9}" title="Por favor, ingrese exactamente 9 dígitos.">
        </div>


        <div class="form-group">
          <label for="direccion">Dirección:</label>
          <input type="text" name="direccion" id="direccion" placeholder="coloque su direccion completa" value="<?= isset($direccion)? $direccion: '' ?>"required>
        </div>

        <div class="form-group">
          <label for="email">Correo electrónico:</label>
          <input type="email" name="email" id="email" placeholder="example@example.com" value="<?= isset($correo)? $correo: '' ?>"required>
        </div>

        <div class="form-group">
          <label for="password">Contraseña:</label>
          <input type="password" name="password" id="password" placeholder="La contraseña debe tener 6 digitos como mínimo"required>
        </div>

        <div class="form-group">
          <button type="submit">Registrarse</button>
        </div>
      </form>
    </section>

    <footer>
      <?php include_once('./src/partials/footer.php')?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html>
