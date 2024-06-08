CREATE TABLE informe(
    id_informe SERIAL PRIMARY KEY,
    id_atencion_medica_servicio INT NOT NULL,
    contenido_informe TEXT NULL,
    numero_orden_dia INT DEFAULT 0 NOT NULL,
    fecha_hora_registrado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    fecha_hora_actualizado TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP(),
    fecha_hora_eliminado TIMESTAMP NULL,
    id_usuario_registrado INT NOT NULL,
    id_usuario_actualizado INT NULL,
    id_usuario_eliminado INT NULL,
    INDEX(id_atencion_medica_servicio)
);

CREATE TABLE informe_bitacora(
    id_informe_bitacora SERIAL PRIMARY KEY,
    id_informe INT NOT NULL,
    contenido TEXT NOT NULL,
    id_usuario_registrado INT NOT NULL,
    fecha_hora_registrado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tipo_registro CHAR(1) NOT NULL DEFAULT 'R' COMMENT 'R: Regular (bitácora normal), B: Borrador'
);


ALTER TABLE `informe_bitacora` ADD `fecha_hora_eliminado` TIMESTAMP NULL DEFAULT NULL AFTER `fecha_hora_registrado`;

INSERT INTO `interfaz` (`id_interfaz`, `rotulo`, `url`, `icon`, `id_interfaz_padre`, `numero_orden`, `es_padre`) VALUES (NULL, 'Informes de Exámenes', 'informes-examenes', NULL, '0', '0', '0');

INSERT INTO `rol` (`id_rol`, `descripcion`, `interfaz_inicio_sesion`, `es_gestion_cajas`, `es_gestion_descuentos`, `estado_mrcb`) VALUES (NULL, 'MÉDICO', 'informes-examenes', '0', '0', '1');


ALTER TABLE `usuario` ADD `id_medico` INT NULL AFTER `id_promotora`, ADD INDEX (`id_medico`);

ALTER TABLE `bitacora_medico` ADD `puede_tener_usuario` BOOLEAN NULL DEFAULT FALSE AFTER `id_sede`;
ALTER TABLE `medico` ADD `puede_tener_usuario` BOOLEAN NULL DEFAULT FALSE AFTER `fecha_nacimiento`;

ALTER TABLE `medico` ADD `id_rol` INT NOT NULL DEFAULT '16' AFTER `puede_tener_usuario`;