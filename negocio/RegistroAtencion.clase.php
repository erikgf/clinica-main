<?php

require_once '../datos/Conexion.clase.php';

class RegistroAtencion extends Conexion {
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

    public function buscarPacientes($cadenaBuscar){
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

    public function obtenerPacienteXId($idPaciente){
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

    public function obtenerPacienteXDocumentoFull($numeroDocumento){
        try {
            $sql = "SELECT 
                    id_paciente,
                    apellidos_paterno,
                    apellidos_materno,
                    nombres,
                    sexo,
                    fecha_nacimiento,
                    id_tipo_documento,
                    numero_documento,
                    numero_historia,
                    COALESCE(ocupacion,'') as ocupacion,
                    id_tipo_paciente,
                    estado_civil,
                    COALESCE(correo,'') as correo,
                    COALESCE(telefono_fijo,'') as telefono_fijo,
                    COALESCE(celular_uno,'') as celular_uno,
                    COALESCE(celular_dos,'') as celular_dos,
                    COALESCE(domicilio, '') as domicilio,
                    COALESCE(p.codigo_ubigeo_departamento,'') as codigo_ubigeo_departamento,
                    COALESCE(dep.name,'') as departamento,
                    COALESCE(p.codigo_ubigeo_provincia,'') as codigo_ubigeo_provincia,
                    COALESCE(prov.name,'') as provincia,
                    COALESCE(p.codigo_ubigeo_distrito,'') as codigo_ubigeo_distrito,
                    COALESCE(dist.name,'') as distrito
                    FROM paciente p
                    LEFT JOIN ubigeo_peru_departments dep ON p.codigo_ubigeo_departamento = dep.id
                    LEFT JOIN ubigeo_peru_provinces prov ON p.codigo_ubigeo_provincia = prov.id
                    LEFT JOIN ubigeo_peru_districts dist ON p.codigo_ubigeo_distrito = dist.id
                    WHERE estado_mrcb AND id_paciente = :0";
                    
            $data =  $this->consultarFila($sql, [$idPaciente]);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerPacienteXIdFull($idPaciente){
        try {
            $sql = "SELECT 
                    id_paciente,
                    apellidos_paterno,
                    apellidos_materno,
                    nombres,
                    CONCAT(nombres, ' ', apellidos_paterno, ' ', apellidos_materno) as nombres_completos,
                    sexo,
                    fecha_nacimiento,
                    id_tipo_documento,
                    numero_documento,
                    numero_historia,
                    COALESCE(ocupacion,'') as ocupacion,
                    id_tipo_paciente,
                    estado_civil,
                    COALESCE(correo,'') as correo,
                    COALESCE(telefono_fijo,'') as telefono_fijo,
                    COALESCE(celular_uno,'') as celular_uno,
                    COALESCE(celular_dos,'') as celular_dos,
                    COALESCE(domicilio, '') as domicilio,
                    COALESCE(p.codigo_ubigeo_departamento,'') as codigo_ubigeo_departamento,
                    COALESCE(dep.name,'') as departamento,
                    COALESCE(p.codigo_ubigeo_provincia,'') as codigo_ubigeo_provincia,
                    COALESCE(prov.name,'') as provincia,
                    COALESCE(p.codigo_ubigeo_distrito,'') as codigo_ubigeo_distrito,
                    COALESCE(dist.name,'') as distrito
                    FROM paciente p
                    LEFT JOIN ubigeo_peru_departments dep ON p.codigo_ubigeo_departamento = dep.id
                    LEFT JOIN ubigeo_peru_provinces prov ON p.codigo_ubigeo_provincia = prov.id
                    LEFT JOIN ubigeo_peru_districts dist ON p.codigo_ubigeo_distrito = dist.id
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
                    Se hace un registro en la tabla bitacora_paciente
                        con los datos anteriores del registro_paciente
                    se marca el registro anterior como actualizado
                                fecha_hora_editado => TIME, id_usuario_editado => id_user)
                                tb debe ewxistir un fecha_hora_anulado => TIME, idusuario_anulado =î TIME
                                tb debe exiwterier un fecha_hora_registrado => TIME , idusuario_registrado (DEFAULT -1)
                                tb debe existe un campo fhr_modificacion

                    Se edita  los registros
                    y/o
                    El nuevo registro queda listo para las posibles operaciones con respecto hacia adealnte
            */
            $this->id_usuario_registrado = "-1";

            $fecha_ahora = date("Y-m-d H:i:s");
            $estoyEditando = $this->id_paciente != null;

            $campos_valores = [
                "id_tipo_documento"=>$this->id_tipo_documento,
                "numero_documento"=>$this->numero_documento,
                "nombres"=>$this->nombres,
                "apellidos_paterno"=>$this->apellidos_paterno,
                "apellidos_materno"=>$this->apellidos_materno,
                "sexo"=>$this->sexo,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "ocupacion"=>$this->ocupacion,
                "id_tipo_paciente"=> "1",
                "estado_civil"=>$this->estado_civil,
                "telefono_fijo"=>$this->telefono_fijo,
                "celular_uno"=>$this->celular_uno,
                "celular_dos"=>$this->celular_dos,
                "correo"=>$this->correo,
                "domicilio"=>$this->domicilio,
                "codigo_ubigeo_distrito"=>$this->codigo_ubigeo_distrito,
                "codigo_ubigeo_provincia"=>$this->codigo_ubigeo_provincia,
                "codigo_ubigeo_departamento"=>$this->codigo_ubigeo_departamento
            ];

            if ($estoyEditando){
                $campos_valores["id_usuario_editado"] = $this->id_usuario_registrado;
                $campos_valores["fecha_hora_editado"] = $fecha_ahora;

                $campos_valores_where = ["id_paciente"=>$this->id_paciente];

                $sql  = "INSERT INTO bitacora_paciente( 
                        id_paciente,
                        id_tipo_documento, 
                        numero_documento,
                        numero_historia,
                        nombres,
                        apellidos_paterno,
                        apellidos_materno,
                        sexo,
                        fecha_nacimiento,
                        ocupacion,
                        id_tipo_paciente,
                        estado_civil,
                        telefono_fijo,
                        celular_uno,
                        celular_dos,
                        correo,
                        domicilio,
                        codigo_ubigeo_distrito,
                        codigo_ubigeo_provincia,
                        codigo_ubigeo_departamento,
                        id_usuario_registrado,
                        fecha_hora_registrado)
                        SELECT  id_paciente,
                                id_tipo_documento, 
                                numero_documento,
                                numero_historia,
                                nombres,
                                apellidos_paterno,
                                apellidos_materno,
                                sexo,
                                fecha_nacimiento,
                                ocupacion,
                                id_tipo_paciente,
                                estado_civil,
                                telefono_fijo,
                                celular_uno,
                                celular_dos,
                                correo,
                                domicilio,
                                codigo_ubigeo_distrito,
                                codigo_ubigeo_provincia,
                                codigo_ubigeo_departamento,
                                :0,
                                CURRENT_TIMESTAMP
                                FROM paciente WHERE id_paciente = :1 AND estado_mrcb";

                $this->ejecutarSimple($sql, [$this->id_usuario_registrado, $this->id_paciente]);

                $this->update("paciente", $campos_valores, $campos_valores_where);
            } else{                

                $this->obtenerNumeroHistoriaCorrelativo();

                $campos_valores["numero_historia"] = $this->numero_historia;
                $campos_valores["id_usuario_registrado"] = $this->id_usuario_registrado;
                $campos_valores["fecha_hora_registrado"] = $fecha_ahora;

                $this->insert("paciente", $campos_valores);

                $this->id_paciente = $this->getLastID();
            }


            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", 
                                    "paciente"=>[
                                        "id"=>$this->id_paciente,
                                        "documento_nombres_completos"=>$this->numero_documento.' - '.$this->nombres.' '.$this->apellidos_paterno.' '.$this->apellidos_materno]
                                    );  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){
        try {
            $this->id_usuario_registrado = "-1";

            $fecha_ahora = date("Y-m-d H:i:s");

            if ($this->id_paciente == NULL || $this->id_paciente == ""){
                throw new Exception("ID de paciente no valido.", 1);
            } 

            $campos_valores = [
                "id_usuario_anulado"=>$this->id_usuario_registrado,
                "fecha_hora_anulado"=>$fecha_ahora,
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_paciente"=>$this->id_paciente
            ];

            $this->update("paciente", $campos_valores, $campos_valores_where);

            return array("rpt"=>true, "msj"=>"Paciente eliminado correctamente.");

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
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