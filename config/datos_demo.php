<?php
// config/datos_demo.php
defined('APP_ACCESO') or exit('Acceso denegado');

// Arreglo multidimensional simulando una tabla de usuarios
$usuarios_demo = [
    [
        'correo' => 'wai.chung@utp.ac.pa',
        'contrasena' => 'P4$$w0rd2026.',
        'rol' => 'Administrador',
        'nombre' => 'Waix'
    ],
    [
        'correo' => 'encargado@ejemplo.com',
        'contrasena' => 'P4$$w0rd2026.',
        'rol' => 'Encargado de inventario',
        'nombre' => 'Luis Pérez'
    ]
];

// Aquí debajo podrás agregar los arreglos $productos_demo y $proveedores_demo más adelante
?>