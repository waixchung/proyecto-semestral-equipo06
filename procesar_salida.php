<?php
// procesar_salida.php · Procesa el formulario de registro-salida.php
session_start();
 
// 1. Proteger el script: sin sesión, al login
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}
 
// Solo el Encargado de inventario registra salidas
$rol_sesion = $_SESSION['rol_usuario'];
if ($rol_sesion !== 'Encargado de inventario' && $rol_sesion !== 'Encargado') {
    header('Location: acceso-denegado.php');
    exit;
}
 
define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/validador.php'; // en minúscula: así se llama el archivo (Linux distingue mayúsculas)
require_once 'config/datos_demo.php';
 
// Hora de Panamá
date_default_timezone_set('America/Panama');
 
// 2. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: registro-salida.php');
    exit;
}
 
// 3. Recibir y sanear (los nombres son los atributos name de registro-salida.php)
$codigo_producto = (isset($_POST['producto']) && is_string($_POST['producto'])) ? limpiar($_POST['producto']) : '';
$fecha           = (isset($_POST['fecha']) && is_string($_POST['fecha'])) ? limpiar($_POST['fecha']) : '';
$cantidad        = (isset($_POST['cantidad']) && is_string($_POST['cantidad'])) ? limpiar($_POST['cantidad']) : '';
$motivo          = (isset($_POST['motivo']) && is_string($_POST['motivo'])) ? limpiar($_POST['motivo']) : '';
// La observación no pasa por limpiar(): se escapa al imprimirla (así no se escapa dos veces)
$observacion     = (isset($_POST['observacion']) && is_string($_POST['observacion'])) ? trim($_POST['observacion']) : '';
 
// 4. Validar el formato de los datos
$validador = new Validador();
$validador->requerido('producto', $codigo_producto, 'Debe seleccionar un producto.');
$validador->requerido('fecha', $fecha, 'La fecha es obligatoria.');
$validador->requerido('cantidad', $cantidad, 'La cantidad es obligatoria.');
$validador->requerido('motivo', $motivo, 'Debe seleccionar un motivo.');
$errores = $validador->getErrores();
 
// Fecha real en formato AAAA-MM-DD
if (!isset($errores['fecha'])) {
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha);
    if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha) {
        $errores['fecha'] = 'La fecha no es válida.';
    }
}
 
// Cantidad numérica y mayor que 0
if (!isset($errores['cantidad']) && (!is_numeric($cantidad) || (float) $cantidad <= 0)) {
    $errores['cantidad'] = 'La cantidad debe ser un número mayor que 0.';
}
 
// Motivo dentro de la lista permitida
$motivos = ['consumo' => 'Consumo', 'utilizacion' => 'Utilización', 'retiro' => 'Retiro'];
if (!isset($errores['motivo']) && !isset($motivos[$motivo])) {
    $errores['motivo'] = 'El motivo seleccionado no es válido.';
}
 
// Observación opcional: máximo 200 caracteres
if (strlen($observacion) > 200) {
    $errores['observacion'] = 'La observación no puede superar los 200 caracteres.';
}
 
// 5. Buscar el producto en el catálogo demo
$producto = null;
if (!isset($errores['producto'])) {
    foreach ($productos_demo as $item) {
        if ($item['codigo'] === $codigo_producto) {
            $producto = $item;
            break;
        }
    }
    if ($producto === null) {
        $errores['producto'] = 'El producto seleccionado no existe en el catálogo.';
    }
}
 
// 6. Reglas de negocio
$resumen = null;
if ($producto !== null) {
    $disponible = (float) $producto['stock'];
    $minimo = (float) $producto['minimo'];
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
 
    // 6.2 Regla asignada: una salida no puede superar el stock disponible
    if (!isset($errores['cantidad'])) {
        if ((float) $cantidad > $disponible) {
            $errores['cantidad'] = 'Stock insuficiente: la cantidad (' . (float) $cantidad
                . ') supera lo disponible (' . $disponible . ').';
        } else {
            // 6.3 Saldo resultante; si queda por debajo del mínimo del producto: "Stock bajo"
            $resumen['saldo'] = round($disponible - (float) $cantidad, 2);
            $resumen['stock_bajo'] = ($resumen['saldo'] < $minimo);
        }
    }
}
 
// 7. Resultado: volver al formulario con errores o mostrar la confirmación
if (!empty($errores)) {
    $valores = $_POST; // registro-salida.php usa $valores, $errores y $resumen
    require_once 'registro-salida.php';
} else {
    // Aún no hay base de datos (Hito #3): la salida se muestra pero no se guarda
    $titulo_pagina = 'Confirmación de salida · Inventario';
    $pagina_actual = 'salidas';
    $rol_usuario = $rol_sesion;
    $nombre_usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '';
    $usuario_autenticado = true;
 
    require_once 'includes/encabezado.php';
    require_once 'includes/menu.php';
    ?>
    <main class="eq06-main">
      <div class="eq06-titulo-pagina">
        <h1>Salida registrada</h1>
      </div>
 
      <div class="eq06-seccion">
        <div class="eq06-alerta eq06-alerta-info" role="status">
          <strong>Salida registrada correctamente.</strong> El movimiento de inventario se procesó con éxito.
        </div>
 
        <ul>
          <li><strong>Producto:</strong> <?php echo htmlspecialchars($producto['nombre'], ENT_QUOTES, 'UTF-8'); ?> (<abbr title="Código"><?php echo htmlspecialchars($producto['codigo'], ENT_QUOTES, 'UTF-8'); ?></abbr>)</li>
          <li><strong>Fecha:</strong> <time datetime="<?php echo htmlspecialchars($fecha, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $fecha_obj->format('d/m/Y'); ?></time></li>
          <li><strong>Cantidad retirada:</strong> <?php echo (float) $cantidad; ?></li>
          <li><strong>Motivo:</strong> <?php echo htmlspecialchars($motivos[$motivo], ENT_QUOTES, 'UTF-8'); ?></li>
          <li><strong>Observación:</strong> <?php echo ($observacion !== '') ? htmlspecialchars($observacion, ENT_QUOTES, 'UTF-8') : 'Sin observación'; ?></li>
          <li>
            <strong>Saldo:</strong> <?php echo $resumen['saldo']; ?> ·
            <strong>Mínimo del producto:</strong> <?php echo $resumen['minimo']; ?>
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
    require_once 'includes/pie.php';
}