<?php
session_start();

// Proteger la vista: si no hay sesión, al login
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

// Requerir los datos semilla
require_once 'config/datos_demo.php';

$titulo_pagina = 'Catálogo de productos · Inventario';
$pagina_actual = 'productos'; 

$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Catálogo de productos</h1>
    <a href="formulario-producto.php" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Nuevo</a>
  </div>

  <!-- Formulario de Búsqueda y Filtros -->
  <section class="eq06-seccion" style="margin-bottom: 20px;">
    <form method="get" action="gestion-productos.php" class="eq06-form" style="border: 1px solid #ddd; padding: 15px; border-radius: 4px;">
      <div style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <div style="flex: 2; min-width: 200px;">
          <label for="buscar" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Buscar</label>
          <input type="text" id="buscar" name="buscar" class="eq06-campo" placeholder="Nombre o código" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
        </div>

        <div style="flex: 1; min-width: 150px;">
          <label for="categoria" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Categoría</label>
          <select id="categoria" name="categoria" class="eq06-campo" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">Todas</option>
            <option value="Granos">Granos</option>
            <option value="Aceites">Aceites</option>
            <option value="Salsas">Salsas</option>
            <option value="Lácteos">Lácteos</option>
          </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
          <label for="estado" style="display: block; font-weight: bold; margin-bottom: 5px; font-size: 0.875rem;">Estado</label>
          <select id="estado" name="estado" class="eq06-campo" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">Todos</option>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
          </select>
        </div>

        <div style="display: flex; gap: 10px;">
          <button type="submit" class="eq06-btn" style="background-color: #A61E4D; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer;">Aplicar</button>
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario" style="background-color: white; color: #333; border: 1px solid #ccc; padding: 8px 16px; text-decoration: none; border-radius: 4px;">Limpiar</a>
        </div>
      </div>
    </form>
  </section>

  <!-- Enlaces de ayuda -->
  <div style="border: 1px solid #ddd; padding: 10px 15px; margin-bottom: 20px; background-color: #fafafa; border-radius: 4px; font-size: 0.875rem;">
    <a href="#" style="color: #A61E4D; text-decoration: underline;">Categorías → C05</a> · 
    <a href="#" style="color: #A61E4D; text-decoration: underline;">Unidades → C05</a>
  </div>

  <section class="eq06-seccion">
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla" style="width: 100%; border-collapse: collapse;">
        <caption style="text-align: left; font-weight: bold; margin-bottom: 10px;">Listado de productos del catálogo</caption>
        <thead>
          <tr style="border-bottom: 2px solid #ddd; text-align: left;">
            <th scope="col" style="padding: 10px;">Código</th>
            <th scope="col" style="padding: 10px;">Producto</th>
            <th scope="col" style="padding: 10px;">Categoría</th>
            <th scope="col" style="padding: 10px;">Estado</th>
            <th scope="col" style="padding: 10px;">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $productos = isset($productos_demo) ? $productos_demo : [];
          
          if (empty($productos)): ?>
            <tr>
                <td colspan="5" style="padding: 15px; text-align: center; color: #666;">No hay productos registrados.</td>
            </tr>
          <?php else: 
            foreach ($productos as $prod): 
                // Simulamos el estado si no existe en el arreglo semilla (para cumplir con el diseño visual)
                $estado = isset($prod['estado']) ? $prod['estado'] : 'activo';
                $badge_estado = ($estado === 'activo') 
                    ? '<span class="eq06-badge" style="border: 1px solid #666; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">Activo</span>' 
                    : '<span class="eq06-badge" style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;">Inactivo</span>';
          ?>
          <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><abbr title="Código de producto" style="text-decoration: underline dotted;"><?php echo htmlspecialchars($prod['codigo']); ?></abbr></td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($prod['nombre']); ?></td>
            <td style="padding: 10px;"><?php echo htmlspecialchars($prod['categoria']); ?></td>
            <td style="padding: 10px;"><?php echo $badge_estado; ?></td>
            <td style="padding: 10px;">
              <a href="detalle-producto.php?id=<?php echo urlencode($prod['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;">Ver</a> / 
              <a href="formulario-producto.php?id=<?php echo urlencode($prod['codigo']); ?>" style="color: #A61E4D; text-decoration: underline;">Editar</a> 
              <?php if ($estado === 'activo'): ?>
                / <a href="#" style="color: #A61E4D; text-decoration: underline;">Desactivar</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php 
            endforeach; 
          endif; 
          ?>
        </tbody>
      </table>
    </div>
    
    <div style="border: 1px solid #ddd; padding: 10px 15px; margin-top: 20px; background-color: #f8f9fa; border-radius: 4px; font-size: 0.875rem; color: #495057;">
      Confirmación de desactivación: el producto dejará de estar disponible para nuevos movimientos.
    </div>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>