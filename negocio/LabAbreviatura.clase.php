<?php

require_once '../datos/Conexion.clase.php';

class LabAbreviatura extends Conexion {
    
    public function obtenerAbreviaturas($id_atencion_medica_servicio = null){
        try {

            $sqlWhere = " true ";
            if ($id_atencion_medica_servicio != null){
                $sqlWhere = " id_lab_seccion IN (SELECT le.id_lab_seccion 
                                FROM atencion_medica_servicio ams 
                                INNER JOIN lab_examen le ON le.id_servicio = ams.id_servicio 
                                WHERE le.estado_mrcb AND ams.id_atencion_medica_servicio = ".$id_atencion_medica_servicio.")";
            }

            $sql = "SELECT 
                        distinct descripcion
                    FROM lab_abreviatura
                    WHERE estado_mrcb AND $sqlWhere
                    ORDER BY descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}