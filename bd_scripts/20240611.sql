CREATE TABLE entrega_sobre(
    id_entrega_sobre SERIAL PRIMARY KEY,
    id_medico INT NOT NULL,
    id_promotora INT NOT NULL,
    mes_principal CHAR(2) NOT NULL,
    anio_principal CHAR(4) NOT NULL,
	fecha_entregado DATE NULL,
    fecha_aceptado DATE NULL,
    observaciones VARCHAR(400) NULL,
    id_usuario_registrado INT NOT NULL,
    fecha_hora_registrado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    fecha_hora_eliminado TIMESTAMP NULL,
    INDEX(id_medico, id_promotora),
    INDEX(fecha_hora_eliminado)
);

CREATE TABLE entrega_sobre_detalle(
    id_entrega_sobre_detalle SERIAL PRIMARY KEY,
    id_entrega_sobre INT NOT NULL,
    mes CHAR(2) NOT NULL,
    anio CHAR(4) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    es_registro_principal BOOLEAN NOT NULL DEFAULT false,
    INDEX(id_entrega_sobre)
);


ALTER TABLE `liquidacion_detalle` ADD `entregado` CHAR(1) NOT NULL DEFAULT '0' AFTER `cantidad_servicios`;
ALTER TABLE `entrega_sobre_detalle` ADD `fecha_hora_eliminado` TIMESTAMP NULL DEFAULT NULL AFTER `es_registro_principal`;
INSERT INTO `interfaz` (`id_interfaz`, `rotulo`, `url`, `icon`, `id_interfaz_padre`, `numero_orden`, `es_padre`) VALUES
(107, 'Gestión Sobres Liquidaciones', 'gestion-sobres-liquidaciones', NULL, 0, 0, 0);