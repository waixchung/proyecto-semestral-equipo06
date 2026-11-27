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

* ## Sesión de Trabajo: 09 de Noviembre de 2026

**Participantes:**
* Wai Chung

**Actividades realizadas:**
* **Proceso de QA:** Se realizaron las pruebas de funcionalidades y validación de integridad en cuanto a las reglas de negocio. Se aplicaron correcciones referentes al aspecto visual, así como de integración entre formularios y scripts de procesamiento. Se creó el script `procesar_inicio_sesion.php` y se corrigieron las referencias a formularios y dependencias del directorio `includes`. Se corrigieron aspectos de seguridad como el manejo de sesiones con los scripts y se crearon los datos de prueba bajo el directorio `config/datos_demo.php`

**Tareas pendientes:**
* Documentación técnica y de trazabilidad.

* ## Sesión de Trabajo: 26 y 27 de Noviembre de 2026
## Resumen Ejecutivo del Avance

Durante las sesiones de trabajo correspondientes al **26 y 27 de noviembre de 2026**, se ejecutó la migración integral del sistema INVENTRA desde arreglos estáticos en memoria (`config/datos_demo.php`) hacia una arquitectura persistente, relacional y segura sobre MySQL (`inventra_db`) mediante **PDO** y programación orientada a objetos. Se implementaron controles de acceso por rol en servidor (RBAC), protección contra inyección SQL y XSS, procedimientos almacenados transaccionales, bitácora de auditoría en base de datos y pruebas funcionales completas de extremo a extremo.

---

## Sesión 1 · Jueves, 26 de noviembre de 2026
**Foco:** Arquitectura de Base de Datos, Capa POO, Autenticación Segura y Núcleo de Sesiones

### Actividades Realizadas
1. **Diseño e Implementación de Base de Datos (`inventra_db`):**
   - Estructuración del esquema relacional en Tercera Forma Normal (3FN) con codificación `utf8mb4` compuesto por 7 tablas: `categorias`, `usuarios`, `proveedores`, `productos`, `producto_proveedor` (relación N:M), `movimientos` y `auditoria`.
   - Incorporación del campo `ubicacion` en la tabla `productos` para responder al cambio de requerimiento operativo de bodega.
   - Carga de datos semilla (`PR-0511` a `PR-0515`), catálogos base y credenciales de prueba con contraseñas encriptadas (`password_hash`).
2. **Procedimientos Almacenados en MySQL:**
   - `sp_registrar_movimiento`: Procedimiento transaccional (`START TRANSACTION` / `COMMIT` / `ROLLBACK`) que registra entradas o salidas y actualiza el saldo en `productos` de forma atómica, validando disponibilidad ante concurrencia.
   - `sp_reporte_stock_bajo_ubicacion`: Procedimiento de consulta que filtra artículos con `stock < minimo` y los ordena por su ubicación física en bodega.
3. **Capa de Acceso a Datos y Seguridad (`clases/` e `includes/`):**
   - Implementación de `clases/Conexion.php` con PDO en modo excepciones (`PDO::ERRMODE_EXCEPTION`), consultas preparadas nativas (`EMULATE_PREPARES => false`) y captura segura de errores sin exponer credenciales.
   - Implementación de `clases/Auditoria.php` para registrar trazabilidad (`id_usuario`, `accion`, `tabla_afectada`, `id_registro`, `detalle` y marca de tiempo).
   - Desarrollo de modelos POO `clases/Producto.php` y `clases/Proveedor.php` con búsquedas parametrizadas, lista blanca en `ORDER BY`, paginación (`LIMIT`/`OFFSET`) y manejo de integridad referencial.
   - Actualización de `includes/funciones.php` incorporando:
     - `e()`: escape seguro de salida HTML (`htmlspecialchars` con `ENT_QUOTES`, `UTF-8`) contra XSS.
     - `limpiarEntrada()`: saneamiento previo a persistencia sin doble codificación en BD.
     - `verificarAcceso()`: middleware RBAC en servidor por lista blanca de roles.
     - `determinarEstadoVencimiento()`: regla de negocio para clasificar productos en *Vigente*, *Por vencer* ($\le 30$ días) y *Vencido*.
4. **Módulo de Sesiones:**
   - Migración de `procesar_inicio_sesion.php` con `password_verify()`, `session_regenerate_id(true)` y registro de eventos `LOGIN_OK` / `LOGIN_FALLIDO`.
   - Actualización de `cerrar-sesion.php` con auditoría `LOGOUT`, limpieza de cookies (`SameSite=Lax`) y cabeceras `Cache-Control: no-store`.

---

## Sesión 2 · Viernes, 27 de noviembre de 2026
**Foco:** Migración Completa de Vistas CRUD, Movimientos Transaccionales, Dashboards por Rol y Pruebas de Integración

### Actividades Realizadas
1. **Módulo de Productos e Inventario (`gestion-productos.php`, `formulario-producto.php`, `procesar_producto.php`, `detalle-producto.php`, `listado-inventario.php`):**
   - Conexión completa del catálogo y existencias vigentes con MySQL, eliminando la dependencia de `config/datos_demo.php`.
   - Implementación de alta, edición y eliminación exclusivamente vía `POST` (prevención de CSRF por enlaces `GET`), conservando la regla `RB-01` (el stock no se edita manualmente desde el formulario).
   - Integración de vista de **Reporte de stock bajo por ubicación** (`?stock_bajo=1`) invocando `CALL sp_reporte_stock_bajo_ubicacion(...)`.
   - En `detalle-producto.php`, incorporación de consultas con `JOIN` sobre `producto_proveedor` (proveedores vinculados con precio de compra y tiempo de entrega) y sobre `movimientos` + `usuarios` (historial real del artículo).
2. **Módulo de Entradas, Salidas e Historial (`recepcion-mercancia.php`, `procesar_recepcion.php`, `registro-salida.php`, `procesar_salida.php`, `historial-movimientos.php`):**
   - Creación de `procesar_recepcion.php` y actualización de `recepcion-mercancia.php` para registrar múltiples líneas de mercancía recibida de proveedores activos mediante `CALL sp_registrar_movimiento(..., 'entrada', ...)`, garantizando que no se guarde stock parcial ante errores.
   - Migración de `registro-salida.php` y `procesar_salida.php` invocando `CALL sp_registrar_movimiento(..., 'salida', ...)`, manteniendo el bloqueo estricto de salida para productos vencidos y la validación de saldo insuficiente.
   - Migración de `historial-movimientos.php` con doble `JOIN` (`movimientos`, `productos`, `categorias`, `usuarios`), filtros dinámicos por fecha, producto, tipo y usuario, paginación y exportación directa a archivo CSV compatible con Excel (UTF-8 BOM).
3. **Módulo de Proveedores (`gestion-proveedores.php`, `formulario-proveedor.php`, `procesar_proveedor.php`):**
   - Implementación del CRUD completo de proveedores con modo consulta (`?modo=ver`), edición, activación/desactivación y eliminación vía `POST` con captura amigable de restricciones de clave foránea (`SQLSTATE 23000`).
4. **Dashboards por Rol y Navegación (`dashboard-administrador.php`, `dashboard-encargado.php`, `dashboard-recepcionista.php`, `includes/menu.php`):**
   - Reemplazo de indicadores estáticos por métricas reales en tiempo real (productos registrados, alertas de stock bajo ordenadas por ubicación, alertas de caducidad, entradas/salidas del día en zona horaria `America/Panama` y movimientos recientes).
   - Incorporación de vista previa de la tabla `auditoria` en `dashboard-administrador.php`.
   - Sincronización de `includes/menu.php` con los permisos de cada rol y estados activos de navegación.

---

## Pruebas Funcionales y de Seguridad Ejecutadas (27 de noviembre de 2026)

| Caso de Prueba | Módulo / Archivo | Resultado Obtenido | Estado |
| :--- | :--- | :--- | :---: |
| **Control de acceso por rol (RBAC)** | `includes/funciones.php`, `includes/menu.php` | Bloqueo de acceso cruzado en servidor para rutas no autorizadas según rol (`administrador`, `encargado`, `recepcionista`). | Aprobado |
| **Recepción de mercancía (Entrada)** | `recepcion-mercancia.php` → `procesar_recepcion.php` | Incremento atómico de stock vía `sp_registrar_movimiento`, correlativo `REC-000X` y registro `ENTRADA_INVENTARIO` en `auditoria`. | Aprobado |
| **Bloqueo de salida por vencimiento** | `registro-salida.php` → `procesar_salida.php` | Rechazo de retiro de `Queso fresco (kg) / PR-0513` (vencido el `01/11/2026`) con alerta de error y saldo intacto en MySQL. | Aprobado |
| **Salida válida y alerta de stock bajo** | `procesar_salida.php` → `gestion-productos.php?stock_bajo=1` | Retiro de 2 frascos de `Salsa BBQ & chipotle (PR-0512)` (stock `4 → 2`, mínimo `6`), activación de etiqueta `Stock bajo` y listado en reporte por ubicación. | Aprobado |
| **Prevención XSS y caracteres especiales** | `includes/funciones.php` (`e()`) | Renderizado correcto de caracteres especiales (`&`) sin doble codificación en BD ni ejecución de etiquetas HTML/JS. | Aprobado |
| **Auditoría de operaciones** | `dashboard-administrador.php`, `clases/Auditoria.php` | Registro verificable de inicios/cierres de sesión, altas, ediciones, eliminaciones y movimientos con usuario y fecha/hora. | Aprobado |

---

## Próximos Pasos (Cierre Documental y Git)
- [ ] Verificar archivo `.gitignore` y plantilla `config/config.example.php` sin credenciales reales.
- [ ] Crear script `sql/usuario.sql` con usuario dedicado de MySQL y privilegios mínimos (`SELECT, INSERT, UPDATE, DELETE, EXECUTE`).
- [ ] Exportar respaldo final `sql/inventra_db.sql` incluyendo estructura, datos semilla y procedimientos almacenados.
- [ ] Consolidar entregables documentales en `docs/` (`diagrama_er.png`, `respaldo.md`, `pruebas_seguridad.pdf`, `matriz_responsabilidades.pdf`, `informe_final.pdf`) y `README.md`.
- [ ] Completar distribución de commits en ramas individuales (`int1`–`int4`) y revisión cruzada de Pull Requests hacia `main`.