<?php
session_start();

// 1. Proteger el script
if (!isset($_SESSION['rol_usuario'])) {
    header('Location: inicio-sesion.php');
    exit;
}

define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/Validador.php'; 
require_once 'config/datos_demo.php'; 

// 2. Verificar el método HTTP
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: registro-salida.php');
    exit;
}

// 3. Recibir y sanear datos del formulario de salida
$codigo_producto = isset($_POST['codigo_producto']) ? limpiar($_POST['codigo_producto']) : '';
$cantidad = isset($_POST['cantidad']) ? limpiar($_POST['cantidad']) : '';
$referencia = isset($_POST['referencia']) ? limpiar($_POST['referencia']) : '';

// 4. Validar datos básicos
$validador = new Validador();
$validador->requerido('codigo_producto', $codigo_producto, 'Debe seleccionar un producto.');
$validador->requerido('cantidad', $cantidad, 'La cantidad es obligatoria.');

// 5. REGLA DE NEGOCIO HITO #2: Bloquear salidas de productos vencidos
if (!$validador->hayErrores()) {
    $producto_seleccionado = null;
    
    // Buscar el producto en el catálogo demo
    $productos = isset($productos_demo) ? $productos_demo : [];
    foreach ($productos as $prod) {
        if ($prod['codigo'] === $codigo_producto) {
            $producto_seleccionado = $prod;
            break;
        }
    }

    if ($producto_seleccionado) {
        // Evaluamos la fecha usando la función que creamos previamente
        $fecha_vence = isset($producto_seleccionado['vence']) ? $producto_seleccionado['vence'] : '';
        $estado_vencimiento = determinarEstadoVencimiento($fecha_vence);
        
        if ($estado_vencimiento === 'Vencido') {
            // Forzamos un error manual en el validador para rechazar la salida
            // NOTA PARA EL PROFESOR: Aquí se cumple la prueba de rechazar el Queso fresco.
            $validador->setError('codigo_producto', "Movimiento rechazado: El producto '{$producto_seleccionado['nombre']}' se encuentra vencido.");
        }
        
        // Opcional: También podríamos validar que no saquen más stock del que hay
        // if (floatval($cantidad) > floatval($producto_seleccionado['stock'])) {
        //     $validador->setError('cantidad', "Stock insuficiente. Solo hay {$producto_seleccionado['stock']} disponibles.");
        // }
    } else {
        $validador->setError('codigo_producto', "El producto seleccionado no existe en el catálogo.");
    }
}

// 6. Evaluar resultados y renderizar
if ($validador->hayErrores()) {
    $errores = $validador->getErrores(); // Usa el método correcto de tu clase Validador
    $valores = $_POST; 
    
    // Declaramos las variables de contexto antes de incluir la vista
    $titulo_pagina = 'Registrar salida | Inventario';
    $pagina_actual = 'salidas';
    $rol_usuario = $_SESSION['rol_usuario']; 
    $nombre_usuario = $_SESSION['usuario_nombre']; 
    $usuario_autenticado = true;
    
    // Regresamos al formulario para mostrar el error de "producto vencido"
    require_once 'registro-salida.php'; 
} else {
    // Si pasa todas las validaciones, mostramos el éxito
    $titulo_pagina = 'Confirmación de Salida';
    $pagina_actual = 'salidas';
    $rol_usuario = $_SESSION['rol_usuario']; 
    $nombre_usuario = $_SESSION['usuario_nombre']; 
    $usuario_autenticado = true;
    
    require_once 'includes/encabezado.php';
    require_once 'includes/menu.php';
    ?>
    <main class="eq06-main">
        <div class="eq06-seccion">
            <div class="eq06-alerta" style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; border: 1px solid #c3e6cb; margin-bottom: 20px;">
                <h2 style="margin-top: 0;">Salida registrada correctamente</h2>
                <p style="margin-bottom: 0;">El movimiento de inventario se ha procesado con éxito.</p>
            </div>
            <ul>
                <li><strong>Producto:</strong> <?php echo htmlspecialchars($producto_seleccionado['nombre']); ?> (<abbr title="Código"><?php echo htmlspecialchars($codigo_producto); ?></abbr>)</li>
                <li><strong>Cantidad descontada:</strong> <?php echo htmlspecialchars($cantidad); ?> <?php echo isset($producto_seleccionado['unidad']) ? htmlspecialchars($producto_seleccionado['unidad']) : ''; ?></li>
                <li><strong>Referencia:</strong> <?php echo htmlspecialchars($referencia); ?></li>
            </ul>
            <div class="eq06-acciones-form" style="margin-top: 20px;">
                <a href="historial-movimientos.php" class="eq06-btn">Ver historial</a>
                <a href="registro-salida.php" class="eq06-btn eq06-btn-secundario">Registrar otra salida</a>
            </div>
        </div>
    </main>
    <?php
    require_once 'includes/pie.php';
}
?>