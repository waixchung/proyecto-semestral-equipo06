<?php
define('APP_ACCESO', true);

$titulo_pagina = 'Detalle de producto · Inventario';
// Nota: En el futuro esto vendrá de la sesión ($_SESSION['rol'])
$rol_usuario = 'Administrador'; 
$usuario_autenticado = true; 
$pagina_actual = 'productos'; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-main">
  <div class="eq06-titulo-pagina">
    <h1>Arroz · <abbr title="Código de producto">PR-001</abbr></h1>
    <div class="eq06-acciones-top">
      <a href="formulario-producto.php" class="eq06-btn">Editar</a>
    </div>
  </div>

  <section class="eq06-seccion">
    <div class="eq06-grid-datos">
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Categoría</span>
        <span class="eq06-dato-valor">Granos</span>
      </div>
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Unidad</span>
        <span class="eq06-dato-valor">kg</span>
      </div>
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Proveedor principal</span>
        <span class="eq06-dato-valor">Distribuidora Central</span>
      </div>

      <!-- NUEVO CAMPO: PRECIO -->
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Precio unitario</span>
        <span class="eq06-dato-valor">$1.50</span>
      </div>

      <!-- NUEVO CAMPO: FECHA DE CADUCIDAD -->
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Fecha de caducidad</span>
        <span class="eq06-dato-valor">20/12/2026</span>
      </div>
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Stock actual</span>
        <span class="eq06-dato-valor">5 kg · <span class="eq06-badge eq06-badge-bajo">Stock bajo</span></span>
      </div>
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Stock mínimo</span>
        <span class="eq06-dato-valor">8 kg</span>
      </div>
      
      <div class="eq06-dato-tarjeta">
        <span class="eq06-dato-label">Estado del producto</span>
        <span class="eq06-dato-valor"><span class="eq06-badge eq06-badge-normal">Activo</span></span>
      </div>

    </div>
  </section>

  <section class="eq06-seccion">
    <h2>Historial del producto · Más reciente primero</h2>
    <div class="eq06-tabla-wrap">
      <table class="eq06-tabla">
        <caption>Movimientos del producto Arroz</caption>
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
          <tr>
            <td>05/10/2026 10:30</td>
            <td>Entrada</td>
            <td>+5 kg</td>
            <td>Ana</td>
            <td>REC-0021</td>
          </tr>
          <tr>
            <td>04/10/2026 16:10</td>
            <td>Salida</td>
            <td>-3 kg</td>
            <td>Luis</td>
            <td>SAL-0034</td>
          </tr>
        </tbody>
      </table>
    </div>
    
    <div class="eq06-acciones-form" style="margin-top: 20px;">
      <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Volver</a>
    </div>
    
    <div class="eq06-alerta eq06-alerta-info" style="margin-top: 20px;">
      Recepcionista: mismos datos básicos, sin botón Editar.
    </div>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>