<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Sesion.clase.php';
require_once '../negocio/Globals.clase.php';

$objUsuario = Sesion::obtenerSesion();

if ($objUsuario == null){
    Funciones::imprimeJSON(Globals::$HTTP_NO_CREDENCIALES, "ERROR", "No hay credenciales válidas.");
    exit;
}

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR])){
    Funciones::imprimeJSON(Globals::$HTTP_NO_PERMISOS, "ERROR", "No tiene permisos para ver esto.");
    exit;
}

require_once '../negocio/MedicoPromotoraTemporalAdmin.clase.php';
$obj = new MedicoPromotoraTemporalAdmin();
$obj->id_usuario_registrado = $objUsuario["id_usuario_registrado"];
$op = $_GET["op"];

try {
    switch($op){
        case "listar_para_aprobar":
            $obj->estado_activo = isset($_POST["p_estado"]) ? $_POST["p_estado"] : "P";
            $data = $obj->listarParaAprobar();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;

        case "obtener_cantidad_medicos_aprobar":
            $data = $obj->obtenerCantidadMedicosParaAprobar();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;
        
        case "rechazar":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";
            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.", Globals::$HTTP_NO_VALIDO);
            }

            $observaciones = isset($_POST["p_observaciones"]) ? $_POST["p_observaciones"] : "";
            $obj->id_medico = $id_medico;
            $obj->observacion_rechazo = $observaciones;
            $data = $obj->rechazarMedico();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;
        case "aprobar":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.", Globals::$HTTP_NO_VALIDO);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->aprobarMedico();
            Funciones::imprimeJSON(Globals::$HTTP_OK, "OK", $data);
        break;
        default:
            throw new Exception( "No existe la función consultada en el API.", Globals::$HTTP_NO_ENCONTRADO);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON($th->getCode(), "ERROR", $th->getMessage());
}