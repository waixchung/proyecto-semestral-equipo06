<?php
// procesar_salida.php · Procesa el formulario de registro-salida.php con MySQL (SP transaccional)
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Auditoria.php';

// 1. Proteger el script en el servidor: solo encargado y administrador
verificarAcceso(['administrador', 'encargado']);

$rol_sesion = $_SESSION['rol_usuario'];
$id_usuario = (int)$_SESSION['id_usuario'];

// Hora de Panamá
date_default_timezone_set('America/Panama');

// 2. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro-salida.php');
    exit;
}

// 3. Recibir y limpiar entradas (sin escapar HTML antes de validar/guardar)
$codigo_producto = (isset($_POST['producto']) && is_string($_POST['producto'])) ? limpiarEntrada($_POST['producto']) : '';
$fecha           = (isset($_POST['fecha']) && is_string($_POST['fecha'])) ? limpiarEntrada($_POST['fecha']) : '';
$cantidad        = (isset($_POST['cantidad']) && is_string($_POST['cantidad'])) ? limpiarEntrada($_POST['cantidad']) : '';
$motivo          = (isset($_POST['motivo']) && is_string($_POST['motivo'])) ? limpiarEntrada($_POST['motivo']) : '';
$observacion     = (isset($_POST['observacion']) && is_string($_POST['observacion'])) ? trim($_POST['observacion']) : '';

// 4. Validar formato de datos
$validador = new Validador();
$validador->requerido('producto', $codigo_producto, 'Debe seleccionar un producto.');
$validador->requerido('fecha', $fecha, 'La fecha es obligatoria.');
$validador->requerido('cantidad', $cantidad, 'La cantidad es obligatoria.');
$validador->requerido('motivo', $motivo, 'Debe seleccionar un motivo.');
$errores = $validador->getErrores();

// Fecha real en formato AAAA-MM-DD
$fecha_obj = null;
if (!isset($errores['fecha'])) {
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha) {
        $errores['fecha'] = 'La fecha no es válida.';
    }
}

// Cantidad numérica entera mayor que 0 (alineado con INT de la tabla productos/movimientos)
if (!isset($errores['cantidad']) && (!is_numeric($cantidad) || (int)$cantidad <= 0 || (float)$cantidad != (int)$cantidad)) {
    $errores['cantidad'] = 'La cantidad debe ser un número entero mayor que 0.';
}

// Motivo dentro de la lista permitida
$motivos = ['consumo' => 'Consumo', 'utilizacion' => 'Utilización', 'retiro' => 'Retiro'];
if (!isset($errores['motivo']) && !isset($motivos[$motivo])) {
    $errores['motivo'] = 'El motivo seleccionado no es válido.';
}

// Observación opcional: máximo 200 caracteres
if (mb_strlen($observacion) > 200) {
    $errores['observacion'] = 'La observación no puede superar los 200 caracteres.';
}

// 5. Buscar el producto real en MySQL
$producto = null;
if (!isset($errores['producto'])) {
    $producto = Producto::obtenerPorCodigo($codigo_producto);
    if ($producto === null) {
        $errores['producto'] = 'El producto seleccionado no existe en el catálogo.';
    }
}

// 6. Reglas de negocio previas al procedimiento almacenado
$resumen = null;
if ($producto !== null) {
    $disponible = (int)$producto['stock'];
    $minimo     = (int)$producto['minimo'];
    $cantInt    = (int)$cantidad;

    $resumen = [
        'nombre'     => $producto['nombre'],
        'disponible' => $disponible,
        'minimo'     => $minimo,
        'saldo'      => null,
        'stock_bajo' => false
    ];

    // 6.1 Un producto vencido no puede salir
    if (determinarEstadoVencimiento($producto['vence']) === 'Vencido') {
        $errores['producto'] = "Movimiento rechazado: el producto '" . $producto['nombre']
            . "' está vencido (venció el " . date('d/m/Y', strtotime($producto['vence'])) . ').';
    }

    // 6.2 Una salida no puede superar el stock disponible
    if (!isset($errores['cantidad'])) {
        if ($cantInt > $disponible) {
            $errores['cantidad'] = 'Stock insuficiente: la cantidad (' . $cantInt
                . ') supera lo disponible (' . $disponible . ').';
        } else {
            $resumen['saldo']      = $disponible - $cantInt;
            $resumen['stock_bajo'] = ($resumen['saldo'] < $minimo);
        }
    }
}

// 7. Ejecutar procedimiento almacenado transaccional en MySQL si no hay errores
if (empty($errores) && $producto !== null) {
    try {
        $pdo = Conexion::obtener();
        $idProducto = (int)$producto['id_producto'];
        $cantInt    = (int)$cantidad;

        // Construir detalle del motivo incluyendo fecha seleccionada y observación opcional
        $motivoDetalle = $motivos[$motivo] . ' (Fecha: ' . $fecha_obj->format('d/m/Y') . ')';
        if ($observacion !== '') {
            $motivoDetalle .= ' - ' . $observacion;
        }
        $motivoDetalle = mb_substr($motivoDetalle, 0, 255);

        // Invocar procedimiento almacenado transaccional (descuenta stock + inserta en movimientos con ROLLBACK automático si falla)
        $stmt = $pdo->prepare('CALL sp_registrar_movimiento(:id_producto, :id_usuario, :tipo, :cantidad, :motivo)');
        $stmt->execute([
            ':id_producto' => $idProducto,
            ':id_usuario'  => $id_usuario,
            ':tipo'        => 'salida',
            ':cantidad'    => $cantInt,
            ':motivo'      => $motivoDetalle
        ]);
        $stmt->closeCursor();

        // Consultar el saldo actualizado directamente desde MySQL
        $productoActualizado   = Producto::obtenerPorId($idProducto);
        $saldoReal             = (int)($productoActualizado['stock'] ?? ($disponible - $cantInt));
        $resumen['saldo']      = $saldoReal;
        $resumen['stock_bajo'] = ($saldoReal < (int)$producto['minimo']);

        // Registrar evento en auditoría
        Auditoria::registrar(
            $id_usuario,
            'SALIDA_INVENTARIO',
            'movimientos',
            $idProducto,
            "Salida de {$cantInt} uds. de {$producto['codigo']} ({$producto['nombre']}). Saldo actual: {$saldoReal}"
        );

    } catch (PDOException $e) {
        // Si el procedimiento almacenado lanza SIGNAL SQLSTATE '45000' (ej. stock insuficiente concurrente), capturarlo limpio
        error_log('Error en sp_registrar_movimiento: ' . $e->getMessage());
        $errores['cantidad'] = 'No se pudo procesar la salida: verifique que haya stock disponible suficiente.';
    }
}

// 8. Resultado: volver al formulario con errores o mostrar la confirmación
if (!empty($errores)) {
    $valores = $_POST;
    require_once __DIR__ . '/registro-salida.php';
} else {
    $titulo_pagina       = 'Confirmación de salida · Inventario';
    $pagina_actual       = 'salidas';
    $rol_usuario         = $rol_sesion;
    $nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
    $usuario_autenticado = true;

    require_once __DIR__ . '/includes/encabezado.php';
    require_once __DIR__ . '/includes/menu.php';
    ?>
    <main class="eq06-main">
      <div class="eq06-titulo-pagina">
        <h1>Salida registrada</h1>
      </div>

      <div class="eq06-seccion">
        <div class="eq06-alerta eq06-alerta-info" role="status">
          <strong>Salida registrada correctamente.</strong> El movimiento de inventario se guardó en la base de datos y el stock fue descontado.
        </div>

        <ul>
          <li><strong>Producto:</strong> <?php echo e($producto['nombre']); ?> (<abbr title="Código"><?php echo e($producto['codigo']); ?></abbr>)</li>
          <li><strong>Fecha:</strong> <time datetime="<?php echo e($fecha); ?>"><?php echo $fecha_obj->format('d/m/Y'); ?></time></li>
          <li><strong>Cantidad retirada:</strong> <?php echo (int)$cantidad; ?> <?php echo e($producto['unidad_medida']); ?></li>
          <li><strong>Motivo:</strong> <?php echo e($motivos[$motivo]); ?></li>
          <li><strong>Observación:</strong> <?php echo ($observacion !== '') ? e($observacion) : 'Sin observación'; ?></li>
          <li>
            <strong>Saldo actual:</strong> <?php echo (int)$resumen['saldo']; ?> <?php echo e($producto['unidad_medida']); ?> ·
            <strong>Mínimo del producto:</strong> <?php echo (int)$resumen['minimo']; ?> <?php echo e($producto['unidad_medida']); ?>
            <?php if ($resumen['stock_bajo']): ?>
              <span class="eq06-etiqueta-bajo">Stock bajo</span>
            <?php endif; ?>
          </li>
        </ul>

        <div class="eq06-acciones-form">
          <a class="eq06-boton" href="historial-movimientos.php">Ver historial</a>
          <a class="eq06-boton eq06-boton-secundario" href="registro-salida.php">Registrar otra salida</a>
        </div>
      </div>
    </main>
    <?php
    require_once __DIR__ . '/includes/pie.php';
}