<?php

define('APP_ACCESO', true);

// Configuración de variables contextuales
$titulo_pagina = 'Página no encontrada';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Encargado de inventario'; 
$usuario_autenticado = true; 
$pagina_actual = ''; // No hay pestaña activa en el menú para esta página

// Carga de componentes modulares
require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal" class="eq06-recurso">
  <section class="eq06-tarjeta-recurso" aria-labelledby="eq06-titulo-recurso">
    <h1 id="eq06-titulo-recurso">Página no encontrada</h1>
    <p class="eq06-codigo-recurso"><strong>Error 404</strong></p>
    <p>No encontramos esta página. Puede que el enlace sea incorrecto o que la página ya no esté disponible.</p>
    <nav class="eq06-acciones eq06-retorno-recurso" aria-label="Opciones de retorno">
      <a class="eq06-boton" href="panel.php">Volver al dashboard</a>
    </nav>
  </section>
  <aside class="eq06-ayuda-recurso" aria-labelledby="eq06-titulo-retorno">
    <h2 class="eq06-oculto" id="eq06-titulo-retorno">Retorno sin sesión</h2>
    <article>
      <h3 class="eq06-oculto">Acceso al inicio del sistema</h3>
      <p>Si no has iniciado sesión, <a href="login.php">volver al inicio</a>.</p>
    </article>
  </aside>
  <!-- Vista estática para Encargado de inventario.
       Al implementar la lógica se ajustarán menú y destino al rol.
       Sin sesión, se mostrará navegación pública y retorno al Inicio (P01).
       La autorización debe comprobarse también para URL directa.
       Esta página no impone permisos ni devuelve un estado HTTP real. -->
</main>

<?php
require_once 'includes/pie.php';
?>