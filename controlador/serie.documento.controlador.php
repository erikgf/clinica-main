<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/SerieDocumento.clase.php';

$op = $_GET["op"];
$obj = new SerieDocumento();

try {
    switch($op){
        case "obtener_series":
            $tipoComprobantes = $_POST["p_tipocomprobantes"];
            if ($tipoComprobantes == ""){
                throw new Exception("No se ha enviado tipo de comprobantes.", 1);
            }

            $tipoComprobantes = json_decode($tipoComprobantes);
            $data = $obj->obtenerSeries($tipoComprobantes);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}