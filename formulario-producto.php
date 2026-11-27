<?php
// formulario-producto.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Producto.php';
require_once __DIR__ . '/clases/Proveedor.php';

// Solo administrador y encargado pueden crear o editar productos
verificarAcceso(['administrador', 'encargado']);

$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;
$pagina_actual       = 'productos';

// Cargar catálogos reales desde MySQL
$categorias = Producto::obtenerCategorias();
$listaProveedores = Proveedor::listar('', 'nombre_empresa', 'ASC', 1, 100)['datos'];

// Determinar si es edición (?id=...) o creación
$idProducto = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$modoEdicion = $idProducto > 0;

// Recuperar valores/errores previos si vienen de una validación fallida en procesar_producto.php
$valores = $_SESSION['valores_producto'] ?? null;
$errores = $_SESSION['errores_producto'] ?? [];
unset($_SESSION['valores_producto'], $_SESSION['errores_producto']);

if ($valores === null) {
    if ($modoEdicion) {
        $prodBD = Producto::obtenerPorId($idProducto);
        if (!$prodBD) {
            $_SESSION['mensaje_error'] = 'El producto solicitado no existe.';
            header('Location: gestion-productos.php');
            exit;
        }
        $proveedoresAsociados = Producto::listarProveedoresPorProducto($idProducto);
        $idProvPrincipal = !empty($proveedoresAsociados) ? (string)$proveedoresAsociados[0]['id_proveedor'] : '';

        $valores = [
            'id_producto'     => (int)$prodBD['id_producto'],
            'nombre'          => $prodBD['nombre'],
            'codigo'          => $prodBD['codigo'],
            'precio'          => $prodBD['precio'],
            'fecha_caducidad' => $prodBD['vence'] ?? '',
            'stock'           => $prodBD['stock'],
            'stock_minimo'    => $prodBD['minimo'],
            'id_categoria'    => (string)$prodBD['id_categoria'],
            'ubicacion'       => $prodBD['ubicacion'],
            'id_proveedor'    => $idProvPrincipal
        ];
    } else {
        $valores = [
            'id_producto'     => 0,
            'nombre'          => '',
            'codigo'          => '',
            'precio'          => '',
            'fecha_caducidad' => '',
            'stock'           => '0',
            'stock_minimo'    => '5',
            'id_categoria'    => '',
            'ubicacion'       => '',
            'id_proveedor'    => ''
        ];
    }
} else {
    $idProducto  = (int)($valores['id_producto'] ?? 0);
    $modoEdicion = $idProducto > 0;
}

$titulo_pagina = ($modoEdicion ? 'Editar producto' : 'Nuevo producto') . ' · Inventario';

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1><?php echo $modoEdicion ? 'Editar producto: ' . e($valores['codigo']) : 'Nuevo producto'; ?></h1>
  </div>

  <?php if (isset($errores['general'])): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
      <?php echo e($errores['general']); ?>
    </div>
  <?php endif; ?>

  <div class="eq06-seccion">
    <form class="eq06-form" method="post" action="procesar_producto.php" id="eq06-form-producto">
      <input type="hidden" name="accion" value="<?php echo $modoEdicion ? 'actualizar' : 'crear'; ?>">
      <input type="hidden" name="id_producto" value="<?php echo (int)$idProducto; ?>">

      <fieldset>
        <legend>Datos del producto</legend>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-nombre">Nombre <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-nombre" name="nombre"
                   class="eq06-campo <?php echo isset($errores['nombre']) ? 'eq06-campo-error' : ''; ?>"
                   required minlength="2" maxlength="120" autocomplete="off"
                   placeholder="Ej. Aceite vegetal (L)"
                   value="<?php echo e($valores['nombre']); ?>">
            <?php if (isset($errores['nombre'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['nombre']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-codigo">Código único <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-codigo" name="codigo"
                   class="eq06-campo <?php echo isset($errores['codigo']) ? 'eq06-campo-error' : ''; ?>"
                   required pattern="PR-[0-9]{3,}" maxlength="20" autocomplete="off"
                   placeholder="PR-0516"
                   title="Formato: PR- seguido de números"
                   value="<?php echo e($valores['codigo']); ?>">
            <?php if (isset($errores['codigo'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['codigo']); ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-precio">Precio Unitario ($) <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-precio" name="precio"
                   class="eq06-campo <?php echo isset($errores['precio']) ? 'eq06-campo-error' : ''; ?>"
                   required min="0.01" step="0.01" placeholder="Ej. 3.75"
                   value="<?php echo e($valores['precio']); ?>">
            <?php if (isset($errores['precio'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['precio']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-fecha-caducidad">Fecha de caducidad (Opcional)</label>
            <input type="date" id="eq06-fecha-caducidad" name="fecha_caducidad"
                   class="eq06-campo <?php echo isset($errores['fecha_caducidad']) ? 'eq06-campo-error' : ''; ?>"
                   value="<?php echo e($valores['fecha_caducidad']); ?>">
            <?php if (isset($errores['fecha_caducidad'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['fecha_caducidad']); ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-categoria-form">Categoría y unidad de medida <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-categoria-form" name="id_categoria"
                    class="eq06-campo <?php echo isset($errores['id_categoria']) ? 'eq06-campo-error' : ''; ?>" required>
              <option value="">Seleccione…</option>
              <?php foreach ($categorias as $cat): ?>
                <option value="<?php echo (int)$cat['id_categoria']; ?>"
                  <?php echo ((string)$valores['id_categoria'] === (string)$cat['id_categoria']) ? 'selected' : ''; ?>>
                  <?php echo e($cat['nombre'] . ' (' . $cat['unidad_medida'] . ')'); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errores['id_categoria'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['id_categoria']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-ubicacion">Ubicación en bodega <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-ubicacion" name="ubicacion"
                   class="eq06-campo <?php echo isset($errores['ubicacion']) ? 'eq06-campo-error' : ''; ?>"
                   required maxlength="80" placeholder="Ej. Zona A - Estante 1"
                   value="<?php echo e($valores['ubicacion']); ?>">
            <?php if (isset($errores['ubicacion'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['ubicacion']); ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-stock-min">Stock mínimo <abbr title="obligatorio">*</abbr> · CE-01</label>
            <input type="number" id="eq06-stock-min" name="stock_minimo"
                   class="eq06-campo <?php echo isset($errores['stock_minimo']) ? 'eq06-campo-error' : ''; ?>"
                   required min="0" step="1" placeholder="0"
                   value="<?php echo e($valores['stock_minimo']); ?>">
            <p class="eq06-nota">CE-01: mínimo propio del producto, en su unidad.</p>
            <?php if (isset($errores['stock_minimo'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo e($errores['stock_minimo']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-proveedor">Proveedor principal (Opcional)</label>
            <select id="eq06-proveedor" name="id_proveedor" class="eq06-campo">
              <option value="">Sin proveedor asignado</option>
              <?php foreach ($listaProveedores as $prov): ?>
                <option value="<?php echo (int)$prov['id_proveedor']; ?>"
                  <?php echo ((string)$valores['id_proveedor'] === (string)$prov['id_proveedor']) ? 'selected' : ''; ?>>
                  <?php echo e($prov['codigo'] . ' - ' . $prov['nombre_empresa']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <?php if ($modoEdicion): ?>
          <div class="eq06-alerta eq06-alerta-info" role="note">
            <strong>RB-01 (Stock actual: <?php echo (int)$valores['stock']; ?>):</strong>
            saldo &lt; mínimo → Stock bajo; saldo = mínimo → Normal.
            No hay edición directa del stock desde este formulario (use Entradas/Salidas).
          </div>
        <?php else: ?>
          <div class="eq06-alerta eq06-alerta-info" role="note">
            <strong>RB-01:</strong> El producto se creará con stock inicial en 0. Para sumar existencias utilice el módulo de movimientos.
          </div>
        <?php endif; ?>

        <div class="eq06-acciones-form">
          <button type="submit" class="eq06-btn"><?php echo $modoEdicion ? 'Actualizar producto' : 'Guardar producto'; ?></button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Cancelar</a>
        </div>
      </fieldset>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>