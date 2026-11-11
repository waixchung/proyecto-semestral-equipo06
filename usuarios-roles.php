<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Usuarios - Roles · Inventario'; 
$pagina_actual = 'usuarios-roles'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Usuarios y roles</h1>
  </div>

  <section class="eq06-seccion">
    <form method="get" action="#" class="eq06-form-busqueda">
      <fieldset>
        <legend>Buscar cuenta</legend>
        <div class="eq06-grupo-campo">
          <label class="eq06-etiqueta" for="buscar-usuario">Nombre o correo</label>
          <input class="eq06-campo" type="text" id="buscar-usuario" name="buscar" placeholder="Ejemplo: Ana o ana@ejemplo.com">
        </div>
        <div class="eq06-acciones" style="margin-top: 10px;">
          <button type="submit" class="eq06-btn">Buscar</button>
          <button type="button" class="eq06-btn eq06-btn-secundario">Nueva cuenta</button>
        </div>
      </fieldset>
    </form>
  </section>

  <section class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Cuentas de ejemplo y sus roles asignados</caption>
        <thead>
          <tr>
            <th scope="col">Usuario</th>
            <th scope="col">Correo</th>
            <th scope="col">Rol</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Ana</td>
            <td>ana@ejemplo.com</td>
            <td>Recepcionista</td>
            <td>Activa</td>
            <td>
              <a href="#">Editar</a> / <button class="eq06-btn-link">Desactivar</button>
            </td>
          </tr>
          <tr>
            <td>Luis</td>
            <td>luis@ejemplo.com</td>
            <td>Encargado</td>
            <td>Activa</td>
            <td>
              <a href="#">Editar</a> / <button class="eq06-btn-link">Desactivar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="eq06-seccion">
    <h2>Panel de creación / edición</h2>
    <form method="post" action="procesar_usuario.php" class="eq06-form">
      <fieldset>
        <legend>Datos de la cuenta</legend>
        
        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="nombre-usuario">Nombre <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="nombre-usuario" name="nombre" class="eq06-campo" required placeholder="Ejemplo: Ana">
          </div>
          <div class="eq06-campo-grupo">
            <label for="correo-usuario">Correo electrónico <abbr title="obligatorio">*</abbr></label>
            <input type="email" id="correo-usuario" name="correo" class="eq06-campo" required placeholder="ana@ejemplo.com">
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="rol-usuario">Rol(es) válidos <abbr title="obligatorio">*</abbr></label>
            <select id="rol-usuario" name="rol[]" class="eq06-campo" multiple required style="height: auto;">
              <option value="Administrador">Administrador</option>
              <option value="Encargado">Encargado</option>
              <option value="Recepcionista">Recepcionista</option>
            </select>
            <p class="eq06-nota">Selecciona al menos un rol. En escritorio, usa Ctrl (Windows) o Command (Mac) para seleccionar varios.</p>
          </div>
          <div class="eq06-campo-grupo">
            <label for="estado-usuario">Estado</label>
            <select id="estado-usuario" name="estado" class="eq06-campo">
              <option value="Activa" selected>Activa</option>
              <option value="Inactiva">Inactiva</option>
            </select>
          </div>
        </div>
        <p class="eq06-nota">Los campos con * son obligatorios.</p>
        
        <div class="eq06-acciones-form">
          <button type="submit" class="eq06-btn">Guardar</button>
          <button type="reset" class="eq06-btn eq06-btn-secundario">Cancelar</button>
        </div>
      </fieldset>
    </form>
    
    <div class="eq06-alerta eq06-alerta-info" style="margin-top: 20px;">
      <h3>Consideraciones de administración</h3>
      <ul style="margin-left: 20px;">
        <li>Cada cuenta debe tener al menos un rol válido.</li>
        <li>No se puede desactivar ni quitar el rol al último administrador activo.</li>
        <li>Los roles se administran aquí; no se editan desde el perfil personal.</li>
        <li>El alta y la entrega de contraseña están <em>pendientes de definición</em>.</li>
      </ul>
      <details>
        <summary>Ver ejemplo de operación bloqueada</summary>
        <p style="margin-top: 10px;">(Ejemplo de alerta cuando se intenta desactivar al único administrador).</p>
      </details>
    </div>
  </section>

</main>

<?php require_once 'includes/pie.php'; ?>