<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/datos.empresa.php';
require_once '../datos/variables.php';

class ResumenDiario extends Conexion {
    public $id_documento_electronico_rd;
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

    public function generarResumenDiario($fecha_inicio, $fecha_fin, $status_envio = "1"){ //estatus_Envio = 1 registro, 3 = anulado
        try {

            $this->beginTransaction();

            $this->fecha_ahora = date("Y-m-d H:i:s");
            $fechaGeneracion = date("Y-m-d");
            $codigo = "RC";

            if ($status_envio == "1"){
                $sql = "SELECT distinct(de.fecha_emision)  as fecha_emision,
                        (SELECT  COALESCE(MAX(rd.secuencia) + 1, 1) 
                        FROM documento_electronico_resumen_diario rd 
                        WHERE rd.estado_mrcb and rd.fecha_generacion = '$fechaGeneracion') as secuencia
                        FROM documento_electronico de                     
                        WHERE de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%' AND 
                            de.estado_mrcb AND (de.fecha_emision BETWEEN :0 AND :1)";
            } else {
                $sql = "SELECT distinct(de.fecha_emision)  as fecha_emision,
                        (SELECT  COALESCE(MAX(rd.secuencia) + 1, 1) 
                        FROM documento_electronico_resumen_diario rd 
                        WHERE rd.estado_mrcb and rd.fecha_generacion = '$fechaGeneracion') as secuencia
                        FROM documento_electronico de                     
                        WHERE de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%' AND 
                            de.estado_mrcb AND (de.fecha_emision BETWEEN :0 AND :1) AND de.estado_anulado = 1 AND (DATE(de.fecha_hora_anulacion) = de.fecha_emision)";
            }
            
            $rangoFechas = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            $secuencia = 0;
            foreach ($rangoFechas as $key => $objResumen) {
                $fechaEmision = $objResumen["fecha_emision"];
                //$fechaGeneracion = date('Y-m-d', strtotime($fechaEmision . ' +5 day'));
                $serie = str_replace("-","",$fechaGeneracion);
                $secuencia = $secuencia == 0  ? $objResumen["secuencia"] : ($secuencia + 1);

                $sqlComprobantes = "SELECT 
                                    CONCAT(de.serie,'-', LPAD(de.numero_correlativo,6,'0')) as NRO_COMPROBANTE,
                                    de.serie as SERIE_COMPROBANTE,
                                    de.numero_correlativo as CORRELATIVO_COMPROBANTE,
                                    de.idtipo_comprobante as TIPO_COMPROBANTE,
                                    de.idtipo_moneda as COD_MONEDA,
                                    de.numero_documento_cliente as NRO_DOCUMENTO,
                                    de.idtipo_documento_cliente as TIPO_DOCUMENTO,
                                    de.idtipo_comprobante_modifica as TIPO_COMPROBANTE_REF,
                                    COALESCE(CONCAT(de.serie_documento_modifica,'-',de.numero_documento_modifica),'') as NRO_COMPROBANTE_REF,
                                    COALESCE(de.serie_documento_modifica,'') as SERIE_COMPROBANTE_REF,
                                    COALESCE(de.numero_documento_modifica,'') as CORRELATIVO_COMPROBANTE_REF,
                                    '$status_envio' as STATUS,
                                    de.total_igv as IGV,
                                    de.total_isc as ISC,
                                    de.idtipo_moneda as COD_MONEDA,
                                    de.total_otro_imp as OTROS,
                                    de.importe_total as TOTAL,
                                    de.total_gravadas as GRAVADA,
                                    de.total_inafectas as INAFECTO,
                                    de.total_exoneradas as EXONERADO,
                                    '0.00' as EXPORTACION,
                                    '0.00' as GRATUITAS
                                    FROM documento_electronico de 
                                    WHERE de.fecha_emision = :0 AND de.estado_mrcb 
                                        AND de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%'
                                        AND cdr_estado IS NULL AND estado_sunat = 0
                                        AND ".($status_envio == "1" ? " true " : "  estado_anulado = 1 AND (DATE(de.fecha_hora_anulacion) = de.fecha_emision) ");
                
                $comprobantes = $this->consultarFilas($sqlComprobantes, [$fechaEmision]);

                if (count($comprobantes) <= 0 ){
                    continue;
                }

                $campos_valores = [
                    "codigo" => $codigo,
                    "serie"=>$serie,
                    "secuencia"=>$secuencia,
                    "fecha_emision"=>$fechaEmision,
                    "fecha_generacion"=>$fechaGeneracion,
                    "nombre_resumen"=>F_RUC.'-'.$codigo."-".$serie."-".$secuencia,
                    "fecha_hora_registro"=>$this->fecha_ahora
                ];

                $this->insert("documento_electronico_resumen_diario", $campos_valores);
                $this->id_documento_electronico_rd = $this->getLastID();

                $campos = [
                    "id_documento_electronico_resumen_diario",
                    "item",
                    "idtipo_comprobante",
                    "serie_comprobante",
                    "numero_correlativo_comprobante",
                    "idtipo_documento_cliente",
                    "numero_documento_cliente",
                    "serie_comprobante_modificado",
                    "numero_correlativo_comprobante_modificado",
                    "idtipo_comprobante_modificado",
                    "status",
                    "id_moneda",
                    "importe_gravadas",
                    "importe_exoneradas",
                    "importe_inafectas",
                    "importe_exportacion",
                    "importe_gratuitas",
                    "importe_otros",
                    "importe_igv",
                    "importe_isc",
                    "importe_total"
                ];
                $valores = [];
                foreach ($comprobantes as $key => $value) {
                    array_push($valores, [
                        $this->id_documento_electronico_rd,
                        ($key + 1),
                        $value["TIPO_COMPROBANTE"],
                        $value["SERIE_COMPROBANTE"],
                        $value["CORRELATIVO_COMPROBANTE"],
                        $value["TIPO_DOCUMENTO"],
                        $value["NRO_DOCUMENTO"],
                        $value["SERIE_COMPROBANTE_REF"],
                        $value["CORRELATIVO_COMPROBANTE_REF"],
                        $value["TIPO_COMPROBANTE_REF"],
                        $value["STATUS"],
                        $value["COD_MONEDA"],
                        $value["GRAVADA"],
                        $value["EXONERADO"],
                        $value["INAFECTO"],
                        $value["EXPORTACION"],
                        $value["GRATUITAS"],
                        $value["OTROS"],
                        $value["IGV"],
                        $value["ISC"],
                        $value["TOTAL"]
                    ]);
                }

                $this->insertMultiple("documento_electronico_resumen_diario_detalle", $campos, $valores);

                if ($this->generar_xml){
                    $objXMLComprobante = $this->generarXML();
                    $fue_generado = $objXMLComprobante["fue_generado"];
                    $datosComprobante = $objXMLComprobante["datos_comprobante"];
                    $respuesta = $objXMLComprobante["respuesta"];
                }
    
                if ($this->firmar_comprobante){
                    //DEBO FIRMAR EL BENDITO COMPROBANTE ?
                    $objXMLFirmaComprobante =  $this->firmarXML($datosComprobante);
                    $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                    $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                    $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
                }
                
                $campos_valores = [];

                if ($fue_generado == "1"){
                    $campos_valores["fue_generado"] = $fue_generado;
                }
    
                if ($valor_firma != NULL){
                    $campos_valores["fue_firmado"] = "1";
                    $campos_valores["valor_firma"] = $valor_firma;
                    $campos_valores["valor_resumen"] = $valor_resumen;
                }
    
                if (count($campos_valores)){
                    $this->update("documento_electronico_resumen_diario", 
                                $campos_valores,
                                ["id_documento_electronico_resumen_diario"=>$this->id_documento_electronico_rd]);
                }
            }

            $this->commit();
            return  ["msj"=>"Se ha generado ".count($rangoFechas)." resumenes diarios."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarXML(){
        try {

            if ($this->id_documento_electronico_rd == NULL || $this->id_documento_electronico_rd == ""){
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
                if ($this->id_documento_electronico_rd == NULL || $this->id_documento_electronico_rd == ""){
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
                    rd.codigo as CODIGO,
                    rd.serie as SERIE,
                    rd.secuencia as SECUENCIA,
                    rd.fecha_emision as FECHA_REFERENCIA,
                    rd.fecha_emision as FECHA_EMISION,
                    rd.fecha_generacion as FECHA_DOCUMENTO,
                    rd.nombre_resumen as NOMBRE_RESUMEN,
                    rd.codigo as COD_TIPO_DOCUMENTO,
                    CONCAT(rd.serie,'-',rd.secuencia) as NRO_COMPROBANTE
                    FROM documento_electronico_resumen_diario rd
                    WHERE rd.id_documento_electronico_resumen_diario = :0 AND rd.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico_rd]);

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
                        id_moneda as COD_MONEDA,
                        numero_documento_cliente as NRO_DOCUMENTO,
                        idtipo_documento_cliente as TIPO_DOCUMENTO,
                        idtipo_comprobante_modificado as TIPO_COMPROBANTE_REF,
                        CONCAT(serie_comprobante_modificado,'-',numero_correlativo_comprobante_modificado) as NRO_COMPROBANTE_REF,
                        status as STATUS,
                        importe_igv as IGV,
                        importe_isc as ISC,
                        importe_otros as OTROS,
                        importe_total as TOTAL,
                        importe_gravadas as GRAVADA,
                        importe_inafectas as INAFECTO,
                        importe_exoneradas as EXONERADO,
                        importe_exportacion as EXPORTACION,
                        importe_gratuitas as GRATUITAS
                        FROM documento_electronico_resumen_diario_detalle rdd
                        WHERE id_documento_electronico_resumen_diario = :0 AND estado_mrcb";
            $datos["detalle"] = $this->consultarFilas($sql, [$this->id_documento_electronico_rd]);

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
                    rd.fecha_emision as FECHA_EMISION,
                    rd.fecha_generacion as FECHA_DOCUMENTO
                    FROM documento_electronico_resumen_diario rd
                    WHERE rd.id_documento_electronico_resumen_diario = :0 AND rd.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico_rd]);

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

    public function enviarResumenesDiarios($fecha_inicio, $fecha_fin, $forzado = false){
        try {   

            /*Sí es $forzado = true, enviará el documento aun ya haya sido enviado y tenga un ticket asociado.*/
            if ($forzado){
                $sql  = "SELECT id_documento_electronico_resumen_diario as id, nombre_resumen as nombre_archivo, fecha_emision 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND  (fecha_emision BETWEEN :0 AND :1) AND cdr_estado > 0";
            } else {
                $sql  = "SELECT id_documento_electronico_resumen_diario as id, nombre_resumen as nombre_archivo, fecha_emision 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND  ticket IS NULL AND (fecha_emision BETWEEN :0 AND :1) AND numero_envios <= 0 AND cdr_estado IS NULL";
            }
            
            $resumenes = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            $data_json = json_encode(["resumenes"=>$resumenes, "id_tipo_comprobante"=>"RC", "tipo_proceso"=>F_MODO_PROCESO]);

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

                $sql  = "UPDATE documento_electronico_resumen_diario SET 
                        ticket = ".($ticket ? "'".$ticket."'" : 'NULL').",
                        numero_envios = numero_envios + 1,
                        fecha_hora_envio = CURRENT_TIMESTAMP,
                        estado_sunat = ".($ticket ? '1' : '0')."
                        WHERE 
                        estado_mrcb AND fue_generado = 1 AND fue_firmado = 1 AND
                        id_documento_electronico_resumen_diario = '".$respuesta->id."'";
                $this->consultaRaw($sql);
            }
       
            $this->commit();

            return ["respuestas"=>$respuestasEnvio];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function consultarTickets($fecha_inicio, $fecha_fin){
        try {

            /*
                Se ingresar un rango de fechas, tomará todos los resumenes que tengan ticket, y los consultará, actualizará el resultado acorde su ID.
            */

            $sql  = "SELECT id_documento_electronico_resumen_diario, nombre_resumen, fecha_emision, ticket 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND  ticket IS NOT NULL AND (fecha_emision BETWEEN :0 AND :1)";
            $tickets = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            $data_json = json_encode(["tickets"=>$tickets, "id_tipo_comprobante"=>"RC", "tipo_proceso"=>F_MODO_PROCESO]);

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

                $sql  = "UPDATE documento_electronico_resumen_diario SET 
                        cdr_estado = ".(isset($cod_sunat) ? "'".$cod_sunat."'" : 'NULL').",
                        cdr_descripcion = ".(isset($msj_sunat) ? "'".str_replace("'","\'",$msj_sunat)."'" : 'NULL').",
                        hash_cdr = ".($hash_cdr ? "'".$hash_cdr."'" : 'NULL')."
                        WHERE 
                        estado_mrcb AND id_documento_electronico_resumen_diario = '".$respuesta->id_documento_electronico_resumen_diario."'";
                $this->consultaRaw($sql);
            }
       
            $this->commit();

            return ["respuestas"=>$respuestas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarResumenDiarioXID($id_por_comas, $status_envio = "1"){ //estatus_Envio = 1 registro, 3 = anulado
        try {

            $this->beginTransaction();

            $this->fecha_ahora = date("Y-m-d H:i:s");
            $fechaGeneracion = date("Y-m-d");
            $codigo = "RC";

            if ($status_envio == "3"){
                $sql = "SELECT distinct(de.fecha_emision)  as fecha_emision,
                (SELECT  COALESCE(MAX(rd.secuencia) + 1, 1) 
                FROM documento_electronico_resumen_diario rd 
                WHERE rd.estado_mrcb and rd.fecha_generacion = '$fechaGeneracion') as secuencia
                FROM documento_electronico de                     
                WHERE de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%' AND 
                    de.estado_mrcb AND de.iddocumento_electronico IN ('".$id_por_comas."') AND de.estado_anulado = 1 AND (DATE(de.fecha_hora_anulacion) = de.fecha_emision)";
                    
            } else {
             
                $sql = "SELECT distinct(de.fecha_emision)  as fecha_emision,
                        (SELECT  COALESCE(MAX(rd.secuencia) + 1, 1) 
                        FROM documento_electronico_resumen_diario rd 
                        WHERE rd.estado_mrcb and rd.fecha_generacion = '$fechaGeneracion') as secuencia
                        FROM documento_electronico de                     
                        WHERE de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%' AND 
                            de.estado_mrcb AND de.iddocumento_electronico IN ('".$id_por_comas."')";
            }
            
            $rangoFechas = $this->consultarFilas($sql);


            $secuencia = 0;
            foreach ($rangoFechas as $key => $objResumen) {
                $fechaEmision = $objResumen["fecha_emision"];
                //$fechaGeneracion = date('Y-m-d', strtotime($fechaEmision . ' +5 day'));
                $serie = str_replace("-","",$fechaGeneracion);
                $secuencia = $secuencia == 0  ? $objResumen["secuencia"] : ($secuencia + 1);

                $sqlComprobantes = "SELECT 
                                    CONCAT(de.serie,'-', LPAD(de.numero_correlativo,6,'0')) as NRO_COMPROBANTE,
                                    de.serie as SERIE_COMPROBANTE,
                                    de.numero_correlativo as CORRELATIVO_COMPROBANTE,
                                    de.idtipo_comprobante as TIPO_COMPROBANTE,
                                    de.idtipo_moneda as COD_MONEDA,
                                    de.numero_documento_cliente as NRO_DOCUMENTO,
                                    de.idtipo_documento_cliente as TIPO_DOCUMENTO,
                                    de.idtipo_comprobante_modifica as TIPO_COMPROBANTE_REF,
                                    COALESCE(CONCAT(de.serie_documento_modifica,'-',de.numero_documento_modifica),'') as NRO_COMPROBANTE_REF,
                                    COALESCE(de.serie_documento_modifica,'') as SERIE_COMPROBANTE_REF,
                                    COALESCE(de.numero_documento_modifica,'') as CORRELATIVO_COMPROBANTE_REF,
                                    '$status_envio' as STATUS,
                                    de.total_igv as IGV,
                                    de.total_isc as ISC,
                                    de.idtipo_moneda as COD_MONEDA,
                                    de.total_otro_imp as OTROS,
                                    de.importe_total as TOTAL,
                                    de.total_gravadas as GRAVADA,
                                    de.total_inafectas as INAFECTO,
                                    de.total_exoneradas as EXONERADO,
                                    '0.00' as EXPORTACION,
                                    '0.00' as GRATUITAS
                                    FROM documento_electronico de 
                                    WHERE de.fecha_emision = :0 AND de.estado_mrcb 
                                        AND de.idtipo_comprobante IN ('03','07','08') AND de.serie LIKE 'B%'
                                        AND ".($status_envio == "1" ? "cdr_estado IS NULL" : " true ")."
                                        AND de.iddocumento_electronico IN ('".$id_por_comas."')
                                        AND ".($status_envio == "3" ? "  estado_anulado = 1 AND (DATE(de.fecha_hora_anulacion) = de.fecha_emision)  " : " true ");
                
                $comprobantes = $this->consultarFilas($sqlComprobantes, [$fechaEmision]);

                if (count($comprobantes) <= 0 ){
                    continue;
                }

                $campos_valores = [
                    "codigo" => $codigo,
                    "serie"=>$serie,
                    "secuencia"=>$secuencia,
                    "fecha_emision"=>$fechaEmision,
                    "fecha_generacion"=>$fechaGeneracion,
                    "nombre_resumen"=>F_RUC.'-'.$codigo."-".$serie."-".$secuencia,
                    "fecha_hora_registro"=>$this->fecha_ahora
                ];

                $this->insert("documento_electronico_resumen_diario", $campos_valores);
                $this->id_documento_electronico_rd = $this->getLastID();

                $campos = [
                    "id_documento_electronico_resumen_diario",
                    "item",
                    "idtipo_comprobante",
                    "serie_comprobante",
                    "numero_correlativo_comprobante",
                    "idtipo_documento_cliente",
                    "numero_documento_cliente",
                    "serie_comprobante_modificado",
                    "numero_correlativo_comprobante_modificado",
                    "idtipo_comprobante_modificado",
                    "status",
                    "id_moneda",
                    "importe_gravadas",
                    "importe_exoneradas",
                    "importe_inafectas",
                    "importe_exportacion",
                    "importe_gratuitas",
                    "importe_otros",
                    "importe_igv",
                    "importe_isc",
                    "importe_total"
                ];
                $valores = [];
                foreach ($comprobantes as $key => $value) {
                    array_push($valores, [
                        $this->id_documento_electronico_rd,
                        ($key + 1),
                        $value["TIPO_COMPROBANTE"],
                        $value["SERIE_COMPROBANTE"],
                        $value["CORRELATIVO_COMPROBANTE"],
                        $value["TIPO_DOCUMENTO"],
                        $value["NRO_DOCUMENTO"],
                        $value["SERIE_COMPROBANTE_REF"],
                        $value["CORRELATIVO_COMPROBANTE_REF"],
                        $value["TIPO_COMPROBANTE_REF"],
                        $value["STATUS"],
                        $value["COD_MONEDA"],
                        $value["GRAVADA"],
                        $value["EXONERADO"],
                        $value["INAFECTO"],
                        $value["EXPORTACION"],
                        $value["GRATUITAS"],
                        $value["OTROS"],
                        $value["IGV"],
                        $value["ISC"],
                        $value["TOTAL"]
                    ]);
                }

                $this->insertMultiple("documento_electronico_resumen_diario_detalle", $campos, $valores);

                if ($this->generar_xml){
                    $objXMLComprobante = $this->generarXML();
                    $fue_generado = $objXMLComprobante["fue_generado"];
                    $datosComprobante = $objXMLComprobante["datos_comprobante"];
                    $respuesta = $objXMLComprobante["respuesta"];
                }
    
                if ($this->firmar_comprobante){
                    //DEBO FIRMAR EL BENDITO COMPROBANTE ?
                    $objXMLFirmaComprobante =  $this->firmarXML($datosComprobante);
                    $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                    $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                    $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
                }
                
                $campos_valores = [];

                if ($fue_generado == "1"){
                    $campos_valores["fue_generado"] = $fue_generado;
                }
    
                if ($valor_firma != NULL){
                    $campos_valores["fue_firmado"] = "1";
                    $campos_valores["valor_firma"] = $valor_firma;
                    $campos_valores["valor_resumen"] = $valor_resumen;
                }
    
                if (count($campos_valores)){
                    $this->update("documento_electronico_resumen_diario", 
                                $campos_valores,
                                ["id_documento_electronico_resumen_diario"=>$this->id_documento_electronico_rd]);
                }
            }

            $this->commit();
            return  ["msj"=>"Se ha generado ".count($rangoFechas)." resumenes diarios."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function enviarResumenesDiariosXID($id_por_comas, $forzado = false){
        try {   

            /*Sí es $forzado = true, enviará el documento aun ya haya sido enviado y tenga un ticket asociado.*/
            if ($forzado){
                $sql  = "SELECT id_documento_electronico_resumen_diario as id, nombre_resumen as nombre_archivo, fecha_emision 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND id_documento_electronico_resumen_diario  IN ('".$id_por_comas."') AND cdr_estado > 0";
            } else {
                $sql  = "SELECT id_documento_electronico_resumen_diario as id, nombre_resumen as nombre_archivo, fecha_emision 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND  ticket IS NULL AND id_documento_electronico_resumen_diario  IN ('".$id_por_comas."') AND numero_envios <= 0 AND cdr_estado IS NULL";
            }
            
            $resumenes = $this->consultarFilas($sql);

            $data_json = json_encode(["resumenes"=>$resumenes, "id_tipo_comprobante"=>"RC", "tipo_proceso"=>F_MODO_PROCESO]);

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

                $sql  = "UPDATE documento_electronico_resumen_diario SET 
                        ticket = ".($ticket ? "'".$ticket."'" : 'NULL').",
                        numero_envios = numero_envios + 1,
                        fecha_hora_envio = CURRENT_TIMESTAMP,
                        estado_sunat = ".($ticket ? '1' : '0')."
                        WHERE 
                        estado_mrcb AND fue_generado = 1 AND fue_firmado = 1 AND
                        id_documento_electronico_resumen_diario = '".$respuesta->id."'";
                $this->consultaRaw($sql);
            }
       
            $this->commit();

            return ["respuestas"=>$respuestasEnvio];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function consultarTicketsXID($id_por_comas){
        try {

            /*
                Se ingresar un rango de fechas, tomará todos los resumenes que tengan ticket, y los consultará, actualizará el resultado acorde su ID.
            */

            $sql  = "SELECT id_documento_electronico_resumen_diario, nombre_resumen, fecha_emision, ticket 
                        FROM documento_electronico_resumen_diario
                        WHERE estado_mrcb AND  ticket IS NOT NULL AND id_documento_electronico_resumen_diario  IN ('".$id_por_comas."') ";
            $tickets = $this->consultarFilas($sql);

            $data_json = json_encode(["tickets"=>$tickets, "id_tipo_comprobante"=>"RC", "tipo_proceso"=>F_MODO_PROCESO]);

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

                $sql  = "UPDATE documento_electronico_resumen_diario SET 
                        cdr_estado = ".(isset($cod_sunat) ? "'".$cod_sunat."'" : 'NULL').",
                        cdr_descripcion = ".(isset($msj_sunat) ? "'".str_replace("'","\'",$msj_sunat)."'" : 'NULL').",
                        hash_cdr = ".($hash_cdr ? "'".$hash_cdr."'" : 'NULL')."
                        WHERE 
                        estado_mrcb AND id_documento_electronico_resumen_diario = '".$respuesta->id_documento_electronico_resumen_diario."'";
                $this->consultaRaw($sql);
            }
       
            $this->commit();

            return ["respuestas"=>$respuestas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}