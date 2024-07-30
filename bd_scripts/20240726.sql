CREATE TABLE sub_categoria_servicio(
   id_sub_categoria_servicio SERIAL PRIMARY KEY,
    descripcion VARCHAR(200) NOT NULL,
    estado_mrcb BOOLEAN not null default true
 );

 ALTER TABLE `servicio` ADD `id_sub_categoria_servicio` INT NULL DEFAULT NULL AFTER `cantidad_examenes`, ADD INDEX (`id_sub_categoria_servicio`);