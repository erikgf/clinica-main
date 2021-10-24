<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/DocumentoElectronico.clase.php';

$op = $_GET["op"];
$obj = new DocumentoElectronico();

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

            $obj->id_documento_electronico = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarBoleta());
        break;

        case "firmar_xml":
            $id = $_POST["p_id"];
            if ($id == ""){
                throw new Exception("No se ha enviado ID.", 1);
            }

            $obj->id_documento_electronico = $id;
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = false;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarFactura());
        break;

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

        case "consultar_documento_cliente":
            $numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : "";
            Funciones::imprimeJSON("200", "OK", $obj->consultarDocumentoCliente($numero_documento));
        break;
    
        case "generar_firmar_xml_todos":	
            $obj->registrar_en_bbdd = false;
            $obj->generar_xml = true;
            $obj->firmar_xml = true;

            Funciones::imprimeJSON("200", "OK", $obj->generarTodos());
        break;

        case "generar_firmar_resumenes_diarios":
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

            Funciones::imprimeJSON("200", "OK", $obj->generarResumenDiario($fecha_inicio, $fecha_final));
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

            Funciones::imprimeJSON("200", "OK", $obj->generarResumenDiarioBajas($fecha_inicio, $fecha_final));
        break;
            
        case "crear_notas_comprobantes_anulados":
            $fecha_inicio = $_POST["p_fecha_inicio"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_fecha_final"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->_crearNotasCreditoComprobantesAnulados($fecha_inicio, $fecha_final));
        break;
        
        case "listar_atenciones_comprobante":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);

            $data = $obj->listarAtencionesComprobantes($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "anular_comprobante_nota":
            $idUsuarioAnulado = Funciones::sanitizar($_POST["p_id_usuario"]);
            $motivo_anulacion = Funciones::sanitizar($_POST["p_motivo_anulacion"]);
            $iddocumento_electronico = Funciones::sanitizar($_POST["p_iddocumento_electronico"]);
            $comprobanteAsociado["iddocumento_electronico"] = $iddocumento_electronico;

            $data = $obj->anularComprobanteDesdeAtencionMedica($comprobanteAsociado, $motivo_anulacion, $idUsuarioAnulado);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "enviar_comprobante_factura":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarComprobantesFactura("01",$fecha_inicio, $fecha_final));

        break;

        case "enviar_comprobante_nota_credito_factura":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarComprobantesFactura("07", $fecha_inicio, $fecha_final));

        break;

        case "enviar_comprobante_nota_debito_factura":
            $fecha_inicio = $_POST["p_fi"];
            if ($fecha_inicio == ""){
                throw new Exception("No se ha enviado FECHA INICIO para procesar.", 1);
            }

            $fecha_final = $_POST["p_ff"];
            if ($fecha_final == ""){
                throw new Exception("No se ha enviado FECHA FINAL para procesar.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->enviarComprobantesFactura("08", $fecha_inicio, $fecha_final));

        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}