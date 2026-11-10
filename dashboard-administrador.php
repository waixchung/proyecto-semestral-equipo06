
<?php
session_start();

// 1. Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

// 2. Obtener y normalizar el rol del usuario
$rol_actual = strtolower(trim((string) $_SESSION['rol_usuario']));

// 3. Permitir acceso únicamente al administrador
if ($rol_actual !== 'Administrador') {
    http_response_code(403);

    exit(
        '<h2>403 - Acceso denegado</h2>' .
        '<p>No tienes permisos para acceder al dashboard de administrador.</p>' .
        '<a href="index.php">Volver al inicio</a>'
    );
}

// 4. Continuar con la carga del sistema
define('APP_ACCESO', true);
require_once 'config/datos_demo.php';
require_once 'includes/funciones.php';


$titulo_pagina = 'Dashboard Administrador · Inventario';
$pagina_actual = 'dashboard';

$rol_usuario = $_SESSION['rol_usuario'];
$nombre_usuario = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;

// --- LÓGICA DEL DASHBOARD (Procesamiento de alertas) ---
$productos = isset($productos_demo) ? $productos_demo : [];
$alertas_stock = [];
$alertas_vencimiento = [];

foreach ($productos as $prod) {
    // 1. Evaluar si tiene stock bajo
    if (isset($prod['stock']) && isset($prod['minimo']) && $prod['stock'] < $prod['minimo']) {
        $alertas_stock[] = $prod;
    }

    // 2. Evaluar si está próximo a vencer o ya venció
    $estado_vence = determinarEstadoVencimiento(isset($prod['vence']) ? $prod['vence'] : '');
    if ($estado_vence === 'Por vencer' || $estado_vence === 'Vencido') {
        $prod['estado_vencimiento'] = $estado_vence; // Guardamos el estado para imprimirlo luego
        $alertas_vencimiento[] = $prod;
    }
}
// -------------------------------------------------------

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Resumen del inventario</h1>
  </div>

  <!-- SECCIÓN STOCK BAJO -->
  <section class="eq06-seccion">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h2 style="margin: 0; font-size: 1.25rem;">Alertas: stock actual &lt; stock mínimo</h2>
        <a href="gestion-productos.php" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Gestionar productos</a>
    </div>
    
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con stock bajo</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Stock actual</th>
            <th scope="col" style="padding: 10px;">Mínimo</th>
            <th scope="col" style="padding: 10px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alertas_stock)): ?>
            <tr>
              <td colspan="4" style="padding: 15px; text-align: center; color: #666;">El inventario está en niveles óptimos. No hay productos con stock bajo.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($alertas_stock as $item): 
                $unidad = isset($item['unidad']) ? $item['unidad'] : '';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['nombre']); ?></td>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['stock']) . ' ' . htmlspecialchars($unidad); ?></td>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['minimo']) . ' ' . htmlspecialchars($unidad); ?></td>
              <td style="padding: 10px;">
                <a href="detalle-producto.php?id=<?php echo urlencode($item['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECCIÓN VENCIMIENTOS -->
  <section class="eq06-seccion" style="margin-top: 30px;">
    <h2 style="margin-bottom: 15px; font-size: 1.25rem;">Alertas: Productos próximos a vencer</h2>
    
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con fecha de caducidad cercana en stock</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Código</th>
            <th scope="col" style="padding: 10px;">Fecha de Vencimiento</th>
            <th scope="col" style="padding: 10px;">Estado</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($alertas_vencimiento)): ?>
            <tr>
              <td colspan="4" style="padding: 15px; text-align: center; color: #666;">No hay productos próximos a vencer.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($alertas_vencimiento as $item): 
                // Color dinámico según sea Vencido o Por vencer
                $badge_estilo = ($item['estado_vencimiento'] === 'Vencido') 
                    ? 'background-color: #A61E4D; color: white;' 
                    : 'background-color: #F08C00; color: white;'; 
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['nombre']); ?></td>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['codigo']); ?></td>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['vence']); ?></td>
              <td style="padding: 10px;">
                <span class="eq06-badge" style="<?php echo $badge_estilo; ?> padding: 4px 8px; border-radius: 4px; font-size: 0.75rem;">
                    <?php echo htmlspecialchars($item['estado_vencimiento']); ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>