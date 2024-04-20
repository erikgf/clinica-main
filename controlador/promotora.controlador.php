<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Promotora.clase.php';

$op = $_GET["op"];
$obj = new Promotora();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            $obj->descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : NULL;

            if ($obj->descripcion == NULL || $obj->descripcion == ""){
                throw new Exception("No se ha enviado el nombre/descripción de promotora", 1);
            }

            $obj->porcentaje_comision = isset($_POST["p_comision"]) ? $_POST["p_comision"] : NULL;

            if ($obj->porcentaje_comision == NULL || $obj->porcentaje_comision == ""){
                throw new Exception("No se ha enviado el nombre/descripción de promotora", 1);
            }

            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : NULL;
            $obj->id_promotora = $id_promotora;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Promotora consultada no válida.", 1);
            }
            $obj->id_promotora = $id_promotora;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
           $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Promotora consultada no válida.", 1);
            }
            $obj->id_promotora = $id_promotora;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "asignar_medicos_promotora":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            $arregloIdMedicos = json_decode(isset($_POST["p_arreglo_id_medicos"]) ? $_POST["p_arreglo_id_medicos"] : "");

            if ($id_promotora == ""){
                throw new Exception("Promotora ingresada no válida.", 1);
            }

            $obj->id_promotora = $id_promotora;
            
            $data = $obj->asignarMedicosPromotora($arregloIdMedicos);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "quitar_medicos_promotora":
            $arregloIdMedicos = json_decode(isset($_POST["p_arreglo_id_medicos"]) ? $_POST["p_arreglo_id_medicos"] : "");

            $data = $obj->quitarMedicosPromotora($arregloIdMedicos);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_usuario":
            $data = $obj->listarUsuario();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar_usuario":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Promotora a registrar no válida.", 1);
            }

            $obj->id_promotora = $id_promotora;
            $obj->estado_acceso = isset($_POST["p_estado_acceso"]) ? $_POST["p_estado_acceso"] : NULL;
            $data = $obj->guardarUsuario();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer_usuario":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";
            if ($id_promotora == ""){
                throw new Exception("Promotora consultada no válida.", 1);
            }
            $obj->id_promotora = $id_promotora;

            $data = $obj->leerUsuario();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "cambiar_clave":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : NULL;
            $obj->id_promotora = $id_promotora;

            $obj->clave = isset($_POST["p_clave"]) ? $_POST["p_clave"] : NULL;
            if ($obj->clave == "" || $obj->clave == NULL){
                throw new Exception("Se debe ingresar una clave válida.", 1);
            }

            $data = $obj->cambiarClave();
            Funciones::imprimeJSON("200", "OK", $data);
        break;


        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}