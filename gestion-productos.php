<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Gestión de productos · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'gestion-productos'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Catálogo de productos</h1>
    <a href="formulario-producto.php" class="eq06-btn">Nuevo</a>
  </div>

  <section class="eq06-seccion" aria-labelledby="filtros-productos">
    <h2 id="filtros-productos" class="eq06-sr-only">Filtros</h2>
    <form class="eq06-filtros" method="post" action="#" role="search">
      <div class="eq06-campo-grupo">
        <label for="eq06-buscar-prod">Buscar</label>
        <input type="text" id="eq06-buscar-prod" name="buscar" class="eq06-campo"
               placeholder="Nombre o código" maxlength="80" autocomplete="off">
      </div>
      <div class="eq06-campo-grupo">
        <label for="eq06-cat-prod">Categoría</label>
        <select id="eq06-cat-prod" name="categoria" class="eq06-campo">
          <option value="">Todas</option>
          <option value="granos">Granos</option>
          <option value="aceites">Aceites</option>
          <option value="vegetales">Vegetales</option>
        </select>
      </div>
      <div class="eq06-campo-grupo">
        <label for="eq06-estado-prod">Estado</label>
        <select id="eq06-estado-prod" name="estado" class="eq06-campo">
          <option value="">Todos</option>
          <option value="activo">Activo</option>
          <option value="inactivo">Inactivo</option>
        </select>
      </div>
      <div class="eq06-filtros-acciones">
        <button type="submit" class="eq06-btn">Aplicar</button>
        <button type="reset" class="eq06-btn eq06-btn-secundario">Limpiar</button>
      </div>
    </form>
    <p class="eq06-nota">
      <a href="categorias-unidades.php">Categorías → C05</a> · <a href="categorias-unidades.php">Unidades → C05</a>
    </p>
  </section>

  <div class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Listado de productos del catálogo</caption>
        <thead>
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Producto</th>
            <th scope="col">Categoría</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><abbr title="Código de producto">PR-001</abbr></td>
            <td>Arroz</td>
            <td>Granos</td>
            <td><span class="eq06-badge eq06-badge-activo">Activo</span></td>
            <td>
              <a href="detalle-producto.php">Ver</a> /
              <a href="formulario-producto.php">Editar</a> /
              <a href="#" class="eq06-btn-peligro" style="text-decoration:underline;border:none;background:none;padding:0;color:#A61E4D;">Desactivar</a>
            </td>
          </tr>
          <tr>
            <td><abbr title="Código de producto">PR-002</abbr></td>
            <td>Aceite</td>
            <td>Aceites</td>
            <td><span class="eq06-badge eq06-badge-activo">Activo</span></td>
            <td>
              <a href="detalle-producto.php">Ver</a> /
              <a href="formulario-producto.php">Editar</a> /
              <a href="#" style="color:#A61E4D;">Desactivar</a>
            </td>
          </tr>
          <tr>
            <td><abbr title="Código de producto">PR-009</abbr></td>
            <td>Harina anterior</td>
            <td>Granos</td>
            <td><span class="eq06-badge eq06-badge-inactivo">Inactivo</span></td>
            <td>
              <a href="detalle-producto.php">Ver</a> /
              <a href="formulario-producto.php">Editar</a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="eq06-alerta eq06-alerta-info eq06-mt-16" role="status">
      Confirmación de desactivación: el producto dejará de estar disponible para nuevos movimientos.
    </div>
  </div>
</main>

<?php
require_once 'includes/pie.php';
?>