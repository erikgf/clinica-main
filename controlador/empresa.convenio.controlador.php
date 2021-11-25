<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/EmpresaConvenio.clase.php';

$op = $_GET["op"];
$obj = new EmpresaConvenio();

try {
    switch($op){
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            $obj->razon_social = isset($_POST["p_razon_social"]) ? $_POST["p_razon_social"] : NULL;

            if ($obj->razon_social == NULL || $obj->razon_social == ""){
                throw new Exception("No se ha enviado el nombre/descripción del registro.", 1);
            }

            $obj->id_empresa_convenio = isset($_POST["p_id_empresa_convenio"]) ? $_POST["p_id_empresa_convenio"] : NULL;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id_empresa_convenio = isset($_POST["p_id_empresa_convenio"]) ? $_POST["p_id_empresa_convenio"] : "";
            if ($id_empresa_convenio == ""){
                throw new Exception("Registro consultado no válido.", 1);
            }
            $obj->id_empresa_convenio = $id_empresa_convenio;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "dar_baja":
            $id_empresa_convenio = isset($_POST["p_id_empresa_convenio"]) ? $_POST["p_id_empresa_convenio"] : "";
            if ($id_empresa_convenio == ""){
                throw new Exception("Registro consultado no válida.", 1);
            }
            $obj->id_empresa_convenio = $id_empresa_convenio;
            $obj->estado = isset($_POST["p_estado"]) ? $_POST["p_estado"] : "I";

            $data = $obj->darBaja();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
            $id_empresa_convenio = isset($_POST["p_id_empresa_convenio"]) ? $_POST["p_id_empresa_convenio"] : "";
            if ($id_empresa_convenio == ""){
                throw new Exception("Registro consultado no válida.", 1);
            }
            $obj->id_empresa_convenio = $id_empresa_convenio;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_combo":
            $data = $obj->obtenerCombo();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

       throw new Exception( "No existe la función consultada en el API.", 1);
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}