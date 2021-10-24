<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Paciente.clase.php';

$op = $_GET["op"];
$obj = new Paciente();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "registrar":
            $obj->id_paciente = Funciones::sanitizar($_POST["p_id_paciente"]);
            $obj->id_tipo_documento = Funciones::sanitizar($_POST["p_id_tipo_documento"]);
            $obj->numero_documento = Funciones::sanitizar($_POST["p_numero_documento"]);
            $obj->nombres = strtoupper(Funciones::sanitizar($_POST["p_nombres"]));
            $obj->apellidos_paterno = strtoupper(Funciones::sanitizar($_POST["p_apellidos_paterno"]));
            $obj->apellidos_materno = strtoupper(Funciones::sanitizar($_POST["p_apellidos_materno"]));
            $obj->sexo = Funciones::sanitizar($_POST["p_sexo"]);
            $obj->fecha_nacimiento = Funciones::sanitizar($_POST["p_fecha_nacimiento"]);
            $obj->ocupacion = strtoupper(Funciones::sanitizar($_POST["p_ocupacion"]));
            $obj->idtipo_paciente = Funciones::sanitizar($_POST["p_idtipo_paciente"]);
            $obj->estado_civil = Funciones::sanitizar($_POST["p_estado_civil"]);
            $obj->telefono_fijo = Funciones::sanitizar($_POST["p_telefono_fijo"]);
            $obj->celular_uno = Funciones::sanitizar($_POST["p_celular_uno"]);
            $len_celular_uno = strlen($obj->celular_uno);
            if ($len_celular_uno > 1){
                if ($len_celular_uno != 9){
                    throw new Exception("CELULAR UNO necesita 9 carácteres para ser válido.", 1);
                }
            }
            $obj->celular_dos = Funciones::sanitizar($_POST["p_celular_dos"]);
            $len_celular_dos = strlen($obj->celular_dos);
            if ($len_celular_dos > 1){
                if ($len_celular_dos != 9){
                    throw new Exception("CELULAR DOS necesita 9 carácteres para ser válido.", 1);
                }
            }
            $obj->correo = Funciones::sanitizar($_POST["p_correo"]);
            $obj->domicilio = Funciones::sanitizar($_POST["p_domicilio"]);
            $obj->codigo_ubigeo_distrito = Funciones::sanitizar($_POST["p_codigo_ubigeo_distrito"]);
            $obj->codigo_ubigeo_provincia = Funciones::sanitizar($_POST["p_codigo_ubigeo_provincia"]);
            $obj->codigo_ubigeo_departamento = Funciones::sanitizar($_POST["p_codigo_ubigeo_departamento"]);

            $data = $obj->registrar();

            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "obtener_pacientes_activos":
            $data = $obj->obtenerPacientesActivos();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "buscar_pacientes":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscarPacientes($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_paciente_x_id":
            $idPaciente = $_POST["p_idpaciente"];
            $data = $obj->obtenerPacienteXId($idPaciente);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "obtener_paciente_x_id_full":
            $idPaciente = $_POST["p_idpaciente"];
            $data = $obj->obtenerPacienteXIdFull($idPaciente);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_paciente_x_documento":
            $numeroDocumento = $_POST["p_numerodocumento"];
            $data = $obj->obtenerPacienteXDocumentoFull($numeroDocumento);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "eliminar_paciente":
            $obj->id_paciente = isset($_POST["p_idpaciente"]) ? $_POST["p_idpaciente"] : "";
            $data = $obj->eliminar();
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        

        default:
            Funciones::imprimeJSON("500", "No existe la función consultada en el API.","");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}