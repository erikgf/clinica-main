<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Sesion.clase.php';
require_once '../negocio/Globals.clase.php';

$objUsuario = Sesion::obtenerSesion();

if ($objUsuario == null){
    Funciones::imprimeJSON(Globals::$HTTP_NO_CREDENCIALES, "ERROR", "No hay credenciales válidas.");
    exit;
}

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_PROMOTORA, Globals::$ID_ROL_ADMINISTRADOR,  Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR])){
    Funciones::imprimeJSON(Globals::$HTTP_NO_PERMISOS, "ERROR", "No tiene permisos para ver esto.");
    exit;
}

$obj = null;
$op = "";
try {
    require_once '../negocio/MedicoPromotoraTemporal.clase.php';
    $obj = new MedicoPromotoraTemporal($objUsuario["id_usuario_registrado"]);
    $op = $_GET["op"];
} catch (\Throwable $th) {
    Funciones::imprimeJSON(Globals::$HTTP_NO_PERMISOS, "ERROR", $th->getMessage());
    exit;
}

try {
    switch($op){
        case "leer":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.",  Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->leer();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "anular":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->anular();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "guardar":
            /*
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            if (!$obj->numero_documento){
                throw new Exception("Número documento no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            */

            $obj->apellidos_nombres = isset($_POST["p_apellidos_nombres"]) ? $_POST["p_apellidos_nombres"] : NULL;
            if (!$obj->apellidos_nombres){
                throw new Exception("Nombres y apellidos no ingresados", Globals::$HTTP_NO_VALIDO);
            }
            $obj->colegiatura = isset($_POST["p_colegiatura"]) ? $_POST["p_colegiatura"] : NULL;
            if (!$obj->colegiatura){
                throw new Exception("CMP no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_especialidad = isset($_POST["p_id_especialidad"]) ? $_POST["p_id_especialidad"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Especialidad no ingresada", Globals::$HTTP_NO_VALIDO);
            }
            $obj->fecha_nacimiento = isset($_POST["p_fecha_nacimiento"]) ? $_POST["p_fecha_nacimiento"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Fecha de nacimiento no ingresada", Globals::$HTTP_NO_VALIDO);
            }

            $obj->celular = isset($_POST["p_celular"]) ? $_POST["p_celular"] : "";
            $obj->direccion = isset($_POST["p_direccion"]) ? $_POST["p_direccion"] : "";

            $obj->id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : NULL;
            if (!$obj->id_sede){
                throw new Exception("Sede no ingresada", Globals::$HTTP_NO_VALIDO);
            }

            $data = $obj->guardar();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "editar":
            /*
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            if (!$obj->numero_documento){
                throw new Exception("Número documento no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            */
            $obj->apellidos_nombres = isset($_POST["p_apellidos_nombres"]) ? $_POST["p_apellidos_nombres"] : NULL;
            if (!$obj->apellidos_nombres){
                throw new Exception("Nombres y apellidos no ingresados", Globals::$HTTP_NO_VALIDO);
            }
            $obj->colegiatura = isset($_POST["p_colegiatura"]) ? $_POST["p_colegiatura"] : NULL;
            if (!$obj->colegiatura){
                throw new Exception("CMP no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_especialidad = isset($_POST["p_id_especialidad"]) ? $_POST["p_id_especialidad"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Especialidad no ingresada", Globals::$HTTP_NO_VALIDO);
            }
            $obj->fecha_nacimiento = isset($_POST["p_fecha_nacimiento"]) ? $_POST["p_fecha_nacimiento"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Fecha de nacimiento no ingresada", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_medico =isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : NULL;
            if (!$obj->id_medico){
                throw new Exception("No se ha enviado el ID del registro a modificar", Globals::$HTTP_NO_VALIDO);
            }
            $obj->celular = isset($_POST["p_celular"]) ? $_POST["p_celular"] : "";
            $obj->direccion = isset($_POST["p_direccion"]) ? $_POST["p_direccion"] : "";

            $obj->id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : NULL;
            if (!$obj->id_sede){
                throw new Exception("Sede no ingresada", Globals::$HTTP_NO_VALIDO);
            }

            $data = $obj->editar();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "editar_viejo":
            /*
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            if (!$obj->numero_documento){
                throw new Exception("Número documento no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            */
            $obj->apellidos_nombres = isset($_POST["p_apellidos_nombres"]) ? $_POST["p_apellidos_nombres"] : NULL;
            if (!$obj->apellidos_nombres){
                throw new Exception("Nombres y apellidos no ingresados", Globals::$HTTP_NO_VALIDO);
            }
            $obj->colegiatura = isset($_POST["p_colegiatura"]) ? $_POST["p_colegiatura"] : NULL;
            if (!$obj->colegiatura){
                throw new Exception("CMP no ingresado", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_especialidad = isset($_POST["p_id_especialidad"]) ? $_POST["p_id_especialidad"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Especialidad no ingresada", Globals::$HTTP_NO_VALIDO);
            }
            $obj->fecha_nacimiento = isset($_POST["p_fecha_nacimiento"]) ? $_POST["p_fecha_nacimiento"] : NULL;
            if (!$obj->id_especialidad){
                throw new Exception("Fecha de nacimiento no ingresada", Globals::$HTTP_NO_VALIDO);
            }
            $obj->celular = isset($_POST["p_celular"]) ? $_POST["p_celular"] : "";
            $obj->direccion = isset($_POST["p_direccion"]) ? $_POST["p_direccion"] : "";

            $obj->id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : NULL;
            if (!$obj->id_sede){
                throw new Exception("Sede no ingresada", Globals::$HTTP_NO_VALIDO);
            }

            $obj->id_medico_modificado = isset($_POST["p_id_medico_modificado"]) ? $_POST["p_id_medico_modificado"] : NULL;
            if (!$obj->id_medico_modificado){
                throw new Exception("No se ha enviado el ID del registro a modificar", Globals::$HTTP_NO_VALIDO);
            }
            $data = $obj->editarViejo();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "listar_medicos_activos":
            $data = $obj->listarMedicosActivos();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "leer_medico":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.",  Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->leerMedicoActivo();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;
        default:
            throw new Exception( "No existe la función consultada en el API.", Globals::$HTTP_NO_ENCONTRADO);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON($th->getCode(), "ERROR", $th->getMessage());
}