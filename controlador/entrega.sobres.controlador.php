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

require_once '../negocio/EntregaSobre.clase.php';
$obj = new EntregaSobre();
$obj->id_usuario_registrado = $objUsuario["id_usuario_registrado"];
$op = $_GET["op"];

try {
    switch($op){
        case "listar_para_registrar":
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : "";
            if ($mes == ""){
                throw new Exception("Mes no válido.", 1);
            }

            $año = isset($_POST["p_anio"]) ? $_POST["p_anio"] : "";
            if ($año == ""){
                throw new Exception("Año no válido.", 1);
            }

            $monto_minimo = isset($_POST["p_monto_minimo"]) ? $_POST["p_monto_minimo"] : "";
            if ($monto_minimo == ""){
                throw new Exception("Monto mínimo no válido.", 1);
            }

            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : NULL;

            $data = $obj->listarParaRegistrar($mes, $año, $id_promotora, $monto_minimo);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "registrar_sobres":
            $JSONRegistros = isset($_POST["p_registros"]) ? $_POST["p_registros"] : "[]";
            if ($JSONRegistros == "[]"){
                throw new Exception("No hay registros de sobres para enviar.", 1);
            }

            $registros = json_decode($JSONRegistros, true);
            $data = $obj->registrarSobres($registros);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "registrar_actualizacion_sobres":
            $JSONRegistros = isset($_POST["p_registros"]) ? $_POST["p_registros"] : "[]";
            if ($JSONRegistros == "[]"){
                throw new Exception("No hay registros de sobres para enviar.", 1);
            }

            $registros = json_decode($JSONRegistros, true);
            $data = $obj->registrarActualizacionSobres($registros);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "listar_sobre_entrega":
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : "";
            if ($mes == ""){
                throw new Exception("Mes no válido.", 1);
            }

            $año = isset($_POST["p_anio"]) ? $_POST["p_anio"] : "";
            if ($año == ""){
                throw new Exception("Año no válido.", 1);
            }

            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : NULL;
            $data = $obj->listarSobresEntrega($mes, $año, $id_promotora);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}