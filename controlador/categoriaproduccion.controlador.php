<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/SubCategoriaServicio.clase.php';

$op = $_GET["op"];
$obj = new SubCategoriaServicio();

try {
    switch($op){
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->descripcion = isset($_POST["p_descripcion"]) ? strtoupper($_POST["p_descripcion"]) : NULL;

            if ($obj->descripcion == NULL || $obj->descripcion == ""){
                throw new Exception("No se ha enviado el nombre/descripción del registro.", 1);
            }

            $id = isset($_POST["p_id"]) ? $_POST["p_id"] : NULL;
            $obj->id_sub_categoria_servicio = $id;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            if ($id == ""){
                throw new Exception("Registro consultado no válido.", 1);
            }
            $obj->id_sub_categoria_servicio = $id;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
           $id = isset($_POST["p_id"]) ? $_POST["p_id"] : "";
            if ($id == ""){
                throw new Exception("Registro consultado no válida.", 1);
            }
            $obj->id_sub_categoria_servicio = $id;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;


       throw new Exception( "No existe la función consultada en el API.", 1);
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}