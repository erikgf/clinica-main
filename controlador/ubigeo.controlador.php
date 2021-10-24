<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Ubigeo.clase.php';

$op = $_GET["op"];
$obj = new Ubigeo();

try {
    switch($op){
        case "obtener_departamentos":
            $cadenaBuscar = $_POST["p_cadenabuscar"];

            $data = $obj->obtenerDepartamentos($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "obtener_provincias":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $idDepartamento = $_POST["p_iddepartamento"];

            $data = $obj->obtenerProvincias($cadenaBuscar, $idDepartamento);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "obtener_distritos":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $idProvincia = $_POST["p_idprovincia"];

            $data = $obj->obtenerDistritos($cadenaBuscar, $idProvincia);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}