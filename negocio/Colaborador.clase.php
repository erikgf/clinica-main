<?php

require_once '../datos/Conexion.clase.php';

class Colaborador extends Conexion {
    
    public $id_colaborador;
    public $numero_documento;
    public $nombres;
    public $apellido_paterno;
    public $apellido_materno;
    public $id_tipo_documento;
    public $correo;
    public $telefono;
    public $id_rol;

    public $clave;
    public $estado_acceso;

    public function listar(){
        try {
            $sql = "SELECT 
                        c.id_colaborador as id,
                        c.numero_documento,
                        COALESCE(c.apellido_paterno,' ',c.apellido_materno,', ',c.nombres) as nombres_apellidos,
                        c.id_tipo_documento,
                        c.correo,
                        c.telefono,
                        r.descripcion as rol
                    FROM colaborador c
                    INNER JOIN rol r ON c.id_rol = r.id_rol
                    WHERE c.estado_mrcb
                    ORDER BY apellido_paterno, apellido_materno, nombres";
                    
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
                "id_tipo_documento"=>$this->id_tipo_documento,
                "numero_documento"=>($this->numero_documento == "" ? NULL : $this->numero_documento),
                "apellido_paterno"=>mb_strtoupper($this->apellido_paterno,'UTF-8'),
                "apellido_materno"=>mb_strtoupper($this->apellido_materno,'UTF-8'),
                "nombres"=>mb_strtoupper($this->nombres,'UTF-8'),
                "correo"=>$this->correo,
                "telefono"=>$this->telefono,
                "id_rol"=>$this->id_rol
            ];


            if ($this->id_colaborador == NULL){
                $this->insert("colaborador", $campos_valores);
                $this->id_colaborador = $this->getLastID();

            } else {
                $campos_valores_where = [
                    "id_colaborador"=>$this->id_colaborador
                ];

                $this->update("colaborador", $campos_valores, $campos_valores_where);
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
                "id_colaborador"=>$this->id_colaborador
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
                        c.id_colaborador,
                        c.numero_documento,
                        c.apellido_paterno,
                        c.apellido_materno,
                        c.nombres,
                        c.id_tipo_documento,
                        c.correo,
                        c.telefono,
                        c.id_rol
                    FROM colaborador c
                    WHERE c.estado_mrcb AND c.id_colaborador = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_colaborador);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}