<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Campaña.clase.php';

$op = $_GET["op"];
$obj = new Campaña();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
    exit;
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "obtener":
            $id_caja = isset($_POST["p_idcaja"]) ? $_POST["p_idcaja"] : NULL;
            if ($id_caja == NULL || $id_caja == ""){
                Funciones::imprimeJSON("200", "OK", []);
                exit; 
            }

            $data = $obj->obtener($id_caja);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->nombre = isset($_POST["p_nombre"]) ? $_POST["p_nombre"] : NULL;
            if ($obj->nombre == NULL || $obj->nombre == ""){
                throw new Exception("No se ha enviado el descripción", 1);
            }
            $obj->descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : NULL;
            if ($obj->descripcion == NULL || $obj->descripcion == ""){
                throw new Exception("No se ha enviado el descripción", 1);
            }

            $obj->fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : NULL;
            if ($obj->fecha_inicio == NULL || $obj->fecha_inicio == ""){
                throw new Exception("No se ha enviado la fecha de inicio de la campaña", 1);
            }

            $obj->fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : NULL;
            if ($obj->fecha_fin == NULL || $obj->fecha_fin == ""){
                throw new Exception("No se ha enviado la fecha fin de la campaña", 1);
            }

            $obj->id_campaña = isset($_POST["p_id_campaña"]) ? $_POST["p_id_campaña"] : NULL;

            $obj->id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : NULL;
            if ($obj->id_sede == NULL || $obj->id_sede == ""){
                throw new Exception("No se ha enviado la sede de la campaña", 1);
            }

            $obj->monto_maximo = (isset($_POST["p_monto_maximo"]) && $_POST["p_monto_maximo"] != "") ? $_POST["p_monto_maximo"] : NULL;
            $obj->monto_minimo = (isset($_POST["p_monto_minimo"]) && $_POST["p_monto_minimo"] != "") ? $_POST["p_monto_minimo"] : NULL;
            $obj->tipo_pago = isset($_POST["p_tipo_pago"]) ? $_POST["p_tipo_pago"] : NULL;
            if ($obj->tipo_pago == NULL || $obj->tipo_pago == ""){
                throw new Exception("No se ha enviado el tipo de pago válido de la campaña", 1);
            }
            $obj->descuento_categorias_json = isset($_POST["p_descuento_categorias_json"]) ? $_POST["p_descuento_categorias_json"] : '[]';
            
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id_campaña = isset($_POST["p_id_campaña"]) ? $_POST["p_id_campaña"] : "";
            if ($id_campaña == ""){
                throw new Exception("Campaña consultada no válida.", 1);
            }
            $obj->id_campaña = $id_campaña;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
            $id_campaña = isset($_POST["p_id_campaña"]) ? $_POST["p_id_campaña"] : "";
            if ($id_campaña == ""){
                throw new Exception("Campaña consultada no válida.", 1);
            }
            $obj->id_campaña = $id_campaña;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}