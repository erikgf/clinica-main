<?php

require_once '../datos/Conexion.clase.php';

class Rol extends Conexion {
    public static $ID_ADMINISTRADOR = "1";

    public $id_rol;
    public $descripcion;
    public $es_gestion_descuentos;
    public $es_gestion_cajas;

    public function listar(){
        try {
            $sql = "SELECT 
                        r.id_rol as id,
                        r.descripcion,
                        IF(r.es_gestion_descuentos = '1', 'SÍ', 'NO') as es_gestion_descuentos,
                        IF(r.es_gestion_cajas = '1', 'SÍ', 'NO') as es_gestion_cajas
                    FROM rol r
                    WHERE r.estado_mrcb
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

            $fecha_ahora = date("Y-m-d H:i:s");

            $campos_valores = [
                "descripcion"=>mb_strtoupper($this->descripcion == "" ? NULL : $this->descripcion,'UTF-8'),
                "es_gestion_descuentos"=>$this->es_gestion_descuentos,
                "es_gestion_cajas"=>$this->es_gestion_cajas
            ];

            if ($this->id_rol == NULL){
                $this->insert("rol", $campos_valores);
                $this->id_rol = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_rol"=>$this->id_rol
                ];

                $this->update("rol", $campos_valores, $campos_valores_where);
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
                "id_rol"=>$this->id_rol
            ];

            $this->update("promotora", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
                    $sql = "SELECT 
                        c.id_rol,
                        c.descripcion,
                        c.es_gestion_descuentos,
                        c.es_gestion_cajas
                    FROM rol r
                    WHERE c.estado_mrcb AND c.id_rol = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_rol);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}