<?php
// dashboard-encargado.php
define('APP_ACCESO', true);
session_start();

// 1. Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

// 2. Obtener y normalizar el rol del usuario
$rol_actual = strtolower(trim((string)$_SESSION['rol_usuario']));

// 3. Permitir acceso únicamente al encargado
if ($rol_actual !== 'encargado') {
    http_response_code(403);
    exit(
        '<h2>403 - Acceso denegado</h2>' .
        '<p>No tienes permisos para acceder al dashboard del encargado.</p>' .
        '<a href="index.php">Volver al inicio</a>'
    );
}

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';

date_default_timezone_set('America/Panama');

$titulo_pagina       = 'Dashboard Encargado · Inventario';
$pagina_actual       = 'dashboard';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

$pdo = Conexion::obtener();
$hoy = date('Y-m-d');

// --- INDICADORES REALES DESDE MYSQL ---
$total_activos = (int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();

// Stock bajo ordenado por ubicación (Procedimiento Almacenado)
$alertas_stock       = Producto::listarStockBajoPorUbicacion('');
$cantidad_stock_bajo = count($alertas_stock);

// Entradas y salidas registradas en el día actual
$stmtEntradas = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE tipo = 'entrada' AND DATE(fecha_movimiento) = :hoy");
$stmtEntradas->execute([':hoy' => $hoy]);
$entradas_hoy = (int)$stmtEntradas->fetchColumn();

$stmtSalidas = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE tipo = 'salida' AND DATE(fecha_movimiento) = :hoy");
$stmtSalidas->execute([':hoy' => $hoy]);
$salidas_hoy = (int)$stmtSalidas->fetchColumn();

// Productos próximos a vencer o vencidos
$todos_productos     = Producto::listar('', 0, 'vence', 'ASC', 1, 200)['datos'];
$alertas_vencimiento = [];
foreach ($todos_productos as $prod) {
    $estado_vence = determinarEstadoVencimiento($prod['vence']);
    if ($estado_vence === 'Por vencer' || $estado_vence === 'Vencido') {
        $prod['estado_vencimiento'] = $estado_vence;
        $alertas_vencimiento[]      = $prod;
    }
}

// Últimos 6 movimientos reales (JOIN movimientos + productos + categorias + usuarios)
$movimientos_recientes = $pdo->query(
    'SELECT m.fecha_movimiento, m.tipo, m.cantidad,
            p.id_producto, p.nombre AS producto, p.codigo, c.unidad_medida,
            u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON m.id_producto = p.id_producto
     INNER JOIN categorias c ON p.id_categoria = c.id_categoria
     INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
     ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC
     LIMIT 6'
)->fetchAll();

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main id="eq06-principal" class="eq06-main">
  <h1>Resumen del inventario</h1>

  <!-- INDICADORES REALES -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-indicadores">
    <h2 class="eq06-oculto" id="eq06-titulo-indicadores">Indicadores del día</h2>
    <div class="eq06-indicadores" style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf('%02d', $total_activos); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Productos registrados</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf('%02d', $cantidad_stock_bajo); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Stock bajo</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf('%02d', $entradas_hoy); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Entradas del día</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf('%02d', $salidas_hoy); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Salidas del día</h3>
      </article>
    </div>
  </section>

  <!-- ACCIONES RÁPIDAS -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-acciones" style="margin-bottom: 30px;">
    <h2 class="eq06-oculto" id="eq06-titulo-acciones">Acciones rápidas</h2>
    <div class="eq06-acciones" style="display: flex; gap: 10px; flex-wrap: wrap;">
      <a class="eq06-btn" href="registro-salida.php" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Registrar salida</a>
      <a class="eq06-btn eq06-btn-secundario" href="gestion-productos.php" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Gestionar productos</a>
      <a class="eq06-btn eq06-btn-secundario" href="gestion-productos.php?stock_bajo=1" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Reporte stock bajo por ubicación</a>
    </div>
  </section>

  <!-- SECCIÓN STOCK BAJO -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alertas">
    <h2 id="eq06-titulo-alertas" style="margin-bottom: 15px; font-size: 1.25rem;">Alertas: stock actual &lt; stock mínimo</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos cuyo stock actual está por debajo del mínimo (ordenados por ubicación)</caption>
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
              <th scope="row" style="padding: 10px; font-weight: normal; text-align: left;"><?php echo e($item['nombre']); ?> (<?php echo e($item['codigo']); ?>)</th>
              <td style="padding: 10px;">
                  <?php echo (int)$item['stock']; ?> <abbr title="unidad"><?php echo e($unidad); ?></abbr> 
                  <span class="eq06-badge eq06-badge-bajo" style="background-color: #A61E4D; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px;">Stock bajo</span>
              </td>
              <td style="padding: 10px;"><?php echo (int)$item['minimo']; ?> <abbr title="unidad"><?php echo e($unidad); ?></abbr></td>
              <td style="padding: 10px;">
                <a href="detalle-producto.php?id=<?php echo (int)$item['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;" aria-label="Ver detalle del producto <?php echo e($item['nombre']); ?>">Ver detalle</a>
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
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con fecha de caducidad cercana o vencidos</caption>
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
              <td style="padding: 10px;"><?php echo e($item['nombre']); ?></td>
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

  <!-- MOVIMIENTOS RECIENTES REALES -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimientos" style="margin-top: 30px;">
    <h2 id="eq06-titulo-movimientos" style="margin-bottom: 15px; font-size: 1.25rem;">Movimientos recientes</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Últimas entradas y salidas registradas en MySQL</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Fecha</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Tipo</th>
            <th scope="col" style="padding: 10px;">Cantidad</th>
            <th scope="col" style="padding: 10px;">Usuario</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($movimientos_recientes)): ?>
            <tr>
              <td colspan="5" style="padding: 15px; text-align: center; color: #666;">Aún no hay movimientos registrados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($movimientos_recientes as $mov):
              $esEntrada = ($mov['tipo'] === 'entrada');
              $signo     = $esEntrada ? '+' : '−';
              $colorCant = $esEntrada ? '#155724' : '#A61E4D';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px;"><time datetime="<?php echo e($mov['fecha_movimiento']); ?>"><?php echo e(date('d/m/Y H:i', strtotime($mov['fecha_movimiento']))); ?></time></td>
              <td style="padding: 10px;"><?php echo e($mov['producto']); ?> (<?php echo e($mov['codigo']); ?>)</td>
              <td style="padding: 10px;"><?php echo e(ucfirst($mov['tipo'])); ?></td>
              <td style="padding: 10px; font-weight: bold; color: <?php echo $colorCant; ?>;">
                <?php echo $signo . (int)$mov['cantidad']; ?> <abbr title="unidad"><?php echo e($mov['unidad_medida']); ?></abbr>
              </td>
              <td style="padding: 10px;"><?php echo e($mov['usuario']); ?></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>