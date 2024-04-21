<?php

require_once '../datos/Conexion.clase.php';

class HistorialPaciente extends Conexion {
    public $id_paciente;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }
    public function listar(){
        try {

            $sql = "SELECT DATE_FORMAT(am.fecha_atencion,'%d-%m-%Y') as fecha_atencion, 
                            ams.nombre_servicio as examen,
                            ams.precio_unitario as precio,
                            m.nombres_apellidos as medico,
                            cs.descripcion as area,
                            cs.id_categoria_servicio as id_area
                    FROM atencion_medica am
                    INNER JOIN atencion_medica_servicio ams ON ams.id_atencion_medica = am.id_atencion_medica
                    INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio
                    INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = ser.id_categoria_servicio
                    LEFT JOIN medico m ON m.id_medico = ams.id_medico_atendido
                    WHERE am.estado_mrcb AND am.id_paciente = :0
                    ORDER BY cs.descripcion, am.fecha_atencion  DESC";

            $data = $this->consultarFilas($sql, [$this->id_paciente]);
            $data = Funciones::reagruparArregloPorKeys($data, ["id_area", "area"], "items");
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}

