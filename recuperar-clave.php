<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Recuperación de contraseña · Inventario';
$rol_usuario = 'Acceso público'; 
$usuario_autenticado = false; 
$pagina_actual = 'recuperar-clave'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido" class="eq06-principal">
  <section class="eq06-caja">
    <h1>Recuperar contraseña</h1>
    <form class="eq06-formulario" method="post" action="#">
      <fieldset class="eq06-conjunto">
        <legend class="eq06-leyenda">Solicitud de enlace</legend>
        <p>Escribe tu correo para solicitar instrucciones.</p>
        <div class="eq06-grupo">
          <label class="eq06-etiqueta" for="eq06-correo">Correo electrónico *</label>
          <input class="eq06-campo" type="email" id="eq06-correo" name="correo" required maxlength="100" autocomplete="email" placeholder="usuario@negocio.com">
        </div>
        <button class="eq06-boton eq06-boton-ancho" type="submit">Enviar solicitud</button>
        <!-- Enlace adaptado manteniendo el nombre original pero con extensión .php -->
        <p><a class="eq06-enlace" href="inicio-sesion.php">Volver al inicio de sesión</a></p>
      </fieldset>
    </form>
    <p class="eq06-aviso" role="status"><strong>Confirmación:</strong> Si el correo está registrado, recibirás instrucciones para restablecer tu contraseña.</p>
  </section>
  <aside class="eq06-nota">
    <h2>Cómo funciona</h2>
    <ul>
      <li>Solo se valida el formato del correo; no se confirma si la cuenta existe.</li>
      <li>El enlace es <em>temporal y de un solo uso</em>.</li>
      <li>Si el enlace venció o ya se usó, puedes solicitarlo de nuevo.</li>
    </ul>
  </aside>
</main>

<?php
require_once 'includes/pie.php';
?>