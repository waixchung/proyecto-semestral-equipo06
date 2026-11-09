<?php
defined('APP_ACCESO') or exit('Acceso denegado');

class Usuario {
    private $nombre;
    private $correo;
    private $contraseña_hash;
    private $ID_rol;
    private $estado;

    public function __construct($nombre, $correo, $contraseña_hash, $ID_rol, $estado = 'activo') {
        $this->setNombre($nombre);
        $this->setCorreo($correo);
        $this->setContraseñaHash($contraseña_hash);
        $this->setIDRol($ID_rol);
        $this->setEstado($estado);
    }

    // Método de verificación exigido
    public function verificarClave($clave_ingresada) {
        return password_verify($clave_ingresada, $this->contraseña_hash);
    }

    // Getters
    public function getNombre() { return $this->nombre; }
    public function getCorreo() { return $this->correo; }
    public function getContraseñaHash() { return $this->contraseña_hash; }
    public function getIDRol() { return $this->ID_rol; }
    public function getEstado() { return $this->estado; }

    // Setters
    public function setNombre($nombre) {
        $this->nombre = trim($nombre); return true;
    }
    public function setCorreo($correo) {
        $this->correo = trim($correo); return true;
    }
    public function setContraseñaHash($hash) {
        $this->contraseña_hash = $hash; return true;
    }
    public function setIDRol($id) {
        $this->ID_rol = $id; return true;
    }
    public function setEstado($estado) {
        $estado = strtolower(trim($estado));
        if ($estado !== 'activo' && $estado !== 'inactivo') return false;
        $this->estado = $estado; return true;
    }
}