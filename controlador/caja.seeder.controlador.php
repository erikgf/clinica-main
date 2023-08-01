<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/CajaSeeder.clase.php';

$op = $_GET["op"];
$obj = new CajaSeeder();

try {
    switch($op){
        case "actualizar_correlativo_proactivo":
            $data = $obj->actualizarCorrelativoProactivo();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}