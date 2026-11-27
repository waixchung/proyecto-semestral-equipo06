<?php
// dashboard-administrador.php
define('APP_ACCESO', true);
session_start();

// 1. Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

// 2. Obtener y normalizar el rol del usuario
$rol_actual = strtolower(trim((string)$_SESSION['rol_usuario']));

// 3. Permitir acceso únicamente al administrador
if ($rol_actual !== 'administrador') {
    http_response_code(403);
    exit(
        '<h2>403 - Acceso denegado</h2>' .
        '<p>No tienes permisos para acceder al dashboard de administrador.</p>' .
        '<a href="index.php">Volver al inicio</a>'
    );
}

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';

$titulo_pagina       = 'Dashboard Administrador · Inventario';
$pagina_actual       = 'dashboard';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

// --- LÓGICA DEL DASHBOARD DESDE MYSQL ---
// 1. Stock bajo ordenado por ubicación (Procedimiento Almacenado)
$alertas_stock = Producto::listarStockBajoPorUbicacion('');

// 2. Productos próximos a vencer o vencidos
$todos_productos     = Producto::listar('', 0, 'vence', 'ASC', 1, 200)['datos'];
$alertas_vencimiento = [];

foreach ($todos_productos as $prod) {
    $estado_vence = determinarEstadoVencimiento($prod['vence']);
    if ($estado_vence === 'Por vencer' || $estado_vence === 'Vencido') {
        $prod['estado_vencimiento'] = $estado_vence;
        $alertas_vencimiento[]      = $prod;
    }
}

// 3. Últimos eventos de auditoría (JOIN auditoria + usuarios)
$pdo = Conexion::obtener();
$ultimos_eventos = $pdo->query(
    'SELECT a.fecha_hora, a.accion, a.tabla_afectada, a.detalle, u.nombre AS usuario
     FROM auditoria a
     LEFT JOIN usuarios u ON a.id_usuario = u.id_usuario
     ORDER BY a.fecha_hora DESC, a.id_auditoria DESC
     LIMIT 8'
)->fetchAll();

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Resumen del inventario</h1>
  </div>

  <!-- SECCIÓN STOCK BAJO -->
  <section class="eq06-seccion">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; flex-wrap: wrap; gap: 10px;">
        <h2 style="margin: 0; font-size: 1.25rem;">Alertas: stock actual &lt; stock mínimo</h2>
        <div style="display: flex; gap: 10px;">
          <a href="gestion-productos.php?stock_bajo=1" class="eq06-btn eq06-btn-secundario" style="padding: 8px 14px; text-decoration: none; border-radius: 4px;">Reporte por ubicación</a>
          <a href="gestion-productos.php" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Gestionar productos</a>
        </div>
    </div>
    
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con stock bajo (ordenados por ubicación)</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Ubicación</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Stock actual</th>
            <th scope="col" style="padding: 10px;">Mínimo</th>
            <th scope="col" style="padding: 10px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alertas_stock)): ?>
            <tr>
              <td colspan="5" style="padding: 15px; text-align: center; color: #666;">El inventario está en niveles óptimos. No hay productos con stock bajo.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($alertas_stock as $item): 
                $unidad = $item['unidad_medida'] ?? '';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px;"><strong><?php echo e($item['ubicacion']); ?></strong></td>
              <td style="padding: 10px;"><?php echo e($item['nombre']); ?> (<?php echo e($item['codigo']); ?>)</td>
              <td style="padding: 10px; color: #A61E4D; font-weight: bold;"><?php echo (int)$item['stock'] . ' ' . e($unidad); ?></td>
              <td style="padding: 10px;"><?php echo (int)$item['minimo'] . ' ' . e($unidad); ?></td>
              <td style="padding: 10px;">
                <a href="detalle-producto.php?id=<?php echo (int)$item['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECCIÓN VENCIMIENTOS -->
  <section class="eq06-seccion" style="margin-top: 30px;">
    <h2 style="margin-bottom: 15px; font-size: 1.25rem;">Alertas: Productos próximos a vencer o vencidos</h2>
    
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con fecha de caducidad cercana o vencida en stock</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Código</th>
            <th scope="col" style="padding: 10px;">Ubicación</th>
            <th scope="col" style="padding: 10px;">Fecha de Vencimiento</th>
            <th scope="col" style="padding: 10px;">Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alertas_vencimiento)): ?>
            <tr>
              <td colspan="5" style="padding: 15px; text-align: center; color: #666;">No hay productos próximos a vencer.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($alertas_vencimiento as $item): 
                $badge_estilo = ($item['estado_vencimiento'] === 'Vencido') 
                    ? 'background-color: #A61E4D; color: white;' 
                    : 'background-color: #F08C00; color: white;'; 
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px;">
                <a href="detalle-producto.php?id=<?php echo (int)$item['id_producto']; ?>" style="color: inherit; text-decoration: underline;">
                  <?php echo e($item['nombre']); ?>
                </a>
              </td>
              <td style="padding: 10px;"><?php echo e($item['codigo']); ?></td>
              <td style="padding: 10px;"><?php echo e($item['ubicacion']); ?></td>
              <td style="padding: 10px;"><?php echo e($item['vence']); ?></td>
              <td style="padding: 10px;">
                <span class="eq06-badge" style="<?php echo $badge_estilo; ?> padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">
                    <?php echo e($item['estado_vencimiento']); ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECCIÓN AUDITORÍA RECIENTE -->
  <section class="eq06-seccion" style="margin-top: 30px;">
    <h2 style="margin-bottom: 15px; font-size: 1.25rem;">Bitácora reciente del sistema (Auditoría)</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Últimas acciones registradas en la base de datos</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Fecha y hora</th>
            <th scope="col" style="padding: 10px;">Usuario</th>
            <th scope="col" style="padding: 10px;">Acción</th>
            <th scope="col" style="padding: 10px;">Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($ultimos_eventos)): ?>
            <tr>
              <td colspan="4" style="padding: 15px; text-align: center; color: #666;">Aún no hay eventos registrados en auditoría.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($ultimos_eventos as $ev): ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px; white-space: nowrap;"><?php echo e($ev['fecha_hora']); ?></td>
              <td style="padding: 10px;"><?php echo e($ev['usuario'] ?? 'Anónimo / Fallido'); ?></td>
              <td style="padding: 10px;"><strong><?php echo e($ev['accion']); ?></strong></td>
              <td style="padding: 10px;"><?php echo e($ev['detalle']); ?></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>