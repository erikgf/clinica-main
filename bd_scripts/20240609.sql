DROP TABLE IF EXISTS liquidacion;
CREATE TABLE liquidacion(
	id_liquidacion SERIAL PRIMARY KEY,
    anio CHAR(4) NOT NULL,
    mes CHAR(2) NOT NULL,
    id_sede INTEGER NOT NULL,
    id_promotora INTEGER NOT NULL,
    porcentaje_promotora DECIMAL(5,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    id_usuario_registrado INTEGER NOT NULL,
    fecha_hora_registrado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
    INDEX(id_promotora),
    INDEX(id_sede)
);

DROP TABLE IF EXISTS  liquidacion_detalle;
CREATE TABLE liquidacion_detalle(
	id_liquidacion_detalle SERIAL PRIMARY KEY,
    id_liquidacion INTEGER NOT NULL,
    id_medico INTEGER NOT NULL,
    monto_sin_igv DECIMAL(10,2),
    comision_con_igv DECIMAL(10,2),
    comision_sin_igv DECIMAL(10,2),
    cantidad_servicios INTEGER,
    INDEX(id_medico),
    INDEX(id_liquidacion)
);