
<?php
session_start();

// 1. Vaciar todas las variables de sesión
$_SESSION = [];

// 2. Eliminar la cookie de sesión
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]
    );
}

// 3. Destruir la sesión del servidor
session_destroy();

// 4. Evitar que la respuesta quede almacenada en caché
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');

// 5. Redirigir al inicio de sesión
header('Location: inicio-sesion.php');
exit;
