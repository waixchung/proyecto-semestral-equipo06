<?php
defined('APP_ACCESO') or exit('Acceso denegado');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($titulo_pagina ?? 'Inventario'); ?></title>
  <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
  <header class="eq06-header">
    <a href="dashboard-administrador.php" class="eq06-logo">
      <!-- Se corrige la ruta estática al directorio "img" -->
      <img src="img/logo.svg" alt="Logotipo de Inventario" width="40" height="40"> 
      Inventario
    </a>
    <div class="eq06-header-right">
      <span class="eq06-rol"><?php echo htmlspecialchars($rol_usuario ?? 'Usuario'); ?></span>
      <div class="eq06-header-actions">
        <a href="perfil-usuario.php" class="eq06-btn eq06-btn-secundario">Perfil</a>
        <a href="inicio-sesion.php" class="eq06-btn eq06-btn-secundario">Salir</a>
      </div>
    </div>
  </header>