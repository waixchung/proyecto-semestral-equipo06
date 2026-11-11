<?php
session_start();

// Validar que el usuario tenga una sesión activa
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

// Requerimos los archivos de datos y funciones para las reglas de negocio
require_once 'config/datos_demo.php'; 
require_once 'includes/funciones.php';

$titulo_pagina = 'Listado de Inventario · Inventario'; 
$pagina_actual = 'listado-inventario'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Inventario</h1>
  </div>
  
  <div class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Existencias vigentes y alertas de vencimiento</caption>
        <thead>
          <tr>
            <th scope="col">Producto / código</th>
            <th scope="col">Categoría</th>
            <th scope="col">Stock</th>
            <th scope="col">Unidad</th>
            <th scope="col">Precio Unit.</th>
            <th scope="col">Estado</th>
            <th scope="col">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          // Si por alguna razón no existe el arreglo, evitamos errores
          $productos = isset($productos_demo) ? $productos_demo : [];
          
          foreach ($productos as $prod): 
              
              // 1. Evaluar estado del Stock
              $es_stock_bajo = ($prod['stock'] < $prod['minimo']);
              $badge_stock = $es_stock_bajo 
                  ? '<span class="eq06-badge eq06-badge-bajo">Stock bajo</span>' 
                  : '<span class="eq06-badge eq06-badge-normal">Normal</span>';

              // 2. Evaluar estado de Vencimiento usando la función
              $estado_vence = determinarEstadoVencimiento($prod['vence']);
              $badge_vence = '';
              if ($estado_vence === 'Vencido') {
                  $badge_vence = '<span class="eq06-badge eq06-badge-bajo" style="margin-left: 4px;">Vencido</span>';
              } elseif ($estado_vence === 'Por vencer') {
                  // Usamos un color naranja/amarillo para la alerta de "Por vencer"
                  $badge_vence = '<span class="eq06-badge" style="background-color: #F08C00; color: white; margin-left: 4px;">Por vencer</span>';
              }

              // 3. Controlar campos faltantes (el arreglo del profesor no tiene unidad ni precio)
              $unidad = isset($prod['unidad']) ? $prod['unidad'] : '-';
              $precio = isset($prod['precio']) ? '$' . number_format($prod['precio'], 2) : '-';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($prod['nombre']); ?> / <abbr title="Código de producto"><?php echo htmlspecialchars($prod['codigo']); ?></abbr></td>
            <td><?php echo htmlspecialchars($prod['categoria']); ?></td>
            <td><?php echo htmlspecialchars($prod['stock']); ?></td>
            <td><?php echo htmlspecialchars($unidad); ?></td>
            <td><?php echo htmlspecialchars($precio); ?></td>
            <td>
                <?php echo $badge_stock; ?>
                <?php echo $badge_vence; ?>
            </td>
            <td><a href="detalle-producto.php?id=<?php echo urlencode($prod['codigo']); ?>">Ver</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<?php require_once 'includes/pie.php'; ?>