<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/ResumenDiario.clase.php';

$op = $_GET["op"];
$obj = new ResumenDiario();
/*
require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

*/

try {
    switch($op){
        case "generar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $obj->id_documento_electronico_rd = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarXML());
        break;

        case "firmar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $obj->id_documento_electronico_rd = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = false;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->firmarXML());
        break;
        /*

	    case "generar_firmar_xml":	
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $tc = $_POST["p_tc"];

            $obj->id_documento_electronico = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $tc == "01" ? $obj->generarFactura() : $obj->generarBoleta());
        break;
        */

        case "generar_firmar_resumenes_diarios":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            $notas = isset($_POST["p_nota"]) ? $_POST["p_nota"] : 0;
        
            $obj->registrar_en_bbdd = (isset($_POST["p_registrar"]) ? $_POST["p_registrar"] : "0") == "1";
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarResumenDiario($fecha_inicio, $fecha_final, "1", $notas));
        break;
        
        case "generar_firmar_resumenes_diarios_bajas":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            $obj->registrar_en_bbdd = (isset($_POST["p_registrar"]) ? $_POST["p_registrar"] : "0") == "1";
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarResumenDiario($fecha_inicio, $fecha_final, "3"));
        break;


        case "enviar_resumenes_diarios":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarResumenesDiarios($fecha_inicio, $fecha_final));

        break;

        case "consultar_tickets":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->consultarTickets($fecha_inicio, $fecha_final));
        break;

        case "generar_firmar_resumenes_diarios_x_id":
            $id_por_comas = $_POST["p_id"];
            if ($id_por_comas == ""){
                throw new Exception("Se necesita ingresar IDs.", 1);
            }

            $status_envio = isset($_POST["p_status"]) ? $_POST["p_status"] : "1";
            if ($status_envio == ""){
                throw new Exception("Se necesita ingresar status.", 1);
            }

            $obj->registrar_en_bbdd = (isset($_POST["p_registrar"]) ? $_POST["p_registrar"] : "0") == "1";
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarResumenDiarioXID($id_por_comas, $status_envio));
        break;

        case "enviar_resumenes_diarios_x_id":
            $id_por_comas = $_POST["p_id"];
            if ($id_por_comas == ""){
                throw new Exception("Se necesita ingresar IDs.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarResumenesDiariosXID($id_por_comas));

        break;

        case "consultar_tickets_x_id":
            $id_por_comas = $_POST["p_id"];
            if ($id_por_comas == ""){
                throw new Exception("Se necesita ingresar IDs.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->consultarTicketsXID($id_por_comas));
        break;

        case "copiar_resumen_diario_x_id":
            $arreglo_ids = $_POST["p_id"];
            if ($arreglo_ids == ""){
                throw new Exception("Se necesita ingresar IDs.", 1);
            }

            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            $arreglo_ids = json_decode($arreglo_ids, true);

            Funciones::imprimeJSON("200", "OK", $obj->copiarResumenDiarioXID($arreglo_ids));
        break;
        
        case "extra":
            Funciones::imprimeJSON("200", "OK", $obj->extra());
        break;
        
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}