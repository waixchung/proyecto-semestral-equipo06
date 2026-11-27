<?php
// formulario-proveedor.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Proveedor.php';

$idProveedor = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$soloLectura = ($idProveedor > 0 && ($_GET['modo'] ?? '') === 'ver');

// Si es solo lectura (?id=...&modo=ver), cualquier rol autenticado puede consultar;
// si es crear o editar, solo administrador y encargado tienen permiso.
if ($soloLectura) {
    verificarAcceso(['administrador', 'encargado', 'recepcionista']);
} else {
    verificarAcceso(['administrador', 'encargado']);
}

$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;
$pagina_actual       = 'proveedores';

// Recuperar valores/errores previos tras validación fallida en procesar_proveedor.php
$valores = $_SESSION['valores_proveedor'] ?? null;
$errores = $_SESSION['errores_proveedor'] ?? [];
unset($_SESSION['valores_proveedor'], $_SESSION['errores_proveedor']);

$modoEdicion = ($idProveedor > 0 && !$soloLectura);

if ($valores === null) {
    if ($idProveedor > 0) {
        $provBD = Proveedor::obtenerPorId($idProveedor);
        if (!$provBD) {
            $_SESSION['mensaje_error_prov'] = 'El proveedor solicitado no existe.';
            header('Location: gestion-proveedores.php');
            exit;
        }

        // Separar dirección principal y referencia si se guardaron unidas por " | "
        $partesDir = explode(' | ', (string)($provBD['direccion'] ?? ''), 2);

        $valores = [
            'id_proveedor'              => (int)$provBD['id_proveedor'],
            'eq06-nombre-proveedor'     => $provBD['nombre_empresa'],
            'eq06-contacto-proveedor'   => $provBD['contacto'],
            'eq06-telefono-proveedor'   => $provBD['telefono'],
            'eq06-correo-proveedor'     => $provBD['correo'] ?? '',
            'eq06-direccion-proveedor'  => $partesDir[0] ?? '',
            'eq06-referencia-proveedor' => $partesDir[1] ?? '',
            'eq06-estado-proveedor'     => ((int)$provBD['activo'] === 1) ? 'activo' : 'inactivo'
        ];
    } else {
        $valores = [
            'id_proveedor'              => 0,
            'eq06-nombre-proveedor'     => '',
            'eq06-contacto-proveedor'   => '',
            'eq06-telefono-proveedor'   => '',
            'eq06-correo-proveedor'     => '',
            'eq06-direccion-proveedor'  => '',
            'eq06-referencia-proveedor' => '',
            'eq06-estado-proveedor'     => 'activo'
        ];
    }
} else {
    $idProveedor = (int)($valores['id_proveedor'] ?? 0);
    $modoEdicion = ($idProveedor > 0 && !$soloLectura);
}

if ($soloLectura) {
    $titulo_pagina = 'Detalle de proveedor | Inventario';
    $encabezado_h1 = 'Detalle del proveedor';
} elseif ($modoEdicion) {
    $titulo_pagina = 'Editar proveedor | Inventario';
    $encabezado_h1 = 'Editar proveedor';
} else {
    $titulo_pagina = 'Nuevo proveedor | Inventario';
    $encabezado_h1 = 'Nuevo proveedor';
}

$attrDisabled = $soloLectura ? 'disabled' : '';

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main id="eq06-principal" class="eq06-main">
  <section class="eq06-panel-proveedor" aria-labelledby="eq06-titulo-proveedor">
    <h1 id="eq06-titulo-proveedor"><?php echo e($encabezado_h1); ?></h1>

    <?php if (isset($errores['general'])): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <?php echo e($errores['general']); ?>
      </div>
    <?php endif; ?>

    <form method="post" action="procesar_proveedor.php">
      <input type="hidden" name="accion" value="<?php echo $modoEdicion ? 'actualizar' : 'crear'; ?>">
      <input type="hidden" name="id_proveedor" value="<?php echo (int)$idProveedor; ?>">

      <fieldset class="eq06-conjunto">
        <legend>Información del proveedor</legend>
        <div class="eq06-formulario-proveedor">

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-nombre-proveedor">Nombre o razón social *</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-nombre-proveedor']) ? 'eq06-campo-error' : ''; ?>"
                   type="text" id="eq06-nombre-proveedor" name="eq06-nombre-proveedor" required maxlength="120"
                   autocomplete="organization" placeholder="Ejemplo: Distribuidora Central"
                   value="<?php echo e($valores['eq06-nombre-proveedor']); ?>" <?php echo $attrDisabled; ?>>
            <?php if (isset($errores['eq06-nombre-proveedor'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo e($errores['eq06-nombre-proveedor']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-contacto-proveedor">Persona de contacto *</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-contacto-proveedor']) ? 'eq06-campo-error' : ''; ?>"
                   type="text" id="eq06-contacto-proveedor" name="eq06-contacto-proveedor" required maxlength="100"
                   autocomplete="name" placeholder="Ejemplo: Ana López"
                   value="<?php echo e($valores['eq06-contacto-proveedor']); ?>" <?php echo $attrDisabled; ?>>
            <?php if (isset($errores['eq06-contacto-proveedor'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo e($errores['eq06-contacto-proveedor']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-telefono-proveedor">Teléfono *</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-telefono-proveedor']) ? 'eq06-campo-error' : ''; ?>"
                   type="tel" id="eq06-telefono-proveedor" name="eq06-telefono-proveedor" required maxlength="25"
                   autocomplete="tel" placeholder="Ejemplo: 6000-0001"
                   value="<?php echo e($valores['eq06-telefono-proveedor']); ?>" <?php echo $attrDisabled; ?>>
            <?php if (isset($errores['eq06-telefono-proveedor'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo e($errores['eq06-telefono-proveedor']); ?></span>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-correo-proveedor">Correo electrónico</label>
            <input class="eq06-campo <?php echo isset($errores['eq06-correo-proveedor']) ? 'eq06-campo-error' : ''; ?>"
                   type="email" id="eq06-correo-proveedor" name="eq06-correo-proveedor" maxlength="100"
                   autocomplete="email" placeholder="Ejemplo: ana@ejemplo.com" aria-describedby="eq06-ayuda-correo-proveedor"
                   value="<?php echo e($valores['eq06-correo-proveedor']); ?>" <?php echo $attrDisabled; ?>>
            <?php if (isset($errores['eq06-correo-proveedor'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem; display:block; margin-top:4px;"><?php echo e($errores['eq06-correo-proveedor']); ?></span>
            <?php else: ?>
              <p class="eq06-ayuda-proveedor" id="eq06-ayuda-correo-proveedor">Opcional. Si lo completas, debe tener un formato válido.</p>
            <?php endif; ?>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-direccion-proveedor">Dirección</label>
            <input class="eq06-campo" type="text" id="eq06-direccion-proveedor"
                   name="eq06-direccion-proveedor" maxlength="140" autocomplete="address-line1" placeholder="Ejemplo: Ciudad de Panamá"
                   value="<?php echo e($valores['eq06-direccion-proveedor']); ?>" <?php echo $attrDisabled; ?>>

            <label class="eq06-etiqueta eq06-referencia-proveedor" for="eq06-referencia-proveedor" style="margin-top: 10px;">Continuación / referencia</label>
            <textarea class="eq06-campo" id="eq06-referencia-proveedor"
                      name="eq06-referencia-proveedor" rows="3" maxlength="100"
                      placeholder="Ejemplo: Local 2, frente al parque" <?php echo $attrDisabled; ?>><?php echo e($valores['eq06-referencia-proveedor']); ?></textarea>
          </div>

          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-estado-proveedor">Estado</label>
            <select class="eq06-campo" id="eq06-estado-proveedor" name="eq06-estado-proveedor" <?php echo $attrDisabled; ?>>
              <option value="activo" <?php echo ($valores['eq06-estado-proveedor'] === 'activo') ? 'selected' : ''; ?>>Activo</option>
              <option value="inactivo" <?php echo ($valores['eq06-estado-proveedor'] === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
            </select>
          </div>
        </div>
        <?php if (!$soloLectura): ?>
          <p class="eq06-ayuda-proveedor">Los campos con * son obligatorios.</p>
        <?php endif; ?>
      </fieldset>

      <div class="eq06-acciones">
        <?php if (!$soloLectura): ?>
          <button class="eq06-boton" type="submit"><?php echo $modoEdicion ? 'Actualizar proveedor' : 'Guardar'; ?></button>
        <?php elseif (in_array($rol_usuario, ['administrador', 'encargado'], true)): ?>
          <a class="eq06-boton" href="formulario-proveedor.php?id=<?php echo (int)$idProveedor; ?>">Habilitar edición</a>
        <?php endif; ?>
        <a class="eq06-boton eq06-cancelar-proveedor" href="gestion-proveedores.php">Volver al listado</a>
      </div>
    </form>

    <article class="eq06-nota-proveedor">
      <h2 class="eq06-oculto">Historial de recepciones</h2>
      <p>Actualizar los datos de contacto <strong>no modifica las recepciones históricas</strong>.</p>
    </article>
  </section>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>