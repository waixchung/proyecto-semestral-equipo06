<?php
// listado-inventario.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Producto.php';

// Validar sesión activa para los roles del sistema
verificarAcceso(['administrador', 'encargado', 'recepcionista']);

$titulo_pagina       = 'Listado de Inventario · Inventario';
$pagina_actual       = 'listado-inventario';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;

// Parámetros opcionales de búsqueda y paginación
$busqueda  = trim($_GET['buscar'] ?? '');
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 15;

$resultado    = Producto::listar($busqueda, 0, 'codigo', 'ASC', $pagina, $porPagina);
$productos    = $resultado['datos'];
$total        = $resultado['total'];
$totalPaginas = $resultado['total_paginas'];

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <h1>Inventario</h1>
    <form method="get" action="listado-inventario.php" style="display: flex; gap: 8px;">
      <input type="text" name="buscar" class="eq06-campo" value="<?php echo e($busqueda); ?>" placeholder="Buscar producto, código o ubicación..." style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px;">
      <button type="submit" class="eq06-btn" style="padding: 6px 14px;">Filtrar</button>
      <?php if ($busqueda !== ''): ?>
        <a href="listado-inventario.php" class="eq06-btn eq06-btn-secundario" style="padding: 6px 12px;">Limpiar</a>
      <?php endif; ?>
    </form>
  </div>
  
  <div class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Existencias vigentes y alertas de vencimiento (<?php echo $total; ?> productos)</caption>
        <thead>
          <tr>
            <th scope="col">Producto / código</th>
            <th scope="col">Categoría</th>
            <th scope="col">Ubicación</th>
            <th scope="col">Stock</th>
            <th scope="col">Unidad</th>
            <th scope="col">Precio Unit.</th>
            <th scope="col">Estado</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($productos)): ?>
            <tr>
              <td colspan="8" style="text-align: center; color: #666; padding: 15px;">No hay existencias registradas que coincidan con la búsqueda.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($productos as $prod): 
                // 1. Evaluar estado del Stock (RB-01)
                $es_stock_bajo = ((int)$prod['stock'] < (int)$prod['minimo']);
                $badge_stock = $es_stock_bajo 
                    ? '<span class="eq06-badge eq06-badge-bajo">Stock bajo</span>' 
                    : '<span class="eq06-badge eq06-badge-normal">Normal</span>';

                // 2. Evaluar estado de Vencimiento
                $estado_vence = determinarEstadoVencimiento($prod['vence']);
                $badge_vence = '';
                if ($estado_vence === 'Vencido') {
                    $badge_vence = '<span class="eq06-badge eq06-badge-bajo" style="margin-left: 4px;">Vencido</span>';
                } elseif ($estado_vence === 'Por vencer') {
                    $badge_vence = '<span class="eq06-badge" style="background-color: #F08C00; color: white; margin-left: 4px;">Por vencer</span>';
                }

                // 3. Formatear unidad y precio unitario desde MySQL
                $unidad = !empty($prod['unidad_medida']) ? $prod['unidad_medida'] : '-';
                $precio = isset($prod['precio']) ? '$' . number_format((float)$prod['precio'], 2) : '-';
            ?>
            <tr>
              <td><?php echo e($prod['nombre']); ?> / <abbr title="Código de producto"><?php echo e($prod['codigo']); ?></abbr></td>
              <td><?php echo e($prod['categoria']); ?></td>
              <td><?php echo e($prod['ubicacion']); ?></td>
              <td><strong><?php echo (int)$prod['stock']; ?></strong></td>
              <td><?php echo e($unidad); ?></td>
              <td><?php echo e($precio); ?></td>
              <td>
                  <?php echo $badge_stock; ?>
                  <?php echo $badge_vence; ?>
              </td>
              <td><a href="detalle-producto.php?id=<?php echo (int)$prod['id_producto']; ?>">Ver</a></td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
      <nav aria-label="Paginación de inventario" style="margin-top: 15px; display: flex; gap: 6px;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($busqueda); ?>"
             style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; <?php echo ($i === $pagina) ? 'background-color: #A61E4D; color: white; font-weight: bold;' : 'background-color: white; color: #333;'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>