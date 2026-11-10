<?php
session_start();

if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);
require_once 'config/datos_demo.php';
require_once 'includes/funciones.php';

$titulo_pagina = 'Dashboard Encargado · Inventario';
$pagina_actual = 'dashboard';

$rol_usuario = $_SESSION['rol_usuario'];
$nombre_usuario = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;

// --- LÓGICA DEL DASHBOARD (Procesamiento de alertas) ---
$productos = isset($productos_demo) ? $productos_demo : [];
$alertas_stock = [];
$alertas_vencimiento = [];
$total_activos = count($productos);

foreach ($productos as $prod) {
    if (isset($prod['stock']) && isset($prod['minimo']) && $prod['stock'] < $prod['minimo']) {
        $alertas_stock[] = $prod;
    }

    $estado_vence = determinarEstadoVencimiento(isset($prod['vence']) ? $prod['vence'] : '');
    if ($estado_vence === 'Por vencer' || $estado_vence === 'Vencido') {
        $prod['estado_vencimiento'] = $estado_vence;
        $alertas_vencimiento[] = $prod;
    }
}
$cantidad_stock_bajo = count($alertas_stock);
// -------------------------------------------------------

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-principal">
  <h1>Resumen del inventario</h1>

  <!-- INDICADORES (Datos estáticos de entradas/salidas temporalmente) -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-indicadores">
    <h2 class="eq06-oculto" id="eq06-titulo-indicadores">Indicadores del día</h2>
    <div class="eq06-indicadores" style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf("%02d", $total_activos); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Productos activos</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong><?php echo sprintf("%02d", $cantidad_stock_bajo); ?></strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Stock bajo</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong>05</strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Entradas del día</h3>
      </article>
      <article class="eq06-indicador" style="flex: 1; min-width: 150px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; text-align: center;">
        <p class="eq06-indicador-valor" style="font-size: 2rem; margin: 0; color: #A61E4D;"><strong>02</strong></p>
        <h3 class="eq06-indicador-titulo" style="margin: 5px 0 0; font-size: 1rem;">Salidas del día</h3>
      </article>
    </div>
  </section>

  <!-- ACCIONES RÁPIDAS -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-acciones" style="margin-bottom: 30px;">
    <h2 class="eq06-oculto" id="eq06-titulo-acciones">Acciones rápidas</h2>
    <div class="eq06-acciones" style="display: flex; gap: 10px;">
      <a class="eq06-btn" href="recepcion-mercancia.php" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Registrar recepción</a>
      <a class="eq06-btn eq06-btn-secundario" href="registro-salida.php" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Registrar salida</a>
    </div>
  </section>

  <!-- SECCIÓN STOCK BAJO (Dinámica) -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alertas">
    <h2 id="eq06-titulo-alertas" style="margin-bottom: 15px; font-size: 1.25rem;">Alertas: stock actual &lt; stock mínimo</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos cuyo stock actual está por debajo del mínimo</caption>
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
              <th scope="row" style="padding: 10px; font-weight: normal; text-align: left;"><?php echo htmlspecialchars($item['nombre']); ?></th>
              <td style="padding: 10px;">
                  <?php echo htmlspecialchars($item['stock']); ?> <abbr title="unidad"><?php echo htmlspecialchars($unidad); ?></abbr> 
                  <span class="eq06-badge eq06-badge-bajo" style="background-color: #A61E4D; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px;">Stock bajo</span>
              </td>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['minimo']); ?> <abbr title="unidad"><?php echo htmlspecialchars($unidad); ?></abbr></td>
              <td style="padding: 10px;">
                <a href="detalle-producto.php?id=<?php echo urlencode($item['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;" aria-label="Ver detalle del producto <?php echo htmlspecialchars($item['nombre']); ?>">Ver detalle</a>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- SECCIÓN VENCIMIENTOS (Añadida para cumplir Hito #2) -->
  <section class="eq06-seccion" style="margin-top: 30px;">
    <h2 style="margin-bottom: 15px; font-size: 1.25rem;">Alertas: Productos próximos a vencer</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Productos con fecha de caducidad cercana o vencidos</caption>
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

  <!-- MOVIMIENTOS RECIENTES (Estático temporalmente) -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimientos" style="margin-top: 30px;">
    <h2 id="eq06-titulo-movimientos" style="margin-bottom: 15px; font-size: 1.25rem;">Movimientos recientes</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Últimas entradas y salidas registradas</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Fecha</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Tipo</th>
            <th scope="col" style="padding: 10px;">Cantidad</th>
            <th scope="col" style="padding: 10px;">Usuario</th>
          </tr>
        </thead>
        <tbody>
          <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><time datetime="2026-10-05">05/10/2026</time></td>
            <td style="padding: 10px;">Arroz</td>
            <td style="padding: 10px;">Entrada</td>
            <td style="padding: 10px;">+5 <abbr title="kilogramos">kg</abbr></td>
            <td style="padding: 10px;">Ana</td>
          </tr>
          <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><time datetime="2026-10-05">05/10/2026</time></td>
            <td style="padding: 10px;">Aceite</td>
            <td style="padding: 10px;">Salida</td>
            <td style="padding: 10px;">−2 <abbr title="litros">L</abbr></td>
            <td style="padding: 10px;">Luis</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>