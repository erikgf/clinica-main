<?php

require_once '../datos/Conexion.clase.php';

class CategoriaServicio extends Conexion {

    public function obtenerActivos(){
        try {

            $sql = "SELECT  id_categoria_servicio as id, 
                    cat.descripcion as categoria
                    FROM categoria_servicio cat
                    WHERE cat.estado_mrcb ";
                    
            $servicios =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$servicios);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    public function buscar($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_categoria_servicio as id,
                    descripcion as text
                    FROM categoria_servicio
                    WHERE estado_mrcb AND descripcion LIKE '%".$cadenaBuscar."%' 
                    LIMIT 6";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}