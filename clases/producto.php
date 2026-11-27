<?php
// clases/Producto.php
defined('APP_ACCESO') or exit('Acceso denegado');

require_once __DIR__ . '/Conexion.php';

class Producto {
    private ?int $id_producto = null;
    private $codigo;
    private $nombre;
    private $stock_actual;
    private $stock_minimo;
    private $ID_categoria;
    private $fecha_caducidad;
    private $precio;
    private $ubicacion;
    private $ID_unidad;
    private $ID_proveedor;
    private $estado;

    public function __construct(
        $codigo = '',
        $nombre = '',
        $stock_actual = 0,
        $stock_minimo = 0,
        $ID_categoria = 1,
        $fecha_caducidad = null,
        $precio = 0.0,
        $ubicacion = '',
        $ID_unidad = null,
        $ID_proveedor = null,
        $estado = 'activo'
    ) {
        if ($codigo !== '') {
            $this->setCodigo($codigo);
        }
        if ($nombre !== '') {
            $this->setNombre($nombre);
        }
        $this->setStockActual($stock_actual);
        $this->setStockMinimo($stock_minimo);
        $this->setIDCategoria($ID_categoria);
        $this->setFechaCaducidad($fecha_caducidad ?? '');
        $this->setPrecio($precio);
        $this->setUbicacion($ubicacion);
        $this->setIDUnidad($ID_unidad);
        $this->setIDProveedor($ID_proveedor);
        $this->setEstado($estado);
    }

    // ==================== GETTERS (Compatibles con Hito #2 + Hito #3) ====================
    public function getIdProducto(): ?int { return $this->id_producto; }
    public function getCodigo() { return $this->codigo; }
    public function getNombre() { return $this->nombre; }
    public function getStockActual() { return $this->stock_actual; }
    public function getStockMinimo() { return $this->stock_minimo; }
    public function getIDCategoria() { return $this->ID_categoria; }
    public function getFechaCaducidad() { return $this->fecha_caducidad; }
    public function getPrecio() { return $this->precio; }
    public function getUbicacion() { return $this->ubicacion; }
    public function getIDUnidad() { return $this->ID_unidad; }
    public function getIDProveedor() { return $this->ID_proveedor; }
    public function getEstado() { return $this->estado; }

    // ==================== SETTERS (Con validación de dominio) ====================
    public function setIdProducto(?int $id): void { $this->id_producto = $id; }

    public function setCodigo($codigo): bool {
        if (empty(trim((string)$codigo))) return false;
        $this->codigo = trim((string)$codigo);
        return true;
    }

    public function setNombre($nombre): bool {
        if (empty(trim((string)$nombre))) return false;
        $this->nombre = trim((string)$nombre);
        return true;
    }

    public function setStockActual($stock_actual): bool {
        $stock_actual = (int)$stock_actual;
        if ($stock_actual < 0) return false;
        $this->stock_actual = $stock_actual;
        return true;
    }

    public function setStockMinimo($stock_minimo): bool {
        $stock_minimo = (int)$stock_minimo;
        if ($stock_minimo < 0) return false;
        $this->stock_minimo = $stock_minimo;
        return true;
    }

    public function setIDCategoria($id): bool {
        $this->ID_categoria = (int)$id;
        return true;
    }

    public function setFechaCaducidad($fecha): bool {
        $fecha = trim((string)$fecha);
        if (empty($fecha)) {
            $this->fecha_caducidad = null;
            return true;
        }
        if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $fecha)) {
            $this->fecha_caducidad = $fecha;
            return true;
        }
        return false;
    }

    public function setPrecio($precio): bool {
        $precio = (float)$precio;
        if ($precio < 0) return false;
        $this->precio = $precio;
        return true;
    }

    public function setUbicacion($ubicacion): bool {
        $this->ubicacion = trim((string)$ubicacion);
        return true;
    }

    public function setIDUnidad($id): bool { $this->ID_unidad = $id; return true; }
    public function setIDProveedor($id): bool { $this->ID_proveedor = $id; return true; }

    public function setEstado($estado): bool {
        $estado = strtolower(trim((string)$estado));
        if ($estado !== 'activo' && $estado !== 'inactivo') return false;
        $this->estado = $estado;
        return true;
    }

    // ==================== PERSISTENCIA EN MYSQL (PDO) ====================

    /**
     * Guarda un nuevo producto usando los atributos validados del objeto
     */
    public function guardar(): int {
        $pdo = Conexion::obtener();
        $sql = "INSERT INTO productos (codigo, nombre, id_categoria, stock, minimo, precio, vence, ubicacion)
                VALUES (:codigo, :nombre, :id_categoria, :stock, :minimo, :precio, :vence, :ubicacion)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':codigo'       => $this->codigo,
            ':nombre'       => $this->nombre,
            ':id_categoria' => (int)$this->ID_categoria,
            ':stock'        => (int)$this->stock_actual,
            ':minimo'       => (int)$this->stock_minimo,
            ':precio'       => (float)$this->precio,
            ':vence'        => $this->fecha_caducidad,
            ':ubicacion'    => $this->ubicacion
        ]);
        $this->id_producto = (int)$pdo->lastInsertId();
        return $this->id_producto;
    }

    /**
     * Actualiza un producto existente en MySQL usando los atributos del objeto
     */
    public function actualizar(int $idProducto): bool {
        $pdo = Conexion::obtener();
        $sql = "UPDATE productos
                SET codigo = :codigo,
                    nombre = :nombre,
                    id_categoria = :id_categoria,
                    stock = :stock,
                    minimo = :minimo,
                    precio = :precio,
                    vence = :vence,
                    ubicacion = :ubicacion
                WHERE id_producto = :id";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':codigo'       => $this->codigo,
            ':nombre'       => $this->nombre,
            ':id_categoria' => (int)$this->ID_categoria,
            ':stock'        => (int)$this->stock_actual,
            ':minimo'       => (int)$this->stock_minimo,
            ':precio'       => (float)$this->precio,
            ':vence'        => $this->fecha_caducidad,
            ':ubicacion'    => $this->ubicacion,
            ':id'           => $idProducto
        ]);
    }

    /**
     * Listado paginado con búsqueda, filtro, JOIN a categorias y lista blanca en ORDER BY
     */
    public static function listar(
        string $busqueda = '',
        int $idCategoria = 0,
        string $orden = 'codigo',
        string $direccion = 'ASC',
        int $pagina = 1,
        int $porPagina = 10
    ): array {
        $pdo = Conexion::obtener();

        $columnasPermitidas = ['codigo', 'nombre', 'stock', 'minimo', 'precio', 'vence', 'ubicacion'];
        $ordenSeguro = in_array($orden, $columnasPermitidas, true) ? 'p.' . $orden : 'p.codigo';
        $dirSegura   = strtoupper($direccion) === 'DESC' ? 'DESC' : 'ASC';

        $pagina    = max(1, $pagina);
        $porPagina = max(1, min(100, $porPagina));
        $offset    = ($pagina - 1) * $porPagina;

        $where  = [];
        $params = [];

        if ($busqueda !== '') {
            $where[] = '(p.codigo LIKE :b_cod OR p.nombre LIKE :b_nom OR p.ubicacion LIKE :b_ubi)';
            $like = '%' . $busqueda . '%';
            $params[':b_cod'] = $like;
            $params[':b_nom'] = $like;
            $params[':b_ubi'] = $like;
        }

        if ($idCategoria > 0) {
            $where[] = 'p.id_categoria = :id_categoria';
            $params[':id_categoria'] = $idCategoria;
        }

        $sqlWhere = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM productos p $sqlWhere");
        $stmtTotal->execute($params);
        $totalRegistros = (int)$stmtTotal->fetchColumn();

        $sql = "SELECT p.id_producto, p.codigo, p.nombre, p.id_categoria,
                       c.nombre AS categoria, c.unidad_medida,
                       p.stock, p.minimo, p.precio, p.vence, p.ubicacion
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                $sqlWhere
                ORDER BY $ordenSeguro $dirSegura
                LIMIT :limite OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $clave => $valor) {
            $stmt->bindValue($clave, $valor, is_int($valor) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limite', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'datos'         => $stmt->fetchAll(),
            'total'         => $totalRegistros,
            'pagina'        => $pagina,
            'por_pagina'    => $porPagina,
            'total_paginas' => max(1, (int)ceil($totalRegistros / $porPagina))
        ];
    }

    public static function obtenerPorId(int $idProducto): ?array {
        $pdo = Conexion::obtener();
        $sql = "SELECT p.*, c.nombre AS categoria, c.unidad_medida
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.id_producto = :id
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $idProducto]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function obtenerPorCodigo(string $codigo): ?array {
        $pdo = Conexion::obtener();
        $sql = "SELECT p.*, c.nombre AS categoria, c.unidad_medida
                FROM productos p
                INNER JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.codigo = :codigo
                LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':codigo' => $codigo]);
        $fila = $stmt->fetch();
        return $fila ?: null;
    }

    public static function obtenerCategorias(): array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->query('SELECT id_categoria, nombre, unidad_medida FROM categorias ORDER BY nombre ASC');
        return $stmt->fetchAll();
    }

    /**
     * Eliminación segura: captura integridad referencial (SQLSTATE 23000)
     */
    public static function eliminar(int $idProducto): array {
        try {
            $pdo = Conexion::obtener();
            $stmt = $pdo->prepare('DELETE FROM productos WHERE id_producto = :id');
            $stmt->execute([':id' => $idProducto]);
            return ['exito' => true, 'mensaje' => 'Producto eliminado correctamente.'];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return [
                    'exito'   => false,
                    'mensaje' => 'No se puede eliminar este producto porque tiene movimientos o proveedores asociados.'
                ];
            }
            error_log('Error al eliminar producto: ' . $e->getMessage());
            return ['exito' => false, 'mensaje' => 'Ocurrió un error al intentar eliminar el producto.'];
        }
    }

    /**
     * Cambio de requerimiento: procedimiento almacenado de stock bajo por ubicación
     */
    public static function listarStockBajoPorUbicacion(string $zona = ''): array {
        $pdo = Conexion::obtener();
        $stmt = $pdo->prepare('CALL sp_reporte_stock_bajo_ubicacion(:zona)');
        $stmt->execute([':zona' => $zona]);
        $resultados = $stmt->fetchAll();
        $stmt->closeCursor();
        return $resultados;
    }

    /**
     * Segundo JOIN requerido (utiliza la tabla intermedia N:M producto_proveedor)
     */
    public static function listarProveedoresPorProducto(int $idProducto): array {
        $pdo = Conexion::obtener();
        $sql = "SELECT pv.id_proveedor, pv.codigo, pv.nombre_empresa, pv.contacto, pv.telefono,
                       pp.precio_compra, pp.tiempo_entrega_dias
                FROM producto_proveedor pp
                INNER JOIN proveedores pv ON pp.id_proveedor = pv.id_proveedor
                WHERE pp.id_producto = :id_producto
                ORDER BY pp.precio_compra ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_producto' => $idProducto]);
        return $stmt->fetchAll();
    }
}