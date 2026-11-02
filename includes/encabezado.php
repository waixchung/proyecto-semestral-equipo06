<?php
defined('APP_ACCESO') or exit('Acceso denegado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($titulo_pagina) ? htmlspecialchars($titulo_pagina, ENT_QUOTES, 'UTF-8') : 'Inventario'; ?> · Inventario</title>
  <meta name="description" content="Sistema de gestión de inventario para pequeños negocios de comida.">
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <header class="eq06-header">
    <span class="eq06-logo" style="display: flex; align-items: center; gap: 8px;">
      <img src="img/logo.svg" alt="Logotipo de Inventario" width="32" height="32"> Inventario
    </span>
    <div class="eq06-header-right">
      <span class="eq06-rol"><?php echo isset($rol_usuario) ? htmlspecialchars($rol_usuario, ENT_QUOTES, 'UTF-8') : 'Acceso público'; ?></span>
      <div class="eq06-header-actions">
        <?php if (isset($usuario_autenticado) && $usuario_autenticado): ?>
          <a href="perfil.php" class="eq06-btn eq06-btn-secundario">Perfil</a>
          <a href="procesar_login.php?accion=salir" class="eq06-btn eq06-btn-secundario">Salir</a>
        <?php else: ?>
          <a href="login.php" class="eq06-btn">Iniciar sesión</a>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <div class="eq06-layout">