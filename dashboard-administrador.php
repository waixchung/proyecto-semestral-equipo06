<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Dashboard · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'dashboard'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Resumen del inventario</h1>
  </div>

  <section class="eq06-seccion" aria-labelledby="indicadores-titulo">
    <h2 id="indicadores-titulo" class="eq06-sr-only">Indicadores</h2>
    <div class="eq06-indicadores">

      <article class="eq06-card">
          <span class="eq06-card-numero">24</span>
          <h3 class="eq06-card-etiqueta">Productos activos</h3>
      </article>

      <article class="eq06-card">
          <span class="eq06-card-numero">03</span>
          <h3 class="eq06-card-etiqueta">Stock bajo</h3>
      </article>

      <article class="eq06-card">
          <span class="eq06-card-numero">05</span>
          <h3 class="eq06-card-etiqueta">Entradas del día</h3>
      </article>

      <article class="eq06-card">
          <span class="eq06-card-numero">02</span>
          <h3 class="eq06-card-etiqueta">Salidas del día</h3>
      </article>

    </div>
  </section>

  <section class="eq06-seccion">
    <div class="eq06-titulo-pagina">
      <h2>Alertas: stock actual &lt; stock mínimo</h2>
      <!-- Enlace actualizado a la nomenclatura PHP estandarizada del Hito 2 -->
      <a href="gestion-productos.php" class="eq06-btn">Gestionar productos</a>
    </div>

    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Productos con stock bajo</caption>
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
            <td>Arroz</td>
            <td>5 kg</td>
            <td>8 kg</td>
            <!-- Enlaces actualizados a la nomenclatura PHP estandarizada del Hito 2 -->
            <td><a href="detalle-producto.php">Ver detalle</a></td>
          </tr>
          <tr>
            <td>Tomate</td>
            <td>0 kg</td>
            <td>3 kg</td>
            <td><a href="detalle-producto.php">Ver detalle</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <section class="eq06-seccion">
    <h2>Movimientos recientes</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Últimos movimientos registrados</caption>
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
            <td>+5 kg</td>
            <td>Ana</td>
          </tr>
          <tr>
            <td><time datetime="2026-10-05">05/10/2026</time></td>
            <td>Aceite</td>
            <td>Salida</td>
            <td>−2 L</td>
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