<?php

require_once '../datos/Conexion.clase.php';

class LabUnidad extends Conexion {
    public $id_lab_unidad;
    public $descripcion;

    public function buscarCombo($cadena_buscar = ""){
        try {
            $sql = "SELECT 
                        id_lab_unidad as id,
                        descripcion as text
                    FROM lab_unidad
                    WHERE estado_mrcb AND descripcion LIKE '%".$cadena_buscar."%'
                    ORDER BY descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listar(){
        try {
            $sql = "SELECT 
                        id_lab_unidad as id,
                        descripcion
                    FROM lab_unidad
                    WHERE estado_mrcb
                    ORDER BY descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function guardar(){
        try {

            $this->beginTransaction();

            $campos_valores = [
                "descripcion"=>$this->descripcion
            ];

            if ($this->id_lab_unidad == NULL){
                $this->insert("lab_unidad", $campos_valores);
                $this->id_lab_unidad = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_lab_unidad"=>$this->id_lab_unidad
                ];

                $this->update("lab_unidad", $campos_valores, $campos_valores_where);
            }

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.");

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function anular(){
        try {
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_lab_unidad"=>$this->id_lab_unidad
            ];

            $this->update("lab_unidad", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_lab_unidad,
                        descripcion
                    FROM lab_unidad
                    WHERE estado_mrcb AND id_lab_unidad = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_lab_unidad);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}