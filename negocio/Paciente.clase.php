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

    public $nombres_completos;
    public $direccion;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }
    
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
                    WHERE estado_mrcb AND es_paciente ";
                    
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
                    CONCAT( COALESCE(numero_documento,'SD'), ' - ', nombres, ' ', apellidos_paterno, ' ', apellidos_materno) as text
                    FROM paciente p
                    WHERE estado_mrcb  AND es_paciente AND CONCAT(COALESCE(numero_documento,''), ' - ', nombres, ' ', apellidos_paterno, ' ', apellidos_materno) like '%".$cadenaBuscar."%' 
                    ORDER BY apellidos_paterno, apellidos_materno
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
                    CONCAT(apellidos_paterno, ' ', apellidos_materno,' ',nombres) as nombres_completos,
                    COALESCE(numero_documento, '') as numero_documento,
                    numero_historia,
                    COALESCE(domicilio, 'SIN DIRECCIÓN') as direccion,
                    id_tipo_documento,
                    COALESCE(codigo_ubigeo_distrito, '') as codigo_ubigeo_distrito
                    FROM paciente p
                    WHERE estado_mrcb  AND es_paciente AND id_paciente = :0";
                    
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
                    COALESCE(numero_documento, '') as numero_documento,
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
                    WHERE estado_mrcb  AND es_paciente AND numero_documento = :0";
                    
            $data =  $this->consultarFila($sql, [$numeroDocumento]);
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
                    CONCAT(apellidos_paterno, ' ', apellidos_materno,' ',nombres) as nombres_completos,
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
                    WHERE estado_mrcb  AND es_paciente AND id_paciente = :0";
                    
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
            $this->beginTransaction();

            $fecha_ahora = date("Y-m-d H:i:s");
            $estoyEditando = $this->id_paciente != null;

            $numero_documento_repetido = 0;
            if ($this->numero_documento != "" && $this->numero_documento != NULL){
                $params = $estoyEditando ? [$this->numero_documento, $this->id_paciente] : [$this->numero_documento];
                $sql = "SELECT COUNT(*) FROM paciente WHERE estado_mrcb AND numero_documento = :0 ".($estoyEditando ? " AND id_paciente <> :1 " : "");
                $numero_documento_repetido = $this->consultarValor($sql, $params);
            }

            if ($numero_documento_repetido > 0){
                throw new Exception("El DOCUMENTO ingresado ya existe en el sistema.", 1);
            }

            $this->nombres = str_replace('|', '', $this->nombres);
            $this->apellidos_paterno = str_replace('|', '', $this->apellidos_paterno);
            $this->apellidos_materno = str_replace('|', '', $this->apellidos_materno);
            $this->domicilio = str_replace('|', '', $this->domicilio);

            $campos_valores = [
                "id_tipo_documento"=>$this->id_tipo_documento,
                "numero_documento"=>$this->numero_documento == "" ? NULL : $this->numero_documento,
                "nombres"=>$this->nombres,
                "apellidos_paterno"=>$this->apellidos_paterno,
                "apellidos_materno"=>$this->apellidos_materno,
                "sexo"=>$this->sexo,
                "fecha_nacimiento"=>$this->fecha_nacimiento,
                "ocupacion"=>$this->ocupacion,
                "id_tipo_paciente"=> "1",
                "estado_civil"=>$this->estado_civil,
                "telefono_fijo"=>strlen($this->telefono_fijo) <= 0 ? NULL : $this->telefono_fijo,
                "celular_uno"=>strlen($this->celular_uno) <= 0 ? NULL : $this->celular_uno ,
                "celular_dos"=>strlen($this->celular_dos) <= 0 ? NULL : $this->celular_dos,
                "correo"=>$this->correo,
                "domicilio"=>$this->domicilio,
                "codigo_ubigeo_distrito"=>$this->codigo_ubigeo_distrito == "" ? NULL : $this->codigo_ubigeo_distrito,
                "codigo_ubigeo_provincia"=>$this->codigo_ubigeo_provincia == "" ? NULL : $this->codigo_ubigeo_provincia,
                "codigo_ubigeo_departamento"=>$this->codigo_ubigeo_departamento == "" ? NULL : $this->codigo_ubigeo_departamento,
                "es_paciente"=>"1"
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

            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.", 
                                    "paciente"=>[
                                        "id"=>$this->id_paciente,
                                        "documento_nombres_completos"=>$this->numero_documento.' - '.$this->nombres.' '.$this->apellidos_paterno.' '.$this->apellidos_materno]
                                    );  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarClienteXRUC($ruc, $razon_social, $direccion){
        try {

            $fecha_ahora = date("Y-m-d H:i:s");

            $params = [$ruc];
            $sql = "SELECT id_paciente FROM paciente WHERE estado_mrcb AND numero_documento = :0";
            $objPaciente = $this->consultarFila($sql, $params);

            if ($objPaciente != false){
                return array("msj"=>"Registro realizado correctamente.", 
                            "cliente"=>[
                                "id"=>$objPaciente["id_paciente"],
                                "documento_nombres_completos"=>$ruc.' - '.$razon_social]
                            );  
            }

            $this->beginTransaction();
            
            $campos_valores = [
                "id_tipo_documento"=>"6",
                "numero_documento"=>$ruc,
                "razon_social"=>$razon_social,
                "domicilio"=>$direccion,
                "es_paciente"=>"0",
                "id_tipo_paciente"=> "1"
            ];

            $campos_valores["id_usuario_registrado"] = $this->id_usuario_registrado;
            $campos_valores["fecha_hora_registrado"] = $fecha_ahora;

            $this->insert("paciente", $campos_valores);

            $id_paciente = $this->getLastID();

            $this->commit();
            return array("msj"=>"Registro realizado correctamente.", 
                            "cliente"=>[
                                "id"=>$id_paciente,
                                "documento_nombres_completos"=>$ruc.' - '.$razon_social]
                            );  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarClienteXOTRO($id_tipo_documento, $numero_documento, $nombres, $apellido_paterno, $apellido_materno, $sexo, $fecha_nacimiento, $direccion = ""){
        try {

            $fecha_ahora = date("Y-m-d H:i:s");

            $params = [$numero_documento];
            $sql = "SELECT id_paciente FROM paciente WHERE estado_mrcb AND numero_documento = :0";
            $objPaciente = $this->consultarFila($sql, $params);

            if ($objPaciente != false){
                $this->update("paciente", ["fecha_nacimiento"=>$fecha_nacimiento, "domicilio"=>$direccion], ["id_paciente"=>$objPaciente["id_paciente"]]);
                return array("msj"=>"Registro realizado correctamente.", 
                            "cliente"=>[
                                "id"=>$objPaciente["id_paciente"],
                                "direccion"=>$direccion,
                                "documento_nombres_completos"=>$numero_documento." - ".$apellido_paterno." ".$apellido_materno." ".$nombres]
                            );  
            }

            $this->beginTransaction();
            
            $campos_valores = [
                "id_tipo_documento"=>$id_tipo_documento,
                "numero_documento"=>$numero_documento,
                "nombres"=>$nombres,
                "apellidos_paterno"=>$apellido_paterno,
                "apellidos_materno"=>$apellido_materno,
                "domicilio"=>$direccion,
                "es_paciente"=>"1",
                "id_tipo_paciente"=> "1",
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$fecha_ahora
            ];

            $this->obtenerNumeroHistoriaCorrelativo();
            $campos_valores["numero_historia"] = $this->numero_historia;

            $this->insert("paciente", $campos_valores);

            $id_paciente = $this->getLastID();

            $this->commit();
            return array("msj"=>"Registro realizado correctamente.", 
                            "cliente"=>[
                                "id"=>$id_paciente,
                                "direccion"=>$direccion,
                                "documento_nombres_completos"=>$numero_documento." - ".$apellido_paterno." ".$apellido_materno." ".$nombres]
                            );  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){
        try {

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

            $sql  = "SELECT COALESCE(MAX(numero_historia) + 1, 1) FROM paciente WHERE estado_mrcb AND es_paciente = 1";
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