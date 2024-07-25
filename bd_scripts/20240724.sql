ALTER TABLE `servicio` ADD `arreglo_paquete` TEXT NULL DEFAULT NULL AFTER `arreglo_perfil`;
ALTER TABLE `bitacora_servicio` ADD `arreglo_paquete` TEXT NULL DEFAULT NULL AFTER `arreglo_perfil`;
ALTER TABLE `atencion_medica_servicio` ADD `id_paquete` INT NULL DEFAULT NULL AFTER `numero_impresiones_laboratorio`, ADD INDEX (`id_am_paquete`);
ALTER TABLE `atencion_medica_servicio` ADD `es_atendible` BOOLEAN NOT NULL DEFAULT TRUE AFTER `monto_comision_categoria_sin_igv`, ADD INDEX (`es_atendible`);