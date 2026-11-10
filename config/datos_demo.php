<?php
// config/datos_demo.php
defined('APP_ACCESO') or exit('Acceso denegado');

// Arreglo multidimensional simulando una tabla de usuarios
$usuarios_demo = [
    [
        'correo' => 'wai.chung@utp.ac.pa',
        'contrasena' => 'P4$$w0rd2026.',
        'rol' => 'administrador',
        'nombre' => 'Waix'
    ],
    [
        'correo' => 'encargado@ejemplo.com',
        'contrasena' => 'P4$$w0rd2026.',
        'rol' => 'encargado',
        'nombre' => 'Luis Pérez'
    ],
    [
        'correo' => 'recepcionista@ejemplo.com',
        'contrasena' => 'P4$$w0rd2026.',
        'rol' => 'recepcionista',
        'nombre' => 'Luis Pérez'
    ]
];

$productos_demo = [
    [
        'codigo' => 'PR-0407',
        'nombre' => 'Aceite vegetal (L)',
        'categoria' => 'Aceites',
        'stock' => 12,
        'minimo' => 5,
        'vence' => '2027-03-15'
    ],
    [
        'codigo' => 'PR-0408',
        'nombre' => 'Salsa BBQ & chipotle (frasco)',
        'categoria' => 'Salsas',
        'stock' => 2,
        'minimo' => 6,
        'vence' => '2026-11-20'
    ],
    [
        'codigo' => 'PR-0409',
        'nombre' => 'Queso fresco (kg)',
        'categoria' => 'Lácteos',
        'stock' => 4,
        'minimo' => 3,
        'vence' => '2026-10-20'
    ]
];

?>