<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Especialidad.clase.php';

$op = $_GET["op"];
$obj = new Especialidad();

try {
    switch($op){
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

       throw new Exception( "No existe la función consultada en el API.", 1);
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}