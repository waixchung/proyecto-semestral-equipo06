<?php
// clases/Auditoria.php
defined('APP_ACCESO') or exit('Acceso denegado');

require_once __DIR__ . '/Conexion.php';

class Auditoria {
    public static function registrar(?int $idUsuario, string $accion, string $tabla, ?int $idRegistro = null, ?string $detalle = null): void {
        try {
            $pdo = Conexion::obtener();
            $sql = 'INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, detalle)
                    VALUES (:id_usuario, :accion, :tabla, :id_registro, :detalle)';
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_usuario'  => $idUsuario,
                ':accion'      => $accion,
                ':tabla'       => $tabla,
                ':id_registro' => $idRegistro,
                ':detalle'     => $detalle
            ]);
        } catch (PDOException $e) {
            error_log('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}