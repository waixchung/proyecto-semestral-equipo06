<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Dashboard del Encargado de inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Encargado de inventario'; 
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
      <a class="eq06-boton" href="recepcion-mercancia.php">Registrar recepción</a>
      <button class="eq06-boton" type="button">Registrar salida</button>
    </div>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alertas">
    <h2 id="eq06-titulo-alertas">Alertas: stock actual &lt; stock mínimo</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla">
        <caption>Productos cuyo stock actual está por debajo del mínimo</caption>
        <thead>
          <tr>
            <th scope="col">Producto</th>
            <th scope="col">Stock actual</th>
            <th scope="col">Mínimo</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">Arroz</th>
            <td>5 <abbr title="kilogramos">kg</abbr> <span class="eq06-etiqueta-bajo">Stock bajo</span></td>
            <td>8 <abbr title="kilogramos">kg</abbr></td>
            <td><a href="detalle-producto.php" aria-label="Ver detalle del producto Arroz">Ver detalle del producto</a></td>
          </tr>
          <tr>
            <th scope="row">Tomate</th>
            <td>0 <abbr title="kilogramos">kg</abbr> <span class="eq06-etiqueta-bajo">Stock bajo</span></td>
            <td>3 <abbr title="kilogramos">kg</abbr></td>
            <td><a href="detalle-producto.php" aria-label="Ver detalle del producto Tomate">Ver detalle del producto</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimientos">
    <h2 id="eq06-titulo-movimientos">Movimientos recientes</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla">
        <caption>Últimas entradas y salidas registradas</caption>
        <thead>
          <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Producto</th>
            <th scope="col">Tipo</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Usuario</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><time datetime="2026-10-05">05/10/2026</time></td>
            <td>Arroz</td>
            <td>Entrada</td>
            <td>+5 <abbr title="kilogramos">kg</abbr></td>
            <td>Ana</td>
          </tr>
          <tr>
            <td><time datetime="2026-10-05">05/10/2026</time></td>
            <td>Aceite</td>
            <td>Salida</td>
            <td>−2 <abbr title="litros">L</abbr></td>
            <td>Luis</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>