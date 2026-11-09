<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = '404 Not Found · Inventario'; 
$pagina_actual = 'pagina-no-encontrada'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

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