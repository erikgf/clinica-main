<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Liquidacion.clase.php';
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

require_once '../negocio/Liquidacion.clase.php';
$obj = new Liquidacion();
$obj->id_usuario_registrado = $objUsuario["id_usuario_registrado"];
$op = $_GET["op"];

try {

    switch($op){
        case "calcular":
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : "";
            if ($mes == ""){
                throw new Exception("Mes no válido.", 1);
            }

            $anio = isset($_POST["p_anio"]) ? $_POST["p_anio"] : "";
            if ($anio == ""){
                throw new Exception("Año no válido.", 1);
            }

            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Año no válido.", 1);
            }

            $data = $obj->calcular($id_promotora, $mes, $anio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "obtener_liquidaciones_imprimir":
            $mes = isset($_POST["p_mes"]) ? $_POST["p_mes"] : "";
            if ($mes == ""){
                throw new Exception("Mes no válido.", 1);
            }

            $anio = isset($_POST["p_anio"]) ? $_POST["p_anio"] : "";
            if ($anio == ""){
                throw new Exception("Año no válido.", 1);
            }

            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Año no válido.", 1);
            }

            $data = $obj->obtenerLiquidacionesImprimir($id_promotora, $mes, $anio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "calcular_hasta_mayo_2024":
            $data = $obj->calcularHastaMayo2024();
            Funciones::imprimeJSON("200", "OK", $data);
            break;
        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}