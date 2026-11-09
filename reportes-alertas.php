<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Reportes y alertas · Inventario'; 
$pagina_actual = 'reportes-alertas'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido">
  <h1>Reportes y alertas</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-periodo">
    <h2 id="eq06-titulo-periodo">Período y filtros</h2>
    <form action="#" method="post">
      <fieldset class="eq06-grupo">
        <legend>Filtros del reporte</legend>

        <div class="eq06-filtros">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-desde">Desde</label>
            <input type="date" id="eq06-desde" name="desde" class="eq06-campo" required value="2026-10-01" max="2026-12-31">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-hasta">Hasta</label>
            <input type="date" id="eq06-hasta" name="hasta" class="eq06-campo" required value="2026-10-05" min="2026-10-01" max="2026-12-31">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-categoria">Categoría</label>
            <select id="eq06-categoria" name="categoria" class="eq06-campo">
              <option value="" selected>Todas</option>
              <option value="granos">Granos</option>
              <option value="aceites">Aceites</option>
              <option value="vegetales">Vegetales</option>
            </select>
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-producto">Producto</label>
            <select id="eq06-producto" name="producto" class="eq06-campo">
              <option value="" selected>Todos</option>
              <option value="PR-001">Arroz</option>
              <option value="PR-002">Aceite</option>
              <option value="PR-003">Tomate</option>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="eq06-acciones">
        <button type="submit" class="eq06-boton">Aplicar período</button>
      </div>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-resultados">
    <h2 id="eq06-titulo-resultados">Resultados del período</h2>

    <div class="eq06-tarjetas">
      <article class="eq06-tarjeta">
        <h3>Productos con alerta</h3>
        <div class="eq06-tabla-contenedor">
          <table class="eq06-tabla eq06-tabla-compacta">
            <caption>Stock actual por debajo del mínimo</caption>
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col">Actual</th>
                <th scope="col">Mínimo</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Arroz · <span class="eq06-etiqueta-estado">Stock bajo</span></td>
                <td>5 <abbr title="kilogramos">kg</abbr></td>
                <td>8 <abbr title="kilogramos">kg</abbr></td>
              </tr>
              <tr>
                <td>Tomate · <span class="eq06-etiqueta-estado">Stock bajo</span> <span class="eq06-etiqueta-estado eq06-etiqueta-neutra">Sin stock</span></td>
                <td>0 <abbr title="kilogramos">kg</abbr></td>
                <td>3 <abbr title="kilogramos">kg</abbr></td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>

      <article class="eq06-tarjeta">
        <h3>Mayor consumo · Unidad explícita</h3>
        <div class="eq06-tabla-contenedor">
          <table class="eq06-tabla eq06-tabla-compacta">
            <caption>Salidas confirmadas del período</caption>
            <thead>
              <tr>
                <th scope="col">Producto</th>
                <th scope="col">Salidas del período</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Arroz</td>
                <td>14 <abbr title="kilogramos">kg</abbr></td>
              </tr>
              <tr>
                <td>Aceite</td>
                <td>8 <abbr title="litros">L</abbr></td>
              </tr>
            </tbody>
          </table>
        </div>
      </article>
    </div>

    <aside class="eq06-nota-panel" aria-label="Nota sobre el reporte">
      <p><strong>Movimientos confirmados del período:</strong> 5 entradas / 2 salidas. <em>No sumar cantidades de unidades distintas.</em></p>
    </aside>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>