<?php

require_once '../datos/Conexion.clase.php';

class Servicio extends Conexion {

    public $id_categoria_servicio;

    public function listar(){
        try {

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as servicio, 
                    se.id_categoria_servicio as categoria,
                    precio_unitario as precioUnitario,
                    precio_venta_sin_igv
                    FROM servicio se 
                    INNER JOIN  categoria_servicio cat ON se.id_categoria_servicio =  cat.id_categoria_servicio
                    WHERE se.estado_mrcb ";
                    
            $servicios =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$servicios);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    } 

    public function buscar($cadenaBuscar){
        try {
            $params = [];
            $sqlCategoriaServicio  = "true";
            if ($this->id_categoria_servicio != NULL && $this->id_categoria_servicio != ""){
                $params = [$this->id_categoria_servicio];
                $sqlCategoriaServicio  = "id_categoria_servicio = :0";
            }

            $sql = "SELECT 
                    id_servicio as id,
                    CONCAT(descripcion,' - S/', precio_unitario) as text
                    FROM servicio
                    WHERE estado_mrcb AND precio_unitario > 0.00 AND descripcion LIKE '%".$cadenaBuscar."%' AND ".$sqlCategoriaServicio."
                    LIMIT 10";
            $data =  $this->consultarFilas($sql, $params);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerActivos(){
        try {

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as servicio, 
                    se.id_categoria_servicio as categoria,
                    precio_unitario as precioUnitario,
                    precio_venta_sin_igv
                    FROM servicio se 
                    WHERE se.estado_mrcb ";
                    
            $servicios =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$servicios);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    } 

}