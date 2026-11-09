<?php
session_start(); // 1. Iniciar la sesión antes de cualquier otra lógica

define('APP_ACCESO', true);
require_once 'includes/funciones.php';
require_once 'clases/Validador.php';
require_once 'config/datos_demo.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: inicio-sesion.php');
    exit;
}

$usuario    = isset($_POST['usuario']) ? limpiar($_POST['usuario']) : '';
$contrasena = isset($_POST['contrasena']) ? limpiar($_POST['contrasena']) : '';

$validador = new Validador();
$validador->requerido('usuario', $usuario, 'El usuario o correo es obligatorio.');
$validador->requerido('contrasena', $contrasena, 'La contraseña es obligatoria.');

$error_autenticacion = '';
if (!$validador->hayErrores()) {
    $autenticado = false;
    
    foreach ($usuarios_demo as $user_demo) {
        if ($user_demo['correo'] === $usuario && $user_demo['contrasena'] === $contrasena) {
            $autenticado = true;
            
            // 2. Guardar datos en la memoria del servidor
            $_SESSION['usuario_correo'] = $user_demo['correo'];
            $_SESSION['usuario_nombre'] = $user_demo['nombre'];
            $_SESSION['rol_usuario'] = $user_demo['rol'];
            break;
        }
    }

    if ($autenticado) {
        // 3. Redirigir dinámicamente según el rol
        if ($_SESSION['rol_usuario'] === 'Administrador') {
            header('Location: dashboard-administrador.php');
        } elseif ($_SESSION['rol_usuario'] === 'Encargado de inventario' || $_SESSION['rol_usuario'] === 'Encargado') {
            header('Location: dashboard-encargado.php');
        } else {
            header('Location: dashboard-recepcionista.php');
        }
        exit;
    } else {
        $error_autenticacion = 'Credenciales inválidas. Intente nuevamente.';
    }
}

if ($validador->hayErrores() || $error_autenticacion !== '') {
    $errores = $validador->getErrores();
    if ($error_autenticacion !== '') {
        $errores['auth'] = $error_autenticacion;
    }
    $valores = $_POST;
    require_once 'inicio-sesion.php';
}
?>