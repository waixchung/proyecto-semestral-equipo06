<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Registrar salida de inventario | Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Encargado de inventario'; 
$usuario_autenticado = true; 
$pagina_actual = 'registro-salida'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido">
  <h1>Registrar salida</h1>

  <section aria-labelledby="eq06-titulo-formulario">
    <h2 id="eq04-titulo-formulario">Nueva salida de inventario</h2> <br>
    <form action="procesar_salida.php" method="post">
      <fieldset class="eq06-grupo">
        <legend id="eq06-titulo-formulario">Datos de la salida</legend>

        <div class="eq06-fila eq06-fila-dos">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-producto">Producto activo <span class="eq06-obligatorio">*</span></label>
            <select id="eq06-producto" name="producto" class="eq06-campo" required>
              <option value="">Selecciona un producto</option>
              <option value="PR-001" selected>Arroz / PR-001</option>
              <option value="PR-002">Aceite / PR-002</option>
              <option value="PR-003">Tomate / PR-003</option>
            </select>
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-fecha">Fecha <span class="eq06-obligatorio">*</span></label>
            <input type="date" id="eq06-fecha" name="fecha" class="eq06-campo" required value="2026-10-05" min="2026-01-01">
          </div>
        </div>

        <p class="eq06-resumen" id="eq06-resumen-saldo" role="status">
          Disponible: <strong>5 <abbr title="kilogramos">kg</abbr></strong> ·
          Mínimo del producto: <strong>8 <abbr title="kilogramos">kg</abbr></strong> ·
          Cantidad propuesta: <strong>2 <abbr title="kilogramos">kg</abbr></strong> →
          Saldo: <strong>3 <abbr title="kilogramos">kg</abbr></strong> ·
          <span class="eq06-etiqueta-estado">Stock bajo</span>
        </p>

        <div class="eq06-fila eq06-fila-iguales">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-cantidad">Cantidad a retirar <span class="eq06-obligatorio">*</span></label>
            <input type="number" id="eq06-cantidad" name="cantidad" class="eq06-campo" required min="0.01" max="5" step="0.01" value="2" inputmode="decimal" placeholder="Ej.: 2">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-motivo">Motivo <span class="eq06-obligatorio">*</span></label>
            <select id="eq06-motivo" name="motivo" class="eq06-campo" required>
              <option value="">Selecciona un motivo</option>
              <option value="consumo" selected>Consumo</option>
              <option value="utilizacion">Utilización</option>
              <option value="retiro">Retiro</option>
            </select>
          </div>
        </div>

        <div class="eq06-campo-bloque">
          <label class="eq06-etiqueta" for="eq06-observacion">Observación (opcional)</label>
          <textarea id="eq06-observacion" name="observacion" class="eq06-campo" rows="3" maxlength="200" placeholder="Ej.: preparación del almuerzo"></textarea>
        </div>
      </fieldset>

      <div class="eq06-acciones">
        <button type="submit" class="eq06-boton">Confirmar salida</button>
        <a href="dashboard-encargado.php">Cancelar</a>
        <!-- Estado de error (se activará con lógica más adelante): quitar hidden para verlo -->
        <div class="eq06-mensaje-error" role="alert" hidden>Stock insuficiente: la cantidad supera los 5 kg disponibles.</div>
      </div>
    </form>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>