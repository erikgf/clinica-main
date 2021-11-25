<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/LabUnidad.clase.php';

$op = $_GET["op"];
$obj = new LabUnidad();

try {
    switch($op){
        case "buscar_combo":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscarCombo($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : NULL;

            if ($obj->descripcion == NULL || $obj->descripcion == ""){
                throw new Exception("No se ha enviado el nombre/descripción del registro.", 1);
            }

            $id_lab_unidad = isset($_POST["p_id_lab_unidad"]) ? $_POST["p_id_lab_unidad"] : NULL;
            $obj->id_lab_unidad = $id_lab_unidad;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id_lab_unidad = isset($_POST["p_id_lab_unidad"]) ? $_POST["p_id_lab_unidad"] : "";
            if ($id_lab_unidad == ""){
                throw new Exception("Registro consultado no válido.", 1);
            }
            $obj->id_lab_unidad = $id_lab_unidad;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
           $id_lab_unidad = isset($_POST["p_id_lab_unidad"]) ? $_POST["p_id_lab_unidad"] : "";
            if ($id_lab_unidad == ""){
                throw new Exception("Registro consultado no válida.", 1);
            }
            $obj->id_lab_unidad = $id_lab_unidad;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

       throw new Exception( "No existe la función consultada en el API.", 1);
    }
} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}