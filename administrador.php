<?php
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('./src/partials/conexionBD.php');
require_once('controladores/controlAcceso.php');

$totalClientes = contarClientes($bd, 'usuarios');
$totalProductos = contarProductos($bd, 'productos');
$totalProductosActivos = contarProductosactivos($bd, 'productos');
$totalDestacados = contarDestacados($bd, 'productos');
$ProductosSinStock = contarProductosSinStock($bd, 'productos');
$mostrarProductosSinStock = ListarProductosSinStock($bd);
$PedidosPendientes = contarPedidosPendientes($bd, 'pedidos');
$ventasMesAnterior = obtenerVentasMesAnterior($bd);
$ventasMesActual = obtenerVentasMesActual($bd);
$ingresosMesAnterior = obtenerIngresosMesAnterior($bd);
$ingresosMesActual = obtenerIngresosMesActual($bd);
$atributos = obtenerAtributos($bd);
$atributosValores = obtenerAtributosConValores($bd);
// Reclamos del Libro de Reclamaciones
$reclamos = obtenerReclamos($bd);
$totalReclamosPendientes = contarReclamosPendientes($bd);

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

//logica para actualizar estados de pedidos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['accion'] === 'cambiar_estado_pedido') {

    $pedido_id = (int) $_POST['pedido_id'];
    $nuevo_estado = (int) $_POST['nuevo_estado'];

    $stmt = $bd->prepare("
        UPDATE pedidos
        SET estado_id = :estado_id
        WHERE id = :id
    ");

    $stmt->bindValue(':estado_id', $nuevo_estado, PDO::PARAM_INT);
    $stmt->bindValue(':id', $pedido_id, PDO::PARAM_INT);
    $stmt->execute();

    // Obtener las nuevas opciones según el nuevo estado
    $opciones = obtenerOpcionesEstado($nuevo_estado);

    // Obtener los nombres de esos estados
    $estadosDisponibles = obtenerEstadosPorIds($bd, $opciones);

    echo json_encode([
        'success' => true,
        'message' => 'Estado actualizado',
        'nuevo_estado' => $nuevo_estado,
        'estados' => $estadosDisponibles
    ]);

    exit;
}
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
                        <p class="m-0"><?= $totalProductos ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Productos activos:</p>
                        <p class="m-0"> <?= $totalProductosActivos ?> </p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Productos Destacados:</p>
                        <p class="m-0"> <?= $totalDestacados ?> </p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Clientes:</p>
                        <p class="m-0"><?= $totalClientes ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Pedidos pendientes:</p>
                        <p class="m-0"><?= $PedidosPendientes ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ventas mes anterior(S/):</p>
                        <p class="m-0"><?= $ventasMesAnterior ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ventas mes actual(S/):</p>
                        <p class="m-0"><?= $ventasMesActual ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ingresos mes anterior(S/):</p>
                        <p class="m-0"><?= $ingresosMesAnterior ?></p>
                    </div>
                    <div class="cardDashboard">
                        <p class="m-0">Ingresos mes actual(S/):</p>
                        <p class="m-0"><?= $ingresosMesActual ?></p>
                    </div>
                    <div class="cardDashboard cardReclamo">
                        <p class="m-0">reclamo(s) pendiente(s) de atención.</p>
                        <p class="m-0"><?= $totalReclamosPendientes ?></p>                        
                    </div>
                    <div class="cardDashboard cardDashboardStock" id="cardDashboardStock">
                        <p class="m-0">Productos con bajo stock (&lt;6 unds.)</p>
                        <p class="m-0">
                            <?= $ProductosSinStock ?>
                        </p>

                        <div class="tooltipStock" id="tooltipStock">
                            <strong>Productos con bajo stock</strong>
                            <?php if (!empty($mostrarProductosSinStock)): ?>
                                <ul>
                                    <?php foreach ($mostrarProductosSinStock as $producto): ?>
                                        <li>
                                            <?= htmlspecialchars($producto['nombre']) ?>
                                            (<?= $producto['stock'] ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p>No hay productos con bajo stock.</p>
                            <?php endif; ?>
                        </div>
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
                        <section class="container-fluid fromybtnproductoadmin">
                            <form class="adminSearchForm" role="search" action="#" method="GET">
                                <input class="form-control me-2" type="search" placeholder="Buscador..." aria-label="Search" name="busquedaProducto">
                                <select name="tipoBusqueda" id="tipoBusqueda">
                                    <option class="m-1" value="nombre">Por nombre</option>
                                    <option class="m-1" value="categoria_nombre">Por categoria</option>
                                    <option class="m-1" value="subcategoria_nombre">Por sub categoria</option>
                                    <option class="m-1" value="destacado">Por destacado</option>
                                </select>
                                <button class="btn btnSearchFrom" data-bs-toggle="collapse" data-bs-target="#verProductos" aria-expanded="<?= $busquedaActivaProductos ? 'true' : 'false' ?>" aria-controls="verProductos">Buscar</button>
                            </form>
                            <div class="mx-2 addproductadmin">
                                <a class="text-decoration-none text-dark" href="adminProductAdd.php"><i class="bi bi-plus-circle-fill"></i> Agregar producto</a>
                            </div>
                        </section>

                        <section class=" tableAdminProductCont">
                            <table class="table table-hover tableAdminProduct ">
                                <thead>
                                    <tr class="table-secondary">
                                        <th class="text-center">Id</th> <!--1-->
                                        <th class="text-center">Nombre</th> <!--2-->
                                        <th class="text-center">Precio</th> <!--3-->
                                        <th class="text-center">Categoria</th> <!--4-->
                                        <th class="text-center">Sub categoria</th> <!--5-->
                                        <th class="text-center">Stock</th> <!--6-->
                                        <th class="text-center">Estado</th> <!--7-->
                                        <th class="text-center">Destacado</th> <!--8-->
                                        <th class="text-center">Acciones</th> <!--9-->
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
                                            <td class="text-center">
                                                <label class="switchEstadoProducto">
                                                    <input type="checkbox" class="inputEstadoProducto" data-id="<?= $producto['id'] ?>" <?= $producto['estado'] === 'activo' ? 'checked' : '' ?> >
                                                    <span class="sliderEstadoProducto"></span>
                                                </label>
                                            </td>
                                            <td class="text-center text-primary-emphasis">
                                                <?= $producto['destacado'] ? 'Sí' : 'No' ?>
                                            </td>
                                            <!-- Envío de ID por Query String -->
                                            <td class="text-center text-primary-emphasis">
                                                <a href="AdminProductView.php?id=<?= $producto['id']; ?>"><i class="bi bi-eyeglasses"></i></a>
                                                <a href="adminProductEdit.php?id=<?= $producto['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="adminProductEliminate.php?id=<?= $producto['id']; ?>"><i class="bi bi-trash3"></i></a>
                                            </td>
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

                        <section class="tableAdminUserCont">
                            <table class="tableAdminUser table table-light">
                                <thead>
                                    <tr class="table-secondary">
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

                <section class="showSelectAdmin">
                    <section class="tableAdminPedidosCont">
                        <table class="tableAdminPedidos table table-hover">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="text-center">ID orden</th>
                                    <th class="text-center">Cliente</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Monto total</th>
                                    <th class="text-center">Dirección</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pedidos as $pedido): ?>
                                    <tr class="table-light">
                                        <td class="text-center text-primary-emphasis">
                                            <?= $pedido['order_id'] ?>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            <?= obtenerNombreUsuario($bd, $pedido['usuario_id']) ?>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            <?= $pedido['fecha_pedido'] ?>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            <form action="./administrador.php#pedidos" method="POST" class="form-estado d-flex justify-content-center align-items-center gap-2">

                                                <input type="hidden" name="accion" value="cambiar_estado_pedido">
                                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">

                                                <?php
                                                    $estadoActual = $pedido['estado_id'];
                                                    $opciones = obtenerOpcionesEstado($estadoActual);
                                                    $estadosDisponibles = obtenerEstadosPorIds($bd, $opciones);
                                                    $soloEstadoActual = count($opciones) === 1;
                                                ?>

                                                <?php if ($soloEstadoActual): ?>
                                                    <span class="estado estado-<?= $pedido['estado_id'] ?>">
                                                        <?= htmlspecialchars($estadosDisponibles[0]['estado'] ?? '') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <select name="nuevo_estado" class="estado estado-<?= $pedido['estado_id'] ?>">
                                                        <?php foreach ($estadosDisponibles as $estado): ?>
                                                            <option value="<?= $estado['id'] ?>"
                                                                <?= ($estado['id'] == $estadoActual) ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($estado['estado']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>

                                                    <button type="submit" class="btn btnUpdtAdmin">
                                                        <i class="bi bi-arrow-repeat"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            S/ <?= number_format($pedido['monto_total'], 2) ?>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            <?= $pedido['direccion_envio'] ?>
                                        </td>

                                        <td class="text-center text-primary-emphasis">
                                            <button class="openModalVerPedidoAdmin"
                                                    data-id="<?= $pedido['id']?>">
                                                <i class="bi bi-eyeglasses"></i>
                                            </button>
                                            <button class="openModalActPedidoAdmin"
                                                    data-id="<?= $pedido['id']?>">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </section>
                    
                </section>
            </section>

            <hr>

            <section id="reclamos"  class="sector">
                <h2>Reclamos</h2>
                <p>Visualiza y gestiona las hojas de reclamación registradas por los consumidores.</p>

                <section class="showSelectAdmin">
                    <section class="tableAdminReclamosCont">
                        <table class="tableAdminReclamos table table-hover">
                            <thead>
                                <tr class="table-secondary">
                                    <th class="text-center">Código</th>
                                    <th class="text-center">Fecha</th>
                                    <th class="text-center">Consumidor</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center">Producto / Servicio</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($reclamos)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No existen reclamos registrados.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($reclamos as $reclamo): ?>
                                        <tr class="table-light">
                                            <td class="text-center text-primary-emphasis"> <?= htmlspecialchars($reclamo['codigo_reclamo']) ?> </td>
                                            <td class="text-center text-primary-emphasis"> <?= date( 'd/m/Y H:i', strtotime($reclamo['fecha_registro']) ) ?> </td>
                                            <td class="text-center text-primary-emphasis"> <?= htmlspecialchars( $reclamo['nombres'] . ' ' . $reclamo['apellido_paterno'] . ' ' . $reclamo['apellido_materno'] ) ?> </td>
                                            <td class="text-center text-primary-emphasis">
                                                <?php if ($reclamo['tipo'] === 'RECLAMO'): ?> 
                                                    <span class="badge bg-danger"> Reclamo </span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning text-dark">
                                                        Queja
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center text-primary-emphasis"> <?= htmlspecialchars( $reclamo['producto_servicio'] ) ?> </td>
                                            <td class="text-center">
                                                <?php if ($reclamo['estado'] === 'PENDIENTE'): ?>
                                                    <span class="badge bg-warning text-dark">
                                                        Pendiente
                                                    </span>
                                                <?php elseif ($reclamo['estado'] === 'EN_PROCESO'): ?>
                                                    <span class="badge bg-primary">
                                                        En proceso
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">
                                                        Atendido
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm  openModalVerReclamoAdmin" data-id="<?= $reclamo['id'] ?>" >
                                                    <i class="bi bi-eyeglasses"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </section>
                    
                </section>
            </section>
        </section>
    </main>

    <!-- modal ver detalle de pedido perfil admin -->
    <div class="modalVerPedidoAdmin" id="idmodalVerPedidoAdmin">
        <div class="modalContentVerPedidoAdmin">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn-close closeModalViewPed"></button>
            </div>

            <div id="contenidoVerPedidoAdmin">
                Cargando...
            </div>
        </div>
    </div>

    <!-- modal editar pedido perfil admin -->
    <div class="modalActPedidoAdmin" id="idmodalActPedidoAdmin">
        <div class="modalContentActPedidoAdmin">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn-close closeModalActPed"></button>
            </div>

            <div id="contenidoActPedidoAdmin">
                Cargando...
            </div>
        </div>
    </div>

    <!-- modal ver/gestionar reclamo perfil admin -->
    <div class="modalVerReclamoAdmin" id="idmodalVerReclamoAdmin">
        <div class="modalContentVerReclamoAdmin">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="m-0">
                    Hoja de Reclamación
                </h4>
                <button
                    type="button"
                    class="btn-close closeModalViewReclamo">
                </button>
            </div>
            <div id="contenidoVerReclamoAdmin">
                Cargando...
            </div>
        </div>
    </div>


    <footer>
        <?php include_once('./src/partials/footer.php') ?>
    </footer>








    <!--Boostrap-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

    
    <!-- script de ajax para actualizar estado de pedido -->
    <script>
    document.querySelectorAll('.form-estado').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            let formData = new FormData(this);
            let selectEstado = this.querySelector('select[name="nuevo_estado"]');
            let botonActualizar = this.querySelector('.btnUpdtAdmin');

            fetch('./administrador.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                if (!data.success) {
                    alert('No se pudo actualizar el estado.');
                    return;
                }
                const nuevoEstado = data.nuevo_estado;

                // Actualizar clase visual
                selectEstado.classList.remove(
                    'estado-1',
                    'estado-2',
                    'estado-3',
                    'estado-4',
                    'estado-5',
                    'estado-6'
                );
                selectEstado.classList.add('estado-' + nuevoEstado);

                // Eliminar las opciones actuales
                selectEstado.innerHTML = '';

                // Agregar las nuevas opciones
                data.estados.forEach(estado => {
                    const option = document.createElement('option');

                    option.value = estado.id;
                    option.textContent = estado.estado;

                    if (parseInt(estado.id) === parseInt(nuevoEstado)) {
                        option.selected = true;
                    }
                    selectEstado.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al actualizar el estado.');
            });
        });
    });
    </script>

    <!-- scrip para ver el stock -->
    <script>
    document.addEventListener("DOMContentLoaded", ()=>{

        const cardStock = document.getElementById("cardDashboardStock");
        const tooltip = document.getElementById("tooltipStock");

        cardStock.addEventListener("click", ()=>{

            if(tooltip.style.display === "block"){
                tooltip.style.display = "none";
            }
            else{
                tooltip.style.display = "block";
            }
        });
    });
    </script>


    <!-- script para modal de detalle pedido -->
    <script>
        document.addEventListener("DOMContentLoaded", () =>{
            const modal = document.getElementById("idmodalVerPedidoAdmin");
            const closeBtn = document.querySelector(".closeModalViewPed");
            const botones = document.querySelectorAll(".openModalVerPedidoAdmin");
            
            botones.forEach(btn => {
                btn.addEventListener("click", function() {
                    // Abrir modal
                    modal.style.display = "flex";

                    // Mensaje mientras carga
                    document.getElementById("contenidoVerPedidoAdmin").innerHTML = "Cargando...";

                    fetch("obtenerDetallePedidoAdmin.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "pedido_id=" + this.dataset.id
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById("contenidoVerPedidoAdmin").innerHTML = html;
                    })
                    .catch(() => {
                        document.getElementById("contenidoVerPedidoAdmin").innerHTML =
                            "<p>Error al cargar el pedido.</p>";
                    });
                });
            });

            closeBtn.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        })        
    </script>

    <!-- script para modal de editar pedido -->
    <script>
        document.addEventListener("DOMContentLoaded", () =>{
            const modal = document.getElementById("idmodalActPedidoAdmin");
            const closeBtn = document.querySelector(".closeModalActPed");
            const botones = document.querySelectorAll(".openModalActPedidoAdmin");
            
            botones.forEach(btn => {
                btn.addEventListener("click", function () {
                    modal.style.display = "flex";

                    document.getElementById("contenidoActPedidoAdmin").innerHTML = "Cargando...";

                    fetch("obtenerEditarPedidoAdmin.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "pedido_id=" + this.dataset.id
                    })
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById("contenidoActPedidoAdmin").innerHTML = html;

                        const form = document.getElementById("formEditarPedidoAdmin");
                        if (!form) return;

                        form.addEventListener("submit", function(e){
                            e.preventDefault();
                            const datos = new FormData(form);
                            fetch("actualizarPedidoAdmin.php", {
                                method: "POST",
                                body: datos
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success){
                                    alert("Pedido actualizado correctamente.");
                                    modal.style.display = "none";
                                }else{
                                    alert("No se pudo actualizar el pedido.");
                                }
                            })
                            .catch(() => {
                                alert("Error al actualizar el pedido.");
                            });
                        });
                    })
                    .catch(() => {
                        document.getElementById("contenidoActPedidoAdmin").innerHTML =
                            "<p>Error al cargar el pedido.</p>";
                    });
                });
            });

            closeBtn.addEventListener("click", () => {
                modal.style.display = "none";
            });

            window.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        })
        
    </script>

    <!-- script para ver detalle del reclamo -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = document.getElementById("idmodalVerReclamoAdmin");
            const closeBtn = document.querySelector(".closeModalViewReclamo");
            const botones = document.querySelectorAll(".openModalVerReclamoAdmin");

            botones.forEach(btn => {
                btn.addEventListener("click", function () {
                    modal.style.display = "flex";
                    document.getElementById(
                        "contenidoVerReclamoAdmin"
                    ).innerHTML = "Cargando...";

                    fetch("obtenerDetalleReclamoAdmin.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "reclamo_id=" + this.dataset.id
                    })

                    .then(res => res.text())
                    .then(html => {
                        document.getElementById(
                            "contenidoVerReclamoAdmin"
                        ).innerHTML = html;

                        // Formulario de gestión cargado dinámicamente
                        const form = document.getElementById(
                            "formGestionReclamoAdmin"
                        );
                        if (!form) return;
                        form.addEventListener("submit", function(e) {
                            e.preventDefault();
                            const datos = new FormData(form);
                            fetch("actualizarReclamoAdmin.php", {
                                method: "POST",
                                body: datos
                            })
                            .then(res => res.json())
                            .then(data => {
                                if (data.success) {
                                    alert(
                                        "Reclamo actualizado correctamente."
                                    );
                                    modal.style.display = "none";
                                    location.reload();
                                } else {
                                    alert(
                                        data.message ||
                                        "No se pudo actualizar el reclamo."
                                    );
                                }
                            })

                            .catch(error => {
                                console.error(error);
                                alert(
                                    "Error al actualizar el reclamo."
                                );
                            });
                        });
                    })

                    .catch(error => {
                        console.error(error);
                        document.getElementById(
                            "contenidoVerReclamoAdmin"
                        ).innerHTML =
                            "<p>Error al cargar el reclamo.</p>";
                    });
                });
            });

            // Cerrar con X
            closeBtn.addEventListener("click", () => {
                modal.style.display = "none";
            });

            // Cerrar haciendo clic fuera
            window.addEventListener("click", (e) => {
                if (e.target === modal) {
                    modal.style.display = "none";
                }
            });
        });
    </script>

    <!-- script para switch de estado -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const switches = document.querySelectorAll(".inputEstadoProducto");
            switches.forEach(switchEstado => {
                switchEstado.addEventListener("change", function () {

                    const productoId = this.dataset.id;
                    const estadoAnterior = !this.checked;
                    const nuevoEstado = this.checked ? "activo" : "inactivo";

                    const datos = new FormData();

                    datos.append("producto_id", productoId);
                    datos.append("estado", nuevoEstado);

                    fetch("actualizarEstadoProducto.php", {
                        method: "POST",
                        body: datos
                    })
                    .then(response => response.json())
                    .then(data => {

                        if (!data.success) {

                            // Si falla, devolvemos el switch
                            // a su posición anterior
                            this.checked = estadoAnterior;

                            alert(data.message || "No se pudo actualizar el estado.");

                            return;
                        }

                        console.log(
                            "Producto " + productoId +
                            " actualizado a " + data.estado
                        );

                    })
                    .catch(error => {

                        console.error("Error:", error);

                        // Devolver switch a su estado anterior
                        this.checked = estadoAnterior;

                        alert("Ocurrió un error al actualizar el estado.");
                    });
                });
            });
        });
    </script>
</body>
</html>