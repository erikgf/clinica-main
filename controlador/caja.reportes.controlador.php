<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/CajaReporte.clase.php';

$op = $_GET["op"];
$obj = new CajaReporte();

try {
    switch($op){
        case "obtener_caja_instancia_por_fecha":
            $fecha =  Funciones::sanitizar($_POST["p_fecha"]) == "" ? NULL : $_POST["p_fecha"];
            $data = $obj->obtenerCajaInstanciaPorFecha($fecha);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}