<?php

require_once '../datos/Conexion.clase.php';

class LabSeccion extends Conexion {
    public $id_lab_seccion;
    public $descripcion;
    
    public function obtenerCombo(){
        try {
            $sql = "SELECT 
                        id_lab_seccion as id,
                        descripcion
                    FROM lab_seccion
                    WHERE estado_mrcb
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
                        id_lab_seccion as id,
                        descripcion
                    FROM lab_seccion
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

            if ($this->id_lab_seccion == NULL){
                $this->insert("lab_seccion", $campos_valores);
                $this->id_lab_seccion = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_lab_seccion"=>$this->id_lab_seccion
                ];

                $this->update("lab_seccion", $campos_valores, $campos_valores_where);
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
                "id_lab_seccion"=>$this->id_lab_seccion
            ];

            $this->update("lab_seccion", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_lab_seccion,
                        descripcion
                    FROM lab_seccion
                    WHERE estado_mrcb AND id_lab_seccion = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_lab_seccion);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}