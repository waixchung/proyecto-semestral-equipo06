<?php
// recepcion-mercancia.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Producto.php';

// Solo recepcionista y administrador registran recepción de mercancía
verificarAcceso(['recepcionista', 'administrador']);

date_default_timezone_set('America/Panama');

$titulo_pagina       = 'Recepción de mercancía · Inventario';
$pagina_actual       = 'recepcion-mercancia';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

$pdo = Conexion::obtener();

// 1. Obtener proveedores activos reales desde MySQL
$proveedores_activos = $pdo->query(
    'SELECT id_proveedor, codigo, nombre_empresa 
     FROM proveedores 
     WHERE activo = 1 
     ORDER BY nombre_empresa ASC'
)->fetchAll();

// 2. Obtener productos del catálogo desde MySQL
$productos_bd = Producto::listar('', 0, 'codigo', 'ASC', 1, 200)['datos'];

// 3. Calcular próximo número correlativo referencial de recepción
$totalEntradas = (int)$pdo->query("SELECT COUNT(*) FROM movimientos WHERE tipo = 'entrada'")->fetchColumn();
$proximoNumero = 'REC-' . str_pad((string)($totalEntradas + 1), 4, '0', STR_PAD_LEFT);

// Mensajes flash tras procesar recepción
$mensaje_exito = $_SESSION['mensaje_exito_recepcion'] ?? null;
$errores       = $_SESSION['errores_recepcion'] ?? [];
$valores       = $_SESSION['valores_recepcion'] ?? [
    'fecha_recepcion' => date('Y-m-d'),
    'id_proveedor'    => ''
];
unset($_SESSION['mensaje_exito_recepcion'], $_SESSION['errores_recepcion'], $_SESSION['valores_recepcion']);

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-contenido eq06-main">
  <section class="eq06-panel" aria-labelledby="eq06-titulo-recepcion">
    <h1 id="eq06-titulo-recepcion">Registrar recepción</h1>

    <?php if ($mensaje_exito): ?>
      <div class="eq06-alerta eq06-alerta-info" role="status" style="margin-bottom: 15px;">
        <?php echo e($mensaje_exito); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
      <div class="eq06-alerta eq06-alerta-error" role="alert" style="margin-bottom: 15px;">
        <p><strong>No se pudo confirmar la recepción:</strong></p>
        <ul>
          <?php foreach ($errores as $err): ?>
            <li><?php echo e($err); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form class="eq06-formulario-recepcion" method="post" action="procesar_recepcion.php">
      <fieldset class="eq06-grupo-datos">
        <legend>Datos de recepción</legend>
        <div class="eq06-campos-recepcion">
          <div class="eq06-grupo-campo">
            <label for="eq06-numero-recepcion">N.º de recepción</label>
            <input class="eq06-campo" type="text" id="eq06-numero-recepcion" name="numero_recepcion"
                   value="<?php echo e($proximoNumero); ?>" readonly>
          </div>
          <div class="eq06-grupo-campo">
            <label for="eq06-fecha-recepcion">Fecha de recepción <span aria-hidden="true">*</span></label>
            <input class="eq06-campo" type="date" id="eq06-fecha-recepcion" name="fecha_recepcion"
                   value="<?php echo e($valores['fecha_recepcion'] ?? date('Y-m-d')); ?>" required>
          </div>
          <div class="eq06-grupo-campo">
            <label for="eq06-proveedor">Proveedor activo <span aria-hidden="true">*</span></label>
            <select class="eq06-campo" id="eq06-proveedor" name="id_proveedor" required>
              <option value="">Seleccione un proveedor</option>
              <?php foreach ($proveedores_activos as $pv): ?>
                <option value="<?php echo (int)$pv['id_proveedor']; ?>"
                  <?php echo ((string)($valores['id_proveedor'] ?? '') === (string)$pv['id_proveedor']) ? 'selected' : ''; ?>>
                  <?php echo e($pv['codigo'] . ' · ' . $pv['nombre_empresa']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset class="eq06-grupo-detalle">
        <legend>Detalle de mercancía recibida</legend>
        <div class="eq06-tabla-contenedor">
          <table class="eq06-tabla-recepcion" id="tabla-lineas-recepcion">
            <caption>Productos que componen la recepción</caption>
            <thead>
              <tr>
                <th scope="col">Producto activo</th>
                <th scope="col">Cantidad <span aria-hidden="true">&gt; 0</span></th>
                <th scope="col">Unidad</th>
                <th scope="col">Acción</th>
              </tr>
            </thead>
            <tbody id="cuerpo-lineas-recepcion">
              <tr class="fila-recepcion">
                <td>
                  <label class="eq06-etiqueta-oculta">Producto</label>
                  <select class="eq06-campo select-producto-rec" name="productos[]" required onchange="actualizarUnidadFila(this)">
                    <option value="" data-unidad="-">Seleccione un producto</option>
                    <?php foreach ($productos_bd as $prod): ?>
                      <option value="<?php echo (int)$prod['id_producto']; ?>" data-unidad="<?php echo e($prod['unidad_medida']); ?>">
                        <?php echo e($prod['nombre'] . ' / ' . $prod['codigo'] . ' (Stock: ' . (int)$prod['stock'] . ')'); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>
                <td>
                  <label class="eq06-etiqueta-oculta">Cantidad</label>
                  <input class="eq06-campo" type="number" name="cantidades[]" min="1" step="1" value="1" required>
                </td>
                <td class="celda-unidad">-</td>
                <td>
                  <button class="eq06-boton-secundario" type="button" onclick="quitarFilaRecepcion(this)">Quitar</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <button class="eq06-boton-secundario eq06-agregar-linea" type="button" onclick="agregarFilaRecepcion()">+ Incluir</button>
      </fieldset>

      <aside class="eq06-aviso" aria-label="Aviso antes de confirmar">
        Antes de confirmar: revisar proveedor, productos y cantidades. No se guarda stock parcial.
      </aside>
      <div class="eq06-acciones">
        <button class="eq06-boton" type="submit">Confirmar recepción</button>
        <a href="<?php echo ($rol_usuario === 'administrador') ? 'dashboard-administrador.php' : 'dashboard-recepcionista.php'; ?>" class="eq06-boton-secundario" style="text-decoration: none; padding: 8px 14px;">Cancelar</a>
      </div>
    </form>
  </section>
</main>

<script>
function actualizarUnidadFila(selectElem) {
  const opcion = selectElem.options[selectElem.selectedIndex];
  const unidad = opcion ? (opcion.getAttribute('data-unidad') || '-') : '-';
  const fila = selectElem.closest('tr');
  if (fila) {
    fila.querySelector('.celda-unidad').textContent = unidad;
  }
}

function agregarFilaRecepcion() {
  const tbody = document.getElementById('cuerpo-lineas-recepcion');
  const primeraFila = tbody.querySelector('.fila-recepcion');
  const nuevaFila = primeraFila.cloneNode(true);
  nuevaFila.querySelector('select').selectedIndex = 0;
  nuevaFila.querySelector('input[type="number"]').value = 1;
  nuevaFila.querySelector('.celda-unidad').textContent = '-';
  tbody.appendChild(nuevaFila);
}

function quitarFilaRecepcion(btn) {
  const tbody = document.getElementById('cuerpo-lineas-recepcion');
  if (tbody.querySelectorAll('.fila-recepcion').length > 1) {
    btn.closest('tr').remove();
  } else {
    alert('La recepción debe contener al menos una línea de producto.');
  }
}
</script>

<?php require_once __DIR__ . '/includes/pie.php'; ?>