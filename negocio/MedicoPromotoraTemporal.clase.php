<?php

require_once '../datos/Conexion.clase.php';
require_once 'Medico.clase.php';

class MedicoPromotoraTemporal extends Conexion {
    public int $id_medico;
    public int $id_promotora;
    public string $apellidos_nombres;
    public ?string $numero_documento;
    public string $colegiatura;
    public string $fecha_nacimiento;
    public int $id_especialidad;
    public ?string $celular;
    public ?string $direccion;
    public int $id_sede;
    public ?int $id_medico_modificado;
    public ?string $estado_activo;

    public int $id_usuario_registrado;

    public function __construct($id_usuario_promotora, $objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }

        $sql = "SELECT id_promotora FROM usuario WHERE id_usuario = :0 AND estado_mrcb";
        $id_promotora = $this->consultarValor($sql, [$id_usuario_promotora]);
        if ($id_promotora == NULL){
            throw new Exception("Código de usuario de promotora inváido.");
        }
        $this->id_promotora = $id_promotora;
        $this->id_usuario_registrado = $id_usuario_promotora;
    }

    public function listar($id = null){
        try {
            $params = [$this->id_promotora];
            $sqlExtra = "";

            if ($id != null){
                $sqlExtra = " AND id_medico = :1 ";
                array_push($params, $id);
            }

            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos,
                        m.cmp,
                        esp.descripcion as especialidad,
                        m.celular,
                        m.direccion,
                        s.nombre as sede,
                        DATE_FORMAT(m.fecha_nacimiento, '%d-%m-%Y') as fecha_nacimiento
                    FROM medico_promotora_temporal m 
                    INNER JOIN sede s ON s.id_sede = m.id_sede
                    LEFT JOIN especialidad_medico esp ON esp.id_especialidad_medico = m.id_especialidad
                    WHERE m.estado_mrcb AND m.id_promotora = :0 AND estado_activo = 'P' $sqlExtra
                    ORDER BY m.fecha_hora_registro";
            
            return  $this->consultarFilas($sql, $params);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try {
            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos,
                        m.cmp,
                        m.fecha_nacimiento,
                        m.id_especialidad,
                        m.direccion,
                        m.celular,
                        m.id_sede
                    FROM medico_promotora_temporal m 
                    WHERE m.estado_mrcb AND m.id_promotora = :0 AND m.id_medico = :1";
                    
            return $this->consultarFila($sql, [$this->id_promotora, $this->id_medico]);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function guardar(){
        try {

            $sql = "SELECT 	COUNT(IF(colegiatura =  :0, 1,NULL)) as cantidad_cmp
                    FROM medico
                    WHERE estado_mrcb AND (colegiatura = :0)";
            $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura]);

            if ($repetidosMedicos["cantidad_cmp"] > 0){
                throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
            }

            $sql = "SELECT 	COUNT(IF(cmp =  :0, 1,NULL)) as cantidad_cmp
                    FROM medico_promotora_temporal
                    WHERE estado_mrcb AND estado_activo = 'P' AND cmp = :0 AND id_promotora = :1";
            $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura, $this->id_promotora]);

            if ($repetidosMedicos["cantidad_cmp"] > 0){
                throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
            }
         
            $this->apellidos_nombres = mb_strtoupper($this->apellidos_nombres,'UTF-8');

            $campos_valores = [
                //"numero_documento"=>$this->numero_documento,
                "nombres_apellidos"=>$this->apellidos_nombres,
                "cmp"=>$this->colegiatura,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "id_promotora"=>$this->id_promotora == "" ? NULL : $this->id_promotora,
                "celular"=>$this->celular,
                "direccion"=>$this->direccion,
                "id_sede"=>$this->id_sede,
                "id_especialidad"=>$this->id_especialidad,
                "estado_activo"=>"P"
            ];

            $this->insert("medico_promotora_temporal", $campos_valores);
            $this->id_medico = $this->getLastID();
            
            return $this->listar($this->id_medico);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public function editar(){
        try {

            $sql = "SELECT 	COUNT(IF(cmp =  :0, 1,NULL)) as cantidad_cmp
                    FROM medico_promotora_temporal
                    WHERE estado_mrcb AND estado_activo = 'P' AND (cmp = :0) AND id_promotora = :1 AND id_medico <> :2";
            $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura, $this->id_promotora, $this->id_medico]);

            if ($repetidosMedicos["cantidad_cmp"] > 0){
                throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
            }

            $sql  = "SELECT id_medico_modificado FROM medico_promotora_temporal
                    WHERE id_medico = :0 AND estado_mrcb";
            $medicoModificado = $this->consultarFila($sql, [$this->id_medico]);

            if ($medicoModificado){
                $this->id_medico_modificado = $medicoModificado["id_medico_modificado"];
                $sql = "SELECT 	COUNT(IF(colegiatura =  :0, 1,NULL)) as cantidad_cmp
                        FROM medico
                        WHERE estado_mrcb AND (colegiatura = :0) AND id_medico <> :1";
                $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura, $this->id_medico_modificado]);

                if ($repetidosMedicos["cantidad_cmp"] > 0){
                    throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
                }
            }

            $this->apellidos_nombres = mb_strtoupper($this->apellidos_nombres,'UTF-8');

            $campos_valores = [
                //"numero_documento"=>$this->numero_documento,
                "nombres_apellidos"=>$this->apellidos_nombres,
                "cmp"=>$this->colegiatura,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "id_especialidad"=>$this->id_especialidad,
                "celular"=>$this->celular,
                "direccion"=>$this->direccion,
                "id_sede"=>$this->id_sede
            ];

            $campos_valores_where = [
                "id_medico"=>$this->id_medico,
            ];

            $this->update("medico_promotora_temporal", $campos_valores, $campos_valores_where);
            
            return $this->listar($this->id_medico);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public function anular(){
        try {
            $campos_valores_where = [
                "id_medico"=>$this->id_medico,
                "id_promotora"=>$this->id_promotora
            ];

            $this->update("medico_promotora_temporal", ["estado_mrcb"=>0], $campos_valores_where);
            return $this->id_medico;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public function editarViejo(){
        try {

            $sql = "SELECT 	COUNT(IF(colegiatura =  :0, 1,NULL)) as cantidad_cmp
                    FROM medico
                    WHERE estado_mrcb AND (colegiatura = :0) AND id_medico <> :1";
            $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura, $this->id_medico_modificado]);

            if ($repetidosMedicos["cantidad_cmp"] > 0){
                throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
            }

            $sql = "SELECT 	COUNT(IF(cmp =  :0, 1,NULL)) as cantidad_cmp
                    FROM medico_promotora_temporal
                    WHERE estado_mrcb AND estado_activo = 'P' AND (cmp = :0) AND id_promotora = :1";
            $repetidosMedicos = $this->consultarFila($sql, [$this->colegiatura, $this->id_promotora]);

            if ($repetidosMedicos["cantidad_cmp"] > 0){
                throw new Exception("Ya existe otro médico con esa colegiatura.", Globals::$HTTP_NO_VALIDO);
            }

            $this->apellidos_nombres = mb_strtoupper($this->apellidos_nombres,'UTF-8');

            $campos_valores = [
                //"numero_documento"=>$this->numero_documento,
                "nombres_apellidos"=>$this->apellidos_nombres,
                "cmp"=>$this->colegiatura,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "id_especialidad"=>$this->id_especialidad,
                "id_promotora"=>$this->id_promotora,
                "id_medico_modificado"=>$this->id_medico_modificado,
                "celular"=>$this->celular,
                "direccion"=>$this->direccion,
                "id_sede"=>$this->id_sede
            ];

            $this->insert("medico_promotora_temporal", $campos_valores);
            $this->id_medico = $this->getLastID();
            
            return $this->listar($this->id_medico);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public function listarMedicosActivos(){
        try {
            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos,
                        m.colegiatura as cmp,
                        esp.descripcion as especialidad,
                        DATE_FORMAT(m.fecha_nacimiento, '%d-%m-%Y') as fecha_nacimiento,
                        m.domicilio as direccion,
                        m.telefono_uno as celular,
                        sede.nombre as sede
                    FROM medico m 
                    LEFT JOIN medico_promotora_temporal mp ON mp.id_medico_modificado = m.id_medico AND mp.estado_mrcb AND mp.estado_activo = 'P'
                    LEFT JOIN especialidad_medico esp ON esp.id_especialidad_medico = m.id_especialidad_medico
                    LEFT JOIN sede ON sede.id_sede = m.id_sede
                    WHERE m.estado_mrcb AND m.id_promotora = :0 AND m.id_medico NOT IN (1,2) AND mp.id_medico IS  NULL";
            
            return  $this->consultarFilas($sql, [$this->id_promotora]);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerMedicoActivo(){
        try {
            $sql = "SELECT 
                        m.id_medico,
                        m.numero_documento,
                        m.nombres_apellidos,
                        m.colegiatura as cmp,
                        m.fecha_nacimiento,
                        m.id_especialidad_medico as id_especialidad,
                        m.telefono_uno as celular,
                        m.domicilio as direccion,
                        m.id_sede
                    FROM medico m 
                    WHERE m.estado_mrcb AND m.id_promotora = :0 AND m.id_medico = :1";
                    
            return $this->consultarFila($sql, [$this->id_promotora, $this->id_medico]);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
}