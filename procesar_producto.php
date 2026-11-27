<?php
// procesar_producto.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Auditoria.php';

// 1. Verificar sesión y rol permitido en el servidor (solo administrador y encargado)
verificarAcceso(['administrador', 'encargado']);

// 2. Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion-productos.php');
    exit;
}

$accion     = $_POST['accion'] ?? 'crear';
$idUsuario  = (int)$_SESSION['id_usuario'];
$idProducto = (int)($_POST['id_producto'] ?? 0);

// ==================== ACCIÓN: ELIMINAR (POST) ====================
if ($accion === 'eliminar') {
    if ($idProducto <= 0) {
        $_SESSION['mensaje_error'] = 'Identificador de producto inválido.';
        header('Location: gestion-productos.php');
        exit;
    }

    $prodActual = Producto::obtenerPorId($idProducto);
    $resultado  = Producto::eliminar($idProducto);

    if ($resultado['exito']) {
        $detalle = $prodActual
            ? "Eliminado producto {$prodActual['codigo']} ({$prodActual['nombre']})"
            : "Eliminado producto ID $idProducto";
        Auditoria::registrar($idUsuario, 'DELETE', 'productos', $idProducto, $detalle);
        $_SESSION['mensaje_exito'] = $resultado['mensaje'];
    } else {
        $_SESSION['mensaje_error'] = $resultado['mensaje'];
    }

    header('Location: gestion-productos.php');
    exit;
}

// ==================== ACCIÓN: CREAR O ACTUALIZAR ====================
// Usar limpiarEntrada() para no guardar entidades HTML (&amp;) dentro de MySQL
$nombre          = limpiarEntrada($_POST['nombre'] ?? '');
$codigo          = strtoupper(limpiarEntrada($_POST['codigo'] ?? ''));
$precio          = limpiarEntrada($_POST['precio'] ?? '');
$fecha_caducidad = limpiarEntrada($_POST['fecha_caducidad'] ?? '');
$id_categoria    = (int)($_POST['id_categoria'] ?? 0);
$ubicacion       = limpiarEntrada($_POST['ubicacion'] ?? '');
$stock_minimo    = limpiarEntrada($_POST['stock_minimo'] ?? '');
$id_proveedor    = (int)($_POST['id_proveedor'] ?? 0);

// Validar campos obligatorios y reglas de negocio
$validador = new Validador();
$validador->requerido('nombre', $nombre, 'El nombre del producto es obligatorio.');
$validador->requerido('codigo', $codigo, 'El código único es obligatorio.');
$validador->requerido('precio', $precio, 'El precio unitario es obligatorio.');
$validador->requerido('ubicacion', $ubicacion, 'La ubicación en bodega es obligatoria.');
$validador->requerido('stock_minimo', $stock_minimo, 'El stock mínimo es obligatorio.');

$errores = $validador->hayErrores() ? ($validador->errores ?? []) : [];

if ($codigo !== '' && !preg_match('/^PR-[0-9]{3,}$/', $codigo)) {
    $errores['codigo'] = 'El código debe tener el formato PR- seguido de al menos 3 dígitos (ej. PR-0516).';
}
if ($id_categoria <= 0) {
    $errores['id_categoria'] = 'Seleccione una categoría válida.';
}
if ($precio !== '' && (!is_numeric($precio) || (float)$precio < 0)) {
    $errores['precio'] = 'El precio debe ser un número mayor o igual a 0.';
}
if ($stock_minimo !== '' && (!ctype_digit((string)$stock_minimo) || (int)$stock_minimo < 0)) {
    $errores['stock_minimo'] = 'El stock mínimo debe ser un número entero mayor o igual a 0.';
}
if ($fecha_caducidad !== '' && !preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $fecha_caducidad)) {
    $errores['fecha_caducidad'] = 'La fecha de caducidad no tiene un formato válido (AAAA-MM-DD).';
}

// Verificar unicidad del código en MySQL
if (!isset($errores['codigo'])) {
    $existente = Producto::obtenerPorCodigo($codigo);
    if ($existente && (int)$existente['id_producto'] !== $idProducto) {
        $errores['codigo'] = "El código $codigo ya está registrado en otro producto.";
    }
}

// Si hay errores, regresar al formulario conservando valores
if (!empty($errores)) {
    $_SESSION['errores_producto'] = $errores;
    $_SESSION['valores_producto'] = [
        'id_producto'     => $idProducto,
        'nombre'          => $nombre,
        'codigo'          => $codigo,
        'precio'          => $precio,
        'fecha_caducidad' => $fecha_caducidad,
        'stock_minimo'    => $stock_minimo,
        'id_categoria'    => (string)$id_categoria,
        'ubicacion'       => $ubicacion,
        'id_proveedor'    => $id_proveedor > 0 ? (string)$id_proveedor : ''
    ];

    $destinoError = ($accion === 'actualizar' && $idProducto > 0)
        ? 'formulario-producto.php?id=' . $idProducto
        : 'formulario-producto.php';
    header('Location: ' . $destinoError);
    exit;
}

try {
    $pdo = Conexion::obtener();

    if ($accion === 'actualizar' && $idProducto > 0) {
        $prodActual = Producto::obtenerPorId($idProducto);
        if (!$prodActual) {
            $_SESSION['mensaje_error'] = 'El producto que intenta editar no existe.';
            header('Location: gestion-productos.php');
            exit;
        }

        // RB-01: conservar el stock actual de BD (no se edita manualmente desde el formulario)
        $producto = new Producto(
            $codigo,
            $nombre,
            (int)$prodActual['stock'],
            (int)$stock_minimo,
            $id_categoria,
            $fecha_caducidad !== '' ? $fecha_caducidad : null,
            (float)$precio,
            $ubicacion
        );
        $producto->actualizar($idProducto);

        // Actualizar relación N:M en producto_proveedor si eligió proveedor principal
        if ($id_proveedor > 0) {
            $stmtRel = $pdo->prepare(
                'INSERT INTO producto_proveedor (id_producto, id_proveedor, precio_compra, tiempo_entrega_dias)
                 VALUES (:id_prod, :id_prov, :precio, 2)
                 ON DUPLICATE KEY UPDATE precio_compra = VALUES(precio_compra)'
            );
            $stmtRel->execute([
                ':id_prod' => $idProducto,
                ':id_prov' => $id_proveedor,
                ':precio'  => (float)$precio
            ]);
        }

        Auditoria::registrar($idUsuario, 'UPDATE', 'productos', $idProducto, "Actualizado producto $codigo ($nombre)");
        $_SESSION['mensaje_exito'] = "Producto $codigo actualizado correctamente.";
    } else {
        // Nuevo producto inicia con stock = 0 (RB-01)
        $producto = new Producto(
            $codigo,
            $nombre,
            0,
            (int)$stock_minimo,
            $id_categoria,
            $fecha_caducidad !== '' ? $fecha_caducidad : null,
            (float)$precio,
            $ubicacion
        );
        $nuevoId = $producto->guardar();

        if ($id_proveedor > 0) {
            $stmtRel = $pdo->prepare(
                'INSERT INTO producto_proveedor (id_producto, id_proveedor, precio_compra, tiempo_entrega_dias)
                 VALUES (:id_prod, :id_prov, :precio, 2)'
            );
            $stmtRel->execute([
                ':id_prod' => $nuevoId,
                ':id_prov' => $id_proveedor,
                ':precio'  => (float)$precio
            ]);
        }

        Auditoria::registrar($idUsuario, 'INSERT', 'productos', $nuevoId, "Creado producto $codigo ($nombre)");
        $_SESSION['mensaje_exito'] = "Producto $codigo registrado correctamente.";
    }

    header('Location: gestion-productos.php');
    exit;

} catch (PDOException $e) {
    error_log('Error en procesar_producto.php: ' . $e->getMessage());
    $_SESSION['errores_producto'] = ['general' => 'Ocurrió un error al guardar los datos en la base de datos.'];
    $_SESSION['valores_producto'] = $_POST;
    header('Location: formulario-producto.php' . ($idProducto > 0 ? '?id=' . $idProducto : ''));
    exit;
}