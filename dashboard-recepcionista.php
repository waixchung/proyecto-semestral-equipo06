<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Dashboard del Recepcionista de mercancía';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Recepcionista de mercancía'; 
$usuario_autenticado = true; 
$pagina_actual = 'dashboard'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal">
  <h1>Resumen del inventario</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-indicadores">
    <h2 class="eq06-oculto" id="eq06-titulo-indicadores">Indicadores del día</h2>
    <div class="eq06-indicadores">
      <article class="eq06-indicador">
        <p class="eq06-indicador-valor"><strong>24</strong></p>
        <h3 class="eq06-indicador-titulo">Productos activos</h3>
      </article>
      <article class="eq06-indicador">
        <p class="eq06-indicador-valor"><strong>03</strong></p>
        <h3 class="eq06-indicador-titulo">Stock bajo</h3>
      </article>
      <article class="eq06-indicador">
        <p class="eq06-indicador-valor"><strong>05</strong></p>
        <h3 class="eq06-indicador-titulo">Entradas del día</h3>
      </article>
      <article class="eq06-indicador">
        <p class="eq06-indicador-valor"><strong>02</strong></p>
        <h3 class="eq06-indicador-titulo">Salidas del día</h3>
      </article>
    </div>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-acciones">
    <h2 class="eq06-oculto" id="eq06-titulo-acciones">Acciones rápidas</h2>
    <div class="eq06-acciones">
      <!-- Se actualiza el enlace usando el nombre definido en la estructura del menú -->
      <a class="eq06-boton" href="recepcion.php">Registrar recepción</a>
      <a class="eq06-boton" href="#eq06-consulta-productos">Consultar productos</a>
    </div>
  </section>

  <!-- Consulta básica; no representa un listado global del inventario. -->
  <section class="eq06-seccion" id="eq06-consulta-productos" aria-labelledby="eq06-titulo-consulta">
    <h2 id="eq06-titulo-consulta">Consulta básica de productos</h2>
    <form method="post" action="#">
      <fieldset class="eq06-conjunto">
        <legend>Buscar producto</legend>
        <div class="eq06-grupo-campo">
          <label class="eq06-etiqueta" for="eq06-busqueda-producto">Nombre o código</label>
          <input class="eq06-campo" type="search" id="eq06-busqueda-producto"
                 name="eq06-busqueda-producto" required minlength="2" maxlength="80"
                 autocomplete="off" placeholder="Ejemplo: Arroz o PR-001">
        </div>
        <button class="eq06-boton" type="submit">Buscar</button>
      </fieldset>
    </form>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla">
        <caption>Ejemplo de resultados de la consulta de productos</caption>
        <thead>
          <tr>
            <th scope="col">Código</th>
            <th scope="col">Producto</th>
            <th scope="col">Unidad</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">PR-001</th>
            <td>Arroz</td>
            <td><abbr title="kilogramos">kg</abbr></td>
            <td><a href="detalle-producto.php" aria-label="Ver detalle del producto Arroz">Ver detalle P06</a></td>
          </tr>
          <tr>
            <th scope="row">PR-002</th>
            <td>Aceite</td>
            <td><abbr title="litros">L</abbr></td>
            <td><a href="detalle-producto.php" aria-label="Ver detalle del producto Aceite">Ver detalle P06</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alerta">
    <h2 class="eq06-oculto" id="eq06-titulo-alerta">Alerta de inventario</h2>
    <p class="eq06-conjunto">
      <strong>Alerta de inventario:</strong> Arroz · Stock 5 <abbr title="kilogramos">kg</abbr>
      / Mínimo 8 <abbr title="kilogramos">kg</abbr> ·
      <span class="eq06-etiqueta-bajo">Stock bajo</span> ·
      <a href="detalle-producto.php" aria-label="Ver detalle del producto Arroz con stock bajo">Ver detalle P06</a>.
    </p>
    <!-- Stock bajo: stock actual < mínimo configurado por producto.
         La comparación y actualización se implementarán en una etapa posterior. -->
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimiento">
    <h2 class="eq06-oculto" id="eq06-titulo-movimiento">Movimiento reciente</h2>
    <p>Movimiento reciente:
      <time datetime="2026-10-05">05/10/2026</time> · Entrada · Arroz
      +5 <abbr title="kilogramos">kg</abbr> · REC-0021 · Ana
    </p>
  </section>
  <!-- Maqueta estática: indicadores y resultados de ejemplo; sin búsqueda ni registro real. -->
</main>

<?php
require_once 'includes/pie.php';
?>