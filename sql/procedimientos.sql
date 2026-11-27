DELIMITER //

-- 1. Procedimiento almacenado con transacción: registrar entrada o salida de inventario
DROP PROCEDURE IF EXISTS sp_registrar_movimiento //
CREATE PROCEDURE sp_registrar_movimiento(
    IN p_id_producto INT,
    IN p_id_usuario INT,
    IN p_tipo VARCHAR(10),
    IN p_cantidad INT,
    IN p_motivo VARCHAR(200)
)
BEGIN
    DECLARE v_stock_actual INT DEFAULT 0;
    DECLARE v_vence DATE DEFAULT NULL;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

        SELECT stock, vence INTO v_stock_actual, v_vence
        FROM productos
        WHERE id_producto = p_id_producto
        FOR UPDATE;

        IF p_tipo = 'salida' THEN
            IF v_vence IS NOT NULL AND v_vence < CURDATE() THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'No se permite registrar salida de un producto caducado.';
            END IF;

            IF v_stock_actual < p_cantidad THEN
                SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Stock insuficiente para realizar la salida.';
            END IF;

            UPDATE productos
            SET stock = stock - p_cantidad
            WHERE id_producto = p_id_producto;
        ELSE
            UPDATE productos
            SET stock = stock + p_cantidad
            WHERE id_producto = p_id_producto;
        END IF;

        INSERT INTO movimientos (id_producto, id_usuario, tipo, cantidad, motivo)
        VALUES (p_id_producto, p_id_usuario, p_tipo, p_cantidad, p_motivo);

        INSERT INTO auditoria (id_usuario, accion, tabla_afectada, id_registro, detalle)
        VALUES (p_id_usuario, CONCAT('MOVIMIENTO_', UPPER(p_tipo)), 'productos', p_id_producto, p_motivo);

    COMMIT;
END //

-- 2. Procedimiento almacenado de reporte: productos con stock bajo ordenados por ubicación
DROP PROCEDURE IF EXISTS sp_reporte_stock_bajo_ubicacion //
CREATE PROCEDURE sp_reporte_stock_bajo_ubicacion(
    IN p_zona VARCHAR(80)
)
BEGIN
    SELECT 
        p.id_producto,
        p.codigo,
        p.nombre,
        c.nombre AS categoria,
        c.unidad_medida,
        p.stock,
        p.minimo,
        (p.minimo - p.stock) AS faltante,
        p.ubicacion,
        p.vence
    FROM productos p
    INNER JOIN categorias c ON p.id_categoria = c.id_categoria
    WHERE p.stock < p.minimo
      AND (p_zona = '' OR p.ubicacion LIKE CONCAT('%', p_zona, '%'))
    ORDER BY p.ubicacion ASC, p.codigo ASC;
END //

DELIMITER ;