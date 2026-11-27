<?php
// clases/Conexion.php
defined('APP_ACCESO') or exit('Acceso denegado');

class Conexion {
    private static $pdo = null;

    public static function obtener() {
        if (self::$pdo === null) {
            $cfg = require __DIR__ . '/../config/config.php';
            try {
                self::$pdo = new PDO(
                    'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['bd'] . ';charset=utf8mb4',
                    $cfg['usuario'],
                    $cfg['clave'],
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false
                    ]
                );
            } catch (PDOException $e) {
                error_log('Error de conexión BD: ' . $e->getMessage());
                exit('No fue posible conectar con el sistema.');
            }
        }
        return self::$pdo;
    }
}