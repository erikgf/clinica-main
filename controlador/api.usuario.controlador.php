<?php 

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../datos/local_config_web.php';
require_once '../negocio/Usuario.clase.php';
require_once '../negocio/Sesion.clase.php';

$op = $_GET["op"];
$obj = new Usuario();

$bodyRequest = file_get_contents("php://input");
$json_post = json_decode($bodyRequest, true);

try {
    switch($op){
        case "iniciar_sesion":
            $obj->nombre_usuario = $json_post["p_nombre_usuario"];
            $obj->clave = $json_post["p_clave"];

            $data = $obj->iniciarSesion();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "cambiar_clave":
            $obj->id_usuario = $json_post["p_id_usuario"];
            $antigua_clave = $json_post["p_antigua_clave"];
            $nueva_clave = $json_post["p_nueva_clave"];
            
            $data = $obj->cambiarclave($antigua_clave, $nueva_clave);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}