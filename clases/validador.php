<?php
defined('APP_ACCESO') or exit('Acceso denegado');

class Validador {
    private $errores = [];

    // Validar que el campo no esté vacío
    public function requerido($campo, $valor, $mensaje = "Este campo es obligatorio.") {
        if (trim($valor) === '') {
            $this->errores[$campo] = $mensaje;
        }
    }

    // Validar formato de correo
    public function correo($campo, $valor, $mensaje = "Formato de correo inválido.") {
        if (!filter_var(trim($valor), FILTER_VALIDATE_EMAIL)) {
            $this->errores[$campo] = $mensaje;
        }
    }
    
    // Validar longitud mínima
    public function minimo($campo, $valor, $min, $mensaje = "El valor es muy corto.") {
        if (strlen(trim($valor)) < $min) {
            $this->errores[$campo] = $mensaje;
        }
    }

    // Verificar si existen errores acumulados
    public function hayErrores() {
        return !empty($this->errores);
    }

    // Método mágico __get
    // Permite acceder a los errores en las vistas usando $validador->nombre_del_campo
    public function __get($propiedad) {
        if (array_key_exists($propiedad, $this->errores)) {
            return $this->errores[$propiedad];
        }
        return ''; // Retorna string vacío si no hay error en ese campo
    }
}