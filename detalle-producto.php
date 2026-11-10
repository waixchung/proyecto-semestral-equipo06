<?php
session_start();

if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);
require_once 'config/datos_demo.php';
require_once 'includes/funciones.php';

// 1. Capturar el ID de la URL
$codigo_producto = isset($_GET['id']) ? $_GET['id'] : '';
$producto_actual = null;

// 2. Buscar el producto en nuestro arreglo (simulando un SELECT en BD)
if (isset($productos_demo)) {
    foreach ($productos_demo as $prod) {
        if ($prod['codigo'] === $codigo_producto) {
            $producto_actual = $prod;
            break;
        }
    }
}

// 3. Si alguien altera la URL y pone un ID que no existe, lo regresamos al listado
if (!$producto_actual) {
    header('Location: listado-inventario.php');
    exit;
}

$titulo_pagina = 'Detalle de producto · Inventario';
$pagina_actual = 'productos'; 

$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';

// 4. Calcular lógicas de negocio (Stock y Vencimiento)
$es_stock_bajo = ($producto_actual['stock'] < $producto_actual['minimo']);
$estado_vence = determinarEstadoVencimiento($producto_actual['vence']);

// Preparar campos que podrían no venir en los datos semilla del profesor
$unidad = isset($producto_actual['unidad']) ? $producto_actual['unidad'] : '-';
$precio = isset($producto_actual['precio']) ? '$' . number_format($producto_actual['precio'], 2) : '-';
$proveedor = isset($producto_actual['proveedor']) ? $producto_actual['proveedor'] : 'Sin asignar';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1><?php echo htmlspecialchars($producto_actual['nombre']); ?> · <abbr title="Código de producto"><?php echo htmlspecialchars($producto_actual['codigo']); ?></abbr></h1>
    <a href="formulario-producto.php?id=<?php echo urlencode($producto_actual['codigo']); ?>" class="eq06-btn">Editar</a>
  </div>

  <section class="eq06-seccion" aria-labelledby="datos-producto">
    <h2 id="datos-producto" class="eq06-sr-only">Datos del producto</h2>
    <dl class="eq06-detalle-grid">
      <div class="eq06-detalle-item">
        <dt>Categoría</dt>
        <dd><?php echo htmlspecialchars($producto_actual['categoria']); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Unidad</dt>
        <dd><?php echo htmlspecialchars($unidad); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Proveedor principal</dt>
        <dd><?php echo htmlspecialchars($proveedor); ?></dd>
      </div>
      
      <div class="eq06-detalle-item">
        <dt>Precio unitario</dt>
        <dd><?php echo htmlspecialchars($precio); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Fecha de caducidad</dt>
        <dd>
            <?php echo !empty($producto_actual['vence']) ? htmlspecialchars($producto_actual['vence']) : 'No aplica'; ?>
            <?php if ($estado_vence === 'Vencido'): ?>
                <span class="eq06-badge eq06-badge-bajo" style="margin-left: 4px;">Vencido</span>
            <?php elseif ($estado_vence === 'Por vencer'): ?>
                <span class="eq06-badge" style="background-color: #F08C00; color: white; margin-left: 4px;">Por vencer</span>
            <?php endif; ?>
        </dd>
      </div>

      <div class="eq06-detalle-item">
        <dt>Stock actual</dt>
        <dd>
            <?php echo htmlspecialchars($producto_actual['stock']); ?> <?php echo htmlspecialchars($unidad); ?>
            <?php if ($es_stock_bajo): ?>
                · <span class="eq06-badge eq06-badge-bajo">Stock bajo</span>
            <?php endif; ?>
        </dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Stock mínimo</dt>
        <dd><?php echo htmlspecialchars($producto_actual['minimo']); ?> <?php echo htmlspecialchars($unidad); ?></dd>
      </div>
      <div class="eq06-detalle-item">
        <dt>Estado del producto</dt>
        <dd><span class="eq06-badge eq06-badge-activo">Activo</span></dd>
      </div>
    </dl>
  </section>

  <section class="eq06-seccion">
    <h2>Historial del producto · Más reciente primero</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Movimientos de <?php echo htmlspecialchars($producto_actual['nombre']); ?></caption>
        <thead>
          <tr>
            <th scope="col">Fecha</th>
            <th scope="col">Tipo</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Usuario</th>
            <th scope="col">Referencia</th>
          </tr>
        </thead>
        <tbody>
          <!-- Como aún no tenemos base de datos para los movimientos, dejamos un mensaje vacío o datos de muestra -->
          <tr>
            <td colspan="5" style="text-align: center; color: #666;">No hay movimientos recientes registrados.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <p class="eq06-mt-16">
      <a href="listado-inventario.php" class="eq06-btn eq06-btn-secundario">Volver</a>
    </p>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>