<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Inicio de sesión · Inventario';
$rol_usuario = 'Acceso público'; 
$usuario_autenticado = false; 
$pagina_actual = 'inicio-sesion'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido" class="eq06-principal">
  <section class="eq06-caja">
    <h1>Iniciar sesión</h1>
    <form class="eq06-formulario" method="post" action="procesar_inicio_sesion.php">
      <fieldset class="eq06-conjunto">
        <legend class="eq06-leyenda">Datos de acceso</legend>
        <div class="eq06-grupo">
          <label class="eq06-etiqueta" for="eq06-usuario">Usuario o correo *</label>
          <input class="eq06-campo" type="text" id="eq06-usuario" name="usuario" required minlength="3" maxlength="100" autocomplete="username" placeholder="usuario@negocio.com">
        </div>
        <div class="eq06-grupo">
          <label class="eq06-etiqueta" for="eq06-clave">Contraseña *</label>
          <input class="eq06-campo" type="password" id="eq06-clave" name="clave" required minlength="8" maxlength="64" autocomplete="current-password" placeholder="Tu contraseña">
        </div>
        <div class="eq06-grupo eq06-casilla">
          <input type="checkbox" id="eq06-recordar" name="recordar" value="si">
          <label class="eq06-etiqueta" for="eq06-recordar">Recordarme</label>
        </div>
        <button class="eq06-boton eq06-boton-ancho" type="submit">Iniciar sesión</button>
        <p class="eq06-aviso eq06-aviso-error" role="alert"><strong>Estado de error:</strong> No fue posible iniciar sesión.</p>
      </fieldset>
    </form>
    <p><a class="eq06-enlace" href="recuperar-clave.php">¿Olvidaste tu contraseña?</a></p>
  </section>
  <aside class="eq06-nota">
    <h2>Reglas de acceso</h2>
    <ul>
      <li>Usuario o correo y contraseña son obligatorios.</li>
      <li>Credenciales inválidas o cuenta inactiva impiden el acceso.</li>
      <li>El mensaje de error no revela qué dato falló.</li>
    </ul>
  </aside>
</main>

<?php
require_once 'includes/pie.php';
?>