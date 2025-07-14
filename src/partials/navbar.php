<?php    
require_once('helpers/dd.php');
require_once('controladores/funciones.php');
require_once('src/partials/conexionBD.php');

// Obtener categorías con subcategorías activas
$sql = "SELECT 
            c.id AS categoria_id, 
            c.nombre AS categoria_nombre, 
            sc.id AS subcategoria_id, 
            sc.nombre AS subcategoria_nombre
        FROM categorias c
        LEFT JOIN subcategorias sc ON sc.categoria_id = c.id
        WHERE c.estado = 'activo' AND sc.estado = 'activo'
        ORDER BY c.nombre, sc.nombre";
$stmt = $bd->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Organizar categorías
$categorias = [];
foreach ($datos as $fila) {
    $catId = $fila['categoria_id'];
    if (!isset($categorias[$catId])) {
        $categorias[$catId] = [
            'nombre' => $fila['categoria_nombre'],
            'subcategorias' => []
        ];
    }
    $categorias[$catId]['subcategorias'][] = [
        'id' => $fila['subcategoria_id'],
        'nombre' => $fila['subcategoria_nombre']
    ];
}

// Contador dinámico del carrito
$totalItems = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $totalItems += $item['cantidad'];
    }
}
?>

<nav class="navbar navbar-expand-lg navbarMain d-block m-0 p-0">  
    <div class="container-fluid d-flex flex-column m-0 p-0">
        <div class="headerGrid">
            <div class="divhead div1">
                <form class="divSearch d-flex" role="search">
                    <input class="form-control me-2" type="search" placeholder="Buscar..." aria-label="Search"/>
                    <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                </form>
            </div>

            <div class="divhead div2"></div>

            <div class="divhead divLogo">
                <a class="navbar-brand" href="index.php">KUNANMI</a>
            </div>

            <div class="divhead div4"></div>

            <div class="divhead divIcon">
                <?php if (isset($_SESSION['nombre'])): ?>
                    <?php if ($_SESSION['perfil'] == 9): ?>
                        <div class="li_enc1 nav-item">
                            <a class="iconNav nav-link" href="administrador.php"><i class="bi bi-person-fill-gear"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="li_enc1 nav-item">
                            <a href="perfilCliente.php" class="iconNav text-dark fs-5"><i class="bi bi-person-hearts"></i></a>
                             <a href="carrito.php" class="iconNav text-dark cart-icon">
                                <i class="bi bi-cart"></i>
                                <span class="cart-count <?= $totalItems === 0 ? 'd-none' : '' ?>"><?= $totalItems ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                   
                    <a class="nav-link" href="logout.php"><i class="iconNav bi bi-box-arrow-right"></i></a>
                <?php else: ?>
                    <a href="login.php" class="iconNav text-dark fs-5"><i class="bi bi-person"></i></a>
                    <a href="carrito.php" class="iconNav text-dark cart-icon">
                        <i class="bi bi-cart"></i>
                        <span class="cart-count <?= $totalItems === 0 ? 'd-none' : '' ?>"><?= $totalItems ?></span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="containerbtnNav">
            <button class="btnNavicon navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>  
        </div>

        <div class="headerMenu collapse navbar-collapse" id="navbarSupportedContent">
            <div class="containermenutext">
                <?php foreach ($categorias as $id => $cat): ?>
                    <div class="navbar-nav navbarMainText">                    
                        <a class="nav-link" href="tienda.php?categoria=<?= $id ?>&categoria_nombre=<?= urlencode($cat['nombre']) ?>">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                        <div class="menuN2">
                            <div class="menuN2Inner">
                                <?php foreach ($cat['subcategorias'] as $sub): ?>
                                    <a class="dropdown-item" href="tienda.php?subcategoria=<?= $sub['id'] ?>&subcategoria_nombre=<?= urlencode($cat['nombre']) ?>">
                                        <?= htmlspecialchars($sub['nombre']) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="navbar-nav navbarMainText">
                    <a class="nav-link" href="nosotros.php">Sobre Nosotros</a>
                </div>
            </div>
        </div>
    </div>
</nav>
