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

* ## Sesión de Trabajo: 04 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Creación de clases:** Se crearon las clases para Productos (entidad principal), Proveedores (entidad secundaria), Usuarios y Validador.

**Tareas pendientes:**
* Finalizar la migración a PHP de todas las vistas faltantes.
* Crear las funciones globales de saneamiento.
* Iniciar el procesamiento de datos (método POST) en los primeros formularios del sistema.

* ## Sesión de Trabajo: 06 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Migración de pantallas a PHP:** Se finalizó la migración de pantallas de HTML base a PHP, integrando header, footer y menúes con los archivos del directorio /includes.

**Tareas pendientes:**
* Crear las funciones globales de saneamiento.
* Iniciar el procesamiento de datos (método POST) en los primeros formularios del sistema.

* ## Sesión de Trabajo: 07 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Implementación de correcciones y mejoras:** Se realizaron las correcciones y mejoras requeridas posterior a la presentación del Hito 1. Se incluyen las modificaciones visuales y la implementación de los campos de precio unitario y fecha de vencimiento.
* **Diseño de scripts de procesamiento:** Se dió inicio al diseño de los scripts que serán desarrollados para el procesamiento de los formularios.

**Tareas pendientes:**
* Crear las funciones globales de saneamiento.
* Iniciar el procesamiento de datos (método POST) en los primeros formularios del sistema.
* Crear los datos de prueba.

* ## Sesión de Trabajo: 08 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Implementación de scripts de procesamiento:** Se realizaró la implementación de los scripts de procesamiento necesarios para el correcto uso de los formularios, validando la correcta funcionalidad y validez de los flujos y reglas de negocio establecidas.

**Tareas pendientes:**
* Crear los datos de prueba.