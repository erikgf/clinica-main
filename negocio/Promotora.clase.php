<?php

require_once '../datos/Conexion.clase.php';

class Promotora extends Conexion {
    
    public $id_promotora;
    public $numero_documento;
    public $descripcion;
    public $porcentaje_comision;
    public $id_usuario_registrado;

    public function listar(){
        try {
            $sql = "SELECT 
                        pr.id_promotora as id,
                        pr.numero_documento,
                        pr.descripcion,
                        COALESCE((SELECT porcentaje_comision 
                            FROM promotora_porcentaje_comision
                            WHERE estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL 
                            AND id_promotora = pr.id_promotora),'0.00') as porcentaje_comision
                    FROM promotora pr
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

            $fecha_ahora = date("Y-m-d H:i:s");

            $campos_valores = [
                "numero_documento"=>($this->numero_documento == "" ? NULL : $this->numero_documento),
                "descripcion"=>$this->descripcion
            ];

            if ($this->id_promotora == NULL){
                $this->insert("promotora", $campos_valores);
                $this->id_promotora = $this->getLastID();

                $porcentaje_comision_actual = -1;
            } else {
                $campos_valores_where = [
                    "id_promotora"=>$this->id_promotora
                ];

                $this->update("promotora", $campos_valores, $campos_valores_where);

                $sql = "SELECT porcentaje_comision 
                    FROM promotora_porcentaje_comision 
                    WHERE id_promotora = :0 AND estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL
                    LIMIT 1";

                $porcentaje_comision_actual = (float) $this->consultarValor($sql, [$this->id_promotora]);
            }

            if ((float) $this->porcentaje_comision != $porcentaje_comision_actual){
                $hoy = date("Y-m-d");

                if ($porcentaje_comision_actual >= 0){
                    //es un registro nuevo
                    $campos_valores = [
                        "estado_validez"=>'I',
                        "fecha_fin"=>$hoy
                    ];

                    $campos_valores_where = [
                        "id_promotora"=>$this->id_promotora                        
                    ];

                    $this->update("promotora_porcentaje_comision", $campos_valores, $campos_valores_where);
                }
                
                $campos_valores = [
                    "estado_validez"=>'A',
                    "id_promotora"=>$this->id_promotora,
                    "fecha_inicio"=>$hoy,
                    "porcentaje_comision"=>$this->porcentaje_comision,
                    "id_usuario_registrado"=>$this->id_usuario_registrado,
                    "fecha_hora_registrado"=>$fecha_ahora
                ];

                $this->insert("promotora_porcentaje_comision", $campos_valores);
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
                "id_promotora"=>$this->id_promotora
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
                        pr.id_promotora,
                        pr.numero_documento,
                        pr.descripcion,
                        (SELECT porcentaje_comision 
                            FROM promotora_porcentaje_comision
                            WHERE estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL AND id_promotora = pr.id_promotora) as comision_promotora
                    FROM promotora pr
                    WHERE estado_mrcb AND id_promotora = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_promotora);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function asignarMedicosPromotora($arregloIdMedicos){
        try {
            $id_medicos_comas = implode(",", $arregloIdMedicos); 
            $sql = "UPDATE medico SET id_promotora = :0 WHERE id_medico IN (".$id_medicos_comas.")";
            $this->ejecutarSimple($sql, [$this->id_promotora]);
            return ["msj"=>count($arregloIdMedicos)." Médicos reasignados"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function quitarMedicosPromotora($arregloIdMedicos){
        try {
            $id_medicos_comas = implode(",", $arregloIdMedicos); 
            $sql = "UPDATE medico SET id_promotora = NULL WHERE id_medico IN (".$id_medicos_comas.")";
            $this->ejecutarSimple($sql);
            return ["msj"=>count($arregloIdMedicos)." Médicos quitados"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}