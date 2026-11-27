<?php
// gestion-productos.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Producto.php';

// Solo usuarios autenticados con roles válidos del sistema
verificarAcceso(['administrador', 'encargado', 'recepcionista']);

$titulo_pagina       = 'Catálogo de productos · Inventario';
$pagina_actual       = 'productos';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;
$puede_editar        = in_array($rol_usuario, ['administrador', 'encargado'], true);

// Mensajes flash tras crear, editar o eliminar
$mensaje_exito = $_SESSION['mensaje_exito'] ?? null;
$mensaje_error = $_SESSION['mensaje_error'] ?? null;
unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

// Parámetros GET de búsqueda, filtro, orden, reporte y paginación
$busqueda      = trim($_GET['buscar'] ?? '');
$idCategoria   = (int)($_GET['categoria'] ?? 0);
$soloStockBajo = isset($_GET['stock_bajo']) && $_GET['stock_bajo'] === '1';
$zonaFiltro    = trim($_GET['zona'] ?? '');
$orden         = $_GET['orden'] ?? 'codigo';
$direccion     = $_GET['dir'] ?? 'ASC';
$pagina        = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina     = 10;

$categorias = Producto::obtenerCategorias();

if ($soloStockBajo) {
    // Cambio de requerimiento: procedimiento almacenado stock bajo agrupado/ordenado por ubicación
    $productos     = Producto::listarStockBajoPorUbicacion($zonaFiltro);
    $total         = count($productos);
    $totalPaginas  = 1;
} else {
    $resultado     = Producto::listar($busqueda, $idCategoria, $orden, $direccion, $pagina, $porPagina);
    $productos     = $resultado['datos'];
    $total         = $resultado['total'];
    $totalPaginas  = $resultado['total_paginas'];
}

// Helper para alternar dirección de ordenamiento en encabezados
$dirSiguiente = (strtoupper($direccion) === 'ASC') ? 'DESC' : 'ASC';

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <h1>Catálogo de productos</h1>
    <div style="display: flex; gap: 10px;">
      <a href="gestion-productos.php?stock_bajo=1" class="eq06-btn" style="background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; padding: 8px 14px; text-decoration: none; border-radius: 4px; font-weight: bold;">
        Reporte stock bajo por ubicación
      </a>
      <?php if ($puede_editar): ?>
        <a href="formulario-producto.php" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Nuevo producto</a>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($mensaje_exito): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 4px;">
      <?php echo e($mensaje_exito); ?>
    </div>
  <?php endif; ?>

  <?php if ($mensaje_error): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
      <?php echo e($mensaje_error); ?>
    </div>
  <?php endif; ?>

  <!-- Formulario de Búsqueda y Filtros -->
  <section class="eq06-seccion" style="margin-bottom: 20px;">
    <form method="get" action="gestion-productos.php" class="eq06-form" style="border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
      <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">

        <?php if ($soloStockBajo): ?>
          <input type="hidden" name="stock_bajo" value="1">
          <div style="flex: 2; min-width: 220px;">
            <label for="zona" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Filtrar ubicación / zona (Procedimiento Almacenado)</label>
            <input type="text" id="zona" name="zona" class="eq06-campo" value="<?php echo e($zonaFiltro); ?>" placeholder="Ej. Zona A, Zona C..." style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
          </div>
        <?php else: ?>
          <div style="flex: 2; min-width: 200px;">
            <label for="buscar" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Buscar</label>
            <input type="text" id="buscar" name="buscar" class="eq06-campo" value="<?php echo e($busqueda); ?>" placeholder="Código, nombre o ubicación" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
          </div>

          <div style="flex: 1; min-width: 160px;">
            <label for="categoria" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Categoría</label>
            <select id="categoria" name="categoria" class="eq06-campo" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
              <option value="0">Todas</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo (int)$cat['id_categoria']; ?>" <?php echo ($idCategoria === (int)$cat['id_categoria']) ? 'selected' : ''; ?>>
                  <?php echo e($cat['nombre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Aplicar</button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Ver todos</a>
        </div>
      </div>
    </form>
  </section>

  <section class="eq06-seccion">
    <div class="eq06-tabla-wrap" style="overflow-x: auto;">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px;">
          <?php echo $soloStockBajo
            ? 'Reporte de productos con stock bajo ordenados por ubicación (' . $total . ' encontrados)'
            : 'Listado de productos del catálogo (' . $total . ' registros)'; ?>
        </caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">
              <a href="?orden=codigo&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>&categoria=<?php echo $idCategoria; ?>" style="color: inherit; text-decoration: none;">Código ↕</a>
            </th>
            <th scope="col" style="padding: 10px;">
              <a href="?orden=nombre&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>&categoria=<?php echo $idCategoria; ?>" style="color: inherit; text-decoration: none;">Producto ↕</a>
            </th>
            <th scope="col" style="padding: 10px;">Categoría</th>
            <th scope="col" style="padding: 10px;">
              <a href="?orden=ubicacion&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>&categoria=<?php echo $idCategoria; ?>" style="color: inherit; text-decoration: none;">Ubicación ↕</a>
            </th>
            <th scope="col" style="padding: 10px;">
              <a href="?orden=stock&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>&categoria=<?php echo $idCategoria; ?>" style="color: inherit; text-decoration: none;">Stock / Mín ↕</a>
            </th>
            <th scope="col" style="padding: 10px;">Vencimiento</th>
            <th scope="col" style="padding: 10px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($productos)): ?>
            <tr>
              <td colspan="7" style="padding: 15px; text-align: center; color: #666;">No se encontraron productos con los filtros seleccionados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($productos as $prod):
              $stockActual = (int)$prod['stock'];
              $stockMinimo = (int)$prod['minimo'];
              $stockBajo   = $stockActual < $stockMinimo;
              $estadoVenc  = determinarEstadoVencimiento($prod['vence']);

              $colorVenc = '#155724';
              if ($estadoVenc === 'Vencido') {
                  $colorVenc = '#721c24';
              } elseif ($estadoVenc === 'Por vencer') {
                  $colorVenc = '#856404';
              }
            ?>
            <tr style="border-bottom: 1px solid #eee; <?php echo $stockBajo ? 'background-color: #fff8f8;' : ''; ?>">
              <td style="padding: 10px;"><strong><?php echo e($prod['codigo']); ?></strong></td>
              <td style="padding: 10px;"><?php echo e($prod['nombre']); ?></td>
              <td style="padding: 10px;"><?php echo e($prod['categoria']); ?> (<?php echo e($prod['unidad_medida']); ?>)</td>
              <td style="padding: 10px;"><?php echo e($prod['ubicacion']); ?></td>
              <td style="padding: 10px;">
                <span style="font-weight: bold; color: <?php echo $stockBajo ? '#A61E4D' : '#212529'; ?>;">
                  <?php echo $stockActual; ?> / <?php echo $stockMinimo; ?>
                </span>
                <?php if ($stockBajo): ?>
                  <span style="background-color: #f8d7da; color: #721c24; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 4px;">Stock bajo</span>
                <?php endif; ?>
              </td>
              <td style="padding: 10px;">
                <?php echo $prod['vence'] ? e($prod['vence']) : 'N/A'; ?>
                <br><small style="color: <?php echo $colorVenc; ?>; font-weight: bold;"><?php echo e($estadoVenc); ?></small>
              </td>
              <td style="padding: 10px; white-space: nowrap;">
                <a href="detalle-producto.php?id=<?php echo (int)$prod['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;">Ver</a>

                <?php if ($puede_editar): ?>
                  | <a href="formulario-producto.php?id=<?php echo (int)$prod['id_producto']; ?>" style="color: #A61E4D; text-decoration: underline;">Editar</a>
                  | <form method="post" action="procesar_producto.php" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar el producto <?php echo e($prod['codigo']); ?>?');">
                      <input type="hidden" name="accion" value="eliminar">
                      <input type="hidden" name="id_producto" value="<?php echo (int)$prod['id_producto']; ?>">
                      <button type="submit" style="background: none; border: none; color: #A61E4D; text-decoration: underline; cursor: pointer; padding: 0; font: inherit;">Eliminar</button>
                    </form>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Controles de Paginación -->
    <?php if (!$soloStockBajo && $totalPaginas > 1): ?>
      <nav aria-label="Paginación de productos" style="margin-top: 15px; display: flex; gap: 6px; align-items: center;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($busqueda); ?>&categoria=<?php echo $idCategoria; ?>&orden=<?php echo urlencode($orden); ?>&dir=<?php echo urlencode($direccion); ?>"
             style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; <?php echo ($i === $pagina) ? 'background-color: #A61E4D; color: white; font-weight: bold;' : 'background-color: white; color: #333;'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>