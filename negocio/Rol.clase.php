<?php

require_once '../datos/Conexion.clase.php';

class Rol extends Conexion {
    public static $ID_ADMINISTRADOR = "1";

    public $id_rol;
    public $descripcion;
    public $es_gestion_descuentos;
    public $es_gestion_cajas;
    public $interfaces;

    public function listar(){
        try {
            $sql = "SELECT 
                        r.id_rol as id,
                        r.descripcion,
                        COALESCE((SELECT url from rol_interfaz ri
                            INNER JOIN interfaz i ON i.id_interfaz = ri.id_interfaz
                            WHERE ri.id_rol = r.id_rol
                            LIMIt 1),'') as nombre_interfaz,
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
            ];

            $campos_valores_where = [
                "id_rol"=>$this->id_rol
            ];

            $this->update("rol", $campos_valores, $campos_valores_where);

            $this->delete("rol_interfaz", ["id_rol"=>$this->id_rol]);

            foreach ($this->interfaces as $key => $id_interfaz) {
                $this->insert("rol_interfaz",["id_rol"=>$this->id_rol, "id_interfaz"=> $id_interfaz]);
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
                        r.id_rol,
                        r.descripcion
                    FROM rol r
                    WHERE r.estado_mrcb AND r.id_rol = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_rol);

            $sql = "SELECT i.id_interfaz, 
                    i.rotulo,
                    ri.id_interfaz IS NOT NULL as active
                    FROM interfaz i
                    LEFT JOIN rol_interfaz ri ON i.id_interfaz = ri.id_interfaz AND  ri.id_rol IN (:0)
                    ORDER BY i.id_interfaz";

            $interfaces = $this->consultarFilas($sql, $this->id_rol);
            $data["interfaces"] = $interfaces;

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerCombo(){
        try {
            $sql = "SELECT 
                        r.id_rol as id,
                        r.descripcion
                    FROM rol r
                    WHERE r.estado_mrcb
                    ORDER BY descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }   
}