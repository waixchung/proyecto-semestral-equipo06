<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Gestión de proveedores · Inventario'; 
$pagina_actual = 'gestion-proveedores'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal">
  <div class="eq06-titulo-fila">
    <h1>Proveedores</h1>
    <!-- Este enlace lleva al formulario de proveedor (P10) -->
    <a class="eq06-boton" href="formulario-proveedor.php">Nuevo proveedor</a>
  </div>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-busqueda">
    <h2 class="eq06-oculto" id="eq06-titulo-busqueda">Búsqueda de proveedores</h2>
    <form method="post" action="#">
      <fieldset class="eq06-conjunto">
        <legend>Buscar proveedor</legend>
        <div class="eq06-busqueda-fila">
          <div class="eq06-grupo-campo">
            <label class="eq06-etiqueta" for="eq06-buscar-proveedor">Nombre o contacto</label>
            <input class="eq06-campo" type="search" id="eq06-buscar-proveedor" name="buscar"
                   minlength="2" maxlength="60" autocomplete="off"
                   placeholder="Nombre o contacto">
          </div>
          <button class="eq06-boton" type="submit">Buscar</button>
        </div>
      </fieldset>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-listado">
    <h2 class="eq06-oculto" id="eq06-titulo-listado">Listado de proveedores</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla eq06-tabla-proveedores">
        <caption>Proveedores registrados</caption>
        <thead>
          <tr>
            <th scope="col">Proveedor</th>
            <th scope="col">Contacto / teléfono</th>
            <th scope="col">Correo</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">Distribuidora Central</th>
            <td>Ana / 6000-0001</td>
            <td>ana@ejemplo.com</td>
            <td><span class="eq06-estado">Activo</span></td>
            <td>
              <ul class="eq06-lista-acciones">
                <!-- Ver y Editar abren el formulario de proveedor (P10) -->
                <li><a href="formulario-proveedor.php" aria-label="Ver proveedor Distribuidora Central">Ver</a></li>
                <li><a href="formulario-proveedor.php" aria-label="Editar proveedor Distribuidora Central">Editar</a></li>
                <li><button class="eq06-boton" type="button" aria-label="Desactivar proveedor Distribuidora Central">Desactivar</button></li>
              </ul>
            </td>
          </tr>
          <tr>
            <th scope="row">Vegetales del Campo</th>
            <td>Luis / 6000-0002</td>
            <td>luis@ejemplo.com</td>
            <td><span class="eq06-estado">Activo</span></td>
            <td>
              <ul class="eq06-lista-acciones">
                <li><a href="formulario-proveedor.php" aria-label="Ver proveedor Vegetales del Campo">Ver</a></li>
                <li><a href="formulario-proveedor.php" aria-label="Editar proveedor Vegetales del Campo">Editar</a></li>
                <li><button class="eq06-boton" type="button" aria-label="Desactivar proveedor Vegetales del Campo">Desactivar</button></li>
              </ul>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <aside class="eq06-nota">
    <p><em>Ver</em> abre el formulario del proveedor en modo consulta; <em>Editar</em> abre el mismo formulario con los campos habilitados.</p>
  </aside>

  <!-- Confirmación al desactivar. Cerrado por defecto: se abrirá con lógica más adelante -->
  <dialog class="eq06-dialogo" id="eq06-dialogo-desactivar" aria-labelledby="eq06-titulo-dialogo">
    <h2 id="eq06-titulo-dialogo">Desactivar proveedor</h2>
    <p>¿Desea desactivar a <strong>Distribuidora Central</strong>? Sus recepciones históricas se conservan y el proveedor dejará de ofrecerse en nuevas recepciones.</p>
    <div class="eq06-dialogo-acciones">
      <button class="eq06-boton" type="button">Confirmar desactivación</button>
      <button class="eq06-boton" type="button">Cancelar</button>
    </div>
  </dialog>
</main>

<?php
require_once 'includes/pie.php';
?>