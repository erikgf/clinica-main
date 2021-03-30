<?php

require_once '../datos/Conexion.clase.php';

class Paciente extends Conexion {
    public $id_paciente;

    public $id_tipo_documento;
    public $numero_documento;
    public $numero_historia;
    public $nombres;
    public $apellidos_paterno;
    public $apellidos_materno;
    public $sexo;
    public $fecha_nacimiento;
    public $ocupacion;
    public $idtipo_paciente;
    public $estado_civil;
    public $telefono_fijo;
    public $celular_uno;
    public $celular_dos;
    public $correo;
    public $domicilio;
    public $codigo_ubigeo_distrito;
    public $codigo_ubigeo_provincia;
    public $codigo_ubigeo_departamento;
    public $id_usuario_registrado;

    public function obtenerPacientesActivos(){
        try {

            $sql = "SELECT 
                    id_paciente as id,
                    id_tipo_documento,
                    numero_documento,
                    nombres,
                    apellido_paterno,
                    apellido_materno,
                    numero_historia,
                    id_tipo_documento,
                    numero_documento,
                    telefono_fijo,
                    celular_uno,
                    celular_dos,
                    titular_numero_documento,
                    titular_nombres_apellidos,
                    id_titular_parentesco,
                    correo,
                    domilicio,    
                    saldo_deuda,
                    sexo,
                    fecha_nacimiento                
                    FROM paciente p
                    WHERE estado_mrcb ";
                    
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function buscarPaciente($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_paciente as id,
                    CONCAT(numero_documento, ' - ', nombres, ' ', apellidos_paterno, ' ', apellidos_materno) as text
                    FROM paciente p
                    WHERE estado_mrcb AND CONCAT(numero_documento, ' - ', nombres, ' ', apellidos_paterno, ' ', apellidos_materno) like '%".$cadenaBuscar."%' 
                    LIMIT 5";
                    
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerPaciente($idPaciente){
        try {
            $sql = "SELECT 
                    id_paciente as id,
                    CONCAT(nombres, ' ', apellidos_paterno, ' ', apellidos_materno) as nombres_completos,
                    numero_documento,
                    numero_historia,
                    COALESCE(domicilio, 'SIN DIRECCIÓN') as direccion
                    FROM paciente p
                    WHERE estado_mrcb AND id_paciente = :0";
                    
            $data =  $this->consultarFila($sql, [$idPaciente]);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    
    
    public function registrar(){
        try {
            /*
                La lógica detrás de registrar debe ser la siguiente
                Cada vez que se crea un registro, se debe crear un nuevo INSAERT en la tabla paciente
                Cada vez que se hace una edición
                    Se da de baja el anteriro registro (se marca como actualizado, 
                                estado_mrcb = 0, fecha_hora_editado => TIME, id_usuario_editado => id_user)
                                tb debe ewxistir un fecha_hora_anulado => TIME, idusuario_anulado =î TIME
                                tb debe exiwterier un fecha_hora_registrado => TIME , idusuario_registrado (DEFAULT -1)
                                tb debe existe un campo fhr_modificacion

                    Esto aplica para todos los registros donde se realiza una modificacion basada en una interación hum ana
                    El nuevo registro queda listo para las posibles operaciones con respecto hacia adealnte
            */
            $this->id_usuario_registrado = "-1";

            $fecha_ahora = date("Y-m-d H:i:s");

            if ($this->id_paciente == NULL){
                //No hay un registro previo
                $sql  = "SELECT numero_historia FROM paciente WHERE id_paciente = :0 AND estado_mrcb";
                $objPaciente  = $this->consultarFila($sql, [$this->id_paciente]);

                if ($objPaciente == false){
                    throw new Exception("Problema con el paciente siendo editado.", 1);
                }

                $this->update("paciente", 
                                    ["estado_mrcb"=>"0", "fecha_hora_editado"=>$fecha_ahora, "id_usuario_editado"=>$this->id_usuario_registrado ?? "-1"],
                                    ["id_paciente"=>$this->id_paciente]);

                $this->numero_historia = $objPaciente["numero_historia"];

            } else {
                $this->obtenerNumeroHistoriaCorrelativo();
            }
            
            $campos_valores = [
                "id_tipo_documento"=>$this->id_tipo_documento,
                "numero_documento"=>$this->numero_documento,
                "numero_historia"=>$this->numero_historia,
                "nombres"=>$this->nombres,
                "apellidos_paterno"=>$this->apellido_paterno,
                "apellidos_materno"=>$this->apellido_materno,
                "sexo"=>$this->sexo,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "ocupacion"=>$this->ocupacion,
                "idtipo_paciente"=> $this->idtipo_paciente ?? "1",
                "estado_civil"=>$this->estado_civil,
                "telefono_fijo"=>$this->telefono_fijo,
                "celular_uno"=>$this->celular_uno,
                "celular_dos"=>$this->celular_dos,
                "correo"=>$this->correo,
                "domicilio"=>$this->domicilio,
                "codigo_ubigeo_distrito"=>$this->codigo_ubigeo_distrito,
                "codigo_ubigeo_provincia"=>$this->codigo_ubigeo_provincia,
                "codigo_ubigeo_departamento"=>$this->codigo_ubigeo_departamento,
                "id_usuario_registrado"=> $this->id_usuario_registrado ?? "-1",
                "fecha_hora_registrado"=> $fecha_ahora
            ];

            $this->insert("paciente", $campos_valores);

            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.");

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

    private function obtenerNumeroHistoriaCorrelativo(){
        try {

            $sql  = "SELECT COALESCE(MAX(numero_historia) + 1, 1) FROM paciente WHERE estado_mrcb";
            $numero_historia = $this->consultarValor($sql);

            if ($numero_historia == NULL){
                throw new Exception($exc->getMessage(), 1); 
            }

            $this->numero_historia = $numero_historia;
            return ["rpt"=>true];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}