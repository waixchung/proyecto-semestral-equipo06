<?php
// Si este archivo es llamado desde procesar_producto.php, APP_ACCESO ya estará definido.
// Si se accede directamente, lo definimos aquí.
if (!defined('APP_ACCESO')) {
    define('APP_ACCESO', true);
}

// Inicializamos arreglos vacíos si es la primera vez que se entra al formulario (GET)
if (!isset($valores)) {
    $valores = [
        'nombre' => '',
        'codigo' => '',
        'precio' => '',
        'fecha_caducidad' => '',
        'stock_minimo' => '8',
        'categoria' => '',
        'unidad' => '',
        'proveedor' => 'dist-central',
        'estado' => 'activo'
    ];
}
if (!isset($errores)) {
    $errores = [];
}

// Configuración de variables contextuales
$titulo_pagina = 'Formulario de producto · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'productos'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Nuevo producto / Editar producto</h1>
  </div>

  <div class="eq06-seccion">
    <form class="eq06-form" method="post" action="procesar_producto.php" id="eq06-form-producto">
      <fieldset>
        <legend>Datos del producto</legend>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-nombre">Nombre <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-nombre" name="nombre" 
                   class="eq06-campo <?php echo isset($errores['nombre']) ? 'eq06-campo-error' : ''; ?>"
                   required minlength="2" maxlength="100" autocomplete="off"
                   placeholder="Ej. Arroz" 
                   value="<?php echo htmlspecialchars($valores['nombre']); ?>">
            <?php if (isset($errores['nombre'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['nombre']; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="eq06-campo-grupo">
            <label for="eq06-codigo">Código único <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-codigo" name="codigo" 
                   class="eq06-campo <?php echo isset($errores['codigo']) ? 'eq06-campo-error' : ''; ?>"
                   required pattern="PR-[0-9]{3,}" maxlength="20" autocomplete="off"
                   placeholder="PR-001" 
                   title="Formato: PR- seguido de números"
                   value="<?php echo htmlspecialchars($valores['codigo']); ?>">
            <?php if (isset($errores['codigo'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['codigo']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-precio">Precio Unitario ($) <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-precio" name="precio" 
                   class="eq06-campo <?php echo isset($errores['precio']) ? 'eq06-campo-error' : ''; ?>" 
                   required min="0.01" step="0.01" placeholder="Ej. 1.20"
                   value="<?php echo htmlspecialchars($valores['precio']); ?>">
            <?php if (isset($errores['precio'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['precio']; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="eq06-campo-grupo">
            <label for="eq06-fecha-caducidad">Fecha de caducidad (Opcional)</label>
            <input type="date" id="eq06-fecha-caducidad" name="fecha_caducidad" 
                   class="eq06-campo <?php echo isset($errores['fecha_caducidad']) ? 'eq06-campo-error' : ''; ?>"
                   value="<?php echo htmlspecialchars($valores['fecha_caducidad']); ?>">
            <?php if (isset($errores['fecha_caducidad'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['fecha_caducidad']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-categoria-form">Categoría <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-categoria-form" name="categoria" 
                    class="eq06-campo <?php echo isset($errores['categoria']) ? 'eq06-campo-error' : ''; ?>" required>
              <option value="">Seleccione…</option>
              <option value="granos" <?php echo ($valores['categoria'] === 'granos') ? 'selected' : ''; ?>>Granos</option>
              <option value="aceites" <?php echo ($valores['categoria'] === 'aceites') ? 'selected' : ''; ?>>Aceites</option>
              <option value="vegetales" <?php echo ($valores['categoria'] === 'vegetales') ? 'selected' : ''; ?>>Vegetales</option>
            </select>
            <?php if (isset($errores['categoria'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['categoria']; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="eq06-campo-grupo">
            <label for="eq06-unidad">Unidad de medida <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-unidad" name="unidad" 
                    class="eq06-campo <?php echo isset($errores['unidad']) ? 'eq06-campo-error' : ''; ?>" required>
              <option value="">Seleccione…</option>
              <option value="kg" <?php echo ($valores['unidad'] === 'kg') ? 'selected' : ''; ?>>kg</option>
              <option value="L" <?php echo ($valores['unidad'] === 'L') ? 'selected' : ''; ?>>L</option>
              <option value="und" <?php echo ($valores['unidad'] === 'und') ? 'selected' : ''; ?>>und</option>
            </select>
            <p class="eq06-nota">Con historial: unidad bloqueada si altera su interpretación.</p>
            <?php if (isset($errores['unidad'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['unidad']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-stock-min">Stock mínimo <abbr title="obligatorio">*</abbr> · CE-01</label>
            <input type="number" id="eq06-stock-min" name="stock_minimo" 
                   class="eq06-campo <?php echo isset($errores['stock_minimo']) ? 'eq06-campo-error' : ''; ?>"
                   required min="0" step="0.01" placeholder="0"
                   value="<?php echo htmlspecialchars($valores['stock_minimo']); ?>">
            <p class="eq06-nota">CE-01: mínimo propio del producto, en su unidad.</p>
            <?php if (isset($errores['stock_minimo'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['stock_minimo']; ?></span>
            <?php endif; ?>
          </div>
          
          <div class="eq06-campo-grupo">
            <label for="eq06-proveedor">Proveedor principal</label>
            <select id="eq06-proveedor" name="proveedor" 
                    class="eq06-campo <?php echo isset($errores['proveedor']) ? 'eq06-campo-error' : ''; ?>">
              <option value="">Sin proveedor</option>
              <option value="dist-central" <?php echo ($valores['proveedor'] === 'dist-central') ? 'selected' : ''; ?>>Distribuidora Central</option>
              <option value="mayorista-norte" <?php echo ($valores['proveedor'] === 'mayorista-norte') ? 'selected' : ''; ?>>Mayorista Norte</option>
            </select>
            <?php if (isset($errores['proveedor'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display: block; margin-top: 4px;"><?php echo $errores['proveedor']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <div class="eq06-campo-grupo">
          <label for="eq06-estado-form">Estado</label>
          <select id="eq06-estado-form" name="estado" class="eq06-campo">
            <option value="activo" <?php echo ($valores['estado'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
            <option value="inactivo" <?php echo ($valores['estado'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
          </select>
        </div>

        <div class="eq06-alerta eq06-alerta-info" role="note">
          <strong>RB-01:</strong> saldo &lt; mínimo → Stock bajo; saldo = mínimo → Normal.
          No hay edición directa del stock desde este formulario.
        </div>

        <div class="eq06-acciones-form">
          <button type="submit" class="eq06-btn">Guardar</button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Cancelar</a>
        </div>
      </fieldset>
    </form>
  </div>
</main>

<?php
require_once 'includes/pie.php';
?>