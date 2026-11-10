<?php

defined('APP_ACCESO') or exit('Acceso denegado');

function limpiar($datos) {
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}

function determinarEstadoVencimiento($fecha_vencimiento) {
    if (empty($fecha_vencimiento)) {
        return 'Sin fecha';
    }
    
    $hoy = new DateTime(); // Tomará la fecha actual del sistema
    $hoy->setTime(0,0,0); // Limpiar horas para cálculo exacto de días
    
    $vence = new DateTime($fecha_vencimiento);
    $vence->setTime(0,0,0);
    
    if ($vence < $hoy) {
        return 'Vencido';
    }
    
    $diferencia = $hoy->diff($vence);
    if ($diferencia->days <= 30) {
        return 'Por vencer';
    }
    
    return 'Vigente';
}

?>