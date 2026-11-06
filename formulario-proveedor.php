<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Nuevo proveedor | Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'gestion-proveedores'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal">
  <section class="eq06-panel-proveedor" aria-labelledby="eq06-titulo-proveedor">
    <h1 id="eq06-titulo-proveedor">Datos del proveedor</h1>
    <!-- Modo Nuevo: campos vacíos. Modo Editar: completar value y el texto
         del textarea con los datos del proveedor; cambiar también el title.
         Modo Consulta: usar readonly en input/textarea y disabled en select;
         retirar el botón Guardar y dejar un enlace Volver a proveedores.
         No se implementa cambio de modo ni persistencia en esta etapa. -->
    <form method="post" action="procesar_proveedor.php">
      <fieldset class="eq06-conjunto">
        <legend>Información del proveedor</legend>
        <div class="eq06-formulario-proveedor">
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-nombre-proveedor">Nombre o razón social *</label>
            <input class="eq06-campo" type="text" id="eq06-nombre-proveedor"
                   name="eq06-nombre-proveedor" required maxlength="120"
                   autocomplete="organization" placeholder="Ejemplo: Distribuidora Central">
          </div>
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-contacto-proveedor">Persona de contacto</label>
            <input class="eq06-campo" type="text" id="eq06-contacto-proveedor"
                   name="eq06-contacto-proveedor" maxlength="100"
                   autocomplete="name" placeholder="Ejemplo: Ana López">
          </div>
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-telefono-proveedor">Teléfono</label>
            <input class="eq06-campo" type="tel" id="eq06-telefono-proveedor"
                   name="eq06-telefono-proveedor" maxlength="25"
                   autocomplete="tel" placeholder="Ejemplo: 6000-0001">
          </div>
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-correo-proveedor">Correo electrónico</label>
            <input class="eq06-campo" type="email" id="eq06-correo-proveedor"
                   name="eq06-correo-proveedor" maxlength="100"
                   autocomplete="email" placeholder="Ejemplo: ana@ejemplo.com"
                   aria-describedby="eq06-ayuda-correo-proveedor">
            <p class="eq06-ayuda-proveedor" id="eq06-ayuda-correo-proveedor">Opcional. Si lo completas, debe tener un formato válido.</p>
          </div>
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-direccion-proveedor">Dirección</label>
            <input class="eq06-campo" type="text" id="eq06-direccion-proveedor"
                   name="eq06-direccion-proveedor" maxlength="200"
                   autocomplete="address-line1" placeholder="Ejemplo: Ciudad de Panamá">
            <label class="eq06-etiqueta eq06-referencia-proveedor" for="eq06-referencia-proveedor">Continuación de dirección / referencia</label>
            <textarea class="eq06-campo" id="eq06-referencia-proveedor"
                      name="eq06-referencia-proveedor" rows="3" maxlength="300"
                      placeholder="Ejemplo: Local 2, frente al parque"></textarea>
          </div>
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-estado-proveedor">Estado</label>
            <select class="eq06-campo" id="eq06-estado-proveedor" name="eq06-estado-proveedor">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
        </div>
        <p class="eq06-ayuda-proveedor">El campo con * es obligatorio.</p>
      </fieldset>
      <div class="eq06-acciones">
        <button class="eq06-boton" type="submit">Guardar</button>
        <!-- Enlace conservando el nombre original y cambiando a .php -->
        <a class="eq06-boton eq06-cancelar-proveedor" href="gestion-proveedores.php">Cancelar</a>
      </div>
    </form>
    <article class="eq06-nota-proveedor">
      <h2 class="eq06-oculto">Historial de recepciones</h2>
      <p>Actualizar los datos de contacto <strong>no modifica las recepciones históricas</strong>.</p>
    </article>
    <!-- Reservar mensajes de error con role="alert" para estados de error
         reales en la implementación futura; no hay errores simulados activos.
         Guardar no persiste datos. Cancelar vuelve a P09 sin enviar el formulario. -->
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>