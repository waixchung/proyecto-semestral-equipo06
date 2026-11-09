<?php
define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/validador.php';
require_once 'config/datos_demo.php'; // Incluimos los datos de prueba

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: inicio-sesion.php');
    exit;
}

// 1. Recibir y sanear datos
$usuario    = isset($_POST['usuario']) ? limpiar($_POST['usuario']) : '';
$contrasena = isset($_POST['contrasena']) ? limpiar($_POST['contrasena']) : '';

// 2. Validar que los campos no estén vacíos
$validador = new Validador();
$validador->requerido('usuario', $usuario, 'El usuario o correo es obligatorio.');
$validador->requerido('contrasena', $contrasena, 'La contraseña es obligatoria.');

// 3. Autenticación contra el arreglo de prueba
$error_autenticacion = '';
if (!$validador->hayErrores()) {
    $autenticado = false;
    
    // Recorrer el arreglo simulando una consulta a la base de datos
    foreach ($usuarios_demo as $user_demo) {
        if ($user_demo['correo'] === $usuario && $user_demo['contrasena'] === $contrasena) {
            $autenticado = true;
            // Nota para el futuro: Aquí se asignarán las variables de sesión
            // $_SESSION['usuario_nombre'] = $user_demo['nombre'];
            // $_SESSION['usuario_rol'] = $user_demo['rol'];
            break;
        }
    }

    if ($autenticado) {
        header('Location: dashboard-administrador.php');
        exit;
    } else {
        $error_autenticacion = 'Credenciales inválidas. Intente nuevamente.';
    }
}

// 4. Evaluar resultados y retornar errores
if ($validador->hayErrores() || $error_autenticacion !== '') {
    $errores = $validador->getErrores();
    if ($error_autenticacion !== '') {
        $errores['auth'] = $error_autenticacion;
    }
    $valores = $_POST;
    require_once 'inicio-sesion.php';
}
?>