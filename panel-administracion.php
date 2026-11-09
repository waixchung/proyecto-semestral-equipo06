<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Panel de administración · Inventario'; 
$pagina_actual = 'panel-administracion'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido">
  <h1>Administración del sistema</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-modulos">
    <h2 id="eq06-titulo-modulos">Usuarios y catálogos</h2>

    <nav class="eq06-tarjetas" aria-label="Accesos de administración">
      <article class="eq06-tarjeta">
        <h3>Usuarios y roles</h3>
        <p class="eq06-texto-tarjeta">Cuentas, permisos y activación</p>
        <a href="usuarios-roles.php">Abrir → C04</a>
      </article>

      <article class="eq06-tarjeta">
        <h3>Categorías</h3>
        <p class="eq06-texto-tarjeta">Agrupación de productos</p>
        <a href="categorias-unidades.php">Abrir → C05</a>
      </article>

      <article class="eq06-tarjeta">
        <h3>Unidades de medida</h3>
        <p class="eq06-texto-tarjeta"><abbr title="kilogramos">kg</abbr>, <abbr title="litros">L</abbr>, unidades y otras</p>
        <a href="categorias-unidades.php">Abrir → C05</a>
      </article>

      <article class="eq06-tarjeta">
        <h3>Productos</h3>
        <p class="eq06-texto-tarjeta">Catálogo y stock mínimo</p>
        <a href="gestion-productos.php">Abrir → P07</a>
      </article>

      <article class="eq06-tarjeta">
        <h3>Proveedores</h3>
        <p class="eq06-texto-tarjeta">Datos de contacto y estado</p>
        <a href="gestion-proveedores.php">Abrir → P09</a>
      </article>
    </nav>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>