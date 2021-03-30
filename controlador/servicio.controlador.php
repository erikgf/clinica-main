<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Servicio.clase.php';

$op = $_GET["op"];
$obj = new Servicio();

try {
    switch($op){
        case "buscar":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $obj->id_categoria_servicio = isset($_POST["p_idcategoria"]) ? $_POST["p_idcategoria"] : NULL;

            $data = $obj->buscar($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR", $th->getMessage());
}