<?php
// procesar_proveedor.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Proveedor.php';
require_once __DIR__ . '/clases/Auditoria.php';

// 1. Verificar sesión y rol permitido en el servidor (solo administrador y encargado)
verificarAcceso(['administrador', 'encargado']);

// 2. Solo aceptar peticiones POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion-proveedores.php');
    exit;
}

$accion      = $_POST['accion'] ?? 'crear';
$idUsuario   = (int)$_SESSION['id_usuario'];
$idProveedor = (int)($_POST['id_proveedor'] ?? 0);

// ==================== ACCIÓN: ELIMINAR (POST) ====================
if ($accion === 'eliminar') {
    if ($idProveedor <= 0) {
        $_SESSION['mensaje_error_prov'] = 'Identificador de proveedor inválido.';
        header('Location: gestion-proveedores.php');
        exit;
    }

    $provActual = Proveedor::obtenerPorId($idProveedor);
    $resultado  = Proveedor::eliminar($idProveedor);

    if ($resultado['exito']) {
        $detalle = $provActual
            ? "Eliminado proveedor {$provActual['codigo']} ({$provActual['nombre_empresa']})"
            : "Eliminado proveedor ID $idProveedor";
        Auditoria::registrar($idUsuario, 'DELETE', 'proveedores', $idProveedor, $detalle);
        $_SESSION['mensaje_exito_prov'] = $resultado['mensaje'];
    } else {
        $_SESSION['mensaje_error_prov'] = $resultado['mensaje'];
    }

    header('Location: gestion-proveedores.php');
    exit;
}

// ==================== ACCIÓN: CAMBIAR ESTADO ACTIVO/INACTIVO (POST) ====================
if ($accion === 'cambiar_estado') {
    $nuevoEstado = (int)($_POST['nuevo_estado'] ?? 1) === 1 ? 1 : 0;

    if ($idProveedor <= 0) {
        $_SESSION['mensaje_error_prov'] = 'Identificador de proveedor inválido.';
        header('Location: gestion-proveedores.php');
        exit;
    }

    try {
        $provActual = Proveedor::obtenerPorId($idProveedor);
        if (!$provActual) {
            $_SESSION['mensaje_error_prov'] = 'El proveedor solicitado no existe.';
            header('Location: gestion-proveedores.php');
            exit;
        }

        $pdo  = Conexion::obtener();
        $stmt = $pdo->prepare('UPDATE proveedores SET activo = :activo WHERE id_proveedor = :id');
        $stmt->execute([
            ':activo' => $nuevoEstado,
            ':id'     => $idProveedor
        ]);

        $etiquetaEstado = $nuevoEstado === 1 ? 'activado' : 'desactivado';
        Auditoria::registrar(
            $idUsuario,
            'UPDATE',
            'proveedores',
            $idProveedor,
            "Proveedor {$provActual['codigo']} ({$provActual['nombre_empresa']}) marcado como $etiquetaEstado"
        );

        $_SESSION['mensaje_exito_prov'] = "Proveedor {$provActual['nombre_empresa']} $etiquetaEstado correctamente.";
    } catch (PDOException $e) {
        error_log('Error al cambiar estado de proveedor: ' . $e->getMessage());
        $_SESSION['mensaje_error_prov'] = 'No se pudo actualizar el estado del proveedor.';
    }

    header('Location: gestion-proveedores.php');
    exit;
}

// ==================== ACCIÓN: CREAR O ACTUALIZAR ====================
// Usar limpiarEntrada() para no guardar entidades HTML (&amp;) en MySQL
$nombre_prov = limpiarEntrada($_POST['eq06-nombre-proveedor'] ?? '');
$contacto    = limpiarEntrada($_POST['eq06-contacto-proveedor'] ?? '');
$telefono    = limpiarEntrada($_POST['eq06-telefono-proveedor'] ?? '');
$correo      = limpiarEntrada($_POST['eq06-correo-proveedor'] ?? '');
$direccion   = limpiarEntrada($_POST['eq06-direccion-proveedor'] ?? '');
$referencia  = limpiarEntrada($_POST['eq06-referencia-proveedor'] ?? '');
$estado      = strtolower(limpiarEntrada($_POST['eq06-estado-proveedor'] ?? 'activo'));

// Validar campos obligatorios y formato
$validador = new Validador();
$validador->requerido('eq06-nombre-proveedor', $nombre_prov, 'El nombre o razón social es obligatorio.');
$validador->requerido('eq06-contacto-proveedor', $contacto, 'La persona de contacto es obligatoria.');
$validador->requerido('eq06-telefono-proveedor', $telefono, 'El teléfono es obligatorio.');

if ($correo !== '') {
    $validador->correo('eq06-correo-proveedor', $correo, 'El correo electrónico no tiene un formato válido.');
}

$errores = $validador->getErrores();

if (!in_array($estado, ['activo', 'inactivo'], true)) {
    $estado = 'activo';
}

// Si hay errores, regresar al formulario conservando valores
if (!empty($errores)) {
    $_SESSION['errores_proveedor'] = $errores;
    $_SESSION['valores_proveedor'] = [
        'id_proveedor'              => $idProveedor,
        'eq06-nombre-proveedor'     => $nombre_prov,
        'eq06-contacto-proveedor'   => $contacto,
        'eq06-telefono-proveedor'   => $telefono,
        'eq06-correo-proveedor'     => $correo,
        'eq06-direccion-proveedor'  => $direccion,
        'eq06-referencia-proveedor' => $referencia,
        'eq06-estado-proveedor'     => $estado
    ];

    $destinoError = ($accion === 'actualizar' && $idProveedor > 0)
        ? 'formulario-proveedor.php?id=' . $idProveedor
        : 'formulario-proveedor.php';
    header('Location: ' . $destinoError);
    exit;
}

// Combinar dirección principal y referencia si ambas existen
$direccionCompleta = $direccion;
if ($referencia !== '') {
    $direccionCompleta = ($direccion !== '')
        ? ($direccion . ' | ' . $referencia)
        : $referencia;
}

try {
    $proveedor = new Proveedor(
        $nombre_prov,
        $telefono,
        $correo,
        $direccionCompleta,
        $estado,
        '',
        $contacto
    );

    if ($accion === 'actualizar' && $idProveedor > 0) {
        $provActual = Proveedor::obtenerPorId($idProveedor);
        if (!$provActual) {
            $_SESSION['mensaje_error_prov'] = 'El proveedor que intenta editar no existe.';
            header('Location: gestion-proveedores.php');
            exit;
        }

        $proveedor->actualizar($idProveedor);
        Auditoria::registrar(
            $idUsuario,
            'UPDATE',
            'proveedores',
            $idProveedor,
            "Actualizado proveedor {$provActual['codigo']} ($nombre_prov)"
        );
        $_SESSION['mensaje_exito_prov'] = "Proveedor $nombre_prov actualizado correctamente.";
    } else {
        $nuevoId = $proveedor->guardar();
        Auditoria::registrar(
            $idUsuario,
            'INSERT',
            'proveedores',
            $nuevoId,
            "Creado proveedor {$proveedor->getCodigo()} ($nombre_prov)"
        );
        $_SESSION['mensaje_exito_prov'] = "Proveedor $nombre_prov ({$proveedor->getCodigo()}) registrado correctamente.";
    }

    header('Location: gestion-proveedores.php');
    exit;

} catch (PDOException $e) {
    error_log('Error en procesar_proveedor.php: ' . $e->getMessage());
    $_SESSION['errores_proveedor'] = ['general' => 'Ocurrió un error al guardar el proveedor en la base de datos.'];
    $_SESSION['valores_proveedor'] = $_POST;
    header('Location: formulario-proveedor.php' . ($idProveedor > 0 ? '?id=' . $idProveedor : ''));
    exit;
}