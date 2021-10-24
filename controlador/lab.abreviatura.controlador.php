<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/LabAbreviatura.clase.php';

$op = $_GET["op"];
$obj = new LabAbreviatura();

try {
    switch($op){
        case "obtener_abreviaturas":
            $id_atencion_medica_servicio = $_POST["p_id_atencion_medica_servicio"];
            $data = $obj->obtenerAbreviaturas($id_atencion_medica_servicio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

       throw new Exception( "No existe la función consultada en el API.", 1);
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}