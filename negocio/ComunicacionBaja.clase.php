<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/datos.empresa.php';
require_once '../datos/variables.php';

class ComunicacionBaja extends Conexion {
    public $id_documento_electronico_ra;
    public $id_usuario_registrado;
    public $fecha_emision;

    public $registrar_en_bbdd = true;
    public $firmar_comprobante = true;
    public $generar_xml = false;

    private $lpad_ceros_numero_correlativo = "6";
    private $RUTA_SISTEMA_FACTURACION = "http://localhost/sistema_facturacion/api/xml.generar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_FIRMADO = "http://localhost/sistema_facturacion/api/xml.firmar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_ENVIAR = "http://localhost/sistema_facturacion/api/xml.enviar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_CONSULTAR_TICKET_RD = "http://localhost/sistema_facturacion/api/xml.consultar.ticket.rd.php";

    private $fecha_ahora;

    public function generarXML(){
        try {

            if ($this->id_documento_electronico_ra == NULL || $this->id_documento_electronico_ra == ""){
                throw new Exception("ID Resumen Diario electrónico no válido.", 1);                    
            }
            $datosComprobante = $this->obtenerDatosParaCreacionXML();
        
            $data_json = json_encode($datosComprobante);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            curl_close($ch);

            $respuesta = json_decode($respuesta);
            
            $fue_generado = 0;
            if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                $fue_generado = 1;
            }

            return ["respuesta"=>$respuesta,"fue_generado"=>$fue_generado, "datos_comprobante"=>$datosComprobante];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function firmarXML($datosComprobante = NULL){
        try {

            if ($datosComprobante == NULL){
                if ($this->id_documento_electronico_ra == NULL || $this->id_documento_electronico_ra == ""){
                    throw new Exception("ID Comprobante electrónico no válido.", 1);                    
                }
                $datosComprobante = $this->obtenerDatosParaFirmaXML();
            }
        
            $data_json = json_encode($datosComprobante);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_FIRMADO);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuestafirma  = curl_exec($ch);
            curl_close($ch);
        
            $respuestafirma = json_decode($respuestafirma);
            $valor_firma = "";
            $valor_resumen = "";
        
            if (isset($respuestafirma->respuesta) && $respuestafirma->respuesta == "ok"){
                $valor_firma = $respuestafirma->signature_cpe;
                $valor_resumen = $respuestafirma->hash_cpe;
            }

            return ["respuestafirma"=>$respuestafirma,"valor_firma"=>$valor_firma, "valor_resumen"=>$valor_resumen];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDatosParaCreacionXML(){
        try {
            $sql  = "SELECT 
                    ra.codigo as CODIGO,
                    ra.serie as SERIE,
                    ra.secuencia as SECUENCIA,
                    ra.fecha_baja as FECHA_REFERENCIA,
                    ra.fecha_baja as FECHA_BAJA,
                    ra.fecha_baja as FECHA_EMISION,
                    ra.fecha_generacion as FECHA_DOCUMENTO,
                    ra.nombre_resumen as NOMBRE_RESUMEN,
                    ra.codigo as COD_TIPO_DOCUMENTO,
                    CONCAT(ra.serie,'-',ra.secuencia) as NRO_COMPROBANTE
                    FROM documento_electronico_comunicacion_baja ra
                    WHERE ra.id_documento_electronico_comunicacion_baja = :0 AND ra.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico_ra]);

            if ($datos == false){
                throw new Exception("Comprobante no existe en el sistema.", 1);
            }

            $datos["NRO_DOCUMENTO_EMPRESA"] = F_RUC;
            $datos["TIPO_DOCUMENTO_EMPRESA"] = "6";
            $datos["NOMBRE_COMERCIAL_EMPRESA"] = F_NOMBRE_COMERCIAL;
            $datos["CODIGO_UBIGEO_EMPRESA"] = F_CODIGO_UBIGEO;
            $datos["DIRECCION_EMPRESA"] = F_DIRECCION;
            $datos["DEPARTAMENTO_EMPRESA"] = F_DIRECCION_DEPARTAMENTO;
            $datos["PROVINCIA_EMPRESA"] = F_DIRECCION_PROVINCIA;
            $datos["DISTRITO_EMPRESA"] = F_DIRECCION_DISTRITO;
            $datos["URBANIZACION_EMPRESA"] = F_URBANIZACION;

            $datos["CODIGO_PAIS_EMPRESA"] = F_CODIGO_PAIS;
            $datos["RAZON_SOCIAL_EMPRESA"] = F_RAZON_SOCIAL;
            $datos["CONTACTO_EMPRESA"] = "";

            $datos["EMISOR_RUC"] = F_RUC;
            $datos["EMISOR_USUARIO_SOL"] = F_USUARIO_SOL;
            $datos["EMISOR_PASS_SOL"] = F_CLAVE_SOL;

            $sql = "SELECT  
                        item as ITEM,
                        CONCAT(serie_comprobante,'-',numero_correlativo_comprobante) as NRO_COMPROBANTE,
                        idtipo_comprobante as TIPO_COMPROBANTE,
                        serie_comprobante as SERIE,
                        numero_correlativo_comprobante as NUMERO,
                        motivo as MOTIVO
                        FROM documento_electronico_comunicacion_baja_detalle rdd
                        WHERE id_documento_electronico_comunicacion_baja = :0 AND estado_mrcb";
            $datos["detalle"] = $this->consultarFilas($sql, [$this->id_documento_electronico_ra]);

            $datos["tipo_proceso"] = F_MODO_PROCESO;
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDatosParaFirmaXML(){
        try {

            $sql  = "SELECT 
                    rd.nombre_resumen as NOMBRE_RESUMEN,
                    rd.codigo as COD_TIPO_DOCUMENTO,
                    CONCAT(rd.serie,'-',rd.secuencia) as NRO_COMPROBANTE,
                    rd.fecha_baja as FECHA_EMISION,
                    rd.fecha_generacion as FECHA_DOCUMENTO
                    FROM documento_electronico_comunicacion_baja rd
                    WHERE rd.id_documento_electronico_comunicacion_baja = :0 AND rd.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico_ra]);

            if ($datos == false){
                throw new Exception("Comprobante no existe en el sistema.", 1);
            }

            $datos["EMISOR_RUC"] = F_RUC;
            $datos["tipo_proceso"] = F_MODO_PROCESO;
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function enviarComunicacionBajaXId($id_por_comas, $forzado = false){
        try {   

            /*Sí es $forzado = true, enviará el documento aun ya haya sido enviado y tenga un ticket asociado.*/
            if ($forzado){
                $sql  = "SELECT id_documento_electronico_comunicacion_baja as id, nombre_resumen as nombre_archivo, fecha_baja  as fecha_emision
                        FROM documento_electronico_comunicacion_baja
                        WHERE estado_mrcb AND id_documento_electronico_comunicacion_baja  IN ('".$id_por_comas."') AND cdr_estado > 0";
            } else {
                $sql  = "SELECT id_documento_electronico_comunicacion_baja as id, nombre_resumen as nombre_archivo, fecha_baja  as fecha_emision
                        FROM documento_electronico_comunicacion_baja
                        WHERE estado_mrcb AND  ticket IS NULL AND id_documento_electronico_comunicacion_baja  IN ('".$id_por_comas."') AND numero_envios <= 0 AND cdr_estado IS NULL";
            }
            
            $resumenes = $this->consultarFilas($sql);

            $data_json = json_encode(["resumenes"=>$resumenes, "id_tipo_comprobante"=>"RA", "tipo_proceso"=>F_MODO_PROCESO]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_ENVIAR);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuestasEnvio  = curl_exec($ch);
            curl_close($ch);

            $respuestasEnvio = json_decode($respuestasEnvio);

            $this->beginTransaction();

            foreach ($respuestasEnvio as $key => $respuesta) {
                $ticket = NULL;

                if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                    $ticket = $respuesta->cod_ticket;
                }

                $sql  = "UPDATE documento_electronico_comunicacion_baja SET 
                        ticket = ".($ticket ? "'".$ticket."'" : 'NULL').",
                        numero_envios = numero_envios + 1,
                        fecha_hora_envio = CURRENT_TIMESTAMP,
                        estado_sunat = ".($ticket ? '1' : '0')."
                        WHERE 
                        estado_mrcb AND 
                        id_documento_electronico_comunicacion_baja = '".$respuesta->id."'";
                $this->consultaRaw($sql);
            }
       
            $this->commit();

            return ["respuestas"=>$respuestasEnvio];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function consultarTicketsXID($id){
        try {

            $sql  = "SELECT id_documento_electronico_comunicacion_baja as id_documento_electronico_resumen_diario, nombre_resumen, fecha_baja as fecha_emision, ticket 
                        FROM documento_electronico_comunicacion_baja
                        WHERE estado_mrcb AND  ticket IS NOT NULL AND id_documento_electronico_comunicacion_baja  IN ('".$id."') ";
            $tickets = $this->consultarFilas($sql);

            $data_json = json_encode(["tickets"=>$tickets, "id_tipo_comprobante"=>"RA", "tipo_proceso"=>F_MODO_PROCESO]);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->RUTA_SISTEMA_FACTURACION_CONSULTAR_TICKET_RD);
            curl_setopt(
                $ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuestas  = curl_exec($ch);
            curl_close($ch);

            $respuestas = json_decode($respuestas);
            

            if ($respuestas != null){
                $this->beginTransaction();

                foreach ($respuestas as $key => $respuesta) {
                    $cod_sunat = NULL;
                    $msj_sunat = NULL;
                    $hash_cdr = NULL;
    
                    if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                        $cod_sunat = $respuesta->cod_sunat;
                        $msj_sunat = $respuesta->msj_sunat;
                        $hash_cdr = $respuesta->hash_cdr;
                    } else {
                        $cod_sunat = $respuesta->cod_sunat;
                        $msj_sunat = $respuesta->mensaje;
                    }
    
                    $sql  = "UPDATE documento_electronico_comunicacion_baja SET 
                            cdr_estado = ".(isset($cod_sunat) ? "'".$cod_sunat."'" : 'NULL').",
                            cdr_descripcion = ".(isset($msj_sunat) ? "'".str_replace("'","\'",$msj_sunat)."'" : 'NULL').",
                            hash_cdr = ".($hash_cdr ? "'".$hash_cdr."'" : 'NULL')."
                            WHERE 
                            estado_mrcb AND id_documento_electronico_comunicacion_baja = '".$respuesta->id_documento_electronico_resumen_diario."'";
                    $this->consultaRaw($sql);
                }

                $this->commit();
            }

            return ["respuestas"=>$respuestas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}