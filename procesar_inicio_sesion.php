<?php
// procesar_inicio_sesion.php
define('APP_ACCESO', true);
session_start();

require_once __DIR__ . '/includes/funciones.php';
require_once __DIR__ . '/clases/Validador.php';
require_once __DIR__ . '/clases/Conexion.php';
require_once __DIR__ . '/clases/Auditoria.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inicio-sesion.php');
    exit;
}

// 1. Obtener entrada exactamente como la envía inicio-sesion.php ('usuario' y 'contrasena')
$usuarioInput = trim($_POST['usuario'] ?? '');
$contrasena   = $_POST['contrasena'] ?? '';

$validador = new Validador();
$validador->requerido('usuario', $usuarioInput, 'El usuario o correo es obligatorio.');
$validador->requerido('contrasena', $contrasena, 'La contraseña es obligatoria.');

$errores = [];
if ($validador->hayErrores()) {
    // Si tiene método errores(), lo usamos; si no, asignamos el mensaje directo
    $errores = method_exists($validador, 'errores')
        ? $validador->errores()
        : ['auth' => 'Complete todos los campos obligatorios.'];
}

if (!$validador->hayErrores()) {
    try {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare(
            'SELECT id_usuario, nombre, correo, contrasena, rol, activo 
             FROM usuarios 
             WHERE correo = :correo OR nombre = :nombre 
             LIMIT 1'
        );
        $stmt->execute([
            ':correo' => $usuarioInput,
            ':nombre' => $usuarioInput
        ]);
        $usuario_encontrado = $stmt->fetch();

        if (
            $usuario_encontrado &&
            (int)$usuario_encontrado['activo'] === 1 &&
            password_verify($contrasena, $usuario_encontrado['contrasena'])
        ) {
            $rol_actual = $usuario_encontrado['rol'];

            $rutas_rol = [
                'administrador' => 'dashboard-administrador.php',
                'encargado'     => 'dashboard-encargado.php',
                'recepcionista' => 'dashboard-recepcionista.php',
            ];
            $destino = $rutas_rol[$rol_actual] ?? null;

            if ($destino === null) {
                $errores['auth'] = 'El usuario no tiene un rol válido asignado.';
            } else {
                session_regenerate_id(true);
                $_SESSION = [];

                $_SESSION['id_usuario']     = (int)$usuario_encontrado['id_usuario'];
                $_SESSION['usuario_correo'] = $usuario_encontrado['correo'];
                $_SESSION['usuario_nombre'] = $usuario_encontrado['nombre'];
                $_SESSION['rol_usuario']    = $rol_actual;

                Auditoria::registrar(
                    (int)$usuario_encontrado['id_usuario'],
                    'LOGIN_OK',
                    'usuarios',
                    (int)$usuario_encontrado['id_usuario'],
                    'Inicio de sesión exitoso'
                );

                header('Location: ' . $destino);
                exit;
            }
        } else {
            $id_fallido = $usuario_encontrado ? (int)$usuario_encontrado['id_usuario'] : null;
            Auditoria::registrar(
                $id_fallido,
                'LOGIN_FALLIDO',
                'usuarios',
                $id_fallido,
                'Intento fallido para: ' . mb_substr($usuarioInput, 0, 100)
            );

            $errores['auth'] = 'Correo o contraseña incorrectos.';
        }
    } catch (PDOException $e) {
        error_log('Error en procesar_inicio_sesion.php: ' . $e->getMessage());
        $errores['auth'] = 'No fue posible conectar con el sistema.';
    }
}

$_SESSION['valores_login'] = ['usuario' => $usuarioInput];
$_SESSION['errores_login'] = $errores;

header('Location: inicio-sesion.php');
exit;