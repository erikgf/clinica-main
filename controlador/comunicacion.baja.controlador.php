<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/ComunicacionBaja.clase.php';

$op = $_GET["op"];
$obj = new ComunicacionBaja();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "generar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $obj->id_documento_electronico_ra = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarXML());
        break;

        case "firmar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $obj->id_documento_electronico_ra = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = false;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->firmarXML());
        break;

        case "consultar_tickets":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("Se necesita ingresar ID.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->consultarTicketsXID($id));
        break;

        case "generar_firmar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("Se necesita ingresar ID.", 1);
            }

            $obj->id_documento_electronico_ra = $id;
            $obj->registrar_en_bbdd = 0;
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            $generar = $obj->generarXML();
            $firmar = $obj->firmarXML();

            Funciones::imprimeJSON("200", "OK", ["generar"=>$generar, "firmar"=>$firmar] );
        break;

        case "enviar":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("Se necesita ingresar ID.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarComunicacionBajaXId($id));
        break;
        
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}