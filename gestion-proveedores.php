<?php
// gestion-proveedores.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Proveedor.php';

// Validar sesión activa en servidor
verificarAcceso(['administrador', 'encargado', 'recepcionista']);

$titulo_pagina       = 'Gestión de proveedores · Inventario';
$pagina_actual       = 'gestion-proveedores';
$rol_usuario         = $_SESSION['rol_usuario'];
$nombre_usuario      = $_SESSION['usuario_nombre'] ?? '';
$usuario_autenticado = true;
$puede_editar        = in_array($rol_usuario, ['administrador', 'encargado'], true);

// Mensajes flash tras crear, editar, cambiar estado o eliminar
$mensaje_exito = $_SESSION['mensaje_exito_prov'] ?? null;
$mensaje_error = $_SESSION['mensaje_error_prov'] ?? null;
unset($_SESSION['mensaje_exito_prov'], $_SESSION['mensaje_error_prov']);

// Parámetros GET de búsqueda, orden y paginación
$busqueda  = trim($_GET['buscar'] ?? '');
$orden     = $_GET['orden'] ?? 'codigo';
$direccion = $_GET['dir'] ?? 'ASC';
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 10;

$resultado    = Proveedor::listar($busqueda, $orden, $direccion, $pagina, $porPagina);
$proveedores  = $resultado['datos'];
$total        = $resultado['total'];
$totalPaginas = $resultado['total_paginas'];

$dirSiguiente = (strtoupper($direccion) === 'ASC') ? 'DESC' : 'ASC';

require_once __DIR__ . '/includes/encabezado.php';
require_once __DIR__ . '/includes/menu.php';
?>

<main id="eq06-principal" class="eq06-main">
  <div class="eq06-titulo-fila" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
    <h1>Proveedores</h1>
    <?php if ($puede_editar): ?>
      <a class="eq06-boton" href="formulario-proveedor.php">Nuevo proveedor</a>
    <?php endif; ?>
  </div>

  <?php if ($mensaje_exito): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 4px;">
      <?php echo e($mensaje_exito); ?>
    </div>
  <?php endif; ?>

  <?php if ($mensaje_error): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 4px;">
      <?php echo e($mensaje_error); ?>
    </div>
  <?php endif; ?>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-busqueda">
    <h2 class="eq06-oculto" id="eq06-titulo-busqueda">Búsqueda de proveedores</h2>
    <form method="get" action="gestion-proveedores.php">
      <fieldset class="eq06-conjunto">
        <legend>Buscar proveedor</legend>
        <div class="eq06-busqueda-fila" style="display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap;">
          <div class="eq06-grupo-campo" style="flex: 1; min-width: 220px;">
            <label class="eq06-etiqueta" for="eq06-buscar-proveedor">Código, empresa, contacto o correo</label>
            <input class="eq06-campo" type="search" id="eq06-buscar-proveedor" name="buscar"
                   maxlength="60" autocomplete="off"
                   value="<?php echo e($busqueda); ?>"
                   placeholder="Ej. Distribuidora Central, PV-001, Ana...">
          </div>
          <button class="eq06-boton" type="submit">Buscar</button>
          <?php if ($busqueda !== ''): ?>
            <a class="eq06-btn eq06-btn-secundario" href="gestion-proveedores.php" style="padding: 8px 14px; text-decoration: none;">Limpiar</a>
          <?php endif; ?>
        </div>
      </fieldset>
    </form>
  </section>

  <section class="eq06-seccion" aria-labelledby="eq06-titulo-listado">
    <h2 class="eq06-oculto" id="eq06-titulo-listado">Listado de proveedores</h2>
    <div class="eq06-tabla-contenedor">
      <table class="eq06-tabla eq06-tabla-proveedores">
        <caption>Proveedores registrados (<?php echo $total; ?> registros)</caption>
        <thead>
          <tr>
            <th scope="col">
              <a href="?orden=codigo&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>" style="color: inherit; text-decoration: none;">Código ↕</a>
            </th>
            <th scope="col">
              <a href="?orden=nombre_empresa&dir=<?php echo $dirSiguiente; ?>&buscar=<?php echo urlencode($busqueda); ?>" style="color: inherit; text-decoration: none;">Proveedor ↕</a>
            </th>
            <th scope="col">Contacto / teléfono</th>
            <th scope="col">Correo</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($proveedores)): ?>
            <tr>
              <td colspan="6" style="text-align: center; color: #666; padding: 15px;">No hay proveedores registrados que coincidan con la búsqueda.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($proveedores as $prov):
              $idProv   = (int)$prov['id_proveedor'];
              $esActivo = ((int)$prov['activo'] === 1);
            ?>
            <tr>
              <td><strong><?php echo e($prov['codigo']); ?></strong></td>
              <th scope="row"><?php echo e($prov['nombre_empresa']); ?></th>
              <td><?php echo e($prov['contacto'] . ' / ' . $prov['telefono']); ?></td>
              <td><?php echo !empty($prov['correo']) ? e($prov['correo']) : '-'; ?></td>
              <td>
                <?php if ($esActivo): ?>
                  <span class="eq06-estado">Activo</span>
                <?php else: ?>
                  <span class="eq06-estado" style="background-color: #6c757d; color: white; padding: 2px 6px; border-radius: 4px;">Inactivo</span>
                <?php endif; ?>
              </td>
              <td>
                <ul class="eq06-lista-acciones" style="display: flex; gap: 8px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap;">
                  <li>
                    <a href="formulario-proveedor.php?id=<?php echo $idProv; ?>&modo=ver" aria-label="Ver proveedor <?php echo e($prov['nombre_empresa']); ?>">Ver</a>
                  </li>
                  <?php if ($puede_editar): ?>
                    <li>
                      <a href="formulario-proveedor.php?id=<?php echo $idProv; ?>" aria-label="Editar proveedor <?php echo e($prov['nombre_empresa']); ?>">Editar</a>
                    </li>
                    <li>
                      <form method="post" action="procesar_proveedor.php" style="display: inline;" onsubmit="return confirm('¿Desea <?php echo $esActivo ? 'desactivar' : 'activar'; ?> a <?php echo e($prov['nombre_empresa']); ?>?');">
                        <input type="hidden" name="accion" value="cambiar_estado">
                        <input type="hidden" name="id_proveedor" value="<?php echo $idProv; ?>">
                        <input type="hidden" name="nuevo_estado" value="<?php echo $esActivo ? '0' : '1'; ?>">
                        <button class="eq06-boton" type="submit" style="padding: 2px 8px; font-size: 0.85rem;">
                          <?php echo $esActivo ? 'Desactivar' : 'Activar'; ?>
                        </button>
                      </form>
                    </li>
                    <li>
                      <form method="post" action="procesar_proveedor.php" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar permanentemente al proveedor <?php echo e($prov['nombre_empresa']); ?>?');">
                        <input type="hidden" name="accion" value="eliminar">
                        <input type="hidden" name="id_proveedor" value="<?php echo $idProv; ?>">
                        <button type="submit" style="background: none; border: none; color: #A61E4D; text-decoration: underline; cursor: pointer; padding: 0; font: inherit;">Eliminar</button>
                      </form>
                    </li>
                  <?php endif; ?>
                </ul>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPaginas > 1): ?>
      <nav aria-label="Paginación de proveedores" style="margin-top: 15px; display: flex; gap: 6px;">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
          <a href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($busqueda); ?>&orden=<?php echo urlencode($orden); ?>&dir=<?php echo urlencode($direccion); ?>"
             style="padding: 6px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; <?php echo ($i === $pagina) ? 'background-color: #A61E4D; color: white; font-weight: bold;' : 'background-color: white; color: #333;'; ?>">
            <?php echo $i; ?>
          </a>
        <?php endfor; ?>
      </nav>
    <?php endif; ?>
  </section>

  <aside class="eq06-nota">
    <p><em>Ver</em> abre el formulario del proveedor en modo consulta; <em>Editar</em> abre el mismo formulario con los campos habilitados.</p>
  </aside>
</main>

<?php require_once __DIR__ . '/includes/pie.php'; ?>