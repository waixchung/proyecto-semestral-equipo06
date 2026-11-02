# Bitácora del Equipo 06 - Proyecto Semestral (Hito #2)

## Sesión de Trabajo: 02 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Migración modular a PHP:** Se adaptaron las maquetas estáticas extrayendo componentes comunes (encabezado, menú y pie) a la carpeta `includes/` e integrándolos con `require_once`.
* **Vistas dinámicas:** Se configuraron variables de contexto (ej. `$titulo_pagina`, `$rol_usuario`) para ajustar la navegación y el contenido según el estado y rol del usuario.
* **Seguridad base:** Se implementó la constante `APP_ACCESO` para proteger los archivos de inclusión contra accesos directos desde la URL.
* **Documentación:** Se generó una guía de estandarización en PDF para que los demás integrantes repliquen esta arquitectura en las plantillas restantes.

**Problemas y soluciones:**
* Se corrigió un problema de visualización del logotipo principal en el encabezado, reemplazando un enlace roto por la etiqueta `<img>` correcta dentro del maquetado.

**Tareas pendientes:**
* Finalizar la migración a PHP de todas las vistas faltantes.
* Crear la clase `Validador` y las funciones globales de saneamiento.
* Iniciar el procesamiento de datos (método POST) en los primeros formularios del sistema.