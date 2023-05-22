<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/variables.php';

class Campaña extends Conexion {

    public $id_campaña;
    public $nombre;
    public $descuento_general;
    public $desripcion;
    public $fecha_inicio;
    public $fecha_fin;
    public $descuento_categorias_json;
    public $id_sede;

    public $id_usuario_registrado;

    public function obtener($id_caja = NULL){
        try {

            $dia = date("Y-m-d");

            $sql = "SELECT nombre, descuento_general, descuento_categorias_json, :1 as igv
                    FROM campaña
                    WHERE :0 BETWEEN fecha_inicio and fecha_fin and estado_mrcb 
                        AND id_sede IN (SELECT id_sede FROM caja WHERE id_caja = :2 AND estado_mrcb)
                    LIMIT 1";
                    
            $data =  $this->consultarFila($sql, [$dia, IGV, $id_caja]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listar(){
        try {
            $sql = "SELECT 
                        c.id_campaña,
                        c.nombre,
                        c.descripcion,
                        DATE_FORMAT(c.fecha_inicio, '%d-%m-%Y') as fecha_inicio,
                        DATE_FORMAT(c.fecha_fin, '%d-%m-%Y') as fecha_fin,
                        (CURRENT_DATE BETWEEN c.fecha_inicio AND c.fecha_fin) as estado
                    FROM campaña c
                    WHERE c.estado_mrcb
                    ORDER BY fecha_inicio DESC, fecha_fin";
                    
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
                "nombre"=>mb_strtoupper($this->nombre == "" ? NULL : $this->nombre,'UTF-8'),
                "descripcion"=>mb_strtoupper($this->descripcion == "" ? NULL : $this->descripcion,'UTF-8'),
                "descuento_general"=>$this->descuento_general,
                "fecha_fin"=>$this->fecha_fin,
                "fecha_inicio"=>$this->fecha_inicio,
                "id_sede"=>$this->id_sede,
                "descuento_categorias_json"=>$this->descuento_categorias_json,
                "id_usuario_registrado"=>$this->id_usuario_registrado
            ];

            if ($this->id_campaña == NULL){
                $this->insert("campaña", $campos_valores);
                $this->id_campaña = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_campaña"=>$this->id_campaña
                ];

                $this->update("campaña", $campos_valores, $campos_valores_where);
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
                "estado_mrcb"=>"0",
                "id_usuario_registrado"=>$this->id_usuario_registrado
            ];

            $campos_valores_where = [
                "id_campaña"=>$this->id_campaña
            ];

            $this->update("campaña", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
                    $sql = "SELECT 
                        c.id_campaña,
                        c.nombre,
                        c.id_sede,
                        c.descripcion,
                        c.descuento_categorias_json,
                        c.fecha_fin,
                        c.fecha_inicio
                    FROM campaña c
                    WHERE c.estado_mrcb AND c.id_campaña = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_campaña);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}