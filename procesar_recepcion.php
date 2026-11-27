<?php
// procesar_recepcion.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Proveedor.php';
require_once __DIR__ . '/clases/Auditoria.php';

verificarAcceso(['recepcionista', 'administrador']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: recepcion-mercancia.php');
    exit;
}

date_default_timezone_set('America/Panama');

$idUsuario       = (int)$_SESSION['id_usuario'];
$numeroRecepcion = limpiarEntrada($_POST['numero_recepcion'] ?? 'REC-0001');
$fechaRecepcion  = limpiarEntrada($_POST['fecha_recepcion'] ?? '');
$idProveedor     = (int)($_POST['id_proveedor'] ?? 0);
$productosIds    = $_POST['productos'] ?? [];
$cantidades      = $_POST['cantidades'] ?? [];

$errores = [];

$fechaObj = DateTime::createFromFormat('Y-m-d', $fechaRecepcion);
if (!$fechaObj || $fechaObj->format('Y-m-d') !== $fechaRecepcion) {
    $errores[] = 'La fecha de recepción no es válida.';
}

$proveedor = $idProveedor > 0 ? Proveedor::obtenerPorId($idProveedor) : null;
if (!$proveedor || (int)$proveedor['activo'] !== 1) {
    $errores[] = 'Debe seleccionar un proveedor activo válido.';
}

// Validar todas las líneas antes de guardar (regla: "No se guarda stock parcial")
$lineasValidas = [];
if (!is_array($productosIds) || !is_array($cantidades) || count($productosIds) === 0) {
    $errores[] = 'Debe incluir al menos un producto en la recepción.';
} else {
    foreach ($productosIds as $idx => $idProdRaw) {
        $idProd   = (int)$idProdRaw;
        $cantRaw  = $cantidades[$idx] ?? '';
        $numLinea = $idx + 1;

        if ($idProd <= 0) {
            $errores[] = "Línea $numLinea: debe seleccionar un producto válido.";
            continue;
        }
        if (!is_numeric($cantRaw) || (int)$cantRaw <= 0 || (float)$cantRaw != (int)$cantRaw) {
            $errores[] = "Línea $numLinea: la cantidad debe ser un número entero mayor que 0.";
            continue;
        }
        $prodInfo = Producto::obtenerPorId($idProd);
        if (!$prodInfo) {
            $errores[] = "Línea $numLinea: el producto seleccionado no existe.";
            continue;
        }

        $lineasValidas[] = [
            'id_producto' => $idProd,
            'codigo'      => $prodInfo['codigo'],
            'nombre'      => $prodInfo['nombre'],
            'cantidad'    => (int)$cantRaw
        ];
    }
}

if (!empty($errores)) {
    $_SESSION['errores_recepcion'] = $errores;
    $_SESSION['valores_recepcion'] = [
        'fecha_recepcion' => $fechaRecepcion,
        'id_proveedor'    => (string)$idProveedor
    ];
    header('Location: recepcion-mercancia.php');
    exit;
}

try {
    $pdo    = Conexion::obtener();
    $stmtSp = $pdo->prepare('CALL sp_registrar_movimiento(:id_producto, :id_usuario, :tipo, :cantidad, :motivo)');

    foreach ($lineasValidas as $linea) {
        $motivo = mb_substr(
            "{$numeroRecepcion} · Proveedor: {$proveedor['nombre_empresa']} (Fecha: " . $fechaObj->format('d/m/Y') . ')',
            0,
            255
        );

        $stmtSp->execute([
            ':id_producto' => $linea['id_producto'],
            ':id_usuario'  => $idUsuario,
            ':tipo'        => 'entrada',
            ':cantidad'    => $linea['cantidad'],
            ':motivo'      => $motivo
        ]);
        $stmtSp->closeCursor();

        Auditoria::registrar(
            $idUsuario,
            'ENTRADA_INVENTARIO',
            'movimientos',
            $linea['id_producto'],
            "Recepción {$numeroRecepcion}: +{$linea['cantidad']} uds. de {$linea['codigo']} ({$linea['nombre']}) - Prov: {$proveedor['nombre_empresa']}"
        );
    }

    $_SESSION['mensaje_exito_recepcion'] = "Recepción {$numeroRecepcion} confirmada correctamente (" . count($lineasValidas) . " producto(s) ingresados al inventario).";
    header('Location: recepcion-mercancia.php');
    exit;

} catch (PDOException $e) {
    error_log('Error en procesar_recepcion.php: ' . $e->getMessage());
    $_SESSION['errores_recepcion'] = ['Ocurrió un error al registrar la recepción en la base de datos.'];
    header('Location: recepcion-mercancia.php');
    exit;
}