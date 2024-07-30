ALTER TABLE `bitacora_servicio` ADD `id_sub_categoria_servicio` INT NULL DEFAULT NULL AFTER `cantidad_examenes`;
ALTER TABLE `medico` ADD `firma` VARCHAR(300) NULL DEFAULT NULL AFTER `id_rol`;
ALTER TABLE `bitacora_medico` ADD `firma` VARCHAR(300) NULL DEFAULT NULL;