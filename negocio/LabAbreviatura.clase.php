<?php

require_once '../datos/Conexion.clase.php';

class LabAbreviatura extends Conexion {
    public $id_lab_abreviatura;
    public $descripcion;
    
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

    public function buscarCombo($cadena_buscar = ""){
        try {
            $sql = "SELECT 
                        id_lab_abreviatura as id,
                        descripcion as text
                    FROM lab_abreviatura
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
                        id_lab_abreviatura as id,
                        descripcion
                    FROM lab_abreviatura
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

            if ($this->id_lab_abreviatura == NULL){
                $this->insert("lab_abreviatura", $campos_valores);
                $this->id_lab_abreviatura = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_lab_abreviatura"=>$this->id_lab_abreviatura
                ];

                $this->update("lab_abreviatura", $campos_valores, $campos_valores_where);
            }

            
            $sql = "SELECT 
                        id_lab_abreviatura as id,
                        descripcion
                    FROM lab_abreviatura
                    WHERE estado_mrcb AND id_lab_abreviatura = :0";
            $registro = $this->consultarFila($sql, [$this->id_lab_abreviatura]);

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", "registro" => $registro);

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
                "id_lab_abreviatura"=>$this->id_lab_abreviatura
            ];

            $this->update("lab_abreviatura", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_lab_abreviatura,
                        descripcion
                    FROM lab_abreviatura
                    WHERE estado_mrcb AND id_lab_abreviatura = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_lab_abreviatura);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}