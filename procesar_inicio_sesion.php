
<?php
session_start();

define('APP_ACCESO', true);

require_once 'includes/funciones.php';
require_once 'clases/Validador.php';
require_once 'config/datos_demo.php';

// 1. Permitir únicamente solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inicio-sesion.php');
    exit;
}

// 2. Obtener y limpiar los datos del formulario
$usuario = isset($_POST['usuario'])
    ? limpiar($_POST['usuario'])
    : '';

$contrasena = isset($_POST['contrasena'])
    ? (string) $_POST['contrasena']
    : '';

// 3. Validar campos obligatorios
$validador = new Validador();

$validador->requerido(
    'usuario',
    $usuario,
    'El usuario o correo es obligatorio.'
);

$validador->requerido(
    'contrasena',
    $contrasena,
    'La contraseña es obligatoria.'
);

$error_autenticacion = '';

if (!$validador->hayErrores()) {

    $usuario_encontrado = null;

    // 4. Verificar las credenciales
    foreach ($usuarios_demo as $user_demo) {

        if (
            $user_demo['correo'] === $usuario &&
            $user_demo['contrasena'] === $contrasena
        ) {
            $usuario_encontrado = $user_demo;
            break;
        }
    }

    // 5. Comprobar si el usuario fue autenticado
    if ($usuario_encontrado !== null) {

        // Obtener el rol real del usuario
        $rol_actual = trim(
            (string) $usuario_encontrado['rol']
        );

        // 6. Determinar el dashboard correspondiente
        switch ($rol_actual) {

            case 'administrador':
                $destino = 'dashboard-administrador.php';
                break;

            case 'encargado':
                $destino = 'dashboard-encargado.php';
                break;

            case 'recepcionista':
                $destino = 'dashboard-recepcionista.php';
                break;

            default:
                $destino = null;
                break;
        }

        // 7. Bloquear roles no reconocidos
        if ($destino === null) {
            $error_autenticacion =
                'El usuario no tiene un rol válido asignado.';
        } else {

            // 8. Regenerar la sesión por seguridad
            session_regenerate_id(true);

            // Limpiar los datos de la sesión anterior
            $_SESSION = [];

            // 9. Guardar los datos del usuario autenticado
            $_SESSION['usuario_correo'] =
                $usuario_encontrado['correo'];

            $_SESSION['usuario_nombre'] =
                $usuario_encontrado['nombre'];

            $_SESSION['rol_usuario'] =
                $rol_actual;

            // 10. Redirigir al dashboard correcto
            header('Location: ' . $destino);
            exit;
        }

    } else {

        $error_autenticacion =
            'Credenciales inválidas. Intente nuevamente.';
    }
}

// 11. Preparar errores para mostrarlos en el formulario
if ($validador->hayErrores() || $error_autenticacion !== '') {

    $errores = $validador->getErrores();

    if ($error_autenticacion !== '') {
        $errores['auth'] = $error_autenticacion;
    }

    // Conservar únicamente el correo, nunca la contraseña
    $valores = [
        'usuario' => $usuario
    ];

    require_once 'inicio-sesion.php';
    exit;
}
