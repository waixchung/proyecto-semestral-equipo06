<?php
defined('APP_ACCESO') or exit('Acceso denegado');

// Rol predeterminado para simulación si no está definido
$rol_actual = isset($rol_usuario) ? $rol_usuario : 'Invitado';
$pagina_activa = isset($pagina_actual) ? $pagina_actual : '';
?>
<aside class="eq06-sidebar">
  <nav aria-label="Menú principal">
    <ul>
      <?php if ($rol_actual === 'Administrador'): ?>
        <li><a href="panel.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
        <li><a href="principal_listado.php" class="<?php echo ($pagina_activa === 'inventario') ? 'eq06-activo' : ''; ?>">Inventario</a></li>
        <li><a href="principal_formulario.php" class="<?php echo ($pagina_activa === 'productos') ? 'eq06-activo' : ''; ?>">Productos</a></li>
        <li><a href="secundaria_formulario.php" class="<?php echo ($pagina_activa === 'proveedores') ? 'eq06-activo' : ''; ?>">Proveedores</a></li>
        <li><a href="historial_movimientos.php" class="<?php echo ($pagina_activa === 'movimientos') ? 'eq06-activo' : ''; ?>">Movimientos</a></li>
        <li><a href="reportes.php" class="<?php echo ($pagina_activa === 'reportes') ? 'eq06-activo' : ''; ?>">Reportes y alertas</a></li>
        <li><a href="usuarios.php" class="<?php echo ($pagina_activa === 'administracion') ? 'eq06-activo' : ''; ?>">Administración</a></li>

      <?php elseif ($rol_actual === 'Encargado de inventario' || $rol_actual === 'Encargado'): ?>
        <li><a href="panel.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
        <li><a href="principal_listado.php" class="<?php echo ($pagina_activa === 'inventario') ? 'eq06-activo' : ''; ?>">Inventario</a></li>
        <li><a href="recepcion.php" class="<?php echo ($pagina_activa === 'recepcion') ? 'eq06-activo' : ''; ?>">Recepción</a></li>
        <li><a href="salida.php" class="<?php echo ($pagina_activa === 'salidas') ? 'eq06-activo' : ''; ?>">Salidas</a></li>
        <li><a href="historial_movimientos.php" class="<?php echo ($pagina_activa === 'movimientos') ? 'eq06-activo' : ''; ?>">Movimientos</a></li>
        <li><a href="reportes.php" class="<?php echo ($pagina_activa === 'reportes') ? 'eq06-activo' : ''; ?>">Reportes y alertas</a></li>

      <?php elseif ($rol_actual === 'Recepcionista de mercancía' || $rol_actual === 'Recepcionista'): ?>
        <li><a href="panel.php" class="<?php echo ($pagina_activa === 'dashboard') ? 'eq06-activo' : ''; ?>">Dashboard</a></li>
        <li><a href="recepcion.php" class="<?php echo ($pagina_activa === 'recepcion') ? 'eq06-activo' : ''; ?>">Recepción</a></li>
        <li><a href="principal_listado.php" class="<?php echo ($pagina_activa === 'inventario') ? 'eq06-activo' : ''; ?>">Consulta de productos</a></li>

      <?php else: // Menú para visitantes / público ?>
        <li><a href="index.php" class="<?php echo ($pagina_activa === 'inicio') ? 'eq06-activo' : ''; ?>">Inicio</a></li>
        <li><a href="login.php" class="<?php echo ($pagina_activa === 'login') ? 'eq06-activo' : ''; ?>">Iniciar sesión</a></li>
        <li><a href="recuperar.php" class="<?php echo ($pagina_activa === 'recuperar') ? 'eq06-activo' : ''; ?>">Recuperar clave</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <p class="eq06-nota" style="padding: 16px 20px;">Negocio de comida</p>
</aside>