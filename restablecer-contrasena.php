<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Restablecer contraseña';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Acceso público'; 
$usuario_autenticado = false; 
$pagina_actual = 'recuperar'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal" class="eq06-restablecer">
  <section class="eq06-tarjeta-clave" aria-labelledby="eq06-titulo-clave">
    <h1 id="eq06-titulo-clave">Crear nueva contraseña</h1>
    <!-- 3. Vista de enlace válido. Comparar contraseñas y verificar el enlace
         requiere lógica futura; HTML no realiza esas comprobaciones. -->
    <form method="post" action="#">
      <fieldset class="eq06-conjunto">
        <legend>Nueva clave de acceso</legend>
        <div class="eq06-grupo-campo">
          <label class="eq06-etiqueta" for="eq06-nueva-clave">Nueva contraseña *</label>
          <input class="eq06-campo" type="password" id="eq06-nueva-clave"
                 name="eq06-nueva-clave" required minlength="8" maxlength="128"
                 autocomplete="new-password" placeholder="Escribe tu nueva contraseña"
                 aria-describedby="eq06-ayuda-clave">
        </div>
        <div class="eq06-grupo-campo">
          <label class="eq06-etiqueta" for="eq06-confirmar-clave">Confirmar contraseña *</label>
          <input class="eq06-campo" type="password" id="eq06-confirmar-clave"
                 name="eq06-confirmar-clave" required minlength="8" maxlength="128"
                 autocomplete="new-password" placeholder="Repite tu nueva contraseña"
                 aria-describedby="eq06-ayuda-clave">
        </div>
        <p id="eq06-ayuda-clave"><strong>Ambas contraseñas deben ser iguales.</strong> Usa entre 8 y 128 caracteres. Los campos con * son obligatorios.</p>
      </fieldset>
      <!-- 4. Botón visual: no guarda ni invalida enlaces en esta etapa. -->
      <button class="eq06-boton" type="submit">Guardar nueva contraseña</button>
    </form>
    <p class="eq06-ayuda-clave">Después de guardar podrás <a href="login.php">iniciar sesión</a>.</p>
  </section>

  <section class="eq06-ayuda-clave" id="eq06-ayuda" aria-labelledby="eq06-titulo-ayuda">
    <h2 class="eq06-oculto" id="eq06-titulo-ayuda">Ayuda para restablecer el acceso</h2>
    <article>
      <h3 class="eq06-oculto">Uso del enlace de recuperación</h3>
      <p>El enlace de recuperación es <strong>temporal y de un solo uso</strong>.</p>
      <!-- Estado alternativo para revisión de la maqueta, no un error real.
           En la implementación futura, un enlace inválido reemplazará el
           formulario por el aviso y no permitirá guardar. Al guardar,
           el enlace se invalidará y se navegará a Iniciar sesión (P02). -->
      <details class="eq06-ayuda-clave">
        <summary>Ver ejemplo de enlace vencido o utilizado</summary>
        <div class="eq06-aviso-clave" role="alert">
          <p><strong>Este enlace ha vencido o ya fue utilizado.</strong> No puedes guardar una nueva contraseña con este enlace.</p>
          <p><a href="#">Solicitar otro enlace</a></p>
        </div>
      </details>
    </article>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>