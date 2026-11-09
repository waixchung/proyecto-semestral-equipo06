<?php
define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/validador.php';
// require_once 'clases/Proveedor.php'; // Habilítalo cuando tengas la clase Proveedor creada

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: gestion-proveedores.php');
    exit;
}

// 1. Recibir y sanear datos
$nombre_prov = isset($_POST['eq06-nombre-proveedor']) ? limpiar($_POST['eq06-nombre-proveedor']) : '';
$contacto    = isset($_POST['eq06-contacto-proveedor']) ? limpiar($_POST['eq06-contacto-proveedor']) : '';
$telefono    = isset($_POST['eq06-telefono-proveedor']) ? limpiar($_POST['eq06-telefono-proveedor']) : '';
$correo      = isset($_POST['eq06-correo-proveedor']) ? limpiar($_POST['eq06-correo-proveedor']) : '';
$direccion   = isset($_POST['eq06-direccion-proveedor']) ? limpiar($_POST['eq06-direccion-proveedor']) : '';
$referencia  = isset($_POST['eq06-referencia-proveedor']) ? limpiar($_POST['eq06-referencia-proveedor']) : '';
$estado      = isset($_POST['eq06-estado-proveedor']) ? limpiar($_POST['eq06-estado-proveedor']) : '';

// 2. Validar datos
$validador = new Validador();
$validador->requerido('eq06-nombre-proveedor', $nombre_prov);

// Validar correo solo si el usuario escribió algo (es opcional según tu HTML)
if (!empty($correo)) {
    $validador->correo('eq06-correo-proveedor', $correo);
}

// 3. Evaluar resultados
if ($validador->hayErrores()) {
    $errores = $validador->getErrores();
    $valores = $_POST;
    require_once 'formulario-proveedor.php';
} else {
    // Confirmación visual
    $titulo_pagina = 'Confirmación de Proveedor';
    require_once 'includes/encabezado.php';
    require_once 'includes/menu.php';
    ?>
    <main id="eq06-principal">
        <section class="eq06-panel-proveedor">
            <h1>Proveedor registrado exitosamente</h1>
            <ul>
                <li><strong>Nombre:</strong> <?php echo $nombre_prov; ?></li>
                <li><strong>Contacto:</strong> <?php echo !empty($contacto) ? $contacto : 'N/A'; ?></li>
                <li><strong>Correo:</strong> <?php echo !empty($correo) ? $correo : 'N/A'; ?></li>
            </ul>
            <a href="gestion-proveedores.php" class="eq06-boton">Volver al listado</a>
        </section>
    </main>
    <?php
    require_once 'includes/pie.php';
}
?>