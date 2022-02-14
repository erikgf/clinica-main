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
                        c.id_colaborador,
                        c.numero_documento,
                        CONCAT(c.apellido_paterno,' ',c.apellido_materno,', ',c.nombres) as nombres_apellidos,
                        c.id_tipo_documento,
                        c.correo,
                        c.telefono,
                        r.descripcion as rol,
                        u.estado_acceso
                    FROM colaborador c
                    INNER JOIN rol r ON c.id_rol = r.id_rol
                    LEFT JOIN usuario u ON u.id_colaborador = c.id_colaborador
                    WHERE c.estado_mrcb AND c.id_colaborador NOT IN ('1')
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
                "correo"=>$this->correo == "" ? NULL : $this->correo,
                "telefono"=>$this->telefono == "" ? NULL : $this->telefono,
                "id_rol"=>$this->id_rol
            ];

            if ($this->id_colaborador == NULL || $this->id_colaborador == ""){

                $sql = "SELECT COUNT(numero_documento) FROM colaborador WHERE numero_documento = :0 AND estado_mrcb";
                $existeRepetido = $this->consultarValor($sql, [$this->numero_documento]);

                if ($existeRepetido > 0){
                    throw new Exception("Ya existe el número de documento ".$this->numero_documento." en el SISTEMA.", 1);
                }

                $this->insert("colaborador", $campos_valores);
                $this->id_colaborador = $this->getLastID();

                $campos_valores = [
                    "id_colaborador"=>$this->id_colaborador,
                    "nombre_usuario"=>$this->numero_documento,
                    "clave"=>md5($this->numero_documento),
                    "estado_acceso"=>$this->estado_acceso
                ];

                $this->insert("usuario", $campos_valores);

            } else {
                $sql = "SELECT COUNT(numero_documento) FROM colaborador WHERE numero_documento = :0 AND estado_mrcb AND id_colaborador NOT IN (:1)";
                $existeRepetido = $this->consultarValor($sql, [$this->numero_documento, $this->id_colaborador]);

                if ($existeRepetido > 0){
                    throw new Exception("Ya existe el número de documento ".$this->numero_documento." en el SISTEMA.", 1);
                }

                $campos_valores_where = [
                    "id_colaborador"=>$this->id_colaborador
                ];

                $this->update("colaborador", $campos_valores, $campos_valores_where);

                $campos_valores = [
                    "nombre_usuario"=>$this->numero_documento,
                    "estado_acceso"=>$this->estado_acceso
                ];

                $campos_valores_where = [
                    "id_colaborador"=>$this->id_colaborador
                ];

                $this->update("usuario", $campos_valores, $campos_valores_where);
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

            $this->update("colaborador", $campos_valores, $campos_valores_where);
            
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
                        c.id_rol,
                        u.estado_acceso
                    FROM colaborador c
                    LEFT JOIN usuario u ON u.id_colaborador = c.id_colaborador
                    WHERE c.estado_mrcb AND c.id_colaborador = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_colaborador);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cambiarClave(){
        try {

            $this->beginTransaction();

            if (strlen($this->clave) < 6){
                throw new Exception("Clave no válida.", 1);
            }

            $campos_valores = [
                "clave"=>md5($this->clave)
            ];
                
            $campos_valores_where = [
                "id_colaborador"=>$this->id_colaborador
            ];

            $this->update("usuario", $campos_valores, $campos_valores_where);

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}