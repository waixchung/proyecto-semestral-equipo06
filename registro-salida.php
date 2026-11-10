<?php

 
// La sesión puede estar iniciada ya (cuando lo incluye procesar_salida.php)
if (!isset($_SESSION)) {
    session_start();
}
 
// 1. Sin sesión activa: al login
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}
 
// 2. Solo el Encargado de inventario registra salidas
$rol_sesion = $_SESSION['rol_usuario'];
if ($rol_sesion !== 'Encargado de inventario' && $rol_sesion !== 'Encargado') {
    header('Location: acceso-denegado.php');
    exit;
}
 
// APP_ACCESO puede estar definida ya (cuando lo incluye procesar_salida.php)
if (!defined('APP_ACCESO')) {
    define('APP_ACCESO', true);
}
require_once 'config/datos_demo.php'; // catálogo: $productos_demo
 
// Hora de Panamá: sin esto PHP usa UTC y "hoy" cambia de día a las 7 p. m.
date_default_timezone_set('America/Panama');
 
// 3. $valores, $errores y $resumen llegan desde procesar_salida.php; si no, se crean vacíos
if (!isset($valores)) {
    $valores = ['fecha' => date('Y-m-d')];
}
if (!isset($errores)) {
    $errores = [];
}
if (!isset($resumen)) {
    $resumen = null;
}
// Todas las claves deben existir y ser texto (evita avisos con datos manipulados)
foreach (['producto', 'fecha', 'cantidad', 'motivo', 'observacion'] as $campo) {
    if (!isset($valores[$campo]) || !is_string($valores[$campo])) {
        $valores[$campo] = '';
    }
}
 
// 4. Variables que usan encabezado.php y menu.php
$titulo_pagina = 'Registro de salidas · Inventario';
$pagina_actual = 'salidas'; // debe coincidir con la clave del menú
$rol_usuario = $rol_sesion;
$nombre_usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '';
$usuario_autenticado = true;
 
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
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
            <li><?php echo htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8'); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
 
    <?php if ($resumen !== null): ?>
      <p class="eq06-nota" role="status">
        Producto: <strong><?php echo htmlspecialchars($resumen['nombre'], ENT_QUOTES, 'UTF-8'); ?></strong> ·
        Disponible: <strong><?php echo $resumen['disponible']; ?></strong> ·
        Mínimo del producto: <strong><?php echo $resumen['minimo']; ?></strong>
        <?php if ($resumen['saldo'] !== null): ?>
          · Saldo tras la salida: <strong><?php echo $resumen['saldo']; ?></strong>
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
              <?php foreach ($productos_demo as $prod): ?>
                <option value="<?php echo htmlspecialchars($prod['codigo'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo ($valores['producto'] === $prod['codigo']) ? ' selected' : ''; ?>>
                  <?php echo htmlspecialchars($prod['nombre'] . ' / ' . $prod['codigo'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
 
          <div class="eq06-campo-grupo">
            <label for="eq06-fecha">Fecha <abbr title="obligatorio">*</abbr></label>
            <input type="date" id="eq06-fecha" name="fecha" class="eq06-campo" required min="2026-01-01"
                   value="<?php echo htmlspecialchars($valores['fecha'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo isset($errores['fecha']) ? ' aria-invalid="true"' : ''; ?>>
          </div>
        </div>
 
        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-cantidad">Cantidad a retirar <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-cantidad" name="cantidad" class="eq06-campo" required min="0.01" step="0.01"
                   inputmode="decimal" placeholder="Ej.: 2"
                   value="<?php echo htmlspecialchars($valores['cantidad'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo isset($errores['cantidad']) ? ' aria-invalid="true"' : ''; ?>>
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
                    placeholder="Ej.: preparación del almuerzo"<?php echo isset($errores['observacion']) ? ' aria-invalid="true"' : ''; ?>><?php echo htmlspecialchars($valores['observacion'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </fieldset>
 
      <div class="eq06-acciones-form">
        <button type="submit" class="eq06-boton">Confirmar salida</button>
        <a href="dashboard-encargado.php">Cancelar</a>
      </div>
    </form>
  </section>
</main>
 
<?php
require_once 'includes/pie.php';