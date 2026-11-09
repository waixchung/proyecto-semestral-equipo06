<?php
// procesar_producto.php
define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/validador.php'; // Asegúrate de que la ruta sea correcta según tu estructura
require_once 'clases/producto.php';

// 1. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: formulario-producto.php');
    exit;
}

// 2. Recibir y sanear datos
$nombre = isset($_POST['nombre']) ? limpiar($_POST['nombre']) : '';
$codigo = isset($_POST['codigo']) ? limpiar($_POST['codigo']) : '';
$categoria = isset($_POST['categoria']) ? limpiar($_POST['categoria']) : '';
$precio = isset($_POST['precio']) ? limpiar($_POST['precio']) : '';
$fecha_caducidad = isset($_POST['fecha_caducidad']) ? limpiar($_POST['fecha_caducidad']) : '';
$stock_minimo = isset($_POST['stock_minimo']) ? limpiar($_POST['stock_minimo']) : '';
$unidad = isset($_POST['unidad']) ? limpiar($_POST['unidad']) : '';

// 3. Validar datos
$validador = new Validador();
// Nota: Ajusta los métodos de validación según lo que hayas definido en tu clase Validador
$validador->requerido('nombre', $nombre);
$validador->requerido('codigo', $codigo);
$validador->requerido('categoria', $categoria);
$validador->requerido('precio', $precio);
$validador->requerido('stock_minimo', $stock_minimo);
$validador->requerido('unidad', $unidad);

// 4. Evaluar resultados
if ($validador->hayErrores()) {
    // Volver al formulario mostrando errores y conservando valores válidos
    $errores = $validador->errores; // Asumiendo que puedes obtener el array de errores
    $valores = $_POST; // Conservamos los datos enviados
    
    // Al incluir el formulario aquí, las variables $errores y $valores estarán disponibles en formulario-producto.php
    require_once 'formulario-producto.php';
} else {
    // Mostrar confirmación con los datos saneados (sin base de datos por ahora)
    $titulo_pagina = 'Confirmación de Producto';
    require_once 'includes/encabezado.php';
    require_once 'includes/menu.php';
    ?>
    <main class="eq06-main">
        <div class="eq06-seccion">
            <h2>Producto registrado correctamente</h2>
            <ul>
                <li><strong>Nombre:</strong> <?php echo $nombre; ?></li>
                <li><strong>Código:</strong> <?php echo $codigo; ?></li>
                <li><strong>Precio:</strong> $<?php echo $precio; ?></li>
                <li><strong>Categoría:</strong> <?php echo $categoria; ?></li>
            </ul>
            <a href="gestion-productos.php" class="eq06-btn">Volver al catálogo</a>
        </div>
    </main>
    <?php
    require_once 'includes/pie.php';
}
?>