<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Listado de inventario · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Encargado de inventario'; 
$usuario_autenticado = true; 
$pagina_actual = 'inventario'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Inventario</h1>
  </div>

  <section class="eq06-seccion" aria-labelledby="filtros-titulo">
    <h2 id="filtros-titulo" class="eq06-sr-only">Filtros de búsqueda</h2>
    <form class="eq06-filtros" method="post" action="#" role="search">
      <div class="eq06-campo-grupo">
        <label for="eq06-buscar">Buscar producto</label>
        <input type="search" id="eq06-buscar" name="buscar" class="eq06-campo"
               placeholder="Nombre o código" maxlength="80" autocomplete="off">
      </div>
      <div class="eq06-campo-grupo">
        <label for="eq06-categoria">Categoría</label>
        <select id="eq06-categoria" name="categoria" class="eq06-campo">
          <option value="">Todas</option>
          <option value="granos">Granos</option>
          <option value="vegetales">Vegetales</option>
          <option value="aceites">Aceites</option>
        </select>
      </div>
      <div class="eq06-campo-grupo">
        <label for="eq06-estado-stock">Estado de stock</label>
        <select id="eq06-estado-stock" name="estado_stock" class="eq06-campo">
          <option value="">Todos</option>
          <option value="bajo">Stock bajo</option>
          <option value="normal">Normal</option>
          <option value="sin">Sin stock</option>
        </select>
      </div>
      <div class="eq06-filtros-acciones">
        <button type="submit" class="eq06-btn">Aplicar filtros</button>
        <button type="reset" class="eq06-btn eq06-btn-secundario">Limpiar</button>
      </div>
    </form>
  </section>

  <div class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Existencias vigentes · 3 resultados · Página 1 de 1</caption>
        <thead>
          <tr>
            <th scope="col">Producto / código</th>
            <th scope="col">Categoría</th>
            <th scope="col">Stock</th>
            <th scope="col">Unidad</th>
            <th scope="col">Estado</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Arroz / <abbr title="Código de producto">PR-001</abbr></td>
            <td>Granos</td>
            <td>5</td>
            <td>kg</td>
            <td><span class="eq06-badge eq06-badge-bajo">Stock bajo</span></td>
            <td><a href="detalle-producto.php">Ver</a></td>
          </tr>
          <tr>
            <td>Tomate / <abbr title="Código de producto">PR-003</abbr></td>
            <td>Vegetales</td>
            <td>0</td>
            <td>kg</td>
            <td>
              <span class="eq06-badge eq06-badge-bajo">Stock bajo</span>
              <span class="eq06-badge eq06-badge-sin">Sin stock</span>
            </td>
            <td><a href="detalle-producto.php">Ver</a></td>
          </tr>
          <tr>
            <td>Aceite / <abbr title="Código de producto">PR-002</abbr></td>
            <td>Aceites</td>
            <td>12</td>
            <td>L</td>
            <td><span class="eq06-badge eq06-badge-normal">Normal</span></td>
            <td><a href="detalle-producto.php">Ver</a></td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="eq06-paginacion">Sin resultados: cambia los filtros o limpia la búsqueda.</p>
  </div>
</main>

<?php
require_once 'includes/pie.php';
?>