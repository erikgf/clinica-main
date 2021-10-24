<?php

require_once '../datos/Conexion.clase.php';

class Especialidad extends Conexion {
    
    public function listar(){
        try {
            $sql = "SELECT 
                        id_especialidad_medico as id,
                        descripcion
                    FROM especialidad_medico
                    WHERE estado_mrcb";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}