<?php
// includes/funciones.php
defined('APP_ACCESO') or exit('Acceso denegado');

/**
 * Escapa texto para salida HTML segura contra XSS (UTF-8)
 */
function e($valor): string {
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Limpieza para salida directa en HTML (compatible con Hito #2)
 */
function limpiar($datos) {
    $datos = trim((string)$datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos, ENT_QUOTES, 'UTF-8');
    return $datos;
}

/**
 * Limpieza de entrada antes de validar o guardar en MySQL (evita doble escape en BD)
 */
function limpiarEntrada($datos): string {
    return trim((string)($datos ?? ''));
}

/**
 * Determina el estado de vencimiento de un producto (regla de negocio Hito #2 / #3)
 */
function determinarEstadoVencimiento($fecha_vencimiento) {
    if (empty($fecha_vencimiento)) {
        return 'Sin fecha';
    }
    
    $hoy = new DateTime();
    $hoy->setTime(0, 0, 0);
    
    $vence = new DateTime($fecha_vencimiento);
    $vence->setTime(0, 0, 0);
    
    if ($vence < $hoy) {
        return 'Vencido';
    }
    
    $diferencia = $hoy->diff($vence);
    if ($diferencia->days <= 30) {
        return 'Por vencer';
    }
    
    return 'Vigente';
}

/**
 * Control de sesión y roles en servidor (RBAC por lista blanca)
 */
function verificarAcceso(array $rolesPermitidos = []): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id_usuario'], $_SESSION['rol_usuario'])) {
        header('Location: inicio-sesion.php');
        exit;
    }

    if (!empty($rolesPermitidos) && !in_array($_SESSION['rol_usuario'], $rolesPermitidos, true)) {
        header('Location: acceso-denegado.php');
        exit;
    }
}
?>