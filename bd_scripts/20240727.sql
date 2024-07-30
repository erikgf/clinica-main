CREATE TABLE categoria_produccion_medico(
 	id_medico INTEGER NOT NULL,
    id_sub_categoria_servicio INTEGER NOT NULL,
    valor DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tipo_valor CHAR(1) NOT NULL,
    INDEX(id_medico, id_sub_categoria_servicio)
 );