<?php
// clases/Proveedor.php
defined('APP_ACCESO') or exit('Acceso denegado');

require_once __DIR__ . '/Conexion.php';

class Proveedor {
    private ?int $id_proveedor = null;
    private $codigo;
    private $nombre;
    private $contacto;
    private $telefono;
    private $correo;
    private $direccion;
    private $estado;

    public function __construct(
        $nombre = '',
        $telefono = '',
        $correo = '',
        $direccion = '',
        $estado = 'activo',
        $codigo = '',
        $contacto = ''
    ) {
        if ($nombre !== '') {
            $this->setNombre($nombre);
        }
        $this->setTelefono($telefono);
        if ($correo !== '') {
            $this->setCorreo($correo);
        }
        $this->setDireccion($direccion);
        $this->setEstado($estado);
        $this->setCodigo($codigo);
        $this->setContacto($contacto !== '' ? $contacto : $nombre);
    }

  
    public function getIdProveedor(): ?int { return $this->id_proveedor; }
    public function getCodigo() { return $this->codigo; }
    public function getNombre() { return $this->nombre; }
    public function getContacto() { return $this->contacto; }
    public function getTelefono() { return $this->telefono; }
    public function getCorreo() { return $this->correo; }
    public function getDireccion() { return $this->direccion; }
    public function getEstado() { return $this->estado; }

    
    public function setIdProveedor(?int $id): void { $this->id_proveedor = $id; }

    public function setCodigo($codigo): bool {
        $this->codigo = trim((string)$codigo);
        return true;
    }

    public function setNombre($nombre): bool {
        if (empty(trim((string)$nombre))) return false;
        $this->nombre = trim((string)$nombre);
        return true;
    }

    public function setContacto($contacto): bool {
        $this->contacto = trim((string)$contacto);
        return true;
    }

    public function setTelefono($telefono): bool {
        $this->telefono = trim((string)$telefono);
        return true;
    }

    public function setCorreo($correo): bool {
        $correo = trim((string)$correo);
        if ($correo === '') {
            $this->correo = null;
            return true;
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;
        $this->correo = $correo;
        return true;
    }

    public function setDireccion($direccion): bool {
        $this->direccion = trim((string)$direccion);
        return true;
    }

    public function setEstado($estado): bool {
        $estado = strtolower(trim((string)$estado));
        if ($estado !== 'activo' && $estado !== 'inactivo') return false;
        $this->estado = $estado;
        return true;
    }

    // ==================== PERSISTENCIA EN MYSQL (PDO) ====================

    private static function generarSiguienteCodigo(PDO $pdo): string {
        $stmt = $pdo->query('SELECT MAX(id_proveedor) FROM proveedores');
        $siguiente = ((int)$stmt->fetchColumn()) + 1;
        return 'PV-' . str_pad((string)$siguiente, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Inserta un nuevo proveedor en MySQL usando los atributos validados del objeto
     */
    public function guardar(): int {
        $pdo = Conexion::obtener();
        $codigoFinal = !empty($this->codigo) ? $this->codigo : self::generarSiguienteCodigo($pdo);
        $contactoFinal = !empty($this->contacto) ? $this->contacto : $this->nombre;
        $activoInt = ($this->estado === 'inactivo') ? 0 : 1;

        $sql = "INSERT INTO proveedores (codigo, nombre_empresa, contacto, telefono, correo, direccion, activo)
                VALUES (:codigo, :nombre_empresa, :contacto, :telefono, :correo, :direccion, :activo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo'         => $codigoFinal,
            ':nombre_empresa' => $this->nombre,
            ':contacto'       => $contactoFinal,
            ':telefono'       => $this->telefono,
            ':correo'         => !empty($this->correo) ? $this->correo : null,
            ':direccion'      => !empty($this->direccion) ? $this->direccion : null,
            ':activo'         => $activoInt
        ]);
        $this->id_proveedor = (int)$pdo->lastInsertId();
        $this->codigo = $codigoFinal;
        return $this->id_proveedor;
    }

    /**
     * Actualiza un proveedor existente en MySQL
     */
    public function actualizar(int $idProveedor): bool {
        $pdo = Conexion::obtener();
        $contactoFinal = !empty($this->contacto) ? $this->contacto : $this->nombre;
        $activoInt = ($this->estado === 'inactivo') ? 0 : 1;

        if (!empty($this->codigo)) {
            $sql = "UPDATE proveedores
                    SET codigo = :codigo,
                        nombre_empresa = :nombre_empresa,
                        contacto = :contacto,
                        telefono = :telefono,
                        correo = :correo,
                        direccion = :direccion,
                        activo = :activo
                    WHERE id_proveedor = :id";
            $params = [
                ':codigo'         => $this->codigo,
                ':nombre_empresa' => $this->nombre,
                ':contacto'       => $contactoFinal,
                ':telefono'       => $this->telefono,
                ':correo'         => !empty($this->correo) ? $this->correo : null,
                ':direccion'      => !empty($this->direccion) ? $this->direccion : null,
                ':activo'         => $activoInt,
                ':id'             => $idProveedor
            ];
        } else {
            $sql = "UPDATE proveedores
                    SET nombre_empresa = :nombre_empresa,
                        contacto = :contacto,
                        telefono = :telefono,
                        correo = :correo,
                        direccion = :direccion,
                        activo = :activo
                    WHERE id_proveedor = :id";
            $params = [
                ':nombre_empresa' => $this->nombre,
                ':contacto'       => $contactoFinal,
                ':telefono'       => $this->telefono,
                ':correo'         => !empty($this->correo) ? $this->correo : null,
                ':direccion'      => !empty($this->direccion) ? $this->direccion : null,
                ':activo'         => $activoInt,
                ':id'             => $idProveedor
            ];
        }

        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Listado paginado con búsqueda y ordenamiento seguro por lista blanca
     */
    public static function listar(
        string $busqueda = '',
        string $orden = 'codigo',
        string $direccion = 'ASC',
        int $pagina = 1,
        int $porPagina = 10
    ): array {
        $pdo = Conexion::obtener();

        $columnasPermitidas = ['codigo', 'nombre_empresa', 'contacto', 'telefono', 'correo', 'activo'];
        $ordenSeguro = in_array($orden, $columnasPermitidas, true) ? $orden : 'codigo';
        $dirSegura   = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';

        $pagina    = max(1, $pagina);
        $porPagina = max(1, min(100, $porPagina));
        $offset    = ($pagina - 1) * $porPagina;

        $where  = [];
        $params = [];

        if ($busqueda !== '') {
            $where[] = '(codigo LIKE :b_cod OR nombre_empresa LIKE :b_emp OR contacto LIKE :b_con OR correo LIKE :b_cor)';
            $like = '%' . $busqueda . '%';
            $params[':b_cod'] = $like;
            $params[':b_emp'] = $like;
            $params[':b_con'] = $like;
            $params[':b_cor'] = $like;
        }

        $sqlWhere = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM proveedores $sqlWhere");
        $stmtTotal->execute($params);
        $total = (int)$stmtTotal->fetchColumn();

        $sql = "SELECT id_proveedor, codigo, nombre_empresa, contacto, telefono, correo, direccion, activo
                FROM proveedores
                $sqlWhere
                ORDER BY $ordenSeguro $dirSegura
                LIMIT :limite OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'datos'         => $stmt->fetchAll(),
            'total'         => $total,
            'pagina'        => $pagina,
            'por_pagina'    => $porPagina,
            'total_paginas' => max(1, (int)ceil($total / $porPagina))
        ];
    }

    public static function obtenerPorId(int $idProveedor): ?array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT * FROM proveedores WHERE id_proveedor = :id LIMIT 1');
        $stmt->execute([':id' => $idProveedor]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    /**
     * Eliminación segura: captura restricción de llave foránea (SQLSTATE 23000)
     */
    public static function eliminar(int $idProveedor): array {
        try {
            $pdo = Conexion::obtener();
            $stmt = $pdo->prepare('DELETE FROM proveedores WHERE id_proveedor = :id');
            $stmt->execute([':id' => $idProveedor]);
            return ['exito' => true, 'mensaje' => 'Proveedor eliminado correctamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return [
                    'exito'   => false,
                    'mensaje' => 'No se puede eliminar este proveedor porque tiene productos vinculados en el catálogo.'
                ];
            }
            error_log('Error al eliminar proveedor: ' . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Ocurrió un error al intentar eliminar el proveedor.'];
        }
    }
}