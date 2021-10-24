<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('America/Lima');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../datos/local_config_web.php';
require "../negocio/AtencionMedica.clase.php";

$op = $_GET["op"];
$obj = new AtencionMedica();

$bodyRequest = file_get_contents("php://input");
$json_post = json_decode($bodyRequest, true);

try {
    switch($op){
        case "listar_atenciones_general":
            $fecha_inicio = Funciones::sanitizar($json_post["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($json_post["p_fecha_fin"]);

            $data = $obj->listarAtencionesGeneral($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}
