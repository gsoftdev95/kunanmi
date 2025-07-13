<?php
//Si o si deben colocarlo
//--------------
session_start();
//--------------
// Para poder enviar correos
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once('./helpers/dd.php');

//Función para efectuar la conexión a la Base de Datos

function conexion($host,$dbname,$user,$password){
    try {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $bd = new PDO($dsn, $user, $password);
        return $bd   ;
    } catch (PDOException $error) {
        echo "Ha ocurrido un error en la conexión ". $error->getMessage();
        exit;
    }
    
}

//Función para validar al usuario
function validarUsuario($datos) {
    $errores = [];

    $nombre = trim($datos['nombre']);
    $apellidoMaterno = trim($datos['apellidoMaterno']);
    $apellidoPaterno = trim($datos['apellidoPaterno']);
    $correo = trim($datos['email']);
    $celular = trim($datos['celular']);
    $direccion = trim($datos['direccion']);
    $contraseña = trim($datos['password']);

    if ($nombre === '') {
        $errores['nombre'] = 'El campo nombre no puede estar vacío.';
    }
    if ($apellidoPaterno === '') {
        $errores['apellidoPaterno'] = 'El apellido paterno es obligatorio.';
    }
    if ($apellidoMaterno === '') {
        $errores['apellidoMaterno'] = 'El apellido materno es obligatorio.';
    }
    if ($celular === '') {
        $errores['celular'] = 'El teléfono es obligatorio.';
    } elseif (!preg_match('/^9\d{8}$/', $celular)) {
        $errores['celular'] = 'El número debe tener 9 dígitos y comenzar con 9.';
    }
    if ($direccion === '') {
        $errores['direccion'] = 'La dirección es obligatoria.';
    }
    if ($correo === '') {
        $errores['email'] = 'El email es obligatorio.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['email'] = 'El email no es válido.';
    }
    if ($contraseña === '') {
        $errores['password'] = 'La contraseña es obligatoria.';
    } elseif (strlen($contraseña) < 6) {
        $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
    }

    return $errores;
}

//Función para guardar los datos del usuario (REGISTRO)
function guardarUsuario($bd, $tabla, $datos) {
    $nombre = $datos['nombre'];
    $apellidoPaterno = $datos['apellidoPaterno'];
    $apellidoMaterno = $datos['apellidoMaterno'];
    $correo = $datos['email'];
    $celular = $datos['celular'];
    $direccion = $datos['direccion'];
    $contraseña = password_hash($datos["password"], PASSWORD_DEFAULT);
    $perfil = 1; // usuario común
    $fechaCreacion = date('Y-m-d H:i:s');

    $sql = "INSERT INTO $tabla (
                nombre, apellido_paterno, apellido_materno, email, 
                celular, direccion, contraseña, perfil, fecha_creacion
            ) VALUES (
                :nombre, :apellidoPaterno, :apellidoMaterno, :correo, 
                :celular, :direccion, :password, :perfil, :fechaCreacion
            )";

    $query = $bd->prepare($sql);
    $query->bindValue(':nombre', $nombre);
    $query->bindValue(':apellidoPaterno', $apellidoPaterno);
    $query->bindValue(':apellidoMaterno', $apellidoMaterno);
    $query->bindValue(':correo', $correo);
    $query->bindValue(':celular', $celular);
    $query->bindValue(':direccion', $direccion);
    $query->bindValue(':password', $contraseña);
    $query->bindValue(':perfil', $perfil);
    $query->bindValue(':fechaCreacion', $fechaCreacion);

    $query->execute();
}

//Función para validar los datos del usuario - Inicia sesión
function validarUsuarioLogin($datos){
    $errores =[];
    
    $correo = trim($datos['email']);
    $contraseña = trim($datos['password']);

    if(filter_var($correo,FILTER_VALIDATE_EMAIL) != true ){
        $errores['email'] = 'correo ó Password invalidos';
    }
    if(empty($contraseña)){
        $errores['password'] = 'correo ó Password invalidos';
    }
    return $errores;
}
//Función para buscar al usuario que intenta ingresar al sistema
function buscarPorEmail($bd, $tabla, $correo) {
    $sql = "SELECT * FROM $tabla WHERE email = :email";
    $query = $bd->prepare($sql);
    $query->bindParam(':email', $correo);
    $query->execute();
    return $query->fetch(PDO::FETCH_ASSOC);
} 

//Función para guardar en sesión al usuario que está ingresando
function seteoUsuario($usuario){
    $_SESSION['id'] = $usuario['id'];
    $_SESSION['nombre'] = $usuario['nombre'];
    $_SESSION['apellidoPaterno'] = $usuario['apellido_paterno'];
    $_SESSION['apellidoMaterno'] = $usuario['apellido_materno'];
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['perfil'] = $usuario['perfil'];
}
function acceso($bd, $tabla, $username) {
    $query = "SELECT id, nombre, apellido_paterno, apellido_materno, email, perfil FROM $tabla WHERE email = :email"; 
    $stmt = $bd->prepare($query);
    $stmt->bindValue(':email', $username);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $usuario ? $usuario : null;
}

// Función para verificar el acceso basado en el perfil del usuario
function verificarAcceso($usuario, $perfilesPermitidos) {
    if (!isset($usuario['perfil'])) {
        header('Location: login.php');
        exit;
    }

    if (!in_array($usuario['perfil'], $perfilesPermitidos)) {
        header('Location: acceso_denegado.php');
        exit;
    }
}

function controlAcceso($bd, $rolesPermitidos = []) {
    if (!isset($_SESSION['email'])) {
        header('location:login.php');
        exit;
    }

    $usuario = acceso($bd, 'usuarios', $_SESSION['email']);
    
    if (!$usuario) {
        header('location:login.php');
        exit;
    }

    verificarAcceso($usuario, $rolesPermitidos);
}

//Función para controlar si el usuario está o no en sesión o cookie
function controlIngreso(){
    // dd($_SESSION['nombre']);
    if(!$_SESSION['nombre']){
        header('location:login.php');
    }
}

//Función para guardar en el navegador los datos del usuario
function seteoCookie($usuario){
    /*setcookie('correo', $usuario['correo'],time()+60*60*24*365*10);*/
    setcookie('correo', $usuario['correo'],time()+3600);
}

// Funcion para contar cliente
function contarClientes($bd, $tabla){
    $sql = "select count(*) from  $tabla where perfil = 1";
    $stmt = $bd->query($sql);
    return $stmt->fetchcolumn();
}

// Funcion para contar productos
function contarProductos($bd, $tabla){
    $sql = "select count(id) from $tabla";
    $stmt = $bd->query($sql);
    return $stmt->fetchcolumn();

}
//funcion para obtener las categorias
function obtenerCategorias($bd) {
    $sql = "SELECT id, nombre FROM categorias where estado='activo' ";
    $stmt = $bd->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//funcion para obtener las sub-categorias
function obtenerSubcategorias($bd) {
    $sql = "SELECT id, nombre, categoria_id FROM subcategorias WHERE estado = 'activo'";
    $stmt = $bd->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//funcion para obtener las supra-categorias
function obtenerSupracategorias($bd) {
    $sql = "SELECT id, nombre, subcategoria_id FROM supracategoria WHERE estado = 'activo'";

    $stmt = $bd->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//funcion para obtener atributos con valores
function obtenerAtributosConValores($bd) {
    $stmt = $bd->query("SELECT va.id, va.valor, a.nombre AS atributo FROM valores_atributos va 
                        JOIN atributos a ON va.id_atributo = a.id ORDER BY a.nombre");
    $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $agrupado = [];
    foreach ($resultado as $fila) {
        $agrupado[$fila['atributo']][] = [
            'id' => $fila['id'],
            'valor' => $fila['valor']
        ];
    }

    return $agrupado;
}


//Función para validar producto
function validarProducto($datos, $archivos) {
    $errores = [];

    // Nombre
    if (trim($datos['nombreProducto']) === '') {
        $errores['nombreProducto'] = 'El nombre no puede estar vacío';
    }

    // Descripción
    if (trim($datos['descripcionProducto']) === '') {
        $errores['descripcionProducto'] = 'La descripción es obligatoria';
    }
    // Beneficios
    if (trim($datos['beneficiosProducto']) === '') {
        $errores['beneficiosProducto'] = 'Los beneficios son obligatorios';
    }
    // modo de empleo
    if (trim($datos['modoEmpleoProducto']) === '') {
        $errores['modoEmpleoProducto'] = 'coloque el modo de empleo';
    }
    // modo de empleo
    if (trim($datos['ingredientesProducto']) === '') {
        $errores['ingredientesProducto'] = 'coloque los ingredientes de su producto';
    }
    // Precio
    if (!is_numeric($datos['precioProducto']) || $datos['precioProducto'] <= 0) {
        $errores['precioProducto'] = 'El precio debe ser un número positivo';
    }
    // Stock
    if (!is_numeric($datos['stockProducto']) || $datos['stockProducto'] < 0) {
        $errores['stockProducto'] = 'El stock debe ser un número válido';
    }
    // Categoría
    if (empty(trim($datos['categoriaProducto']))) {
        $errores['categoriaProducto'] = 'Debe seleccionar una categoría';
    }
    // Subcategoría
    if (empty(trim($datos['subcategoriaProducto']))) {
        $errores['subcategoriaProducto'] = 'Debe seleccionar una subcategoría';
    }
    // Imagen
    if (!isset($archivos['avatar']) || $archivos['avatar']['error'][0] !== 0) {
        $errores['avatar'] = 'Debe subir al menos una imagen';
    }
    // Destacado
    if (!isset($datos['destacado']) || !in_array($datos['destacado'], ['0', '1'])) {
        $errores['destacado'] = 'Seleccione si es destacado o no';
    }
    // Estado
    if (empty(trim($datos['estadoProducto']))) {
        $errores['estadoProducto'] = 'Debe colocar el estado del producto';
    }

    return $errores;
}

//Función para cargar la imagen del producto en el servidor
function armarLaImagenProducto($archivos){
    $rutaCarpeta = __DIR__ . '/../src/imgBD/Productos/';
    if (!is_dir($rutaCarpeta)) {
        mkdir($rutaCarpeta, 0777, true);
    }

    $nombresArchivos = [];

    if (isset($archivos['avatar']) && is_array($archivos['avatar']['name'])) {
        foreach ($archivos['avatar']['name'] as $key => $nombre) {
            if ($archivos['avatar']['error'][$key] === UPLOAD_ERR_OK) {
                $origenTemporal = $archivos['avatar']['tmp_name'][$key];
                $nombreArchivo = uniqid('producto_') . '.' . pathinfo($nombre, PATHINFO_EXTENSION);
                $rutaDestino = $rutaCarpeta . $nombreArchivo;

                // Mover el archivo a la carpeta de destino
                if (move_uploaded_file($origenTemporal, $rutaDestino)) {
                    $nombresArchivos[] = $nombreArchivo;
                }
            }
        }
    }

    return $nombresArchivos;
}
//Función para guardar los datos del producto
function guardarProducto($bd, $tabla, $datos, $imagen) {
    $query = "INSERT INTO $tabla (
                nombre, descripcion, precio, stock, categoria_id,
                subcategoria_id, supracategoria_id, imagen,
                destacado, fecha_creacion, beneficios, modo_empleo,
                ingredientes, estado
              ) VALUES (
                :nombre, :descripcion, :precio, :stock, :categoria,
                :subcategoria, :supracategoria, :imagen,
                :destacado, :fecha, :beneficios, :modoEmpleo,
                :ingredientes, :estado
              )";

    $stmt = $bd->prepare($query);
    $stmt->bindValue(':nombre', $datos['nombreProducto']);
    $stmt->bindValue(':descripcion', $datos['descripcionProducto']);
    $stmt->bindValue(':precio', $datos['precioProducto']);
    $stmt->bindValue(':stock', $datos['stockProducto']);
    $stmt->bindValue(':categoria', $datos['categoriaProducto']);
    $stmt->bindValue(':subcategoria', $datos['subcategoriaProducto']);

    if (!empty($datos['supracategoriaProducto'])) {
        $stmt->bindValue(':supracategoria', $datos['supracategoriaProducto'], PDO::PARAM_INT);
    } else {
        $stmt->bindValue(':supracategoria', null, PDO::PARAM_NULL);
    }

    $imagenesJson = json_encode($imagen);
    $stmt->bindValue(':imagen', $imagenesJson);
    $stmt->bindValue(':destacado', $datos['destacado']);
    $stmt->bindValue(':fecha', date('Y-m-d H:i:s'));
    $stmt->bindValue(':beneficios', $datos['beneficiosProducto']);
    $stmt->bindValue(':modoEmpleo', $datos['modoEmpleoProducto']);
    $stmt->bindValue(':ingredientes', $datos['ingredientesProducto']);
    $stmt->bindValue(':estado', $datos['estadoProducto']);

    $stmt->execute();
    return $bd->lastInsertId(); // Devuelve el ID del producto guardado
}


//funcion para buscar producto
function buscarProductos($bd, $tabla, $busqueda, $tipoBusqueda) {
    $sql = "SELECT p.*, 
                   c.nombre AS categoria_nombre, 
                   s.nombre AS subcategoria_nombre 
            FROM $tabla p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
            WHERE p.$tipoBusqueda LIKE :busqueda";

    $query = $bd->prepare($sql);
    $query->bindValue(':busqueda', "%" . $busqueda . "%");
    $query->execute();
    $producto = $query->fetchAll(PDO::FETCH_ASSOC);
    return $producto;
}


//Función para listar los datos del producto
function listarProductos($bd, $tabla) {
    $sql = "SELECT p.*, 
                   c.nombre AS categoria_nombre, 
                   s.nombre AS subcategoria_nombre 
            FROM $tabla p
            LEFT JOIN categorias c 
                ON p.categoria_id = c.id
            LEFT JOIN subcategorias s 
                ON p.subcategoria_id = s.id";

    $query = $bd->prepare($sql);
    $query->execute();  
    $producto = $query->fetchAll(PDO::FETCH_ASSOC);
    return $producto;
}

// Función para ver los datos del producto y sus imágenes
function detProdForAdmin($bd, $id, $tabla) {
    // 1. Obtener datos del producto + joins con categoría, sub, supra
    $sql = "SELECT
                t1.*,
                t2.nombre as categoria_nombre,
                t3.nombre as subcategoria_nombre,
                t4.nombre as supracategoria_nombre
            FROM $tabla t1
            LEFT JOIN categorias t2 ON t1.categoria_id = t2.id
            LEFT JOIN subcategorias t3 ON t1.subcategoria_id = t3.id
            LEFT JOIN supracategoria t4 ON t1.supracategoria_id = t4.id
            WHERE t1.id = :id";

    $query = $bd->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        return null;
    }

    // 2. Decodificar imágenes
    $producto['imagen'] = json_decode($producto['imagen'], true);

    // 3. Obtener atributos del producto
    $sqlAttr = "SELECT 
                    va.id AS valor_id,
                    va.valor,
                    a.nombre AS atributo
                FROM producto_atributo pa
                JOIN valores_atributos va ON pa.valor_atributo_id = va.id
                JOIN atributos a ON va.id_atributo = a.id
                WHERE pa.producto_id = :id";

    $stmtAttr = $bd->prepare($sqlAttr);
    $stmtAttr->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtAttr->execute();

    $atributos = [];
    $atributosPlano = [];

    foreach ($stmtAttr->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $atributos[$row['atributo']][] = $row['valor'];
        $atributosPlano[] = ['id' => $row['valor_id']];
    }

    // 4. Agregar al producto
    $producto['atributos'] = $atributos;
    $producto['atributosPlano'] = $atributosPlano;

    return $producto;
}

//Función para modificar los datos del producto
function modificarProducto($bd, $tabla, $datos, $avatar)
{
    $id = intval($datos['id']);
    $nombreProducto = $datos['nombreProducto'];
    $descripcionProducto = $datos['descripcionProducto'];
    $precioProducto = $datos['precioProducto'];    
    $stockProducto = $datos['stockProducto'];
    $categoriaProducto = $datos['categoriaProducto'];
    $subcategoriaProducto = $datos['subcategoriaProducto'];
    $supracategoriaProducto = $datos['supracategoriaProducto'];
    $beneficiosProducto = $datos['beneficiosProducto'];
    $modoEmpleo = $datos['modoEmpleo'];
    $ingredProducto = $datos['ingredProducto'];
    $destacadoProducto = $datos['destacadoProducto'];
    $estadoProducto = $datos['estadoProducto'];

    // Convertimos array de imágenes a JSON
    $imagenes_json = is_array($avatar) ? json_encode($avatar) : $avatar;

    $sql = "UPDATE $tabla SET 
            nombre = :nombreProducto, 
            descripcion = :descripcionProducto,
            precio = :precioProducto,  
            stock = :stockProducto,
            categoria_id = :categoriaProducto, 
            subcategoria_id = :subcategoriaProducto,
            supracategoria_id = :supracategoriaProducto,
            beneficios = :beneficiosProducto,
            modo_empleo = :modoEmpleo,
            ingredientes = :ingredProducto,
            destacado = :destacadoProducto,
            estado = :estadoProducto,
            imagen = :imagen
            WHERE id = :id";

    $query = $bd->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->bindValue(':nombreProducto', $nombreProducto);
    $query->bindValue(':descripcionProducto', $descripcionProducto);
    $query->bindValue(':precioProducto', $precioProducto);
    $query->bindValue(':stockProducto', $stockProducto);
    $query->bindValue(':categoriaProducto', $categoriaProducto);
    $query->bindValue(':subcategoriaProducto', $subcategoriaProducto);
    $query->bindValue(':supracategoriaProducto', !empty($supracategoriaProducto) ? $supracategoriaProducto : null, PDO::PARAM_INT);
    $query->bindValue(':beneficiosProducto', $beneficiosProducto);
    $query->bindValue(':modoEmpleo', $modoEmpleo);
    $query->bindValue(':ingredProducto', $ingredProducto);
    $query->bindValue(':destacadoProducto', $destacadoProducto);
    $query->bindValue(':estadoProducto', $estadoProducto);
    $query->bindValue(':imagen', $imagenes_json);
    
    $query->execute();
}



//Función para buscar por usuario
function buscarUsuarios($bd,$tabla,$busqueda,$tipoBusqueda){
    $sql = "select * from $tabla where  $tipoBusqueda like :busqueda";
    $query= $bd->prepare($sql);
    $query->bindValue(':busqueda', "%".$busqueda."%");    
    $query->execute();
    $usuario = $query->fetchAll(PDO::FETCH_ASSOC);
    return $usuario;   
}

//Función para listar los datos del usuario
function listarUsuarios($bd, $tabla){
    $sql = "select * from $tabla";
    $query= $bd->prepare($sql);
    $query->execute();
    $usuario = $query->fetchAll(PDO::FETCH_ASSOC);
    return $usuario;
}  

//funcion para listar pedidos
function listarPedidos($bd) {
    $stmt = $bd->prepare("SELECT * FROM pedidos ORDER BY fecha_pedido DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Obtiene el nombre completo del cliente a partir del usuario_id
function obtenerNombreUsuario($bd, $usuario_id) {
    $stmt = $bd->prepare("SELECT nombre, apellido_paterno, apellido_materno FROM usuarios WHERE id = :id");
    $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        return $usuario['nombre'] . ' ' . $usuario['apellido_paterno'] . ' ' . $usuario['apellido_materno'];
    } else {
        return 'Usuario no encontrado';
    }
}



/****************** */
/****************** */
/********tienda**** */
/****************** */
/****************** */

//Función para listar los datos del producto
function obtenerProdTienda($bd, $tabla) {
    $sql = "SELECT p.*, 
                   c.nombre AS categoria_nombre, 
                   s.nombre AS subcategoria_nombre 
            FROM $tabla p
            LEFT JOIN categorias c 
                ON p.categoria_id = c.id
            LEFT JOIN subcategorias s 
                ON p.subcategoria_id = s.id";

    $query = $bd->prepare($sql);
    $query->execute();  
    $producto = $query->fetchAll(PDO::FETCH_ASSOC);
    return $producto;
}
function obtenerProductosPorCategoria($bd, $categoriaId) {
    $sql = "SELECT p.*, 
                   c.nombre AS categoria_nombre, 
                   s.nombre AS subcategoria_nombre 
            FROM productos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
            WHERE p.categoria_id = :categoriaId
            ORDER BY p.nombre ASC";

    $query = $bd->prepare($sql);
    $query->bindValue(':categoriaId', $categoriaId, PDO::PARAM_INT);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}
function obtenerProductosPorSubcategoria($bd, $subcategoriaId) {
    $sql = "SELECT p.*, sc.nombre AS subcategoria_nombre
            FROM productos p
            LEFT JOIN subcategorias sc ON sc.id = p.subcategoria_id
            WHERE p.subcategoria_id = :subcategoriaId AND p.estado = 'activo'";
    
    $stmt = $bd->prepare($sql);
    $stmt->bindValue(':subcategoriaId', $subcategoriaId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//Función para eliminar el registro del producto
function eliminarProducto($bd,$tabla,$datos){
    $id=intval($datos['id']);
    $sql= "delete from $tabla where id= :id";
    $query=$bd->prepare($sql);
    $query->bindvalue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    if($query->rowCount()>0){
        echo "<h2 style='text-align:center'>Registro eliminado</h2>";
    }else{
        echo "<h2 style='text-align:center'>No se pudo elimiar el registro</h2>";
    }
}

// Obtiene los datos del producto por ID
function obtenerProductoPorId($bd, $id) {
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $bd->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Función para ver los datos del usuario
function detUserForAdmin($bd, $id, $tabla) {
    $sql = "SELECT *
            FROM $tabla p  
            WHERE p.id = :id";
    $query = $bd->prepare($sql);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    $productos = $query->fetch(PDO::FETCH_ASSOC);

    return $productos;
}
//obtener productos destacados
function obtenerProductosDestacados($bd) {
    $sql = "SELECT p.*, s.nombre AS subcategoria_nombre 
            FROM productos p
            LEFT JOIN subcategorias s ON p.subcategoria_id = s.id
            WHERE p.destacado = 1 
            ORDER BY p.fecha_creacion DESC
            LIMIT 10";
    $stmt = $bd->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Función para modificar los datos del usuario
function modificarUsuarioAdmin($bd, $tabla, $datos){

    $id= intval($datos['id']);
    $nombre = $datos['nombreUsuario'];
    $paterno = $datos['paternoUsuario'];
    $materno = $datos['maternoUsuario'];
    $correo = $datos['emailUsuario'];
    $celular = $datos['celularUsuario'];
    $direccion = $datos['direccionUsuario'];
    
    $sql = "update $tabla set id= :id,nombre= :nombre,apellido_paterno= :apellidopat, apellido_materno= :apellidomat ,email= :correo, celular= :celular, direccion= :direccion  where id= :id";
        
    $query = $bd->prepare($sql);
    $query->bindvalue(':id', $id);
    $query->bindValue(':nombre', $nombre);
    $query->bindValue(':apellidopat', $paterno);
    $query->bindValue(':apellidomat', $materno);
    $query->bindValue(':correo', $correo);
    $query->bindValue(':celular', $celular);
    $query->bindValue(':direccion', $direccion);
    $query->execute();    
}

function detalleUsuario($bd, $id, $table) {
    $sql = "select * from $table where id=$id";
    $query = $bd->prepare($sql);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);
    return $usuario;
}
//Función para eliminar el registro del usuario
function eliminarUsuario($bd,$tabla,$datos){
    $id=intval($datos['id']);
    $sql= "delete from $tabla where id= :id";
    $query=$bd->prepare($sql);
    $query->bindvalue(':id', $id, PDO::PARAM_INT);
    $query->execute();
    if($query->rowCount()>0){
        echo "<h2 style='text-align:center'>Registro eliminado</h2>";
    }else{
        echo "<h2 style='text-align:center'>No se pudo elimiar el registro</h2>";
    }
}


/****************** */
/****************** */
/*******perfil cliente**** */
/****************** */
/****************** */

function obtenerUsuarioPorId($bd, $idUsuario) {
    $stmt = $bd->prepare("SELECT id, nombre, apellido_paterno, apellido_materno, email, celular, direccion, perfil, fecha_creacion FROM usuarios WHERE id = :id");
    $stmt->bindValue(':id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    return $usuario ? $usuario : null;
}
function obtenerPedidosPorUsuario(PDO $bd, int $idUsuario): array {
    $stmt = $bd->prepare("SELECT id, fecha_pedido, estado, monto_total FROM pedidos WHERE usuario_id = :usuario_id ORDER BY fecha_pedido DESC");
    $stmt->bindValue(':usuario_id', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}











/****************** */
/****************** */
/*******opiniones**** */
/****************** */
/****************** */

function obtenerOpiniones($bd) {
    $sql = "SELECT o.opinion, o.fecha, 
                   u.nombre, u.apellido_paterno
            FROM opiniones o
            JOIN usuarios u ON o.usuario_id = u.id
            ORDER BY o.fecha DESC";

    $stmt = $bd->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




























/*



  




function detalleUsuario($bd, $id, $table) {
    //Armado de la sentencia
    $sql = "select * from $table where id=$id";
    //Ejecución de la sentencia
    $query = $bd->prepare($sql);
    $query->execute();
    //Lectura de los datos obtenidos en la sentencia como Array Asociativo
    $usuario = $query->fetch(PDO::FETCH_ASSOC);
    //dd($usuario);
    return $usuario;
}











function enviarCorreo($usuario) {    
    //Importar PHPMailer
    require_once 'librerias/PHPMailer/src/PHPMailer.php';
    require_once 'librerias/PHPMailer/src/SMTP.php';
    require_once 'librerias/PHPMailer/src/Exception.php';

    //Guardar los datos del usuario
    $correoUsuario = $usuario['correo'];
    $nombre = $usuario['nombre'];
    $apellido = $usuario['apellidos'];
    $nombreCompleto = $nombre . ' ' . $apellido;

    //Crear instancia de PHPMailer
    $mail = new PHPMailer(true);

    try {
        //Configuración del servidor
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Cambia a SMTP::DEBUG_SERVER para depuración //otro valor es 0
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ratsprotection@gmail.com'; //'gesoftdev@gmail.com'
        $mail->Password   = 'zpod sqtc eshe vmjz';  //'wncl tsrg bxkg fuic';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // o 'ssl'
        $mail->Port       = 587; // Usa 465 si es 'ssl'

        //Destinatarios
        $mail->setFrom('ratsprotection@gmail.com', 'El tio Rats');
        $mail->addAddress($correoUsuario, $nombreCompleto); 
        $mail->addCC('gesoftdev@gmail.com');

        //Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Registro de cuenta - Rats Protection Peru';

        //Cargar el contenido HTML desde un archivo
        $file = fopen("bodyEmail.html", "r");
        $str = fread($file, filesize("bodyEmail.html"));
        fclose($file);

        $mail->Body = trim($str);

        $mail->send();
        echo 'Correo enviado de forma satisfactoria';
    } catch (Exception $e) {
        echo "Ha ocurrido un error: {$mail->ErrorInfo}";
    }
}


/*************************************************** */

/*
CREATE TABLE calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    calificacion DECIMAL(2, 1) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    calificacion DECIMAL(2, 1) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (producto_id) REFERENCES productos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

// Función para guardar la calificación del usuario
function guardarCalificacion($bd, $producto_id, $usuario_id, $calificacion) {
    // Armar la consulta
    $sql = "INSERT INTO calificaciones (producto_id, usuario_id, calificacion) VALUES (:producto_id, :usuario_id, :calificacion)";
    
    // Preparar la consulta
    $query = $bd->prepare($sql);
    $query->bindValue(':producto_id', $producto_id);
    $query->bindValue(':usuario_id', $usuario_id);
    $query->bindValue(':calificacion', $calificacion);
    
    // Ejecutar la consulta
    if ($query->execute()) {
        return true; // Calificación guardada con éxito
    } else {
        return false; // Error al guardar la calificación
    }
}

/*
formulario de calificacion
<form method="POST" action="guardar_calificacion.php">
    <input type="hidden" name="producto_id" value="1"> <!-- Cambia el valor según el producto -->
    <label for="calificacion">Calificación:</label>
    <select name="calificacion" id="calificacion" required>
        <option value="5">5</option>
        <option value="4.5">4.5</option>
        <option value="4">4</option>
        <option value="3.5">3.5</option>
        <option value="3">3</option>
        <option value="2.5">2.5</option>
        <option value="2">2</option>
        <option value="1.5">1.5</option>
        <option value="1">1</option>
        <option value="0.5">0.5</option>
    </select>
    <button type="submit">Calificar</button>
</form>


<?php
session_start();
require_once('tu_archivo_de_conexion.php'); // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $usuario_id = $_SESSION['usuario_id']; // Asegúrate de tener el ID del usuario en la sesión
    $calificacion = $_POST['calificacion'];

    // Llamar a la función para guardar la calificación
    if (guardarCalificacion($bd, $producto_id, $usuario_id, $calificacion)) {
        echo "Calificación guardada con éxito.";
    } else {
        echo "Error al guardar la calificación.";
    }
}

//PRODUCTO
//PRODUCTO
//PRODUCTO
//PRODUCTO  





function obtenerImagenes($bd, $id) {
    $sql = "SELECT avatar FROM producto WHERE id = :id";
    $query = $bd->prepare($sql);
    $query->execute([$id]);
    return $query->fetchAll(PDO::FETCH_ASSOC);
}

function guardarImagenes($bd, $tabla, $id, $imagenes) {
    foreach ($imagenes as $imagen) {
        // Aquí deberías insertar la ruta de la imagen en la base de datos
        $consulta = $bd->prepare("INSERT INTO imagenes (producto_id, ruta) VALUES (?, ?)");
        $consulta->execute([$id, $imagen]);
    }
}



//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM
//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM
//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM
//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM
//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM
//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM//TEAM



function obtenerTeam($bd,$tabla) {
    $sql = "SELECT * FROM $tabla"; // Asegúrate de que la tabla y columnas sean correctas
    $query = $bd->prepare($sql);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


function buscarTeam($bd,$tabla,$busqueda,$tipoBusqueda){
    //Armar la consulta
    $sql = "select * from $tabla where  $tipoBusqueda like :busqueda";
    //Preparar la consulta
    $query= $bd->prepare($sql);
    $query->bindValue(':busqueda', "%".$busqueda."%");    
    //Ejecutar la consulta
    $query->execute();
    //Traer los datos de la consulta
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
    // dd($usuarios);
    return $usuarios;   
}
function listarTeam($bd, $tabla){
    //Armar la consulta
    $sql = "select * from $tabla";
    //Preparar la consulta
    $query= $bd->prepare($sql);
    //Ejecutar la consulta
    $query->execute();
    //Traer los datos de la consulta
    $team = $query->fetchAll(PDO::FETCH_ASSOC);
    return $team;
} 
function validarTeam($datos,$imagen){
    $errores = [];
    $nombre = trim($datos['nombre']);
    $apellido = trim($datos['apellido']);
    if($nombre === ''){
        $errores['nombre'] = 'El campo nombre no puede estar vacio';
    }    
    if(empty($apellido)){
        $errores['apellido'] = 'El campo apellido no puede estar vacio';
    }
    //Validar si me mandaron la imagen
    if(isset($imagen)){
        //dd($imagen);
        if($imagen['avatar']['error']!=0){
            $errores['avatar'] = 'Debe subir una imagen';
        }
    }
    return $errores;
}
function armarLaImagenTeam($imagen){
    $avatar = $imagen['imgteamrats']['name']; // Cambié 'avatar' a 'imgteamrats'
    $ext = pathinfo($avatar, PATHINFO_EXTENSION);
    $archivoOrigen = $imagen['imgteamrats']['tmp_name'];
    $nombreArchivo = uniqid('avatar-').'.'.$ext;
    $ruta = dirname(__DIR__).'/imgRats/Team/';
    $archivoDestino = $ruta.$nombreArchivo;
    
    move_uploaded_file($archivoOrigen, $archivoDestino);
    return $nombreArchivo;
}

function guardarTeam($bd,$tabla,$datos,$imgteamrats){
    //Organizar los datos a ser guardados
    $nombre = $datos['nombre'];
    $apellido = $datos['apellido'];
    
    //Armar la consulta
    $sql = "insert into $tabla (nombre,apellido,imgteamrats) values (:nombre,:apellido,:imgteamrats)";
    //Preparar la consulta
    $query = $bd->prepare($sql);
    $query->bindValue(':nombre', $nombre);
    $query->bindValue(':apellido', $apellido);
    $query->bindValue(':imgteamrats', $imgteamrats);
    $query->execute();
}
function detalleDeportista($bd, $id, $table) {
    // Armado de la sentencia
    $sql = "SELECT * FROM $table WHERE idteamrats = :id"; // Cambia 'id' por 'idteamrats'
    // Ejecución de la sentencia
    $query = $bd->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT); // Usar parámetros preparados
    $query->execute();
    // Lectura de los datos obtenidos en la sentencia como Array Asociativo
    $team = $query->fetch(PDO::FETCH_ASSOC);
    return $team;
}
function armarLaImagenDeportista($imagen) {
    $avatar = $imagen['imgteamrats']['name'];
    $ext = pathinfo($avatar, PATHINFO_EXTENSION);
    $archivoOrigen = $imagen['imgteamrats']['tmp_name'];
    $nombreArchivo = uniqid('avatar-') . '.' . $ext;
    $ruta = dirname(__DIR__) . '/imgRats/Team/';
    $archivoDestino = $ruta . $nombreArchivo;

    move_uploaded_file($archivoOrigen, $archivoDestino);
    return $nombreArchivo;
}

function modificarDeportista($bd, $tabla, $datos) {
    // Recopilación de datos $_POST
    $id = intval($datos['id']);
    $nombre = $datos['nombre'];
    $apellido = $datos['apellido'];    
    $imgteamrats = $datos['imgteamrats'];

    // Armado del update
    $sql = "UPDATE $tabla SET nombre = :nombre, apellido = :apellido, imgteamrats = :imgteamrats WHERE idteamrats = :idteamrats";

    // Inserción de datos en la sentencia
    $query = $bd->prepare($sql);    
    $query->bindValue(':idteamrats', $id);
    $query->bindValue(':nombre', $nombre);
    $query->bindValue(':apellido', $apellido);
    $query->bindValue(':imgteamrats', $imgteamrats);

    // Ejecución de la consulta y manejo de errores
    if ($query->execute()) {
        return true;
    } else {
        $errorInfo = $query->errorInfo();
        throw new Exception("Error al modificar deportista: " . $errorInfo[2]);
    }
}



function eliminarDeportista($bd, $tabla, $datos) {
    try {
        $id = intval($datos['id']);
        $sql = "DELETE FROM $tabla WHERE idteamrats = :id";
        $query = $bd->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            echo "<h2 style='text-align:center'>Registro eliminado</h2>";
        } else {
            echo "<h2 style='text-align:center'>No se pudo eliminar el registro</h2>";
        }
    } catch (Exception $e) {
        echo "<h2 style='text-align:center'>Error: " . $e->getMessage() . "</h2>";
    }
}





//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO
//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO
//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO
//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO
//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO
//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO//EVENTO


function validarEvento($datos, $imagen) {
    $errores = [];
    $nombre = trim($datos['nombreevento']);
    if ($nombre === '') {
        $errores['nombreevento'] = 'El campo nombre no puede estar vacío';
    }
    
    // Validar si me mandaron la imagen
    if (!isset($imagen['imgevento']) || $imagen['imgevento']['error'] != 0) {
        $errores['imgevento'] = 'Debe subir una imagen';
    }
    
    return $errores;
}

function armarLaImagenEvento($imagen) {
    $avatar = $imagen['imgevento']['name'];
    $ext = pathinfo($avatar, PATHINFO_EXTENSION);
    $archivoOrigen = $imagen['imgevento']['tmp_name'];
    $nombreArchivo = uniqid('evento-') . '.' . $ext;
    $ruta = dirname(__DIR__) . '/imgRats/Eventos/';
    $archivoDestino = $ruta . $nombreArchivo;

    move_uploaded_file($archivoOrigen, $archivoDestino);
    return $nombreArchivo;
}

function guardarEvento($bd, $tabla, $datos, $imgevento) {
    // Organizar los datos a ser guardados
    $nombre = $datos['nombreevento'];

    // Armar la consulta
    $sql = "INSERT INTO $tabla (nombreevento, imgevento) VALUES (:nombreevento, :imgevento)";
    
    // Preparar la consulta
    $query = $bd->prepare($sql);
    $query->bindValue(':nombreevento', $nombre);
    $query->bindValue(':imgevento', $imgevento);
    $query->execute();
}

function buscarEvento($bd,$tabla,$busqueda,$tipoBusqueda){
    //Armar la consulta
    $sql = "select * from $tabla where  $tipoBusqueda like :busqueda";
    //Preparar la consulta
    $query= $bd->prepare($sql);
    $query->bindValue(':busqueda', "%".$busqueda."%");    
    //Ejecutar la consulta
    $query->execute();
    //Traer los datos de la consulta
    $usuarios = $query->fetchAll(PDO::FETCH_ASSOC);
    // dd($usuarios);
    return $usuarios;   
}
function listarEvento($bd, $tabla){
    //Armar la consulta
    $sql = "select * from $tabla";
    //Preparar la consulta
    $query= $bd->prepare($sql);
    //Ejecutar la consulta
    $query->execute();
    //Traer los datos de la consulta
    $team = $query->fetchAll(PDO::FETCH_ASSOC);
    return $team;
}

function detalleEvento($bd, $id, $table) {
    // Armado de la sentencia
    $sql = "SELECT * FROM $table WHERE idevento = :id";
    // Ejecución de la sentencia
    $query = $bd->prepare($sql);
    $query->bindParam(':id', $id, PDO::PARAM_INT); // Usar parámetros preparados
    $query->execute();
    // Lectura de los datos obtenidos en la sentencia como Array Asociativo
    $evento = $query->fetch(PDO::FETCH_ASSOC);
    return $evento;
}

function modificarEvento($bd, $tabla, $datos) {
    // Recopilación de datos $_POST
    $id = intval($datos['idevento']);
    $nombreevento = $datos['nombreevento'];
    $imgevento = $datos['imgevento'];

    // Armado del update
    $sql = "UPDATE $tabla SET nombreevento = :nombreevento, imgevento = :imgevento WHERE idevento = :idevento";

    // Inserción de datos en la sentencia
    $query = $bd->prepare($sql);
    $query->bindValue(':nombreevento', $nombreevento);
    $query->bindValue(':imgevento', $imgevento);
    $query->bindValue(':idevento', $id, PDO::PARAM_INT);

    // Ejecución de la consulta y manejo de errores
    if ($query->execute()) {
        return true;
    } else {
        $errorInfo = $query->errorInfo();
        throw new Exception("Error al modificar evento: " . $errorInfo[2]);
    }
}

function obtenerEvento($bd,$tabla) {
    $sql = "SELECT * FROM $tabla"; 
    $query = $bd->prepare($sql);
    $query->execute();
    return $query->fetchAll(PDO::FETCH_ASSOC);
}


function eliminarEvento($bd, $tabla, $datos) {
    try {
        $id = intval($datos['id']);
        $sql = "DELETE FROM $tabla WHERE idevento = :id";
        $query = $bd->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            echo "<h2 style='text-align:center'>Registro eliminado</h2>";
        } else {
            echo "<h2 style='text-align:center'>No se pudo eliminar el registro</h2>";
        }
    } catch (Exception $e) {
        echo "<h2 style='text-align:center'>Error: " . $e->getMessage() . "</h2>";
    }
}
*/

?>