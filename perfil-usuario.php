<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Mi perfil | Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Encargado de inventario'; 
$usuario_autenticado = true; 
$pagina_actual = 'perfil-usuario'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido">
  <h1>Mi perfil</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-datos">
    <h2 id="eq06-titulo-datos">Datos personales</h2>

    <form action="#" method="post">
      <fieldset class="eq06-grupo">
        <legend>Información de la cuenta</legend>

        <div class="eq06-fila eq06-fila-mitad">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-nombre">Nombre</label>
            <input type="text" id="eq06-nombre" name="nombre" class="eq06-campo" required minlength="3" maxlength="60" pattern="[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]{3,60}" title="Solo letras y espacios, entre 3 y 60 caracteres" autocomplete="name" placeholder="Nombre y apellido" value="Luis Pérez">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-correo">Correo</label>
            <input type="email" id="eq06-correo" name="correo" class="eq06-campo" required maxlength="100" autocomplete="email" placeholder="usuario@dominio.com" value="luis@ejemplo.com">
          </div>
        </div>

        <div class="eq06-campo-bloque">
          <label class="eq06-etiqueta" for="eq06-rol">Rol asignado [solo lectura]</label>
          <output id="eq06-rol" name="rol" class="eq06-campo eq06-solo-lectura">Encargado de inventario</output>
        </div>
      </fieldset>

      <div class="eq06-acciones">
        <button type="submit" class="eq06-boton">Guardar datos</button>
      </div>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-clave">
    <h2 id="eq06-titulo-clave">Cambiar contraseña</h2>

    <form action="#" method="post">
      <fieldset class="eq06-grupo">
        <legend>Nueva contraseña</legend>

        <div class="eq06-fila eq06-fila-tres">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-clave-actual">Contraseña actual</label>
            <input type="password" id="eq06-clave-actual" name="clave_actual" class="eq06-campo" required minlength="8" maxlength="64" autocomplete="current-password" placeholder="Contraseña actual">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-clave-nueva">Nueva contraseña</label>
            <input type="password" id="eq06-clave-nueva" name="clave_nueva" class="eq06-campo" required minlength="8" maxlength="64" autocomplete="new-password" placeholder="Mínimo 8 caracteres">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-clave-confirmar">Confirmar nueva</label>
            <input type="password" id="eq06-clave-confirmar" name="clave_confirmar" class="eq06-campo" required minlength="8" maxlength="64" autocomplete="new-password" placeholder="Repite la nueva contraseña">
          </div>
        </div>
      </fieldset>

      <div class="eq06-acciones">
        <button type="submit" class="eq06-boton">Cambiar contraseña</button>
      </div>
    </form>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>