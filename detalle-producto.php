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
        <a href="formulario-producto.php" class="eq06-btn">Editar</a>
      </div>

      <section class="eq06-seccion" aria-labelledby="datos-producto">
        <h2 id="datos-producto" class="eq06-sr-only">Datos del producto</h2>
        <dl class="eq06-detalle-grid">
          <div class="eq06-detalle-item">
            <dt>Categoría</dt>
            <dd>Granos</dd>
          </div>
          <div class="eq06-detalle-item">
            <dt>Unidad</dt>
            <dd>kg</dd>
          </div>
          <div class="eq06-detalle-item">
            <dt>Proveedor principal</dt>
            <dd>Distribuidora Central</dd>
          </div>
          
          <!-- NUEVOS CAMPOS AÑADIDOS CON LAS CLASES ORIGINALES -->
          <div class="eq06-detalle-item">
            <dt>Precio unitario</dt>
            <dd>$1.20</dd>
          </div>
          <div class="eq06-detalle-item">
            <dt>Fecha de caducidad</dt>
            <dd>20/12/2026</dd>
          </div>
          <!-- FIN DE NUEVOS CAMPOS -->

          <div class="eq06-detalle-item">
            <dt>Stock actual</dt>
            <dd>5 kg · <span class="eq06-badge eq06-badge-bajo">Stock bajo</span></dd>
          </div>
          <div class="eq06-detalle-item">
            <dt>Stock mínimo</dt>
            <dd>8 kg</dd>
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
                <td><time datetime="2026-10-05T10:30">05/10/2026 10:30</time></td>
                <td>Entrada</td>
                <td>+5 kg</td>
                <td>Ana</td>
                <td>REC-0021</td>
              </tr>
              <tr>
                <td><time datetime="2026-10-04T16:10">04/10/2026 16:10</time></td>
                <td>Salida</td>
                <td>−3 kg</td>
                <td>Luis</td>
                <td>SAL-0034</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p class="eq06-mt-16">
          <a href="gestion-productos.php" class="eq06-btn eq06-btn-secundario">Volver</a>
        </p>
        <p class="eq06-nota eq06-mt-8">Recepcionista: mismos datos básicos, sin botón Editar.</p>
      </section>
    </main>

<?php require_once 'includes/pie.php'; ?>