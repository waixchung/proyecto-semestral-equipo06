<?php
if (!defined('APP_ACCESO')) { define('APP_ACCESO', true); }

// Inicialización para peticiones GET
if (!isset($valores)) {
    $valores = ['usuario' => ''];
}
if (!isset($errores)) {
    $errores = [];
}

$titulo_pagina = 'Iniciar sesión | Inventario';
// No cargamos el menú interno porque es una página pública
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $titulo_pagina; ?></title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <!-- Ajusta el encabezado público según tu maquetación -->
  <header class="eq06-encabezado">
    <div class="eq06-marca">Inventario</div>
  </header>

  <main id="eq06-principal" style="max-width: 400px; margin: 2rem auto;">
    <h1 style="text-align: center;">Iniciar sesión</h1>

    <form method="post" action="procesar_inicio_sesion.php">
      <fieldset class="eq06-conjunto">
        <legend>Datos de acceso</legend>
        
        <div class="eq06-grupo-campo">
          <label class="eq06-etiqueta" for="usuario">Usuario o correo *</label>
          <input class="eq06-campo <?php echo isset($errores['usuario']) ? 'eq06-campo-error' : ''; ?>" 
                 type="text" id="usuario" name="usuario" required 
                 value="<?php echo htmlspecialchars($valores['usuario']); ?>">
          <?php if (isset($errores['usuario'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo $errores['usuario']; ?></span>
          <?php endif; ?>
        </div>

        <div class="eq06-grupo-campo" style="margin-top: 1rem;">
          <label class="eq06-etiqueta" for="contrasena">Contraseña *</label>
          <input class="eq06-campo <?php echo isset($errores['contrasena']) ? 'eq06-campo-error' : ''; ?>" 
                 type="password" id="contrasena" name="contrasena" required>
          <?php if (isset($errores['contrasena'])): ?>
              <span class="eq06-mensaje-error" style="color: #A61E4D; font-size: 0.875rem;"><?php echo $errores['contrasena']; ?></span>
          <?php endif; ?>
        </div>

        <div class="eq06-grupo-campo" style="margin-top: 1rem;">
          <label>
            <input type="checkbox" name="recordarme"> Recordarme
          </label>
        </div>

        <div class="eq06-acciones" style="margin-top: 1rem;">
          <button class="eq06-boton" type="submit" style="width: 100%; background-color: #A61E4D; color: white; padding: 10px; border: none; cursor: pointer;">Iniciar sesión</button>
        </div>

        <!-- Alerta de credenciales incorrectas -->
        <?php if (isset($errores['auth'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-top: 15px; border: 1px solid #f5c6cb;">
                <strong>Estado de error:</strong> <?php echo $errores['auth']; ?>
            </div>
        <?php endif; ?>

      </fieldset>
    </form>

    <div style="margin-top: 1rem; text-align: center;">
      <a href="recuperar-clave.php" style="color: #A61E4D;">¿Olvidaste tu contraseña?</a>
    </div>
  </main>
</body>
</html>