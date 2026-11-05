<?php
defined('APP_ACCESO') or exit('Acceso denegado');

class Producto {
    private $codigo;
    private $nombre;
    private $stock_actual;
    private $stock_minimo;
    private $ID_categoria;
    private $fecha_caducidad;
    private $ID_unidad;
    private $ID_proveedor;
    private $estado;

    public function __construct($codigo, $nombre, $stock_actual, $stock_minimo, $ID_categoria, $fecha_caducidad, $ID_unidad, $ID_proveedor, $estado = 'activo') {
        $this->setCodigo($codigo);
        $this->setNombre($nombre);
        $this->setStockActual($stock_actual);
        $this->setStockMinimo($stock_minimo);
        $this->setIDCategoria($ID_categoria);
        $this->setFechaCaducidad($fecha_caducidad); // Asignación que faltaba en el constructor
        $this->setIDUnidad($ID_unidad);
        $this->setIDProveedor($ID_proveedor);
        $this->setEstado($estado);
    }

    // ==========================================
    // GETTERS
    // ==========================================
    public function getCodigo() { return $this->codigo; }
    public function getNombre() { return $this->nombre; }
    public function getStockActual() { return $this->stock_actual; }
    public function getStockMinimo() { return $this->stock_minimo; }
    public function getIDCategoria() { return $this->ID_categoria; }
    public function getFechaCaducidad() { return $this->fecha_caducidad; }
    public function getIDUnidad() { return $this->ID_unidad; }
    public function getIDProveedor() { return $this->ID_proveedor; }
    public function getEstado() { return $this->estado; }

    // ==========================================
    // SETTERS CON VALIDACIÓN
    // ==========================================
    public function setCodigo($codigo) {
        if(empty(trim($codigo))) return false;
        $this->codigo = trim($codigo); return true;
    }
    
    public function setNombre($nombre) {
        if(empty(trim($nombre))) return false;
        $this->nombre = trim($nombre); return true;
    }
    
    public function setStockActual($stock_actual) {
        $this->stock_actual = (float) $stock_actual; return true;
    }
    
    public function setStockMinimo($stock_minimo) {
        $stock_minimo = (float) $stock_minimo;
        // Regla CE-01: El stock mínimo no puede ser negativo
        if ($stock_minimo < 0) return false;
        $this->stock_minimo = $stock_minimo; 
        return true;
    }
    
    public function setIDCategoria($id) { $this->ID_categoria = $id; return true; }
    
    public function setFechaCaducidad($fecha) {
        $fecha = trim($fecha);
        // Permitimos nulo o vacío por si un producto no tiene caducidad
        if (empty($fecha)) {
            $this->fecha_caducidad = null;
            return true;
        }
        // Validamos que cumpla con el formato YYYY-MM-DD
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $fecha)) {
            $this->fecha_caducidad = $fecha;
            return true;
        }
        return false;
    }
    
    public function setIDUnidad($id) { $this->ID_unidad = $id; return true; }
    
    public function setIDProveedor($id) { $this->ID_proveedor = $id; return true; }
    
    public function setEstado($estado) {
        $estado = strtolower(trim($estado));
        if ($estado !== 'activo' && $estado !== 'inactivo') return false;
        $this->estado = $estado; return true;
    }
}