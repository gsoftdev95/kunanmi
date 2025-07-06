<?php
require_once('./helpers/dd.php');
require_once('./controladores/funciones.php');
require_once('./src/partials/conexionBD.php');

$errores = [];
$contraseña = "";

if ($_POST) {
    $correo = $_POST['email'] ?? '';
    $contraseña = $_POST['password'] ?? ''; 
    $errores = validarUsuarioLogin($_POST);
    
    if (count($errores) === 0) {
        $usuario = buscarPorEmail($bd, 'usuarios', $correo);
        if ($usuario === false) {
            $errores['email'] = 'Email o contraseña inválidos';
        } else {
            if (password_verify($contraseña, $usuario['contraseña']) === false) { // contraseña es el nombre de la columna de la BD
                $errores['password'] = 'Email o contraseña inválidos';
            } else {
                seteoUsuario($usuario);
                if (isset($_POST['recordarme'])) {
                    seteoCookie($usuario);
                }
                header('Location: index.php');
                exit();
            }
        }
    }
}
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

    <section class="bodyLogin">
        <section class="login-container">
            <h2>Iniciar Sesión</h2>
            <?php if (isset($errores)) : ?>
                <ul>
                    <?php foreach ($errores as $key => $error) : ?>
                        <li class="alert alert-danger"><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="email" name="email" placeholder="Correo electrónico" required>
                <div class="containerpassword">
                    <input type="password" class="mb-0" name="password" id="password" placeholder="Contraseña" required>
                    <button type="button" id="togglePassword" class="botonEyes">
                        <i class="bi bi-eye"></i>
                    </button>  
                </div>
                
                <button type="submit" class="botonIngresar">Ingresar</button>
            </form>

            <div class="form-group remind">
                <div class="checkbox-container">
                    <input type="checkbox" name="recordarme" id="recordarme">
                    <label for="recordarme" class="checkbox-inline mt-2">Recordarme</label>
                </div>
            </div>

            <div class="registro">
                ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
            </div>
        </section>
    </section>

    <footer>
        <?php include_once('./src/partials/footer.php')?>
    </footer>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <!--visulizar contraseña-->
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            // Cambiar el tipo de input entre 'password' y 'text'
            const type = passwordInput.type === 'password' ? 'text' : 'password';
            passwordInput.type = type;

            // Cambiar el ícono según el estado de la contraseña
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    </script>

  </body>
</html>
