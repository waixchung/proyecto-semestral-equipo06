<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Inicio · Inventario';
$rol_usuario = 'Acceso público'; 
$usuario_autenticado = false; 
$pagina_actual = 'pantalla-inicio'; 

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido" class="eq06-principal">
  <section class="eq06-heroe">
    <div>
      <h1>Tu inventario, bajo control</h1>
      <p>Consulta existencias, recibe mercancía y registra el consumo de tu negocio desde <em>un solo lugar</em>.</p>
      <!-- Se mantiene el nombre original del archivo, cambiando solo la extensión a .php -->
      <a class="eq06-boton" href="inicio-sesion.php">Iniciar sesión</a>
    </div>
    <aside class="eq06-ilustracion">
      <figure class="eq06-figura">
        <img src="images/bodega.svg" alt="Ilustración de una bodega con estantes de productos" width="360" height="220">
        <figcaption>Productos · Entradas · Salidas</figcaption>
      </figure>
    </aside>
  </section>
  <section>
    <h2 class="eq06-oculto">Beneficios del sistema</h2>
    <div class="eq06-tarjetas">
      <article class="eq06-tarjeta"><h3>Existencias claras</h3><p>Consulta lo disponible.</p></article>
      <article class="eq06-tarjeta"><h3>Recepciones simples</h3><p>Registra lo recibido.</p></article>
      <article class="eq06-tarjeta"><h3>Alertas oportunas</h3><p>Identifica <strong>stock bajo</strong>.</p></article>
    </div>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>