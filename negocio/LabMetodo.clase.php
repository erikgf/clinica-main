<?php

require_once '../datos/Conexion.clase.php';

class LabMetodo extends Conexion {
    public $id_lab_metodo;
    public $descripcion;

    public function buscarCombo($cadena_buscar = ""){
        try {
            $sql = "SELECT 
                        id_lab_metodo as id,
                        descripcion as text
                    FROM lab_metodo
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
                        id_lab_metodo as id,
                        descripcion
                    FROM lab_metodo
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

            if ($this->id_lab_metodo == NULL){
                $this->insert("lab_metodo", $campos_valores);
                $this->id_lab_metodo = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_lab_metodo"=>$this->id_lab_metodo
                ];

                $this->update("lab_metodo", $campos_valores, $campos_valores_where);
            }

            $sql = "SELECT 
                        id_lab_metodo as id,
                        descripcion
                    FROM lab_metodo
                    WHERE estado_mrcb AND id_lab_metodo = :0";
            $registro = $this->consultarFila($sql, [$this->id_lab_metodo]);

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", "registro"=>$registro);

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
                "id_lab_metodo"=>$this->id_lab_metodo
            ];

            $this->update("lab_metodo", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_lab_metodo,
                        descripcion
                    FROM lab_metodo
                    WHERE estado_mrcb AND id_lab_metodo = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_lab_metodo);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}