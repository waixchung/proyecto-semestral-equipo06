-- ==========================================================
-- HITO #3 - INVENTRA (Equipo 06)
-- Script: sql/seed.sql
-- ==========================================================
SET NAMES utf8mb4;

-- 1. Usuarios de prueba (Contraseña para todos: P4$$w0rd2026.)
INSERT INTO usuarios (nombre, correo, contrasena, rol) VALUES
('Waix', 'wai.chung@utp.ac.pa', '$2y$10$FAEtXbsrQMjR8gFa93w/DuSQ.qjTnDk5gDkFT53ng5XdjtP1/foXa', 'administrador'),
('Luis Pérez', 'encargado@ejemplo.com', '$2y$10$FAEtXbsrQMjR8gFa93w/DuSQ.qjTnDk5gDkFT53ng5XdjtP1/foXa', 'encargado'),
('Ana Gómez', 'recepcionista@ejemplo.com', '$2y$10$FAEtXbsrQMjR8gFa93w/DuSQ.qjTnDk5gDkFT53ng5XdjtP1/foXa', 'recepcionista');

-- Nota: Si deseas generar tus propios hashes en PHP, usa: password_hash('P4$$w0rd2026.', PASSWORD_DEFAULT)

-- 2. Categorías requeridas por los registros semilla
INSERT INTO categorias (nombre, unidad_medida, descripcion) VALUES
('Aceites', 'L', 'Aceites vegetales y grasas para cocina'),
('Salsas', 'frasco', 'Aderezos, salsas picantes y condimentos líquidos'),
('Lácteos', 'kg', 'Quesos, cremas y productos refrigerados'),
('Frutas', 'unidad', 'Frutas frescas de temporada'),
('Verduras', 'kg', 'Hortalizas, tubérculos y vegetales frescos');

-- 3. Proveedores iniciales
INSERT INTO proveedores (codigo, nombre_empresa, contacto, telefono, correo, direccion) VALUES
('PV-001', 'Distribuidora El Istmo S.A.', 'Carlos Mendoza', '6612-3456', 'ventas@elistmo.pa', 'Vía Tocumen, Bodega 4, Panamá'),
('PV-002', 'Agropecuaria Tierras Altas', 'María Quintero', '6789-1234', 'pedidos@tierrasaltas.pa', 'Mercadito Central, Nave B, Panamá'),
('PV-003', 'Lácteos y Sabores del Sur', 'Roberto Díaz', '6234-9876', 'contacto@lacteossur.pa', 'Parque Industrial Río Abajo, Panamá');

-- 4. Cinco registros semilla dictados en clase (PR-0511 a PR-0515) + ubicación asignada
INSERT INTO productos (codigo, nombre, id_categoria, stock, minimo, precio, vence, ubicacion) VALUES
('PR-0511', 'Aceite vegetal (L)', 1, 15, 5, 3.75, '2027-04-10', 'Zona A - Estante 1'),
('PR-0512', 'Salsa BBQ & chipotle (frasco)', 2, 3, 6, 4.50, '2026-12-10', 'Zona A - Estante 2'),
('PR-0513', 'Queso fresco (kg)', 3, 4, 3, 6.25, '2026-11-01', 'Zona B - Refrigeración 1'),
('PR-0514', 'Piña <b>dulce</b> (unidad)', 4, 0, 10, 2.00, '2026-12-05', 'Zona C - Frescos 1'),
('PR-0515', 'Ají chombo & ñame (kg)', 5, 8, 4, 1.85, '2026-12-20', 'Zona C - Frescos 2');

-- 5. Relación N:M producto_proveedor
INSERT INTO producto_proveedor (id_producto, id_proveedor, precio_compra, tiempo_entrega_dias) VALUES
(1, 1, 2.90, 2),
(2, 1, 3.40, 2),
(3, 3, 4.80, 1),
(4, 2, 1.25, 1),
(5, 2, 1.10, 1),
(1, 3, 3.05, 3);

-- 6. Movimientos iniciales de ejemplo
INSERT INTO movimientos (id_producto, id_usuario, tipo, cantidad, motivo) VALUES
(1, 2, 'entrada', 15, 'Abastecimiento inicial de inventario'),
(2, 2, 'salida', 3, 'Consumo en cocina turno almuerzo');