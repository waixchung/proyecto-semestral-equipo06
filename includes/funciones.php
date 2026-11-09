<?php

defined('APP_ACCESO') or exit('Acceso denegado');

function limpiar($datos) {
    $datos = trim($datos);
    $datos = stripslashes($datos);
    $datos = htmlspecialchars($datos);
    return $datos;
}
?>