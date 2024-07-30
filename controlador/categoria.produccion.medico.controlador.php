<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/CategoriaProduccionMedico.clase.php';

$op = $_GET["op"];
$obj = new CategoriaProduccionMedico();

try {
    switch($op){
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "registrar":
            $obj->id_medico = isset($_POST["p_id_medico"]) ? strtoupper($_POST["p_id_medico"]) : NULL;
            $obj->id_sub_categoria_servicio = isset($_POST["p_id_sub_categoria_servicio"]) ? strtoupper($_POST["p_id_sub_categoria_servicio"]) : NULL;
            $obj->valor = isset($_POST["p_valor"]) ? strtoupper($_POST["p_valor"]) : "";
            $obj->tipo_valor = isset($_POST["p_tipo_valor"]) ? strtoupper($_POST["p_tipo_valor"]) : NULL;

            if ($obj->tipo_valor == NULL || $obj->tipo_valor == ""){
                throw new Exception("No se ha enviado el tipo de valor del registro.", 1);
            }

            $data = $obj->registrar();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "eliminar_medico":
            $obj->id_medico = isset($_POST["p_id_medico"]) ? strtoupper($_POST["p_id_medico"]) : NULL;

            $data = $obj->eliminarMedico();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "eliminar":
            $obj->id_medico = isset($_POST["p_id_medico"]) ? strtoupper($_POST["p_id_medico"]) : NULL;
            $obj->id_sub_categoria_servicio = isset($_POST["p_id_sub_categoria_servicio"]) ? strtoupper($_POST["p_id_sub_categoria_servicio"]) : NULL;

            $data = $obj->eliminar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;


       throw new Exception( "No existe la función consultada en el API.", 1);
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}