<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Historial de movimientos · Inventario'; 
$pagina_actual = 'historial-movimientos'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main id="eq06-contenido">
  <h1>Historial de movimientos</h1>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-filtros">
    <h2 id="eq06-titulo-filtros">Consulta por filtros</h2>
    <form action="#" method="post">
      <fieldset class="eq06-grupo">
        <legend>Filtros</legend>

        <div class="eq06-filtros">
          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-desde">Desde</label>
            <input type="date" id="eq06-desde" name="desde" class="eq06-campo" required value="2026-10-01" max="2026-12-31">
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-hasta">Hasta</label>
            <input type="date" id="eq06-hasta" name="hasta" class="eq06-campo" required value="2026-10-05" max="2026-12-31">
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

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-tipo">Tipo</label>
            <select id="eq06-tipo" name="tipo" class="eq06-campo">
              <option value="" selected>Todos</option>
              <option value="entrada">Entrada</option>
              <option value="salida">Salida</option>
              <option value="ajuste">Ajuste</option>
            </select>
          </div>

          <div class="eq06-campo-bloque">
            <label class="eq06-etiqueta" for="eq06-usuario">Usuario</label>
            <select id="eq06-usuario" name="usuario" class="eq06-campo">
              <option value="" selected>Todos</option>
              <option value="ana">Ana</option>
              <option value="luis">Luis</option>
            </select>
          </div>
        </div>
      </fieldset>

      <div class="eq06-acciones">
        <button type="submit" class="eq06-boton">Consultar</button>
        <button type="button" class="eq06-boton">Exportar (opcional)</button>
      </div>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-resultados">
    <h2 id="eq06-titulo-resultados">Resultados</h2>

    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla eq06-tabla-ancha">
        <caption>Movimientos confirmados del <time datetime="2026-10-01">01/10/2026</time> al <time datetime="2026-10-05">05/10/2026</time></caption>
        <thead>
          <tr>
            <th scope="col">Fecha / hora</th>
            <th scope="col">Producto</th>
            <th scope="col">Tipo</th>
            <th scope="col">Cantidad</th>
            <th scope="col">Usuario</th>
            <th scope="col">Referencia</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><time datetime="2026-10-05T10:30">05/10 10:30</time></td>
            <td>Arroz</td>
            <td>Entrada</td>
            <td>+5 <abbr title="kilogramos">kg</abbr></td>
            <td>Ana</td>
            <td>REC-0021 · <a href="#">Ver</a></td>
          </tr>
          <tr>
            <td><time datetime="2026-10-05T11:00">05/10 11:00</time></td>
            <td>Aceite</td>
            <td>Salida</td>
            <td>−2 <abbr title="litros">L</abbr></td>
            <td>Luis</td>
            <td>SAL-0035 · <a href="#">Ver</a></td>
          </tr>
          <tr>
            <td><time datetime="2026-10-03T09:20">03/10 09:20</time></td>
            <td>Tomate</td>
            <td>Ajuste</td>
            <td>+1 <abbr title="kilogramos">kg</abbr></td>
            <td>Autorizado</td>
            <td>AJ-0002 · <a href="#">Ver</a></td>
          </tr>
        </tbody>
      </table>
    </div>

    <aside class="eq06-nota-panel" aria-label="Ayuda sobre la consulta">
      <p><strong>Ver:</strong> panel de consulta con fecha, usuario, referencia y detalle. <em>Sin editar ni eliminar.</em></p>
    </aside>
  </section>
</main>

<?php
require_once 'includes/pie.php';
?>