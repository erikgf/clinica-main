<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Paciente.clase.php';

$op = $_GET["op"];
$obj = new Paciente();

try {
    switch($op){
        case "registrar":
            $obj->id_paciente = Funciones::sanitizar($_POST["p_idpaciente"]);
            $obj->id_tipo_documento = Funciones::sanitizar($_POST["p_id_tipo_documento"]);
            $obj->numero_documento = Funciones::sanitizar($_POST["p_numero_documento"]);
            $obj->numero_historia = Funciones::sanitizar($_POST["p_numero_historia"]);
            $obj->nombres = Funciones::sanitizar($_POST["p_nombres"]);
            $obj->apellidos_paterno = Funciones::sanitizar($_POST["p_apellidos_paterno"]);
            $obj->apellidos_materno = Funciones::sanitizar($_POST["p_apellidos_materno"]);
            $obj->sexo = Funciones::sanitizar($_POST["p_sexo"]);
            $obj->fecha_nacimiento = Funciones::sanitizar($_POST["p_fecha_nacimiento"]);
            $obj->ocupacion = Funciones::sanitizar($_POST["p_ocupacion"]);
            $obj->idtipo_paciente = Funciones::sanitizar($_POST["p_idtipo_paciente"]);
            $obj->estado_civil = Funciones::sanitizar($_POST["p_estado_civil"]);
            $obj->telefono_fijo = Funciones::sanitizar($_POST["p_telefono_fijo"]);
            $obj->celular_uno = Funciones::sanitizar($_POST["p_celular_uno"]);
            $obj->celular_dos = Funciones::sanitizar($_POST["p_celular_dos"]);
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
            $data = $obj->buscarPaciente($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "obtener_paciente_x_id":
            $idPaciente = $_POST["p_idpaciente"];
            $data = $obj->obtenerPaciente($idPaciente);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500", "No existe la función consultada en el API.","");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR", $th->getMessage());
}