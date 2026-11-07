<?php
define('APP_ACCESO', true);

$titulo_pagina = 'Formulario de producto · Inventario';
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'productos'; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Nuevo producto / Editar producto</h1>
  </div>

  <div class="eq06-seccion">
    <form class="eq06-form" method="post" action="procesar_producto.php" id="eq06-form-producto">
      <fieldset>
        <legend>Datos del producto</legend>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-nombre">Nombre <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-nombre" name="nombre" class="eq06-campo" required minlength="2" maxlength="100" value="Arroz">
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-codigo">Código único <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-codigo" name="codigo" class="eq06-campo" required pattern="PR-[0-9]{3,}" value="PR-001">
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-precio">Precio Unitario ($) <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-precio" name="precio" class="eq06-campo" required min="0.01" step="0.01" value="1.20" placeholder="Ej. 1.20">
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-fecha-caducidad">Fecha de caducidad</label>
            <input type="date" id="eq06-fecha-caducidad" name="fecha_caducidad" class="eq06-campo">
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-stock-min">Stock mínimo <abbr title="obligatorio">*</abbr></label>
            <input type="number" id="eq06-stock-min" name="stock_minimo" class="eq06-campo" required min="0" step="0.01" value="8">
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-unidad">Unidad de medida <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-unidad" name="unidad" class="eq06-campo" required>
              <option value="kg" selected>kg</option>
              <option value="L">L</option>
              <option value="und">und</option>
            </select>
          </div>
        </div>

        <div class="eq06-acciones-form">
          <button type="submit" class="eq06-btn">Guardar</button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Cancelar</a>
        </div>
      </fieldset>
    </form>
  </div>
</main>

<?php require_once 'includes/pie.php'; ?>