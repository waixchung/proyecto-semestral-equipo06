<?php
// detalle-producto.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';

verificarAcceso(['administrador', 'encargado', 'recepcionista']);

// 1. Capturar el parámetro 'id' (acepta tanto id_producto numérico como código PR-xxxx)
$paramId = trim($_GET['id'] ?? '');
$producto_actual = null;

if ($paramId !== '') {
    if (ctype_digit($paramId)) {
        $producto_actual = Producto::obtenerPorId((int)$paramId);
    } else {
        $producto_actual = Producto::obtenerPorCodigo($paramId);
    }
}

// 2. Si no existe, redirigir al catálogo
if (!$producto_actual) {
    $_SESSION['mensaje_error'] = 'El producto solicitado no existe.';
    header('Location: gestion-productos.php');
    exit;
}

$idProducto = (int)$producto_actual['id_producto'];

// 3. Obtener proveedores asociados (JOIN N:M producto_proveedor + proveedores)
$proveedores_asociados = Producto::listarProveedoresPorProducto($idProducto);
$proveedor_principal = !empty($proveedores_asociados)
    ? $proveedores_asociados[0]['nombre_empresa'] . ' ($' . number_format((float)$proveedores_asociados[0]['precio_compra'], 2) . ')'
    : 'Sin asignar';

// 4. Obtener historial de movimientos del producto (JOIN movimientos + usuarios)
$pdo = Conexion::obtener();
$stmtMov = $pdo->prepare(
    'SELECT m.fecha_movimiento, m.tipo, m.cantidad, m.motivo, u.nombre AS usuario
     FROM movimientos m
     INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
     WHERE m.id_producto = :id_producto
     ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC'
);
$stmtMov->execute([':id_producto' => $idProducto]);
$movimientos_producto = $stmtMov->fetchAll();

$titulo_pagina       = 'Detalle de producto · Inventario';
$pagina_actual       = 'productos';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;
$puede_editar        = in_array($rol_usuario, ['administrador', 'encargado'], true);

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';

// 5. Lógicas de negocio (Stock y Vencimiento)
$es_stock_bajo = ((int)$producto_actual['stock'] < (int)$producto_actual['minimo']);
$estado_vence  = determinarEstadoVencimiento($producto_actual['vence']);
$unidad        = $producto_actual['unidad_medida'] ?? '-';
$precio        = '$' . number_format((float)$producto_actual['precio'], 2);
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina" style="display: flex; justify-content: space-between; align-items: center;">
    <h1><?php echo e($producto_actual['nombre']); ?> · <abbr title="Código de producto"><?php echo e($producto_actual['codigo']); ?></abbr></h1>
    <?php if ($puede_editar): ?>
      <a href="formulario-producto.php?id=<?php echo $idProducto; ?>" class="eq06-btn">Editar</a>
    <?php endif; ?>
  </div>

  <section class="eq06-seccion" aria-labelledby="datos-producto">
    <h2 id="datos-producto" class="eq06-sr-only">Datos del producto</h2>
    <dl class="eq06-detalle-grid">
      <div class="eq06-detalle-item">
        <dt>Categoría</dt>
        <dd><?php echo e($producto_actual['categoria']); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Unidad</dt>
        <dd><?php echo e($unidad); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Ubicación en bodega</dt>
        <dd><strong><?php echo e($producto_actual['ubicacion']); ?></strong></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Proveedor principal</dt>
        <dd><?php echo e($proveedor_principal); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Precio unitario</dt>
        <dd><?php echo e($precio); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Fecha de caducidad</dt>
        <dd>
          <?php echo !empty($producto_actual['vence']) ? e($producto_actual['vence']) : 'No aplica'; ?>
          <?php if ($estado_vence === 'Vencido'): ?>
            <span class="eq06-badge eq06-badge-bajo" style="margin-left: 4px;">Vencido</span>
          <?php elseif ($estado_vence === 'Por vencer'): ?>
            <span class="eq06-badge" style="background-color: #F08C00; color: white; margin-left: 4px;">Por vencer</span>
          <?php endif; ?>
        </dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Stock actual</dt>
        <dd>
          <?php echo (int)$producto_actual['stock'] . ' ' . e($unidad); ?>
          <?php if ($es_stock_bajo): ?>
            · <span class="eq06-badge eq06-badge-bajo">Stock bajo</span>
          <?php endif; ?>
        </dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Stock mínimo</dt>
        <dd><?php echo (int)$producto_actual['minimo'] . ' ' . e($unidad); ?></dd>
      </div>
    </dl>
  </section>

  <!-- Proveedores vinculados (JOIN N:M) -->
  <?php if (!empty($proveedores_asociados)): ?>
    <section class="eq06-seccion" style="margin-top: 20px;">
      <h2>Proveedores vinculados</h2>
      <div class="eq06-tabla-wrap">
        <table class="eq06-tabla">
          <thead>
            <tr>
              <th scope="col">Código</th>
              <th scope="col">Empresa</th>
              <th scope="col">Contacto</th>
              <th scope="col">Teléfono</th>
              <th scope="col">Precio compra</th>
              <th scope="col">Entrega (días)</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($proveedores_asociados as $pv): ?>
              <tr>
                <td><?php echo e($pv['codigo']); ?></td>
                <td><?php echo e($pv['nombre_empresa']); ?></td>
                <td><?php echo e($pv['contacto']); ?></td>
                <td><?php echo e($pv['telefono']); ?></td>
                <td>$<?php echo number_format((float)$pv['precio_compra'], 2); ?></td>
                <td><?php echo (int)$pv['tiempo_entrega_dias']; ?> días</td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  <?php endif; ?>

  <!-- Historial real de movimientos del producto -->
  <section class="eq06-seccion" style="margin-top: 20px;">
    <h2>Historial del producto · Más reciente primero</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Movimientos de <?php echo e($producto_actual['nombre']); ?></caption>
        <thead>
          <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Tipo</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Usuario</th>
            <th scope="col">Referencia / Motivo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($movimientos_producto)): ?>
            <tr>
              <td colspan="5" style="text-align: center; color: #666;">No hay movimientos recientes registrados para este producto.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($movimientos_producto as $mov): ?>
              <tr>
                <td><?php echo e($mov['fecha_movimiento']); ?></td>
                <td><?php echo e(ucfirst($mov['tipo'])); ?></td>
                <td><?php echo (int)$mov['cantidad'] . ' ' . e($unidad); ?></td>
                <td><?php echo e($mov['usuario']); ?></td>
                <td><?php echo e($mov['motivo']); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <p class="eq06-mt-16">
      <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Volver al catálogo</a>
    </p>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>