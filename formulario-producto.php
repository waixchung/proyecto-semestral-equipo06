<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Formulario de producto · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'productos'; 

// Carga de componentes modulares
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
            <input type="text" id="eq06-nombre" name="nombre" class="eq06-campo"
                   required minlength="2" maxlength="100" autocomplete="off"
                   placeholder="Ej. Arroz" value="Arroz">
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-codigo">Código único <abbr title="obligatorio">*</abbr></label>
            <input type="text" id="eq06-codigo" name="codigo" class="eq06-campo"
                   required pattern="PR-[0-9]{3,}" maxlength="20" autocomplete="off"
                   placeholder="PR-001" value="PR-001"
                   title="Formato: PR- seguido de números">
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-categoria-form">Categoría <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-categoria-form" name="categoria" class="eq06-campo" required>
              <option value="">Seleccione…</option>
              <option value="granos" selected>Granos</option>
              <option value="aceites">Aceites</option>
              <option value="vegetales">Vegetales</option>
            </select>
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-unidad">Unidad de medida <abbr title="obligatorio">*</abbr></label>
            <select id="eq06-unidad" name="unidad" class="eq06-campo" required>
              <option value="">Seleccione…</option>
              <option value="kg" selected>kg</option>
              <option value="L">L</option>
              <option value="und">und</option>
            </select>
            <p class="eq06-nota">Con historial: unidad bloqueada si altera su interpretación.</p>
          </div>
        </div>

        <div class="eq06-fila-campos">
          <div class="eq06-campo-grupo">
            <label for="eq06-stock-min">Stock mínimo <abbr title="obligatorio">*</abbr> · CE-01</label>
            <input type="number" id="eq06-stock-min" name="stock_minimo" class="eq06-campo"
                   required min="0" step="0.01" value="8"
                   placeholder="0">
            <p class="eq06-nota">CE-01: mínimo propio del producto, en su unidad.</p>
          </div>
          <div class="eq06-campo-grupo">
            <label for="eq06-proveedor">Proveedor principal</label>
            <select id="eq06-proveedor" name="proveedor" class="eq06-campo">
              <option value="">Sin proveedor</option>
              <option value="dist-central" selected>Distribuidora Central</option>
              <option value="mayorista-norte">Mayorista Norte</option>
            </select>
          </div>
        </div>

        <div class="eq06-campo-grupo">
          <label for="eq06-estado-form">Estado</label>
          <select id="eq06-estado-form" name="estado" class="eq06-campo">
            <option value="activo" selected>Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <div class="eq06-alerta eq06-alerta-info" role="note">
          <strong>RB-01:</strong> saldo &lt; mínimo → Stock bajo; saldo = mínimo → Normal.
          No hay edición directa del stock desde este formulario.
        </div>

        <div class="eq06-acciones-form">
          <button type="submit" class="eq06-btn">Guardar</button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Cancelar</a>
        </div>
      </fieldset>
    </form>
  </div>
</main>

<?php
require_once 'includes/pie.php';
?>