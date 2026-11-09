<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Dashboard · Inventario'; 
$pagina_actual = 'dashboard'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Resumen del inventario</h1>
  </div>

  <section class="eq06-seccion">
    <div class="eq06-titulo-pagina">
      <h2>Alertas: stock actual &lt; stock mínimo</h2>
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
            <td><a href="detalle-producto.php">Ver detalle</a></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>

  <!-- Nueva alerta de caducidad -->
  <section class="eq06-seccion">
    <div class="eq06-titulo-pagina">
      <h2>Alertas: Productos próximos a vencer</h2>
    </div>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Productos con fecha de caducidad cercana en stock</caption>
        <thead>
          <tr>
            <th scope="col">Producto</th>
            <th scope="col">Código</th>
            <th scope="col">Fecha de Vencimiento</th>
            <th scope="col">Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Leche evaporada</td>
            <td>PR-005</td>
            <td>2026-11-20</td>
            <td><span class="eq06-badge eq06-badge-bajo">Próximo a vencer</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>