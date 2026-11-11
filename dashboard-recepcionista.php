
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
if ($rol_actual !== 'recepcionista') {
    http_response_code(403);

    exit(
        '<h2>403 - Acceso denegado</h2>' .
        '<p>No tienes permisos para acceder al dashboard del recepcionista.</p>' .
        '<a href="index.php">Volver al inicio</a>'
    );
}

// 4. Continuar con la carga del sistema
define('APP_ACCESO', true);
require_once 'config/datos_demo.php';
require_once 'includes/funciones.php';

$titulo_pagina = 'Dashboard Recepcionista · Inventario';
$pagina_actual = 'dashboard';

$rol_usuario = $_SESSION['rol_usuario'];
$nombre_usuario = $_SESSION['usuario_nombre'];
$usuario_autenticado = true;

// --- LÓGICA DEL DASHBOARD ---
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

  <!-- INDICADORES (Datos estáticos temporalmente) -->
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
      <a class="eq06-btn eq06-btn-secundario" href="#eq06-consulta-productos" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Consultar productos</a>
    </div>
  </section>

  <!-- CONSULTA BÁSICA (Mostrando los productos dinámicamente) -->
  <section class="eq06-seccion" id="eq06-consulta-productos" aria-labelledby="eq06-titulo-consulta">
    <h2 id="eq06-titulo-consulta" style="margin-bottom: 15px; font-size: 1.25rem;">Consulta básica de productos</h2>
    
    <form method="post" action="#" style="margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
      <fieldset class="eq06-conjunto" style="border: none; padding: 0; margin: 0;">
        <legend style="font-weight: bold; margin-bottom: 10px;">Buscar producto</legend>
        <div class="eq06-grupo-campo" style="display: flex; gap: 10px; align-items: flex-end;">
          <div style="flex: 1;">
            <label class="eq06-etiqueta" for="eq06-busqueda-producto" style="display: block; margin-bottom: 5px; font-size: 0.875rem;">Nombre o código</label>
            <input class="eq06-campo" type="search" id="eq06-busqueda-producto"
                   name="eq06-busqueda-producto" minlength="2" maxlength="80"
                   autocomplete="off" placeholder="Ejemplo: Arroz o PR-001" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
          </div>
          <button class="eq06-btn" type="submit" style="background-color: #A61E4D; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Buscar</button>
        </div>
      </fieldset>
    </form>

    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px; font-size: 0.9rem;">Resultados de la consulta de productos (Catálogo actual)</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Código</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Unidad</th>
            <th scope="col" style="padding: 10px;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($productos)): ?>
            <tr>
              <td colspan="4" style="padding: 15px; text-align: center; color: #666;">No hay productos registrados en el inventario.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($productos as $item): 
                $unidad = isset($item['unidad']) ? $item['unidad'] : '-';
            ?>
            <tr style="border-bottom: 1px solid #eee;">
              <th scope="row" style="padding: 10px; font-weight: normal; text-align: left;"><?php echo htmlspecialchars($item['codigo']); ?></th>
              <td style="padding: 10px;"><?php echo htmlspecialchars($item['nombre']); ?></td>
              <td style="padding: 10px;"><abbr title="unidad"><?php echo htmlspecialchars($unidad); ?></abbr></td>
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

  <!-- ALERTAS RESUMIDAS (Dinámicas) -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-alerta" style="margin-top: 30px;">
    <h2 class="eq06-oculto" id="eq06-titulo-alerta">Alertas críticas de inventario</h2>
    <?php if (empty($alertas_stock) && empty($alertas_vencimiento)): ?>
      <p style="padding: 10px; border-left: 4px solid #28a745; background-color: #f8fff9;">No hay alertas críticas en el sistema en este momento.</p>
    <?php else: ?>
      <?php if (!empty($alertas_stock)): 
          $primer_stock = $alertas_stock[0];
          $unidad = isset($primer_stock['unidad']) ? $primer_stock['unidad'] : '';
      ?>
      <p class="eq06-conjunto" style="margin-bottom: 10px; padding: 10px; border-left: 4px solid #A61E4D; background-color: #fff8f9;">
        <strong>Alerta de stock bajo:</strong> <?php echo htmlspecialchars($primer_stock['nombre']); ?> · Stock <?php echo htmlspecialchars($primer_stock['stock']); ?> <abbr title="unidad"><?php echo htmlspecialchars($unidad); ?></abbr>
        / Mínimo <?php echo htmlspecialchars($primer_stock['minimo']); ?> <abbr title="unidad"><?php echo htmlspecialchars($unidad); ?></abbr> ·
        <span class="eq06-etiqueta-bajo" style="background-color: #A61E4D; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">Stock bajo</span> ·
        <a href="detalle-producto.php?id=<?php echo urlencode($primer_stock['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>.
        <?php if ($cantidad_stock_bajo > 1): ?> <em>(y <?php echo $cantidad_stock_bajo - 1; ?> más)</em> <?php endif; ?>
      </p>
      <?php endif; ?>
      
      <?php if (!empty($alertas_vencimiento)): 
          $primer_vence = $alertas_vencimiento[0];
          $color_vence = ($primer_vence['estado_vencimiento'] === 'Vencido') ? '#A61E4D' : '#F08C00';
      ?>
      <p class="eq06-conjunto" style="padding: 10px; border-left: 4px solid <?php echo $color_vence; ?>; background-color: #fffcf8;">
        <strong>Alerta de caducidad:</strong> <?php echo htmlspecialchars($primer_vence['nombre']); ?> · Fecha: <?php echo htmlspecialchars($primer_vence['vence']); ?> ·
        <span style="background-color: <?php echo $color_vence; ?>; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;"><?php echo htmlspecialchars($primer_vence['estado_vencimiento']); ?></span> ·
        <a href="detalle-producto.php?id=<?php echo urlencode($primer_vence['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;">Ver detalle</a>.
      </p>
      <?php endif; ?>
    <?php endif; ?>
  </section>

  <!-- MOVIMIENTO RECIENTE (Estático) -->
  <section class="eq06-seccion" aria-labelledby="eq06-titulo-movimiento" style="margin-top: 20px;">
    <h2 class="eq06-oculto" id="eq06-titulo-movimiento">Movimiento reciente</h2>
    <p style="padding: 10px; border: 1px solid #eee; border-radius: 4px; font-size: 0.9rem;">Movimiento reciente:
      <time datetime="2026-10-05">05/10/2026</time> · Entrada · Arroz
      +5 <abbr title="kilogramos">kg</abbr> · REC-0021 · Ana
    </p>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>