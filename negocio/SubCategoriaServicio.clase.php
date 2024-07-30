<?php

require_once '../datos/Conexion.clase.php';

class SubCategoriaServicio extends Conexion {
    public $id_sub_categoria_servicio;
    public $descripcion;

    public function listar(){
        try {
            $sql = "SELECT 
                        id_sub_categoria_servicio as id,
                        descripcion
                        FROM sub_categoria_servicio
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

            if ($this->id_sub_categoria_servicio == NULL){
                $this->insert("sub_categoria_servicio", $campos_valores);
                $this->id_sub_categoria_servicio = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio
                ];

                $this->update("sub_categoria_servicio", $campos_valores, $campos_valores_where);
            }

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", "registro"=>$this->leer($this->id_sub_categoria_servicio));
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
                "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio
            ];

            $this->update("sub_categoria_servicio", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_sub_categoria_servicio as id,
                        descripcion
                    FROM sub_categoria_servicio
                    WHERE estado_mrcb AND id_sub_categoria_servicio = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_sub_categoria_servicio);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}