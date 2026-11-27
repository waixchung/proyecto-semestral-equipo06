<?php
// registro-salida.php
if (!defined('APP_ACCESO')) {
    define('APP_ACCESO', true);
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Producto.php';

// 1. Verificar sesión activa y rol permitido (solo encargado y administrador)
verificarAcceso(['encargado', 'administrador']);

$rol_sesion = $_SESSION['rol_usuario'];

// Hora de Panamá: sin esto PHP usa UTC y "hoy" cambia de día a las 7 p. m.
date_default_timezone_set('America/Panama');

// 2. Cargar catálogo real desde MySQL (todos los productos ordenados por código)
$productos_bd = Producto::listar('', 0, 'codigo', 'ASC', 1, 200)['datos'];

// 3. $valores, $errores y $resumen pueden llegar por require desde procesar_salida.php o por sesión
if (!isset($valores)) {
    $valores = $_SESSION['valores_salida'] ?? ['fecha' => date('Y-m-d')];
}
if (!isset($errores)) {
    $errores = $_SESSION['errores_salida'] ?? [];
}
if (!isset($resumen)) {
    $resumen = $_SESSION['resumen_salida'] ?? null;
}
unset($_SESSION['valores_salida'], $_SESSION['errores_salida'], $_SESSION['resumen_salida']);

// Todas las claves deben existir y ser texto
foreach (['producto', 'fecha', 'cantidad', 'motivo', 'observacion'] as $campo) {
    if (!isset($valores[$campo]) || !is_string($valores[$campo])) {
        $valores[$campo] = '';
    }
}
if ($valores['fecha'] === '') {
    $valores['fecha'] = date('Y-m-d');
}

// 4. Variables que usan encabezado.php y menu.php
$titulo_pagina       = 'Registro de salidas · Inventario';
$pagina_actual       = 'salidas';
$rol_usuario         = $rol_sesion;
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Registrar salida</h1>
  </div>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-formulario">
    <h2 id="eq06-titulo-formulario">Nueva salida de inventario</h2>

    <?php if (!empty($errores)): ?>
      <div class="eq06-alerta eq06-alerta-error" role="alert">
        <p><strong>No se pudo registrar la salida:</strong></p>
        <ul>
          <?php foreach ($errores as $mensaje): ?>
            <li><?php echo e($mensaje); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($resumen !== null): ?>
      <p class="eq06-nota" role="status">
        Producto: <strong><?php echo e($resumen['nombre']); ?></strong> ·
        Disponible: <strong><?php echo (int)$resumen['disponible']; ?></strong> ·
        Mínimo del producto: <strong><?php echo (int)$resumen['minimo']; ?></strong>
        <?php if ($resumen['saldo'] !== null): ?>
          · Saldo tras la salida: <strong><?php echo (int)$resumen['saldo']; ?></strong>
          <?php if ($resumen['stock_bajo']): ?>
            <span class="eq06-etiqueta-bajo">Stock bajo</span>
          <?php endif; ?>
        <?php endif; ?>
      </p>
    <?php endif; ?>

    <form class="eq06-form" action="procesar_salida.php" method="post">
      <fieldset>
        <legend>Datos de la salida</legend>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-producto">Producto <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-producto" name="producto" class="eq06-campo" required<?php echo isset($errores['producto']) ? ' aria-invalid="true"' : ''; ?>>
              <option value="">Selecciona un producto</option>
              <?php foreach ($productos_bd as $prod): 
                $estadoVenc = determinarEstadoVencimiento($prod['vence']);
                $etiquetaExtra = ' (Disp: ' . (int)$prod['stock'] . ' ' . $prod['unidad_medida'] . ')';
                if ($estadoVenc === 'Vencido') {
                    $etiquetaExtra .= ' [VENCIDO]';
                } elseif ((int)$prod['stock'] < (int)$prod['minimo']) {
                    $etiquetaExtra .= ' [Stock bajo]';
                }
              ?>
                <option value="<?php echo e($prod['codigo']); ?>"<?php echo ($valores['producto'] === $prod['codigo']) ? ' selected' : ''; ?>>
                  <?php echo e($prod['nombre'] . ' / ' . $prod['codigo'] . $etiquetaExtra); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-fecha">Fecha <abbr title="obligatorio">*</abbr></label>
            <input type="date" id="eq06-fecha" name="fecha" class="eq06-campo" required min="2026-01-01"
                   value="<?php echo e($valores['fecha']); ?>"<?php echo isset($errores['fecha']) ? ' aria-invalid="true"' : ''; ?>>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-cantidad">Cantidad a retirar <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-cantidad" name="cantidad" class="eq06-campo" required min="1" step="1"
                   inputmode="numeric" placeholder="Ej.: 2"
                   value="<?php echo e($valores['cantidad']); ?>"<?php echo isset($errores['cantidad']) ? ' aria-invalid="true"' : ''; ?>>
          </div>

          <div class="eq06-campo-grupo">
            <label for="eq06-motivo">Motivo <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-motivo" name="motivo" class="eq06-campo" required<?php echo isset($errores['motivo']) ? ' aria-invalid="true"' : ''; ?>>
              <option value="">Selecciona un motivo</option>
              <option value="consumo"<?php echo ($valores['motivo'] === 'consumo') ? ' selected' : ''; ?>>Consumo</option>
              <option value="utilizacion"<?php echo ($valores['motivo'] === 'utilizacion') ? ' selected' : ''; ?>>Utilización</option>
              <option value="retiro"<?php echo ($valores['motivo'] === 'retiro') ? ' selected' : ''; ?>>Retiro</option>
            </select>
          </div>
        </div>

        <div class="eq06-campo-grupo">
          <label for="eq06-observacion">Observación (opcional)</label>
          <textarea id="eq06-observacion" name="observacion" class="eq06-campo" rows="3" maxlength="200"
                    placeholder="Ej.: preparación del almuerzo"<?php echo isset($errores['observacion']) ? ' aria-invalid="true"' : ''; ?>><?php echo e($valores['observacion']); ?></textarea>
        </div>
      </fieldset>

      <div class="eq06-acciones-form">
        <button type="submit" class="eq06-boton">Confirmar salida</button>
        <a href="<?php echo ($rol_sesion === 'administrador') ? 'dashboard-administrador.php' : 'dashboard-encargado.php'; ?>">Cancelar</a>
      </div>
    </form>
  </section>
</main>

<?php
require_once __DIR__ . '/includes/pie.php';