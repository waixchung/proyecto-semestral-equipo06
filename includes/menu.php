<?php
// includes/menu.php
defined('APP_ACCESO') or exit('Acceso denegado');

$rol_actual    = isset($rol_usuario) ? strtolower(trim((string)$rol_usuario)) : 'invitado';
$pagina_activa = isset($pagina_actual) ? $pagina_actual : '';
?>
<!-- ABRIMOS EL CONTENEDOR PRINCIPAL DE LAYOUT AQUÍ -->
<div class="eq06-layout">

  <aside class="eq06-sidebar">
    <nav aria-label="Menú principal">
      <ul>
        <?php if ($rol_actual === 'administrador'): ?>
          <li><a href="dashboard-administrador.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
          <li><a href="listado-inventario.php" class="<?php echo in_array($pagina_activa, ['inventario', 'listado-inventario'], true) ? 'eq06-activo' : ''; ?>">Inventario</a></li>
          <li><a href="gestion-productos.php" class="<?php echo ($pagina_activa === 'productos') ? 'eq06-activo' : ''; ?>">Productos</a></li>
          <li><a href="gestion-proveedores.php" class="<?php echo in_array($pagina_activa, ['proveedores', 'gestion-proveedores'], true) ? 'eq06-activo' : ''; ?>">Proveedores</a></li>
          <li><a href="recepcion-mercancia.php" class="<?php echo in_array($pagina_activa, ['recepcion', 'recepcion-mercancia'], true) ? 'eq06-activo' : ''; ?>">Recepción</a></li>
          <li><a href="registro-salida.php" class="<?php echo ($pagina_activa === 'salidas') ? 'eq06-activo' : ''; ?>">Salidas</a></li>
          <li><a href="historial-movimientos.php" class="<?php echo in_array($pagina_activa, ['movimientos', 'historial-movimientos'], true) ? 'eq06-activo' : ''; ?>">Movimientos</a></li>
          <li><a href="gestion-productos.php?stock_bajo=1" class="<?php echo ($pagina_activa === 'reportes') ? 'eq06-activo' : ''; ?>">Stock bajo por ubicación</a></li>

        <?php elseif ($rol_actual === 'encargado'): ?>
          <li><a href="dashboard-encargado.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
          <li><a href="listado-inventario.php" class="<?php echo in_array($pagina_activa, ['inventario', 'listado-inventario'], true) ? 'eq06-activo' : ''; ?>">Inventario</a></li>
          <li><a href="gestion-productos.php" class="<?php echo ($pagina_activa === 'productos') ? 'eq06-activo' : ''; ?>">Productos</a></li>
          <li><a href="gestion-proveedores.php" class="<?php echo in_array($pagina_activa, ['proveedores', 'gestion-proveedores'], true) ? 'eq06-activo' : ''; ?>">Proveedores</a></li>
          <li><a href="registro-salida.php" class="<?php echo ($pagina_activa === 'salidas') ? 'eq06-activo' : ''; ?>">Salidas</a></li>
          <li><a href="historial-movimientos.php" class="<?php echo in_array($pagina_activa, ['movimientos', 'historial-movimientos'], true) ? 'eq06-activo' : ''; ?>">Movimientos</a></li>
          <li><a href="gestion-productos.php?stock_bajo=1" class="<?php echo ($pagina_activa === 'reportes') ? 'eq06-activo' : ''; ?>">Stock bajo por ubicación</a></li>

        <?php elseif ($rol_actual === 'recepcionista'): ?>
          <li><a href="dashboard-recepcionista.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
          <li><a href="recepcion-mercancia.php" class="<?php echo in_array($pagina_activa, ['recepcion', 'recepcion-mercancia'], true) ? 'eq06-activo' : ''; ?>">Recepción</a></li>
          <li><a href="listado-inventario.php" class="<?php echo in_array($pagina_activa, ['inventario', 'listado-inventario'], true) ? 'eq06-activo' : ''; ?>">Consulta de productos</a></li>
          <li><a href="gestion-proveedores.php" class="<?php echo in_array($pagina_activa, ['proveedores', 'gestion-proveedores'], true) ? 'eq06-activo' : ''; ?>">Proveedores</a></li>
          <li><a href="historial-movimientos.php" class="<?php echo in_array($pagina_activa, ['movimientos', 'historial-movimientos'], true) ? 'eq06-activo' : ''; ?>">Movimientos</a></li>

        <?php else: // Menú para visitantes / público ?>
          <li><a href="index.php" class="<?php echo ($pagina_activa === 'inicio') ? 'eq06-activo' : ''; ?>">Inicio</a></li>
          <li><a href="inicio-sesion.php" class="<?php echo ($pagina_activa === 'login') ? 'eq06-activo' : ''; ?>">Iniciar sesión</a></li>
        <?php endif; ?>
      </ul>
    </nav>
    <p class="eq06-nota" style="padding: 16px 20px;">Negocio de comida</p>
  </aside>
  <!-- NO SE CIERRA EL DIV layout AQUÍ, EL CONTENIDO <main> DE LAS PÁGINAS VA A CONTINUACIÓN -->