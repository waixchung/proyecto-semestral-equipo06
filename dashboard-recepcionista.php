<?php
// dashboard-recepcionista.php
define('APP_ACCESO', true);
session_start();

// 1. Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

// 2. Obtener y normalizar el rol del usuario
$rol_actual = strtolower(trim((string)$_SESSION['rol_usuario']));

// 3. Permitir acceso únicamente al recepcionista
if ($rol_actual !== 'recepcionista') {
    http_response_code(403);
    exit(
        '<h2>403 - Acceso denegado</h2>' .
        '<p>No tienes permisos para acceder al dashboard del recepcionista.</p>' .
        '<a href="index.php">Volver al inicio</a>'
    );
}

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';

date_default_timezone_set('America/Panama');

$titulo_pagina       = 'Dashboard Recepcionista · Inventario';
$pagina_actual       = 'dashboard';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

$pdo = Conexion::obtener();
$hoy = date('Y-m-d');

// --- INDICADORES REALES DESDE MYSQL ---
$total_activos = (int)$pdo->query('SELECT COUNT(*) FROM productos')->fetchColumn();

$alertas_stock       = Producto::listarStockBajoPorUbicacion('');
$cantidad_stock_bajo = count($alertas_stock);

$stmtEntradas = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE tipo = 'entrada' AND DATE(fecha_movimiento) = :hoy");
$stmtEntradas->execute([':hoy' => $hoy]);
$entradas_hoy = (int)$stmtEntradas->fetchColumn();

$stmtSalidas = $pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE tipo = 'salida' AND DATE(fecha_movimiento) = :hoy");
$stmtSalidas->execute([':hoy' => $hoy]);
$salidas_hoy = (int)$stmtSalidas->fetchColumn();

// Consulta rápida de productos (con filtro opcional por GET)
$busqueda = trim($_GET['buscar'] ?? '');
$productos_consulta = Producto::listar($busqueda, 0, 'codigo', 'ASC', 1, 20)['datos'];

// Alertas de vencimiento en todo el catálogo
$todos_productos     = Producto::listar('', 0, 'vence', 'ASC', 1, 200)['datos'];
$alertas_vencimiento = [];
foreach ($todos_productos as $prod) {
    $estado_vence = determinarEstadoVencimiento($prod['vence']);
    if ($estado_vence === 'Por vencer' || $estado_vence === 'Vencido') {
        $prod['estado_vencimiento'] = $estado_vence;
        $alertas_vencimiento[]      = $prod;
    }
}

// Último movimiento registrado en el sistema
$ultimo_movimiento = $pdo->query(
    'SELECT m.fecha_movimiento, m.tipo, m.cantidad, m.motivo,
            p.nombre AS producto, p.codigo, c.unidad_medida,
            u.nombre AS usuario
     FROM movimientos m
     INNER JOIN productos p ON m.id_producto = p.id_producto
     INNER JOIN categorias c ON p.id_categoria = c.id_categoria
     INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
     ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC
     LIMIT 1'
)->fetch();

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
      <a class="eq06-btn" href="recepcion-mercancia.php" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Registrar recepción</a>
      <a class="eq06-btn eq06-btn-secundario" href="#eq06-consulta-productos" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Consultar productos</a>
    </div>
  </section>

  <!-- CONSULTA BÁSICA DESDE MYSQL -->
  <section class="eq06-seccion" id="eq06-consulta-productos" aria-labelledby="eq06-titulo-consulta">
    <h2 id="eq06-titulo-consulta" style="margin-bottom: 15px; font-size: 1.25rem;">Consulta básica de productos</h2>
    
    <form method="get" action="dashboard-recepcionista.php#eq06-consulta-productos" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
      <fieldset class="eq06-conjunto" style="border: none; padding: 0; margin: 0;">
        <legend style="font-weight: bold; margin-bottom: 10px;">Buscar producto</legend>
        <div class="eq06-grupo-campo" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 200px;">
            <label class="eq06-etiqueta" for="eq06-busqueda-producto" style="display: block; margin-bottom: 5px; font-size: 0.875rem;">Nombre, código o ubicación</label>
            <input class="eq06-campo" type="search" id="eq06-busqueda-producto"
                   name="buscar" maxlength="80" autocomplete="off"
                   value="<?php echo e($busqueda); ?>"
                   placeholder="Ejemplo: Arroz, PR-0511 o Zona A" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
          </div>
          <button class="eq06-btn" type="submit" style="background-color: #A61E4D; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Buscar</button>
          <?php if ($busqueda !== ''): ?>
            <a href="dashboard-recepcionista.php#eq06-consulta-productos" class="eq06-btn eq06-btn-secundario" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 14px; text-decoration: none; border-radius: 4px;">Limpiar</a>
          <?php endif; ?>
        </div>
      </fieldset>
    </form>

    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Resultados de la consulta de productos</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Código</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Ubicación</th>
            <th scope="col" style="padding: 10px;">Stock</th>
            <th scope="col" style="padding: 10px;">Unidad</th>
            <th scope="col" style="padding: 10px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($productos_consulta)): ?>
            <tr>
              <td colspan="6" style="padding: 15px; text-align: center; color: #666;">No hay productos registrados que coincidan con la búsqueda.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($productos_consulta as $item): 
                $unidad = $item['unidad_medida'] ?? '-';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <th scope="row" style="padding: 10px; font-weight: normal; text-align: left;"><?php echo e($item['codigo']); ?></th>
              <td style="padding: 10px;"><?php echo e($item['nombre']); ?></td>
              <td style="padding: 10px;"><?php echo e($item['ubicacion']); ?></td>
              <td style="padding: 10px;"><strong><?php echo (int)$item['stock']; ?></strong></td>
              <td style="padding: 10px;"><abbr title="unidad"><?php echo e($unidad); ?></abbr></td>
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

  <!-- ALERTAS RESUMIDAS REALES -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alerta" style="margin-top: 30px;">
    <h2 class="eq06-oculto" id="eq06-titulo-alerta">Alertas críticas de inventario</h2>
    <?php if (empty($alertas_stock) && empty($alertas_vencimiento)): ?>
      <p style="padding: 10px; border-left: 4px solid #28a745; background-color: #f8fff9;">No hay alertas críticas en el sistema en este momento.</p>
    <?php else: ?>
      <?php if (!empty($alertas_stock)): 
          $primer_stock = $alertas_stock[0];
          $unidad = $primer_stock['unidad_medida'] ?? '';
      ?>
      <p class="eq06-conjunto" style="margin-bottom: 10px; padding: 10px; border-left: 4px solid #A61E4D; background-color: #fff8f9;">
        <strong>Alerta de stock bajo:</strong> <?php echo e($primer_stock['nombre']); ?> (<?php echo e($primer_stock['ubicacion']); ?>) ·
        Stock <?php echo (int)$primer_stock['stock']; ?> <abbr title="unidad"><?php echo e($unidad); ?></abbr>
        / Mínimo <?php echo (int)$primer_stock['minimo']; ?> <abbr title="unidad"><?php echo e($unidad); ?></abbr> ·
        <span class="eq06-etiqueta-bajo" style="background-color: #A61E4D; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">Stock bajo</span> ·
        <a href="detalle-producto.php?id=<?php echo (int)$primer_stock['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>.
        <?php if ($cantidad_stock_bajo > 1): ?> <em>(y <?php echo $cantidad_stock_bajo - 1; ?> más)</em> <?php endif; ?>
      </p>
      <?php endif; ?>
      
      <?php if (!empty($alertas_vencimiento)): 
          $primer_vence = $alertas_vencimiento[0];
          $color_vence = ($primer_vence['estado_vencimiento'] === 'Vencido') ? '#A61E4D' : '#F08C00';
      ?>
      <p class="eq06-conjunto" style="padding: 10px; border-left: 4px solid <?php echo $color_vence; ?>; background-color: #fffcf8;">
        <strong>Alerta de caducidad:</strong> <?php echo e($primer_vence['nombre']); ?> · Fecha: <?php echo e($primer_vence['vence']); ?> ·
        <span style="background-color: <?php echo $color_vence; ?>; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;"><?php echo e($primer_vence['estado_vencimiento']); ?></span> ·
        <a href="detalle-producto.php?id=<?php echo (int)$primer_vence['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>.
      </p>
      <?php endif; ?>
    <?php endif; ?>
  </section>

  <!-- ÚLTIMO MOVIMIENTO REAL -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimiento" style="margin-top: 20px;">
    <h2 class="eq06-oculto" id="eq06-titulo-movimiento">Movimiento reciente</h2>
    <?php if ($ultimo_movimiento):
      $signo = ($ultimo_movimiento['tipo'] === 'entrada') ? '+' : '−';
    ?>
      <p style="padding: 10px; border: 1px solid #eee; border-radius: 4px; font-size: 0.9rem;">
        Movimiento reciente:
        <time datetime="<?php echo e($ultimo_movimiento['fecha_movimiento']); ?>"><?php echo e(date('d/m/Y H:i', strtotime($ultimo_movimiento['fecha_movimiento']))); ?></time> ·
        <?php echo e(ucfirst($ultimo_movimiento['tipo'])); ?> ·
        <?php echo e($ultimo_movimiento['producto']); ?>
        <strong><?php echo $signo . (int)$ultimo_movimiento['cantidad']; ?></strong> <abbr title="unidad"><?php echo e($ultimo_movimiento['unidad_medida']); ?></abbr> ·
        <?php echo e($ultimo_movimiento['motivo']); ?> ·
        <?php echo e($ultimo_movimiento['usuario']); ?>
      </p>
    <?php else: ?>
      <p style="padding: 10px; border: 1px solid #eee; border-radius: 4px; font-size: 0.9rem;">Aún no hay movimientos registrados en el sistema.</p>
    <?php endif; ?>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>