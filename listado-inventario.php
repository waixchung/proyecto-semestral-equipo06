<?php
define('APP_ACCESO', true);

$titulo_pagina = 'Listado de inventario · Inventario';
$rol_usuario = 'Encargado de inventario'; 
$usuario_autenticado = true; 
$pagina_actual = 'inventario'; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Inventario</h1>
  </div>
  
  <div class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Existencias vigentes</caption>
        <thead>
          <tr>
            <th scope="col">Producto / código</th>
            <th scope="col">Categoría</th>
            <th scope="col">Stock</th>
            <th scope="col">Unidad</th>
            <th scope="col">Precio Unit.</th>
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
            <td>$1.20</td>
            <td><span class="eq06-badge eq06-badge-bajo">Stock bajo</span></td>
            <td><a href="detalle-producto.php">Ver</a></td>
          </tr>
          <tr>
            <td>Aceite / <abbr title="Código de producto">PR-002</abbr></td>
            <td>Aceites</td>
            <td>12</td>
            <td>L</td>
            <td>$2.50</td>
            <td><span class="eq06-badge eq06-badge-normal">Normal</span></td>
            <td><a href="detalle-producto.php">Ver</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php require_once 'includes/pie.php'; ?>