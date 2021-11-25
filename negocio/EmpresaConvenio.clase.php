<?php

require_once '../datos/Conexion.clase.php';

class EmpresaConvenio extends Conexion {
    public $id_empresa_convenio;
    public $numero_documento;
    public $razon_social;
    public $fecha_alta;
    public $fecha_baja;
    public $estado;
    
    public function listar(){
        try {
            $sql = "SELECT 
                        id_empresa_convenio as id,
                        COALESCE(numero_documento, 'No registrado') as numero_documento,
                        razon_social,
                        fecha_alta,
                        COALESCE(fecha_baja,'-') as fecha_baja,
                        estado,
                        IF(estado = 'A','ACTIVO','INACTIVO') as estado_rotulo
                    FROM empresa_convenio
                    WHERE estado_mrcb
                    ORDER BY razon_social";
                    
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
                "razon_social"=>$this->razon_social,
                "numero_documento"=>($this->numero_documento === "" ? NULL : $this->numero_documento)
            ];

            if ($this->id_empresa_convenio == NULL){
                $campos_valores["fecha_alta"] = date("Y-m-d H:i:s");
                $campos_valores["estado"] = 'A';
                $this->insert("empresa_convenio", $campos_valores);
                $this->id_empresa_convenio = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_empresa_convenio"=>$this->id_empresa_convenio
                ];

                $this->update("empresa_convenio", $campos_valores, $campos_valores_where);
            }

            $this->commit();

            $sql = "SELECT  id_empresa_convenio as id,
                    COALESCE(numero_documento, 'No registrado') as numero_documento,
                    razon_social,
                    fecha_alta,
                    COALESCE(fecha_baja,'-') as fecha_baja
                    FROM empresa_convenio
                    WHERE estado_mrcb AND id_empresa_convenio = :0";
            $registro = $this->consultarFila($sql, [$this->id_empresa_convenio]);

            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", "registro"=>$registro);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function darBaja(){
        try {
            $campos_valores = [
                "estado" => $this->estado
            ];

            if ($this->estado == 'A'){
                $campos_valores["fecha_alta"] = date("Y-m-d H:i:s");
                $campos_valores["fecha_baja"] = NULL;
            } else {
                $campos_valores["fecha_baja"] =  date("Y-m-d H:i:s");
            }

            $campos_valores_where = [
                "id_empresa_convenio"=>$this->id_empresa_convenio
            ];

            $this->update("empresa_convenio", $campos_valores, $campos_valores_where);
            
            $sql = "SELECT  id_empresa_convenio as id,
                    COALESCE(numero_documento, 'No registrado') as numero_documento,
                    razon_social,
                    fecha_alta,
                    COALESCE(fecha_baja,'-') as fecha_baja,
                    estado,
                    IF(estado = 'A','ACTIVO','INACTIVO') as estado_rotulo
                    FROM empresa_convenio
                    WHERE estado_mrcb AND id_empresa_convenio = :0";
            $registro = $this->consultarFila($sql, [$this->id_empresa_convenio]);

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
                "id_empresa_convenio"=>$this->id_empresa_convenio
            ];

            $this->update("empresa_convenio", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        id_empresa_convenio,
                        numero_documento,
                        razon_social,
                        estado
                    FROM empresa_convenio
                    WHERE estado_mrcb AND id_empresa_convenio = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_empresa_convenio);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerCombo(){
        try {
            $sql = "SELECT 
                        id_empresa_convenio as id,
                        razon_social as descripcion
                    FROM empresa_convenio
                    WHERE estado_mrcb AND estado = 'A'
                    ORDER BY razon_social";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}