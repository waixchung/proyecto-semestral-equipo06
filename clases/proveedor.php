<?php
defined('APP_ACCESO') or exit('Acceso denegado');

class Proveedor {
    private $nombre;
    private $telefono;
    private $correo;
    private $direccion;
    private $estado;

    public function __construct($nombre, $telefono, $correo, $direccion, $estado = 'activo') {
        $this->setNombre($nombre);
        $this->setTelefono($telefono);
        $this->setCorreo($correo);
        $this->setDireccion($direccion);
        $this->setEstado($estado);
    }

    // Getters
    public function getNombre() { return $this->nombre; }
    public function getTelefono() { return $this->telefono; }
    public function getCorreo() { return $this->correo; }
    public function getDireccion() { return $this->direccion; }
    public function getEstado() { return $this->estado; }

    // Setters
    public function setNombre($nombre) {
        if(empty(trim($nombre))) return false;
        $this->nombre = trim($nombre); return true;
    }
    public function setTelefono($telefono) {
        $this->telefono = trim($telefono); return true;
    }
    public function setCorreo($correo) {
        $correo = trim($correo);
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) return false;
        $this->correo = $correo; return true;
    }
    public function setDireccion($direccion) {
        $this->direccion = trim($direccion); return true;
    }
    public function setEstado($estado) {
        $estado = strtolower(trim($estado));
        if ($estado !== 'activo' && $estado !== 'inactivo') return false;
        $this->estado = $estado; return true;
    }
}