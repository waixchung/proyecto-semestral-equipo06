<?php
session_start();

// Validar que el usuario tenga una sesión activa. Si no, expulsarlo al login.
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);

$titulo_pagina = 'Recepción de mercancía · Inventario'; 
$pagina_actual = 'recepcion-mercancia'; 

// Leer los datos directamente de la sesión
$rol_usuario = $_SESSION['rol_usuario']; 
$nombre_usuario = $_SESSION['usuario_nombre']; 
$usuario_autenticado = true; 

require_once 'includes/encabezado.php';
require_once 'includes/menu.php';
?>

<main class="eq06-contenido">
  <section class="eq06-panel" aria-labelledby="eq06-titulo-recepcion">
    <h1 id="eq06-titulo-recepcion">Registrar recepción</h1>
    <form class="eq06-formulario-recepcion" method="post" action="procesar_recepcion.php">
      <fieldset class="eq06-grupo-datos">
        <legend>Datos de recepción</legend>
        <div class="eq06-campos-recepcion">
          <div class="eq06-grupo-campo">
            <label for="eq06-numero-recepcion">N.º de recepción</label>
            <input class="eq06-campo" type="text" id="eq06-numero-recepcion" name="numero_recepcion" placeholder="Se asigna al confirmar" readonly>
          </div>
          <div class="eq06-grupo-campo">
            <label for="eq06-fecha-recepcion">Fecha de recepción <span aria-hidden="true">*</span></label>
            <input class="eq06-campo" type="date" id="eq06-fecha-recepcion" name="fecha_recepcion" required>
          </div>
          <div class="eq06-grupo-campo">
            <label for="eq06-proveedor">Proveedor activo <span aria-hidden="true">*</span></label>
            <select class="eq06-campo" id="eq06-proveedor" name="proveedor" required>
              <option value="">Seleccione un proveedor</option>
              <option value="distribuidora-central">Distribuidora Central</option>
            </select>
          </div>
        </div>
      </fieldset>

      <fieldset class="eq06-grupo-detalle">
        <legend>Detalle de mercancía recibida</legend>
        <div class="eq06-tabla-contenedor">
          <table class="eq06-tabla-recepcion">
            <caption>Productos que componen la recepción</caption>
            <thead>
              <tr>
                <th scope="col">Producto activo</th>
                <th scope="col">Cantidad <span aria-hidden="true">&gt; 0</span></th>
                <th scope="col">Unidad</th>
                <th scope="col">Acción</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <label class="eq06-etiqueta-oculta" for="eq06-producto-1">Producto, línea 1</label>
                  <select class="eq06-campo" id="eq06-producto-1" name="producto_1" required>
                    <option value="">Seleccione un producto</option>
                    <option value="arroz" selected>Arroz / PR-001</option>
                    <option value="aceite">Aceite / PR-002</option>
                  </select>
                </td>
                <td>
                  <label class="eq06-etiqueta-oculta" for="eq06-cantidad-1">Cantidad de la línea 1</label>
                  <input class="eq06-campo" type="number" id="eq06-cantidad-1" name="cantidad_1" min="0.01" step="0.01" value="5" required>
                </td>
                <td>kg (automática)</td>
                <td><button class="eq06-boton-secundario" type="button">Quitar</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <button class="eq06-boton-secundario eq06-agregar-linea" type="button">+ Incluir</button>
      </fieldset>

      <aside class="eq06-aviso" aria-label="Aviso antes de confirmar">
        Antes de confirmar: revisar proveedor, productos y cantidades. No se guarda stock parcial.
      </aside>
      <div class="eq06-acciones">
        <button class="eq06-boton" type="submit">Confirmar recepción</button>
        <button class="eq06-boton-secundario" type="reset">Cancelar</button>
      </div>
    </form>
  </section>
</main>

<?php require_once 'includes/pie.php'; ?>