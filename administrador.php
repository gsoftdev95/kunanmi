<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$totalClientes = contarClientes($bd, 'usuarios');
$totalProductos = contarProductos($bd, 'Productos');
$atributos = obtenerAtributos($bd);
$atributosValores = obtenerAtributosConValores($bd);

//logica para agregar los atributos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_atributo'])) {
    $atributoId = $_POST['atributo_id'];
    $valor = trim($_POST['valor']);

    if ($atributoId && $valor !== '') {
        // Preparamos la consulta SQL para insertar el atributo y su valor
        $stmt = $bd->prepare("
            INSERT INTO valores_atributos (id_atributo, valor) 
            VALUES (:id_atributo, :valor)
        ");

        $stmt->bindValue(':id_atributo', $atributoId, PDO::PARAM_INT);
        $stmt->bindValue(':valor', $valor, PDO::PARAM_STR);
        $stmt->execute();

        echo "<p style='color: green;'>✅ Atributo añadido correctamente.</p>";
    } else {
        echo "<p style='color: red;'>⚠️ Debe seleccionar un atributo y escribir un valor.</p>";
    }
}


//logica para la tabla productos
if (isset($_GET['busquedaProducto']) && trim($_GET['busquedaProducto']) != '') {
    $productos = buscarProductos($bd, 'productos', $_GET['busquedaProducto'], $_GET['tipoBusqueda']);
} else {
    $productos = listarProductos($bd, 'productos');
}
$busquedaActivaProductos = isset($_GET['busquedaProducto']) && trim($_GET['busquedaProducto']) !== '';


//logica para la tabla clientes
if (isset($_GET['busquedaUsuario']) && trim($_GET['busquedaUsuario']) != '') {
    $usuarios = buscarUsuarios($bd, 'usuarios', $_GET['busquedaUsuario'], $_GET['tipoBusqueda']);
} else {
    $usuarios = listarUsuarios($bd, 'usuarios');
}
$busquedaActivaClientes = isset($_GET['busquedaUsuario']) && trim($_GET['busquedaUsuario']) !== '';


//logica para los pedidos
$pedidos = listarPedidos($bd);


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

    <main class="mainAdmin">
        <!-- Sidebar -->
        <?php include_once('./src/partials/asideAdmin.php') ?>

        <!-- Contenido principal -->
        <section class="admin-content">
            <section id="dashboard" class="dashboard">
                <h1>Dashboard</h1>
                <div class="ContainerCardsDashboard">
                    <div class="cardDashboard">
                        <p class="m-0">Total productos:</p>
                        <p class="m-0" style="font-size:3rem"><?= $totalProductos ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Productos activos*:</p>
                        <p class="m-0" style="font-size:3rem">210</p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Productos Destacados*:</p>
                        <p class="m-0" style="font-size:3rem">15</p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Productos con bajo stock*:</p>
                        <p class="m-0" style="font-size:3rem">7</p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Clientes:</p>
                        <p class="m-0" style="font-size:3rem"><?= $totalClientes ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Pedidos pendientes*:</p>
                        <p class="m-0" style="font-size:3rem">5</p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ventas mes anterior(S/)*:</p>
                        <p class="m-0" style="font-size:3rem">10,580</p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ventas mes actual(S/)*:</p>
                        <p class="m-0" style="font-size:3rem">10,580</p>
                    </div>
                </div>
            </section>

            <hr>

            <section id="productos" class="sector">
                <h2>Gestión de productos</h2>
                <p>Aquí puedes registrar, editar o eliminar productos.</p>

                <section class="sectionProductAdmin">
                    <button class="btn btn-link selectAdmin" data-bs-toggle="collapse" data-bs-target="#verAtributos" aria-expanded="false" aria-controls="verAtributos">
                        <span>Atributos</span>
                        <span id="flechaAtributos"><i class="bi bi-caret-down-fill"></i></span>
                    </button>

                    <section id="verAtributos" class="collapse <?= $busquedaActivaAtributos ? 'show' : '' ?> showSelectAdmin">
                        <div class="containerFormAt">
                            <form action="" method="POST" enctype="multipart/form-data" class="formAttributeAdmin">
                                <div>
                                    <label for="añadirAtributo">Atributo</label>
                                    <select id="añadirAtributo" name="atributo_id" class="form-control">
                                        <option value="">Seleccione un atributo</option>
                                        <?php foreach ($atributos as $at): ?>
                                            <option value="<?= $at['id'] ?>"><?= htmlspecialchars($at['nombre']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="valor">Valor</label>
                                    <input type="text" name="valor" class="form-control">
                                </div>
                                <div>
                                    <button type="submit" name="guardar_atributo" class="btn ">Añadir</button>
                                </div>
                            </form>
                        </div>

                        <div class="containerTabAt">
                            <table class="table table-light">
                                <thead>
                                    <th>Atributo</th>
                                    <th>Valor</th>
                                </thead>
                                <tbody>
                                    <?php foreach ($atributosValores as $atributo => $valores): ?>
                                        <?php foreach ($valores as $v): ?>
                                            <tr>
                                                <td class="text-center text-primary-emphasis"><?= ucfirst($atributo) ?></td>
                                                <td class="text-center text-primary-emphasis"><?= ucfirst($v['valor']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </section>

                <section>                    

                    <button class="btn btn-link selectAdmin" data-bs-toggle="collapse" data-bs-target="#verProductos" aria-expanded="false" aria-controls="verProductos">
                        <span>Ver productos</span>
                        <span id="flechaProductos"><i class="bi bi-caret-down-fill"></i></span>
                    </button>

                    <section id="verProductos" class="collapse <?= $busquedaActivaProductos ? 'show' : '' ?> showSelectAdmin">
                        <section class="container-fluid d-flex justify-content-between">
                            <form class="adminSearchForm mt-3 mb-4" role="search" action="#" method="GET">
                                <input class="form-control me-2" type="search" placeholder="Buscador..." aria-label="Search" name="busquedaProducto">
                                <select name="tipoBusqueda" id="tipoBusqueda">
                                    <option class="m-1" value="nombre">Por nombre</option>
                                    <option class="m-1" value="categoria_nombre">Por categoria</option>
                                    <option class="m-1" value="subcategoria_nombre">Por sub categoria</option>
                                    <option class="m-1" value="destacado">Por destacado</option>
                                </select>
                                <button class="btn m-1 btnSearchFrom" data-bs-toggle="collapse" data-bs-target="#verProductos" aria-expanded="<?= $busquedaActivaProductos ? 'true' : 'false' ?>" aria-controls="verProductos">Buscar</button>
                            </form>
                            <div class="mx-2 mt-3 ">
                                <a class="text-decoration-none text-dark" href="adminProductAdd.php"><i class="bi bi-plus-circle-fill"></i> Agregar producto</a>
                            </div>
                        </section>

                        <section class="table-responsive-custom tableAdminProductCont">
                            <table class="tableAdminProduct table table-light">
                                <thead>
                                    <tr>
                                        <th class="text-center">Id</th>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Precio</th>
                                        <th class="text-center">Categoria</th>
                                        <th class="text-center">Sub categoria</th>
                                        <th class="text-center">Stock</th>
                                        <th class="text-center">Estado</th>
                                        <th class="text-center">Destacado</th>
                                        <th class="text-center">Ver</th>
                                        <th class="text-center">Editar</th>
                                        <th class="text-center">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productos as $id => $producto) : ?>
                                        <tr>
                                            <td class="text-center text-primary-emphasis"><?= $producto['id'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $producto['nombre'] ?></td> <!--"nombre" es la columna de la BD-->
                                            <td class="text-center text-primary-emphasis"><?= $producto['precio'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $producto['categoria_nombre'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $producto['subcategoria_nombre'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $producto['stock'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $producto['estado'] ?></td>
                                            <td class="text-center text-primary-emphasis">
                                                <?= $producto['destacado'] ? 'Sí' : 'No' ?>
                                            </td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="AdminProductView.php?id=<?= $producto['id']; ?>"><i class="bi bi-eyeglasses"></i></a></td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="adminProductEdit.php?id=<?= $producto['id']; ?>"><i class="bi bi-pencil-fill"></i></a></td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="adminProductEliminate.php?id=<?= $producto['id']; ?>"><i class="bi bi-trash3"></i></a></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </section>
                    </section>
                </section>                

            </section>

            <hr>

            <section id="clientes" class="sector">
                <h2>Gestión de clientes</h2>
                <p>Lista de usuarios registrados y su actividad.</p>

                <section>
                    <button class="btn btn-link selectAdmin" data-bs-toggle="collapse" data-bs-target="#verClientes" aria-expanded="false" aria-controls="verClientes">
                        <span>Ver clientes</span>
                        <span id="flechaProductos"><i class="bi bi-caret-down-fill"></i></span>
                    </button>

                    <section id="verClientes" class="collapse <?= $busquedaActivaClientes ? 'show' : '' ?> showSelectAdmin">
                        <section class="container-fluid d-flex justify-content-between">
                            <form class="adminSearchForm mt-3 mb-4" role="search" action="#" method="GET">
                                <input class="form-control me-2" type="search" placeholder="Buscador..." aria-label="Search" name="busquedaUsuario">
                                <select name="tipoBusqueda" id="tipoBusqueda">
                                    <option class="m-1" value="nombre">Por nombre</option>
                                    <option class="m-1" value="categoria_nombre">Por categoria</option>
                                    <option class="m-1" value="subcategoria_nombre">Por sub categoria</option>
                                    <option class="m-1" value="destacado">Por destacado</option>
                                </select>
                                <button class="btn m-1 btnSearchFrom" data-bs-toggle="collapse" data-bs-target="#verUsuarios" aria-expanded="<?= $busquedaActivaUsuarios ? 'true' : 'false' ?>" aria-controls="verUsarios">Buscar</button>
                            </form>
                        </section>

                        <section class="table-responsive-custom">
                            <table class="tableAdminUser table table-light">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nombre</th>
                                        <th class="text-center">Ap. paterno</th>
                                        <th class="text-center">Ap. materno</th>
                                        <th class="text-center">email</th>
                                        <th class="text-center">Ver</th>
                                        <th class="text-center">Editar</th>
                                        <th class="text-center">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $id => $usuario) : ?>
                                        <tr>
                                            <td class="text-center text-primary-emphasis"><?= $usuario['nombre'] ?></td> <!--"nombre" es la columna de la BD-->
                                            <td class="text-center text-primary-emphasis"><?= $usuario['apellido_paterno'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $usuario['apellido_materno'] ?></td>
                                            <td class="text-center text-primary-emphasis"><?= $usuario['email'] ?></td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="adminUserView.php?id=<?= $usuario['id']; ?>" clas="iconTabAdmin"><i class="bi bi-eyeglasses"></i></a></td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="adminUserEdit.php?id=<?= $usuario['id']; ?>"><i class="bi bi-pencil-fill"></i></a></td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis"><a href="adminUserEliminate.php?id=<?= $usuario['id']; ?>"><i class="bi bi-trash3"></i></a></td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </section>

                    </section>

                </section>
            </section>

            <hr>

            <section id="pedidos"  class="sector">
                <h2>Gestión de pedidos</h2>
                <p>Controla y actualiza el estado de los pedidos.</p>

                <section class="table-responsive-custom">
                    <table class="tableAdminPedidos table table-light">
                        <thead>
                            <tr>
                                <th class="text-center">Cliente</th>
                                <th class="text-center">Fecha</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Monto total</th>
                                <th class="text-center">Dirección</th>
                                <th class="text-center">Actualizar estado</th>
                                <th class="text-center">Ver detalle</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td class="text-center text-primary-emphasis">
                                        <?= obtenerNombreUsuario($bd, $pedido['usuario_id']) ?>
                                    </td>
                                    <td class="text-center text-primary-emphasis"><?= $pedido['fecha_pedido'] ?></td>
                                    <td class="text-center text-primary-emphasis">
                                        <form action="actualizarEstadoPedido.php" method="POST" class="d-flex justify-content-center align-items-center">
                                            <input type="hidden" name="id_pedido" value="<?= $pedido['id'] ?>">
                                            <select name="estado" class="form-select form-select-sm w-auto">
                                                <?php
                                                $estados = ['pendiente', 'en proceso', 'enviado', 'entregado', 'completado'];
                                                foreach ($estados as $estado) {
                                                    $selected = $pedido['estado'] === $estado ? 'selected' : '';
                                                    echo "<option value=\"$estado\" $selected>$estado</option>";
                                                }
                                                ?>
                                            </select>
                                    </td>
                                    <td class="text-center text-primary-emphasis">S/ <?= number_format($pedido['monto_total'], 2) ?></td>
                                    <td class="text-center text-primary-emphasis"><?= $pedido['direccion_envio'] ?></td>
                                    <td class="text-center text-primary-emphasis">
                                        <button type="submit" class="btn btn-sm btn-primary">Actualizar</button>
                                        </form>
                                    </td>
                                    <td class="text-center text-primary-emphasis">
                                        <a href="detallePedido.php?id=<?= $pedido['id'] ?>"><i class="bi bi-eyeglasses"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </section>
            </section>

            <hr>

            <section id="estadisticas"  class="sector">
                <h2>Estadísticas</h2>
                <p>Visualiza ventas, productos más vendidos y más.</p>
            </section>

        </section>
    </main>

    <footer>
        <?php include_once('./src/partials/footer.php') ?>
    </footer>

    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>


</body>

</html>