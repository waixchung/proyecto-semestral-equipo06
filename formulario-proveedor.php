<?php
if (!defined('APP_ACCESO')) { define('APP_ACCESO', true); }

if (!isset($valores)) {
    $valores = [
        'eq06-nombre-proveedor' => '',
        'eq06-contacto-proveedor' => '',
        'eq06-telefono-proveedor' => '',
        'eq06-correo-proveedor' => '',
        'eq06-direccion-proveedor' => '',
        'eq06-referencia-proveedor' => '',
        'eq06-estado-proveedor' => 'activo'
    ];
}
if (!isset($errores)) { $errores = []; }

$titulo_pagina = 'Nuevo proveedor | Inventario';
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'gestion-proveedores'; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal">
  <section class="eq06-panel-proveedor" aria-labelledby="eq06-titulo-proveedor">
    <h1 id="eq06-titulo-proveedor">Datos del proveedor</h1>
    
    <form method="post" action="procesar_proveedor.php">
      <fieldset class="eq06-conjunto">
        <legend>Información del proveedor</legend>
        <div class="eq06-formulario-proveedor">
          
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-nombre-proveedor">Nombre o razón social *</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-nombre-proveedor']) ? 'eq06-campo-error' : ''; ?>" 
                   type="text" id="eq06-nombre-proveedor" name="eq06-nombre-proveedor" required maxlength="120"
                   autocomplete="organization" placeholder="Ejemplo: Distribuidora Central"
                   value="<?php echo htmlspecialchars($valores['eq06-nombre-proveedor']); ?>">
            <?php if (isset($errores['eq06-nombre-proveedor'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo $errores['eq06-nombre-proveedor']; ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-contacto-proveedor">Persona de contacto</label>
            <input class="eq06-campo" type="text" id="eq06-contacto-proveedor"
                   name="eq06-contacto-proveedor" maxlength="100" autocomplete="name" placeholder="Ejemplo: Ana López"
                   value="<?php echo htmlspecialchars($valores['eq06-contacto-proveedor']); ?>">
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-telefono-proveedor">Teléfono</label>
            <input class="eq06-campo" type="tel" id="eq06-telefono-proveedor"
                   name="eq06-telefono-proveedor" maxlength="25" autocomplete="tel" placeholder="Ejemplo: 6000-0001"
                   value="<?php echo htmlspecialchars($valores['eq06-telefono-proveedor']); ?>">
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-correo-proveedor">Correo electrónico</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-correo-proveedor']) ? 'eq06-campo-error' : ''; ?>" 
                   type="email" id="eq06-correo-proveedor" name="eq06-correo-proveedor" maxlength="100"
                   autocomplete="email" placeholder="Ejemplo: ana@ejemplo.com" aria-describedby="eq06-ayuda-correo-proveedor"
                   value="<?php echo htmlspecialchars($valores['eq06-correo-proveedor']); ?>">
            <?php if (isset($errores['eq06-correo-proveedor'])): ?>
                <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display:block; margin-top:4px;"><?php echo $errores['eq06-correo-proveedor']; ?></span>
            <?php else: ?>
                <p class="eq06-ayuda-proveedor" id="eq06-ayuda-correo-proveedor">Opcional. Si lo completas, debe tener un formato válido.</p>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-direccion-proveedor">Dirección</label>
            <input class="eq06-campo" type="text" id="eq06-direccion-proveedor"
                   name="eq06-direccion-proveedor" maxlength="200" autocomplete="address-line1" placeholder="Ejemplo: Ciudad de Panamá"
                   value="<?php echo htmlspecialchars($valores['eq06-direccion-proveedor']); ?>">
            
            <label class="eq06-etiqueta eq06-referencia-proveedor" for="eq06-referencia-proveedor" style="margin-top: 10px;">Continuación / referencia</label>
            <textarea class="eq06-campo" id="eq06-referencia-proveedor"
                      name="eq06-referencia-proveedor" rows="3" maxlength="300"
                      placeholder="Ejemplo: Local 2, frente al parque"><?php echo htmlspecialchars($valores['eq06-referencia-proveedor']); ?></textarea>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-estado-proveedor">Estado</label>
            <select class="eq06-campo" id="eq06-estado-proveedor" name="eq06-estado-proveedor">
              <option value="activo" <?php echo ($valores['eq06-estado-proveedor'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
              <option value="inactivo" <?php echo ($valores['eq06-estado-proveedor'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
          </div>
        </div>
        <p class="eq06-ayuda-proveedor">El campo con * es obligatorio.</p>
      </fieldset>
      
      <div class="eq06-acciones">
        <button class="eq06-boton" type="submit">Guardar</button>
        <a class="eq06-boton eq06-cancelar-proveedor" href="gestion-proveedores.php">Cancelar</a>
      </div>
    </form>
    
    <article class="eq06-nota-proveedor">
      <h2 class="eq06-oculto">Historial de recepciones</h2>
      <p>Actualizar los datos de contacto <strong>no modifica las recepciones históricas</strong>.</p>
    </article>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>