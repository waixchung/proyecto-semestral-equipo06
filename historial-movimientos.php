<?php
// historial-movimientos.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';

// Validar sesión activa para roles del sistema
verificarAcceso(['administrador', 'encargado', 'recepcionista']);

date_default_timezone_set('America/Panama');

$titulo_pagina       = 'Historial de movimientos · Inventario';
$pagina_actual       = 'historial-movimientos';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

$pdo = Conexion::obtener();

// Cargar catálogos para los selectores de filtro
$productosFiltro = $pdo->query('SELECT id_producto, codigo, nombre FROM productos ORDER BY nombre ASC')->fetchAll();
$usuariosFiltro  = $pdo->query('SELECT id_usuario, nombre FROM usuarios ORDER BY nombre ASC')->fetchAll();

// Parámetros de filtro por GET (permite compartir/paginar resultados sin reenviar POST)
$hoy         = date('Y-m-d');
$haceUnMes   = date('Y-m-d', strtotime('-30 days'));
$desde       = trim($_GET['desde'] ?? $haceUnMes);
$hasta       = trim($_GET['hasta'] ?? $hoy);
$idProducto  = (int)($_GET['producto'] ?? 0);
$tipo        = trim($_GET['tipo'] ?? '');
$idUsuarioF  = (int)($_GET['usuario'] ?? 0);
$pagina      = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina   = 15;
$offset      = ($pagina - 1) * $porPagina;

// Construir cláusula WHERE segura con parámetros preparados
$where  = [];
$params = [];

if ($desde !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde)) {
    $where[] = 'DATE(m.fecha_movimiento) >= :desde';
    $params[':desde'] = $desde;
}
if ($hasta !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta)) {
    $where[] = 'DATE(m.fecha_movimiento) <= :hasta';
    $params[':hasta'] = $hasta;
}
if ($idProducto > 0) {
    $where[] = 'm.id_producto = :id_producto';
    $params[':id_producto'] = $idProducto;
}
if (in_array($tipo, ['entrada', 'salida'], true)) {
    $where[] = 'm.tipo = :tipo';
    $params[':tipo'] = $tipo;
}
if ($idUsuarioF > 0) {
    $where[] = 'm.id_usuario = :id_usuario';
    $params[':id_usuario'] = $idUsuarioF;
}

$sqlWhere = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Exportación opcional a CSV (Excel) si se pulsa ?exportar=csv
if (isset($_GET['exportar']) && $_GET['exportar'] === 'csv') {
    $sqlExport = "SELECT m.id_movimiento, m.fecha_movimiento, p.codigo, p.nombre AS producto,
                         c.unidad_medida, m.tipo, m.cantidad, u.nombre AS usuario, m.motivo
                  FROM movimientos m
                  INNER JOIN productos p ON m.id_producto = p.id_producto
                  INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                  INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
                  $sqlWhere
                  ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC";
    $stmtExp = $pdo->prepare($sqlExport);
    $stmtExp->execute($params);
    $filasExp = $stmtExp->fetchAll();

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="historial_movimientos_' . date('Ymd_His') . '.csv"');
    $salida = fopen('php://output', 'w');
    fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8 para Excel
    fputcsv($salida, ['ID', 'Fecha y Hora', 'Código', 'Producto', 'Tipo', 'Cantidad', 'Unidad', 'Responsable', 'Referencia / Motivo']);
    foreach ($filasExp as $f) {
        fputcsv($salida, [
            $f['id_movimiento'],
            $f['fecha_movimiento'],
            $f['codigo'],
            $f['producto'],
            ucfirst($f['tipo']),
            $f['cantidad'],
            $f['unidad_medida'],
            $f['usuario'],
            $f['motivo']
        ]);
    }
    fclose($salida);
    exit;
}

// Conteo total para paginación
$stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM movimientos m $sqlWhere");
$stmtTotal->execute($params);
$totalRegistros = (int)$stmtTotal->fetchColumn();
$totalPaginas   = max(1, (int)ceil($totalRegistros / $porPagina));

// Consulta principal con JOIN a productos, categorias y usuarios
$sql = "SELECT m.id_movimiento, m.fecha_movimiento, m.tipo, m.cantidad, m.motivo,
               p.id_producto, p.codigo, p.nombre AS producto, c.unidad_medida,
               u.nombre AS usuario
        FROM movimientos m
        INNER JOIN productos p ON m.id_producto = p.id_producto
        INNER JOIN categorias c ON p.id_categoria = c.id_categoria
        INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
        $sqlWhere
        ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC
        LIMIT :limite OFFSET :offset";

$stmt = $pdo->prepare($sql);
foreach ($params as $clave => $valor) {
    $stmt->bindValue($clave, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$movimientos = $stmt->fetchAll();

// Query string actual para enlaces de exportación y paginación
$queryFiltros = http_build_query([
    'desde'    => $desde,
    'hasta'    => $hasta,
    'producto' => $idProducto,
    'tipo'     => $tipo,
    'usuario'  => $idUsuarioF
]);

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main id="eq06-contenido" class="eq06-main">
  <h1>Historial de movimientos</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-filtros">
    <h2 id="eq06-titulo-filtros">Consulta por filtros</h2>
    <form action="historial-movimientos.php" method="get">
      <fieldset class="eq06-grupo">
        <legend>Filtros</legend>

        <div class="eq06-filtros">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-desde">Desde</label>
            <input type="date" id="eq06-desde" name="desde" class="eq06-campo" value="<?php echo e($desde); ?>">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-hasta">Hasta</label>
            <input type="date" id="eq06-hasta" name="hasta" class="eq06-campo" value="<?php echo e($hasta); ?>">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-producto">Producto</label>
            <select id="eq06-producto" name="producto" class="eq06-campo">
              <option value="0">Todos</option>
              <?php foreach ($productosFiltro as $pf): ?>
                <option value="<?php echo (int)$pf['id_producto']; ?>" <?php echo ($idProducto === (int)$pf['id_producto']) ? 'selected' : ''; ?>>
                  <?php echo e($pf['codigo'] . ' · ' . $pf['nombre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-tipo">Tipo</label>
            <select id="eq06-tipo" name="tipo" class="eq06-campo">
              <option value="">Todos</option>
              <option value="entrada" <?php echo ($tipo === 'entrada') ? 'selected' : ''; ?>>Entrada</option>
              <option value="salida" <?php echo ($tipo === 'salida') ? 'selected' : ''; ?>>Salida</option>
            </select>
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-usuario">Usuario</label>
            <select id="eq06-usuario" name="usuario" class="eq06-campo">
              <option value="0">Todos</option>
              <?php foreach ($usuariosFiltro as $uf): ?>
                <option value="<?php echo (int)$uf['id_usuario']; ?>" <?php echo ($idUsuarioF === (int)$uf['id_usuario']) ? 'selected' : ''; ?>>
                  <?php echo e($uf['nombre']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="eq06-acciones" style="display: flex; gap: 10px; margin-top: 10px; flex-wrap: wrap;">
        <button type="submit" class="eq06-boton">Consultar</button>
        <a href="historial-movimientos.php" class="eq06-btn eq06-btn-secundario" style="padding: 8px 14px; text-decoration: none;">Limpiar filtros</a>
        <a href="historial-movimientos.php?<?php echo e($queryFiltros); ?>&exportar=csv" class="eq06-btn eq06-btn-secundario" style="padding: 8px 14px; text-decoration: none;">Exportar CSV</a>
      </div>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-resultados">
    <h2 id="eq06-titulo-resultados">Resultados (<?php echo $totalRegistros; ?> movimientos)</h2>

    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla eq06-tabla-ancha">
        <caption>
          Movimientos confirmados del <?php echo e(date('d/m/Y', strtotime($desde))); ?> al <?php echo e(date('d/m/Y', strtotime($hasta))); ?>
        </caption>
        <thead>
          <tr>
            <th scope="col">Fecha / hora</th>
            <th scope="col">Producto</th>
            <th scope="col">Tipo</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Usuario</th>
            <th scope="col">Referencia / Motivo</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($movimientos)): ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #666; padding: 15px;">No se encontraron movimientos en el rango seleccionado.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($movimientos as $mov):
              $esEntrada = ($mov['tipo'] === 'entrada');
              $signo     = $esEntrada ? '+' : '−';
              $colorCant = $esEntrada ? '#155724' : '#A61E4D';
              $fechaFmt  = date('d/m/Y H:i', strtotime($mov['fecha_movimiento']));
            ?>
            <tr>
              <td><time datetime="<?php echo e($mov['fecha_movimiento']); ?>"><?php echo e($fechaFmt); ?></time></td>
              <td>
                <a href="detalle-producto.php?id=<?php echo (int)$mov['id_producto']; ?>" style="color: #A61E4D;">
                  <?php echo e($mov['producto']); ?> (<?php echo e($mov['codigo']); ?>)
                </a>
              </td>
              <td><?php echo e(ucfirst($mov['tipo'])); ?></td>
              <td style="font-weight: bold; color: <?php echo $colorCant; ?>;">
                <?php echo $signo . (int)$mov['cantidad']; ?> <abbr title="Unidad de medida"><?php echo e($mov['unidad_medida']); ?></abbr>
              </td>
              <td><?php echo e($mov['usuario']); ?></td>
              <td>
                MOV-<?php echo str_pad((string)$mov['id_movimiento'], 4, '0', STR_PAD_LEFT); ?> ·
                <?php echo e($mov['motivo']); ?>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
      <nav aria-label="Paginación de movimientos" style="margin-top: 15px; display: flex; gap: 6px;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>&<?php echo e($queryFiltros); ?>"
             style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; <?php echo ($i === $pagina) ? 'background-color: #A61E4D; color: white; font-weight: bold;' : 'background-color: white; color: #333;'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>

    <aside class="eq06-nota-panel" aria-label="Ayuda sobre la consulta" style="margin-top: 15px;">
      <p><strong>Consulta histórica:</strong> registro auditable con fecha, usuario, referencia y motivo. <em>Sin editar ni eliminar.</em></p>
    </aside>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>