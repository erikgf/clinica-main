<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/datos.empresa.php';
require_once '../datos/variables.php';

class DocumentoElectronico extends Conexion {
    public $id_documento_electronico;
    public $id_tipo_comprobante;
    public $id_atencion_medica;
    public $serie;
    public $numero_correlativo;
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descuento_global;
    public $importe_total;

    public $Cliente;
    public $id_documento_electronico_previo;
    public $cod_tipo_motivo_nota;
    public $id_tipo_comprobante_previo;
    public $serie_comprobante_previo;
    public $numero_correlativo_comprobante_previo;
    public $motivo_anulacion;
    public $id_atencion_medica_convenio;

    public $detalle;
    public $id_usuario_registrado;
    public $observaciones;

    public $cuotas;
    public $forma_pago;

    public $registrar_en_bbdd = true;
    public $firmar_comprobante = true;
    public $generar_xml = false;

    private $lpad_ceros_numero_correlativo = "6";
    private $RUTA_SISTEMA_FACTURACION = "http://localhost/sistema_facturacion/api/xml.generar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_FIRMADO = "http://localhost/sistema_facturacion/api/xml.firmar.comprobante.php";
    private $RUTA_SISTEMA_FACTURACION_ENVIAR = "http://localhost/sistema_facturacion/api/xml.enviar.comprobante.facturas.php";

    public $id_documento_electronico_rd;
    public $es_convenio;
    private $fecha_ahora;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function generarComprobante($id_tipo_comprobante = "", $id_tipo_nota = NULL){
        try {
            
            $this->fecha_ahora = date("Y-m-d H:i:s");

            $this->beginTransaction();

            $fue_generado = 0;
            $valor_resumen = NULL;
            $valor_firma = NULL;

            $objRegistroComprobante = NULL;
            if ($this->registrar_en_bbdd == false && $id_tipo_comprobante == ""){
                throw new Exception("No se ha enviado un tipo de comprobante v??lido", 1);
            }

            $this->id_tipo_comprobante = $id_tipo_comprobante;

            if ($this->registrar_en_bbdd){
                if ( $this->serie_comprobante_previo != NULL && ($this->id_tipo_comprobante == "07" ||  $this->id_tipo_comprobante == "08")){
                    $this->serie = $this->serie_comprobante_previo;
                }

                $this->obtenerCorrelativoXSerie($this->id_tipo_comprobante, $this->serie);

                switch($this->id_tipo_comprobante){
                    case "01":
                    case "03":
                        $objRegistroComprobante = $this->registrarComprobanteBase();
                        break;
                    case "07":
                        if ($id_tipo_nota == "01" && $this->id_documento_electronico_previo != NULL){
                            $objRegistroComprobante = $this->registrarNotaCreditoAnulacionCompleta();
                        } else {
                            $objRegistroComprobante = $this->registrarComprobanteBase();
                        }
                        break;
                    case "08":
                        //$this->registrarFacturaBoleta();
                        break;
                }
            }
            /*Se actualizar??n ciertos campso de ste documento al final. Es mejor hacerlo al final en una sola transacci??n*/
            $campos_valores = [];

            if ($this->registrar_en_bbdd){ 
                /*actualizar los totales si est?? registrando.*/
                if ($id_tipo_nota == NULL){
                    $campos_valores["total_exoneradas"] = $objRegistroComprobante["total_exoneradas"];
                    $campos_valores["total_gravadas"] = $objRegistroComprobante["total_gravadas"];
                    $campos_valores["total_inafectas"] = $objRegistroComprobante["total_inafectas"];
                    $campos_valores["total_igv"] = $objRegistroComprobante["total_igv"];
                }
            }

            if (count($campos_valores)){
                $this->update("documento_electronico", 
                            $campos_valores,
                            ["iddocumento_electronico"=>$this->id_documento_electronico]);

                $campos_valores = [];
            }

            $respuesta = [];
            $respuestafirma = [];
            $datosComprobante = null;
            $xml_filename = NULL;

            if ($this->generar_xml){
                $objXMLComprobante = $this->generarXMLComprobante();
                $fue_generado = $objXMLComprobante["fue_generado"];
                $datosComprobante = $objXMLComprobante["datos_comprobante"];
                $respuesta = $objXMLComprobante["respuesta"];
                $xml_filename = $objXMLComprobante["xml_filename"];
            }

            if ($this->firmar_comprobante){
                //DEBO FIRMAR EL BENDITO COMPROBANTE ?
                $objXMLFirmaComprobante =  $this->firmarXMLComprobante($datosComprobante);
                $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
            }

            if ($fue_generado == "1"){
                $campos_valores["fue_generado"] = $fue_generado;
                $campos_valores["xml_filename"] = $xml_filename;
            }

            if ($valor_firma != NULL){
                $campos_valores["fue_firmado"] = "1";
                $campos_valores["valor_firma"] = $valor_firma;
                $campos_valores["valor_resumen"] = $valor_resumen;
            }

            if (count($campos_valores)){
                $this->update("documento_electronico", 
                            $campos_valores,
                            ["iddocumento_electronico"=>$this->id_documento_electronico]);
            }

            $this->commit();
            return ["msj"=>"Registro realizado correctamente.", 
                    "r"=>$respuesta, 
                    "rfirma"=>$respuestafirma];  
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function generarFacturaOBoleta($id_tipo_comprobante = ""){
        try {
            
            $fecha_ahora = date("Y-m-d H:i:s");

            $this->beginTransaction();

            $this->id_tipo_comprobante = $id_tipo_comprobante;

            $fue_generado = 0;
            $valor_resumen = NULL;
            $valor_firma = NULL;

            if ($this->registrar_en_bbdd){
                $this->obtenerCorrelativoXSerie($id_tipo_comprobante, $this->serie);

                $porcentaje_descuento = 0.00;
                if ($this->descuento_global > 0.00){
                    $porcentaje_descuento =  round($this->descuento_global  / ($this->descuento_global + $this->importe_total) * 100,3);
                }

                $campos_valores = [
                    "idcliente"=>$this->Cliente["id_cliente"],
                    "idtipo_documento_cliente"=>$this->Cliente["id_tipo_documento"],
                    "numero_documento_cliente"=>$this->Cliente["numero_documento"],
                    "descripcion_cliente"=>$this->Cliente["nombres_completos"],
                    "direccion_cliente"=>$this->Cliente["direccion"],
                    "codigo_ubigeo_cliente"=>$this->Cliente["codigo_ubigeo_distrito"],
                    "serie"=>$this->serie,
                    "numero_correlativo"=>$this->numero_correlativo,
                    "idtipo_operacion"=>"0101",
                    "fecha_emision"=>$this->fecha_emision,
                    "fecha_vencimiento"=>$this->fecha_vencimiento == NULL ? $this->fecha_emision : $this->fecha_vencimiento,
                    "idtipo_moneda"=>"PEN",
                    "idtipo_comprobante"=>$id_tipo_comprobante,
                    "descuento_global"=>$this->descuento_global,
                    "porcentaje_descuento"=>$porcentaje_descuento / (1 + IGV),
                    "importe_total"=>$this->importe_total,
                    "total_letras"=>Funciones::numtoletras($this->importe_total),
                    "condicion_pago"=>$this->forma_pago == NULL ? "1": $this->forma_pago,
                    "observaciones"=>$this->observaciones,
                    "id_atencion_medica"=>$this->id_atencion_medica,
                    "id_usuario_registrado"=>$this->id_usuario_registrado,
                    "fecha_hora_registrado"=>$fecha_ahora,
                    "xml_filename"=>F_RUC."-".$id_tipo_comprobante."-".$this->serie."-".str_pad($this->numero_correlativo,6,'0',STR_PAD_LEFT),
                    "es_convenio"=>$this->es_convenio == NULL ? "0": "1"
                ];

                $this->insert("documento_electronico", $campos_valores);
                $this->id_documento_electronico = $this->getLastID();

                $cantidad_servicios = count($this->detalle);

                $campos_detalle = [
                    "iddocumento_electronico",
                    "idproducto",
                    "item",
                    "idunidad_medida",
                    "cantidad_item",
                    "descripcion_item",
                    "precio_venta_unitario",
                    "subtotal",
                    "valor_venta_unitario",
                    "valor_venta",
                    "total_igv",
                    "idtipo_afectacion",
                    "codigo_interno"
                ];

                $valores_detalle = [];
                $total_gravadas = 0.00;
                $total_inafectas = 0.00;
                $total_exoneradas = 0.00;
                $total_igv = 0.00;

                for ($i=0; $i < $cantidad_servicios; $i++) { 
                    $o = $this->detalle[$i];
                    $o->cantidad = $o->cantidad == NULL ? "1" : $o->cantidad;
                    $subtotal = $o->precio_unitario * $o->cantidad;
                    
                    if ($o->idtipo_afectacion < 20){
                        $valor_venta_unitario = round($o->precio_unitario / (1 + IGV), 4);
                        $valor_venta = round($valor_venta_unitario * $o->cantidad, 2);

                        $total_gravadas += $valor_venta;
                        $igv = $subtotal- $valor_venta;
                    } else { //Incluye el 20 - 40 sin igv
                        $valor_venta_unitario = $o->precio_unitario;
                        $valor_venta  = round($o->precio_unitario * $o->cantidad, 2);
                        $igv = 0.00;
                        if ($o->idtipo_afectacion < 30){
                            $total_exoneradas += $valor_venta;
                        } else {
                            $total_infafectas += $valor_venta;
                        }
                    } 

                    array_push($valores_detalle, [
                        $this->id_documento_electronico,
                        $o->id_servicio,
                        ($i+1),
                        $o->idunidad_medida,
                        $o->cantidad,
                        $o->nombre_servicio,
                        $o->precio_unitario,
                        $subtotal,
                        $valor_venta_unitario,
                        $valor_venta,
                        $igv,
                        $o->idtipo_afectacion,
                        "SER".$o->id_servicio
                    ]);

                    $total_igv += $igv;
                }
                
                $this->insertMultiple("documento_electronico_detalle", $campos_detalle, $valores_detalle);

                $this->numero_correlativo = $this->numero_correlativo + 1;
                $this->update("serie_documento", 
                            [ "numero"=>$this->numero_correlativo ],
                            ["serie"=>$this->serie, "idtipo_comprobante"=>$this->id_tipo_comprobante]);

            }

            $respuesta = [];
            $respuestafirma = [];
            $datosComprobante = null;
            $xml_filename = null;

            if ($this->generar_xml){
                $objXMLComprobante = $this->generarXMLComprobante();
                $fue_generado = $objXMLComprobante["fue_generado"];
                $datosComprobante = $objXMLComprobante["datos_comprobante"];
                $respuesta = $objXMLComprobante["respuesta"];
                $xml_filename = $objXMLComprobante["xml_filename"];
                
            }

            if ($this->firmar_comprobante){
                $objXMLFirmaComprobante =  $this->firmarXMLComprobante($datosComprobante);
                $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
            }

            /*Se actualizar??n ciertos campso de ste documento al final. Es mejor hacerlo al final en una sola transacci??n*/
            $campos_valores = [];

            if ($this->registrar_en_bbdd){ 
                /*actualizar los totales si est?? registrando.*/
                $campos_valores["total_exoneradas"] = $total_exoneradas;
                $campos_valores["total_gravadas"] = $total_gravadas;
                $campos_valores["total_inafectas"] = $total_inafectas;
                $campos_valores["total_igv"] = $total_igv;
            }

            if ($fue_generado == "1"){
                $campos_valores["fue_generado"] = $fue_generado;
                $campos_valores["xml_filename"] = $xml_filename;
            }

            if ($valor_firma != NULL){
                $campos_valores["fue_firmado"] = "1";
                $campos_valores["valor_firma"] = $valor_firma;
                $campos_valores["valor_resumen"] = $valor_resumen;
            }

            if (count($campos_valores)){
                $this->update("documento_electronico", 
                            $campos_valores,
                            ["iddocumento_electronico"=>$this->id_documento_electronico]);
            }

            $this->commit();
            return ["msj"=>"Registro realizado correctamente.", "r"=>$respuesta, "rfirma"=>$respuestafirma];  
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function obtenerCorrelativoXSerie($id_tipo_comprobante, $serie){
        try {

            $sql  = "SELECT numero FROM serie_documento WHERE serie = :0 AND idtipo_comprobante = :1";
            $numero_correlativo = $this->consultarValor($sql, [$serie, $id_tipo_comprobante]);

            if ($numero_correlativo == NULL){
                throw new Exception("La serie ingresada no est?? registrada. No hay correlativo de comprobante v??lido."); 
            }

            $this->numero_correlativo = $numero_correlativo;
            return $numero_correlativo;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function generarBoleta(){
        try {
            return $this->generarComprobante("03");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function generarFactura(){
        try {
            return $this->generarComprobante("01");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerDatosParaImpresionTicket($id_documento_electronico){
        try {

            $this->id_documento_electronico = $id_documento_electronico;

            $sql  = "SELECT 
                    serie,
                    numero_correlativo,
                    DATE_FORMAT(de.fecha_emision, '%d/%m/%Y') as fecha_emision,
                    DATE_FORMAT(de.fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento,
                    de.fecha_emision as fecha_emision_raw,
                    de.fecha_vencimiento as fecha_vencimiento_raw,
                    DATE_FORMAT(de.fecha_hora_registrado, '%H:%i:%s') as hora_emision,
                    idtipo_documento_cliente as id_tipo_documento_cliente,
                    numero_documento_cliente,
                    descripcion_cliente as cliente,
                    idtipo_moneda,
                    direccion_cliente,
                    COALESCE(am.nombre_paciente,'') as paciente,
                    COALESCE(am.observaciones,de.observaciones,'') as observaciones,
                    COALESCE(am.numero_acto_medico,'') as numero_recibo,
                    de.idtipo_comprobante,
                    tp.descripcion as tipo_paciente,
                    de.total_letras,
                    de.total_gravadas,
                    de.total_igv,
                    de.importe_total,
                    de.descuento_global,
		            am.pago_credito as monto_saldo,
                    de.condicion_pago,
                    COALESCE(de.valor_resumen,'') as valor_resumen,
                    COALESCE(de.valor_firma,'') as valor_firma,
                    de.estado_anulado,
                    CONCAT(co.nombres,' ',co.apellido_paterno,' ',co.apellido_materno) as usuario_atendido,
                    de.cdr_descripcion as respuesta_sunat,
                    COALESCE(de.descripcion_motivo_nota, mn.descripcion, '') as motivo_nota,
                    COALESCE(CONCAT(de.serie_documento_modifica,'-',LPAD(de.numero_documento_modifica,$this->lpad_ceros_numero_correlativo,'0')),'') as documento_afectado
                    FROM documento_electronico de
                    LEFT JOIN atencion_medica am ON am.id_atencion_medica = de.id_atencion_medica
                    LEFT JOIN motivo_nota mn ON mn.idtipo_nota = de.idtipo_comprobante AND mn.idtipo_motivo_nota = de.idtipo_comprobante_modifica
                    LEFT JOIN paciente p ON p.id_paciente = am.id_paciente
                    LEFT JOIN tipo_paciente tp ON tp.id_tipo_paciente = p.id_tipo_paciente
                    INNER JOIN usuario u ON u.id_usuario = de.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE de.iddocumento_electronico = :0 AND de.estado_mrcb";
            $datos = $this->consultarFila($sql, [$id_documento_electronico]);

            if ($datos == NULL){
                throw new Exception("No existe un comprobante con el ID ingresado."); 
            }

            $sql = "SELECT  
                        item,
                        cantidad_item,
                        idunidad_medida,
                        descripcion_item,
                        precio_venta_unitario,
                        subtotal,
                        valor_venta_unitario,
                        valor_venta
                        FROM documento_electronico_detalle ded
                        WHERE ded.iddocumento_electronico  = :0";
            $datos["detalle"] = $this->consultarFilas($sql, [$id_documento_electronico]);

            $sql = "SELECT  
                        numero_cuota,
                        monto_cuota,
                        DATE_FORMAT(fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento
                        FROM documento_electronico_cuota
                        WHERE iddocumento_electronico = :0 AND estado_mrcb";
            $datos["cuotas"] = $this->consultarFilas($sql, [$id_documento_electronico]);
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    public function obtenerDatosParaCreacionXML(){
        try {

            $sql  = "SELECT 
                    de.idtipo_operacion as TIPO_OPERACION,
                    de.total_gravadas as TOTAL_GRAVADAS,
                    de.total_inafectas as TOTAL_INAFECTA,
                    de.total_exoneradas as TOTAL_EXONERADAS,
                    (de.importe_total - de.total_igv) as SUB_TOTAL,
                    de.igv as POR_IGV,
                    de.total_igv as TOTAL_IGV,
                    de.total_isc as TOTAL_ISC,
                    de.total_otro_imp as TOTAL_OTR_IMP,
                    de.importe_total as TOTAL,
                    de.importe_credito as TOTAL_CREDITO,
                    de.total_letras as TOTAL_LETRAS,
                    CONCAT(de.serie,'-', LPAD(de.numero_correlativo,6,'0')) as NRO_COMPROBANTE,
                    de.fecha_emision as FECHA_DOCUMENTO,
                    DATE_FORMAT(de.fecha_hora_registrado,'%H:%i:%s') as HORA_DOCUMENTO,
                    de.fecha_vencimiento as FECHA_VTO,
                    de.idtipo_comprobante as COD_TIPO_DOCUMENTO,
                    de.idtipo_moneda as COD_MONEDA,
                    de.numero_documento_cliente as NRO_DOCUMENTO_CLIENTE,
                    de.descripcion_cliente as RAZON_SOCIAL_CLIENTE,
                    de.idtipo_documento_cliente as TIPO_DOCUMENTO_CLIENTE,
                    de.direccion_cliente as DIRECCION_CLIENTE,
                    'PE' as COD_PAIS_CLIENTE,
                    de.codigo_ubigeo_cliente as COD_UBIGEO_CLIENTE,
                    COALESCE(updep.id,'') as DEPARTAMENTO_CLIENTE,
                    COALESCE(SUBSTRING(upp.id,3,2),'') as PROVINCIA_CLIENTE,
                    COALESCE(SUBSTRING(upd.id, 5, 2),'') as DISTRITO_CLIENTE,
                    COALESCE(de.observaciones,'') as OBSERVACIONES,
                    condicion_pago as CONDICION_PAGO,
                    '' as NRO_OTR_COMPROBANTE,
                    ''  as NRO_GUIA_REMISION,
                    '0' as TOTAL_VALOR_VENTA_BRUTO,
                    '0' as TOTAL_DESCUENTO,
                    porcentaje_descuento as POR_DESCUENTO,
                    de.descuento_global as DESCUENTO_GLOBAL,
                    de.idtipo_comprobante_modifica as TIPO_COMPROBANTE_MODIFICA,
                    de.idcod_tipo_motivo_nota as COD_TIPO_MOTIVO,
                    COALESCE(de.descripcion_motivo_nota, mn.descripcion,'') as DESCRIPCION_MOTIVO,
                    CONCAT(de.serie_documento_modifica,'-',de.numero_documento_modifica) as NRO_DOCUMENTO_MODIFICA
                    FROM documento_electronico de
                    LEFT JOIN ubigeo_peru_districts upd ON upd.id = de.codigo_ubigeo_cliente
                    LEFT JOIN ubigeo_peru_provinces upp ON upp.id = upd.province_id
                    LEFT JOIN ubigeo_peru_departments updep ON updep.id = upp.department_id
                    LEFT JOIN motivo_nota mn ON mn.idtipo_nota = de.idtipo_comprobante AND mn.idtipo_motivo_nota = de.idtipo_comprobante_modifica
                    WHERE de.iddocumento_electronico = :0 AND de.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico]);

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
                        item as txtITEM,
                        cantidad_item as txtCANTIDAD_DET,
                        precio_venta_unitario as txtPRECIO_DET,
                        valor_venta_unitario as txtPRECIO_SIN_IGV_DET,
                        valor_venta as txtIMPORTE_DET,
                        -- subtotal as txtIMPORTE_DET,
                        total_igv as txtIGV,
                        total_isc as txtISC,
                        idtipo_afectacion as txtCOD_TIPO_OPERACION,
                        COALESCE(codigo_interno,'') as txtCODIGO_DET,
                        descripcion_item as txtDESCRIPCION_DET,
                        idunidad_medida as txtUNIDAD_MEDIDA_DET,
                        idcodigo_precio as txtPRECIO_TIPO_CODIGO
                        FROM documento_electronico_detalle ded
                        WHERE ded.iddocumento_electronico = :0";
            $datos["detalle"] = $this->consultarFilas($sql, [$this->id_documento_electronico]);

            $sql = "SELECT  
                        numero_cuota AS NUMERO_CUOTA,
                        monto_cuota AS MONTO_CUOTA,
                        fecha_vencimiento as FECHA_VENCIMIENTO
                        FROM documento_electronico_cuota
                        WHERE iddocumento_electronico = :0 AND estado_mrcb";
            $datos["cuotas"] = $this->consultarFilas($sql, [$this->id_documento_electronico]);

            $datos["tipo_proceso"] = F_MODO_PROCESO;
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDatosParaFirmaXML(){
        try {

            $sql  = "SELECT 
                    CONCAT(de.serie,'-', LPAD(de.numero_correlativo,6,'0')) as NRO_COMPROBANTE,
                    de.fecha_emision as FECHA_DOCUMENTO,
                    de.idtipo_comprobante as COD_TIPO_DOCUMENTO
                    FROM documento_electronico de
                    WHERE de.iddocumento_electronico = :0 AND de.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico]);

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

    private function dateDiffInDays($date1, $date2){
        // Calculating the difference in timestamps
        $diff = strtotime($date2) - strtotime($date1);
        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return round($diff / 86400);
    }
    
    public function consultarDocumentoCliente($numero_documento){
        $respuesta = [];
		$token_cliente = F_TOKEN_PROVEEDOR; //KEY para que puedas consumir nuestra api
		//$data['ruc_proveedor'] = F_RUC_PROVEEDOR; //Tu n??mero de RUC, el cu??l ser?? responsable por los datos enviados en todos los json
		$ruta = "";
        $HOY = date("d-m-Y");

        $dateDiff = $this->dateDiffInDays($HOY, F_FECHA_TOPE);
        if ($dateDiff < 0){
            $respuesta['respuesta'] = 'error';
			$respuesta['titulo'] = 'Error';
			$respuesta['data'] = '';
			$respuesta['encontrado'] = false;
			$respuesta['mensaje'] = 'El servicio de consulta RENIEC/SUNAT ha vencido. Fecha LIMITE: '.F_FECHA_TOPE;
			$respuesta['errores_curl'] = "";
            return $respuesta;
        }

		$cantidad_digitos = strlen($numero_documento);

		switch($cantidad_digitos){
			case 8:
				$ruta = 'https://facturalahoy.com/api/persona/';
				break;
			case 11:
				$ruta = 'https://facturalahoy.com/api/empresa/';
				break;
			default:
			break;
		}

		if ($ruta == ""){
			

			$respuesta['respuesta'] = 'error';
			$respuesta['titulo'] = 'Error';
			$respuesta['data'] = '';
			$respuesta['encontrado'] = false;
			$respuesta['mensaje'] = 'Tipo de comprobante de consulta no v??lido.';
			$respuesta['errores_curl'] = "";

			return $respuesta;
		}

		$ruta .= $numero_documento.'/'.$token_cliente;

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $ruta,
			CURLOPT_USERAGENT => 'Consulta Datos',
			CURLOPT_CONNECTTIMEOUT => 0,
			CURLOPT_TIMEOUT => 400,
			CURLOPT_FAILONERROR => true
		));

		$respuesta  = curl_exec($curl);
		if (curl_error($curl)) {
			$error_msg = curl_error($curl);
		}
		curl_close($curl);

		if (isset($error_msg)) {
			$respuesta['respuesta'] = 'error';
			$respuesta['titulo'] = 'Error';
			$respuesta['data'] = '';
			$respuesta['encontrado'] = false;
			$respuesta['mensaje'] = 'Error en Api de B??squeda';
			$respuesta['errores_curl'] = $error_msg;
		} else{
			$respuesta = json_decode($respuesta);
		}

		return $respuesta;
	}

    public function generarTodos(){
        try {

            $sql  = "SELECT 
                    iddocumento_electronico,
                    de.idtipo_comprobante
                    FROM documento_electronico de
                    WHERE de.estado_mrcb AND de.cdr_estado IS NULL and fecha_emision >= '2021-05-26' AND idtipo_comprobante = '07'";

            $datos = $this->consultarFilas($sql);

            foreach ($datos as $key => $value) {
                $this->id_documento_electronico = $value["iddocumento_electronico"];
                $o = $this->generarComprobante($value["idtipo_comprobante"]);
            }
            
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function crearNotaCreditoDesdeComprobante($iddocumento_electronico, $id_tipo_nota, $motivo_anulacion, $forzado = 0){
        try {

            $sql = "SELECT iddocumento_electronico, idtipo_comprobante, serie FROM documento_electronico 
                        WHERE iddocumento_electronico = :0 AND estado_mrcb AND ".($forzado == 0 ? " estado_anulado = 0 " : " true");
            $existeComprobante = $this->consultarFila($sql, [$iddocumento_electronico]);

            if ($existeComprobante == false){
                throw new Exception("Comprobante no existe.", 1);
            }
                
            $this->id_documento_electronico_previo = $iddocumento_electronico;
            $this->id_tipo_comprobante_previo = $existeComprobante["idtipo_comprobante"];
            $this->serie_comprobante_previo = $existeComprobante["serie"];
            $this->motivo_anulacion = $motivo_anulacion;

            $this->generar_xml = true;
            $this->firmar_comprobante = true;
            $this->registrar_en_bbdd = true;

            $objComprobante = $this->generarComprobante("07", $id_tipo_nota);
            $objComprobante["comprobante"] = $this->serie.'-'.str_pad($this->numero_correlativo, 7, "0", STR_PAD_LEFT) ;

            return $objComprobante;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarNotaCreditoAnulacionCompleta(){
        try {
            $this->beginTransaction();

            $sql = "INSERT INTO documento_electronico(
                idcliente,
                idtipo_documento_cliente,
                numero_documento_cliente,
                descripcion_cliente,
                direccion_cliente,
                idtipo_comprobante_modifica,
                serie_documento_modifica,
                numero_documento_modifica,
                idcod_tipo_motivo_nota,
                descripcion_motivo_nota,
                idtipo_operacion,
                fecha_emision,
                fecha_vencimiento,
                idtipo_moneda,
                idtipo_comprobante,
                serie, 
                numero_correlativo,
                codigo_ubigeo_cliente,
                total_gravadas,
                total_inafectas,
                total_exoneradas,
                descuento_global,
                porcentaje_descuento,
                importe_total,
                igv,
                total_igv,
                total_isc,
                total_otro_imp,
                total_letras,
                id_documento_electronico_previo,
                id_usuario_registrado,
                xml_filename
                )
                SELECT 
                    idcliente,
                    idtipo_documento_cliente,
                    numero_documento_cliente,
                    descripcion_cliente,
                    direccion_cliente,
                    idtipo_comprobante,
                    serie,
                    numero_correlativo,
                    '01',
                    :1,
                    idtipo_operacion,
                    CURRENT_DATE as fe,
                    CURRENT_DATE as fv,
                    idtipo_moneda,
                    '07',
                    :2,
                    :3,
                    codigo_ubigeo_cliente,
                    total_gravadas,
                    total_inafectas,
                    total_exoneradas,
                    descuento_global,
                    porcentaje_descuento,
                    importe_total,
                    igv,
                    total_igv,
                    total_isc,
                    total_otro_imp,
                    total_letras,
                    iddocumento_electronico,
                    :4,
                    :5
                    FROM documento_electronico
                    WHERE iddocumento_electronico = :0";

            $this->ejecutarSimple($sql,[
                                        $this->id_documento_electronico_previo, 
                                        $this->motivo_anulacion,
                                        $this->serie,
                                        $this->numero_correlativo,
                                        $this->id_usuario_registrado,
                                        F_RUC."-07-".$this->serie."-".str_pad($this->numero_correlativo,6,'0',STR_PAD_LEFT)
                                    ]);

            $iddocumento_electronico_notacredito = $this->getLastID();

            $sql = "INSERT INTO documento_electronico_detalle(
                iddocumento_electronico,
                idproducto,
                item,
                idunidad_medida,
                cantidad_item,
                descripcion_item,
                descripcion_detalle,
                peso_bruto_total,
                peso_neto_total,
                precio_venta_unitario,
                subtotal,
                valor_venta_unitario,
                valor_venta,
                total_igv,
                total_isc,
                idtipo_afectacion,
                idcodigo_precio,
                codigo_sunat,
                codigo_interno
                )
                SELECT 
                    :1,
                    idproducto,
                    item,
                    idunidad_medida,
                    cantidad_item,
                    descripcion_item,
                    descripcion_detalle,
                    peso_bruto_total,
                    peso_neto_total,
                    precio_venta_unitario,
                    subtotal,
                    valor_venta_unitario,
                    valor_venta,
                    total_igv,
                    total_isc,
                    idtipo_afectacion,
                    idcodigo_precio,
                    codigo_sunat,
                    codigo_interno
                    FROM documento_electronico_detalle
                    WHERE iddocumento_electronico = :0";

            $this->ejecutarSimple($sql, [$this->id_documento_electronico_previo, $iddocumento_electronico_notacredito]);
                
            $this->id_documento_electronico = $iddocumento_electronico_notacredito;

            $this->numero_correlativo = $this->numero_correlativo + 1;
            $this->update("serie_documento", 
                        [ "numero"=>$this->numero_correlativo],
                        ["serie"=>$this->serie, "idtipo_comprobante"=>$this->id_tipo_comprobante]);

            $this->commit();

            return ["id_documento_electronico"=> $this->id_documento_electronico];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function anularComprobanteDesdeAtencionMedica($comprobanteAsociado, $motivo_anulacion, $idUsuarioAnulado){
        try {
            /*
                comprobanteAsociado[iddocumento_electronico, id_atencion_medica, fecha_emision]
            */
            $this->beginTransaction();
            
            //$esMismoDia = date('Y-m-d') == $comprobanteAsociado["fecha_emision"];

            //if (!$esMismoDia){
            $tipoNotaCredito = "01"; /*anulacion de operacion*/
            $objNotaCredito = $this->crearNotaCreditoDesdeComprobante($comprobanteAsociado["iddocumento_electronico"], $tipoNotaCredito, $motivo_anulacion);
            //}

            $this->update("documento_electronico", 
                        [   "motivo_anulacion"=> $motivo_anulacion,
                            "estado_anulado"=>"1",
                            "fecha_hora_anulacion"=>date("Y-m-d H:i:s"),
                            "id_usuario_registro_anulacion"=>$idUsuarioAnulado
                        ],
                        ["iddocumento_electronico"=>$comprobanteAsociado["iddocumento_electronico"]]);
            
            $this->commit();
            return ["msj"=>"Comprobante anulado correctamente.", "nota_credito"=>$objNotaCredito["comprobante"]];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarXMLComprobante(){
        try {

            if ($this->id_documento_electronico == NULL || $this->id_documento_electronico == ""){
                throw new Exception("ID Comprobante electr??nico no v??lido.", 1);                    
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
            $xml_filename = NULL;
            if (isset($respuesta->respuesta) && $respuesta->respuesta == "ok"){
                $fue_generado = 1;
                $xml_filename = $respuesta->xml_filename;
            }

            return ["respuesta"=>$respuesta,"fue_generado"=>$fue_generado, "datos_comprobante"=>$datosComprobante, "xml_filename"=>$xml_filename];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function firmarXMLComprobante($datosComprobante){
        try {

            if ($datosComprobante == NULL){
                if ($this->id_documento_electronico == NULL || $this->id_documento_electronico == ""){
                    throw new Exception("ID Comprobante electr??nico no v??lido.", 1);                    
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

    public function registrarComprobanteBase(){
        try {
            
            $porcentaje_descuento = 0.00;
            $descuento_global_igv = 0.00;
            if ($this->descuento_global > 0.00){
                $descuento_global_igv = $this->descuento_global;
                $this->descuento_global = round($descuento_global_igv / (1 + IGV),2);

                $porcentaje_descuento =  round($this->descuento_global  / ($this->descuento_global + $this->importe_total) * 100,3);
            }

            $this->forma_pago = $this->forma_pago == NULL ? "1": $this->forma_pago;

            $campos_valores = [
                "idcliente"=>$this->Cliente["id_cliente"],
                "idtipo_documento_cliente"=>$this->Cliente["id_tipo_documento"],
                "numero_documento_cliente"=>$this->Cliente["numero_documento"],
                "descripcion_cliente"=>$this->Cliente["nombres_completos"],
                "direccion_cliente"=>$this->Cliente["direccion"],
                "codigo_ubigeo_cliente"=>$this->Cliente["codigo_ubigeo_distrito"],
                "serie"=>$this->serie,
                "numero_correlativo"=>$this->numero_correlativo,
                "idtipo_operacion"=>"0101",
                "fecha_emision"=>$this->fecha_emision,
                "fecha_vencimiento"=>$this->fecha_vencimiento == NULL ? $this->fecha_emision : $this->fecha_vencimiento,
                "idtipo_moneda"=>"PEN",
                "idtipo_comprobante"=>$this->id_tipo_comprobante,
                "descuento_global"=>$this->descuento_global,
                "descuento_global_igv"=>$descuento_global_igv,
                "porcentaje_descuento"=>$porcentaje_descuento,
                "condicion_pago"=>$this->forma_pago,
                "importe_credito"=>$this->forma_pago == "1" ? "0.00": $this->importe_total,
                "importe_total"=>$this->importe_total,
                "total_letras"=>Funciones::numtoletras($this->importe_total),
                "observaciones"=>$this->observaciones == "" ? NULL : $this->observaciones,
                "id_atencion_medica"=>$this->id_atencion_medica,
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$this->fecha_ahora,
                "xml_filename"=>F_RUC."-".$this->id_tipo_comprobante."-".$this->serie."-".str_pad($this->numero_correlativo,6,'0',STR_PAD_LEFT),
                "es_convenio"=>$this->es_convenio == NULL ? "0": "1",
                "id_documento_electronico_previo"=>$this->id_documento_electronico_previo,
                "idtipo_comprobante_modifica"=>$this->id_tipo_comprobante_previo,
                "serie_documento_modifica"=>$this->serie_comprobante_previo,
                "numero_documento_modifica"=>$this->numero_correlativo_comprobante_previo,
                "idcod_tipo_motivo_nota"=>$this->cod_tipo_motivo_nota,
                "descripcion_motivo_nota"=>$this->motivo_anulacion,
                "id_atencion_medica_convenio"=>$this->id_atencion_medica_convenio
            ];

            if ($this->id_tipo_comprobante == "01"){
                if (strlen($this->Cliente["numero_documento"]) != 11){
                    throw new Exception("Documento de cliente NO VALIDO para un FACTURA.", 1);
                }
            }

            if ($this->id_tipo_comprobante == "03"){

                if ($this->Cliente["id_tipo_documento"] == "1" && strlen($this->Cliente["numero_documento"]) != 8){
                    throw new Exception("Documento de cliente NO VALIDO para un BOLETA.", 1);
                }
                if ($this->Cliente["id_tipo_documento"] == "6" && strlen($this->Cliente["numero_documento"]) != 11){
                    throw new Exception("Documento de cliente NO VALIDO para un BOLETA.", 1);
                }
            }

            $this->insert("documento_electronico", $campos_valores);
            $this->id_documento_electronico = $this->getLastID();

            if ($this->forma_pago == "0"){
                if (count($this->cuotas) <= 0){
                    throw new Exception("No se han enviado suficientes cuotas en la forma de pago de CR??DITO.", 1);
                }

                $total_cuotas = 0.00;
                foreach ($this->cuotas as $numero_cuota => $cuota) {
                    $total_cuotas  = $total_cuotas + $cuota->monto_cuota;
                    $campos_valores = [
                        "iddocumento_electronico"=>$this->id_documento_electronico,
                        "fecha_vencimiento"=>$cuota->fecha_vencimiento,
                        "monto_cuota"=>$cuota->monto_cuota,
                        "numero_cuota"=>"Cuota". str_pad($numero_cuota + 1,3,'0',STR_PAD_LEFT)
                    ];

                    $this->insert("documento_electronico_cuota", $campos_valores);
                }

                if ($total_cuotas != $this->importe_total){
                    throw new Exception("El monto acumulado de las cuotas no est?? acorde al importe total del comprobante", 1);
                }
            }

            $cantidad_servicios = count($this->detalle);

            $campos_detalle = [
                "iddocumento_electronico",
                "idproducto",
                "item",
                "idunidad_medida",
                "cantidad_item",
                "descripcion_item",
                "precio_venta_unitario",
                "subtotal",
                "valor_venta_unitario",
                "valor_venta",
                "total_igv",
                "idtipo_afectacion",
                "codigo_interno"
            ];

            $valores_detalle = [];
            $total_gravadas = 0.00;
            $total_inafectas = 0.00;
            $total_exoneradas = 0.00;
            $total_igv = 0.00;

            for ($i=0; $i < $cantidad_servicios; $i++) { 
                $o = $this->detalle[$i];
                $o->cantidad = $o->cantidad == NULL ? "1" : $o->cantidad;
                $subtotal = $o->precio_unitario * $o->cantidad;
                
                if ($o->idtipo_afectacion < 20){
                    $valor_venta_unitario = $o->precio_unitario / (1 + IGV);
                    $valor_venta = $valor_venta_unitario * $o->cantidad;

                    $total_gravadas += $valor_venta;
                    $igv = $subtotal - $valor_venta;
                } else { //Incluye el 20 - 40 sin igv
                    $valor_venta_unitario = $o->precio_unitario;
                    $valor_venta  = $o->precio_unitario * $o->cantidad;
                    $igv = 0.00;
                    if ($o->idtipo_afectacion < 30){
                        $total_exoneradas += $valor_venta;
                    } else {
                        $total_infafectas += $valor_venta;
                    }
                }
                array_push($valores_detalle, [
                    $this->id_documento_electronico,
                    $o->id_servicio,
                    ($i+1),
                    $o->idunidad_medida == NULL ? $this->idunidad_medida : "ZZ",
                    $o->cantidad,
                    $o->nombre_servicio,
                    $o->precio_unitario,
                    $subtotal,
                    round($valor_venta_unitario, 4),
                    round($valor_venta, 2),
                    $igv,
                    $o->idtipo_afectacion,
                    "SER".$o->id_servicio
                ]);

                $total_igv += $igv;
            }
            
            $this->insertMultiple("documento_electronico_detalle", $campos_detalle, $valores_detalle);

            $this->numero_correlativo = $this->numero_correlativo + 1;
            $this->update("serie_documento", 
                        [ "numero"=>$this->numero_correlativo],
                        ["serie"=>$this->serie, "idtipo_comprobante"=>$this->id_tipo_comprobante]);

            if ($this->descuento_global > 0.00){
                $total_gravadas = $this->importe_total / (1 + IGV);
            }

            $total_gravadas = round(round($total_gravadas, 3),2);
            $total_igv = round($this->importe_total - $total_gravadas, 2);

            return ["id_documento_electronico"=>$this->id_documento_electronico, 
                    "total_exoneradas"=>$total_exoneradas, "total_gravadas"=> $total_gravadas,
                    "total_inafectas"=>$total_inafectas, "total_igv"=>$total_igv];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarResumenDiario($fecha_inicio, $fecha_fin){
        try {

            $this->beginTransaction();

            $this->fecha_ahora = date("Y-m-d H:i:s");
            $fechaGeneracion = date("Y-m-d");
            $codigo = "RC";

            $sql = "SELECT distinct(de.fecha_emision)  as fecha_emision,
                        (SELECT  COALESCE(MAX(rd.secuencia) + 1, 1) 
                        FROM documento_electronico_resumen_diario rd 
                        WHERE rd.estado_mrcb and rd.fecha_emision = de.fecha_emision) as secuencia
                        FROM documento_electronico de                     
                        WHERE de.idtipo_comprobante IN ('03','07','08') AND 
                            de.estado_mrcb AND (de.fecha_emision BETWEEN :0 and :1)";
            $objFechas = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            foreach ($rango_fechas as $key => $objFechas) {
                $fechaEmision = $objResumen["fecha_emision"];
                $serie = str_replace("-","",$fechaEmision);
                $secuencia = $objResumen["secuencia"];

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
                                    '1' as STATUS,
                                    de.total_igv as IGV,
                                    de.total_isc as ISC,
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
                                        AND cdr_estado IS NULL AND estado_sunat = 0";
                
                $comprobantes = $this->consultarFilas($sqlComprobantes, [$fechaEmision]);

                if (count($comprobantes) <= 0 ){
                    continue;
                }

                $campos_valores = [
                    "codigo" => $codigo,
                    "serie"=>$serie,
                    "secuencia"=>$secuencia,
                    "fecha_emision"=>$fechaEmision,
                    "fecha_generacion"=>$fechaGeneracion
                ];

                $this->insert("documento_electronico_resumen_diario", $campos_valores);
                $this->id_documento_electronico_rd = $this->getLastID();

                $campos = [
                    "id_documento_electronico_resumen_diario",
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
                        $value["TIPO_COMPROBANTE"],
                        $value["SERIE_COMPROBANTE"],
                        $value["CORRELATIVO_COMPROBANTE"],
                        $value["TIPO_DOCUMENTO"],
                        $value["NRO_DOCUMENTO"],
                        $value["SERIE_COMPROBANTE_REF"],
                        $value["CORRELATIVO_COMPROBANTE_REF"],
                        $value["TIPO_COMPROBANTE_REF"],
                        $value["STATUS"],
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
                    $objXMLComprobante = $this->generarXMLResumenDiario();
                    $fue_generado = $objXMLComprobante["fue_generado"];
                    $datosComprobante = $objXMLComprobante["datos_comprobante"];
                    $respuesta = $objXMLComprobante["respuesta"];
                }
    
                if ($this->firmar_comprobante){
                    //DEBO FIRMAR EL BENDITO COMPROBANTE ?
                    $objXMLFirmaComprobante =  $this->firmarXMLComprobante($datosComprobante);
                    $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                    $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                    $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
                }
    
                if ($fue_generado == "1"){
                    $campos_valores["fue_generado"] = $fue_generado;
                }
    
                if ($valor_firma != NULL){
                    $campos_valores["fue_firmado"] = "1";
                    $campos_valores["valor_firma"] = $valor_firma;
                    $campos_valores["valor_resumen"] = $valor_resumen;
                }
    
                if (count($campos_valores)){
                    $this->update("documento_electronico", 
                                $campos_valores,
                                ["iddocumento_electronico"=>$this->id_documento_electronico]);
                }

            }

            

            $this->commit();
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarXMLResumenDiario(){
        try {

            if ($this->id_documento_electronico_rd == NULL || $this->id_documento_electronico_rd == ""){
                throw new Exception("ID Resumen Diario electr??nico no v??lido.", 1);                    
            }
            $datosComprobante = $this->obtenerDatosParaCreacionXMLResumenDiario();
        
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

    public function firmarXMLResumenDiario($datosComprobante){
        try {

            if ($datosComprobante == NULL){
                if ($this->id_documento_electronico == NULL || $this->id_documento_electronico == ""){
                    throw new Exception("ID Comprobante electr??nico no v??lido.", 1);                    
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

    public function _crearNotasCreditoComprobantesAnulados($fecha_inicio, $Fecha_fin){
        try {

            $this->beginTransaction();

            $sql  ="SELECT iddocumento_electronico, motivo_anulacion, id_usuario_registro_anulacion, numero_correlativo, serie
                        FROM documento_electronico de
                        WHERE  estado_anulado = 1 AND estado_mrcb AND (fecha_emision BETWEEN :0 AND :1) AND idtipo_comprobante IN ('01','03')
                            AND iddocumento_electronico <> 2883
                        ORDER BY fecha_emision";

            $comprobantes_anulados = $this->consultarFilas($sql, [$fecha_inicio, $Fecha_fin]);

            foreach ($comprobantes_anulados as $key => $comprobante) {
                $this->id_usuario_registrado = $comprobante["id_usuario_registro_anulacion"];
                $this->serie = $comprobante["serie"];
                $this->numero_correlativo = $comprobante["numero_correlativo"];
                $tipoNotaCredito = "01"; /*anulacion de operacion*/
                $objNotaCredito = $this->crearNotaCreditoDesdeComprobante($comprobante["iddocumento_electronico"], $tipoNotaCredito, $comprobante["motivo_anulacion"], 1);
            }

            $this->commit();

            return ["msj"=>"Comprobantes anulados correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarAtencionesComprobantes($fecha_inicio, $fecha_fin){
        try {
            $sqlSeries = "SELECT de.serie, 
                        SUM(IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF(de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_gravadas,'0.00') , -1 * de.total_gravadas))) as total_gravadas,
                        SUM(IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_igv,'0.00') , -1 * de.total_igv))) as total_igv,
                        SUM(IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.importe_total,'0.00') , -1 * de.importe_total))) as importe_total
                        FROM documento_electronico de
                        WHERE (de.fecha_emision BETWEEN :0 AND :1) AND de.estado_mrcb
                        GROUP BY de.serie";
            $series = $this->consultarFilas($sqlSeries, [$fecha_inicio, $fecha_fin]);

            $sqlDocumentos  = "SELECT 
                    de.idtipo_comprobante,
                    DATE_FORMAT(de.fecha_emision, '%d/%m/%Y') as fecha_emision,
                    de.serie,
                    de.numero_correlativo as comprobante,
                    de.idtipo_documento_cliente,
                    de.numero_documento_cliente,
                    de.descripcion_cliente as cliente,
                    (CASE   WHEN am.pago_efectivo > 0 THEN 'EF'
                            WHEN am.pago_tarjeta > 0 THEN 'TJ'
                            WHEN am.pago_deposito > 0 THEN 'DP'
                            ELSE 'CR' END) as metodo_pago,  
                    DATE_FORMAT(de.fecha_emision,'%d/%m/%Y') as fecha_exportacion,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF(de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_gravadas,'0.00') , -1 * de.total_gravadas)) as total_gravadas,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_igv,'0.00') , -1 * de.total_igv)) as total_igv,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.importe_total,'0.00') , -1 * de.importe_total)) as importe_total,
                    COALESCE(b.codigo_contab,'') as codigo_entidad,
                    COALESCE(am.numero_operacion,'') as numero_operacion_banco,
                    IF(am.numero_voucher IS NOT NULL,'VISAS','') as tipo_tarjeta,
                    '0' as emitido,
                    de.igv as porcentaje_igv,
                    COALESCE(DATE_FORMAT(de_nota.fecha_emision,'%d/%m/%Y'),'00/00/0000') as fecha_modificado,
                    COALESCE(de.idtipo_comprobante_modifica,'00') as td_modifica,
                    COALESCE(de.serie_documento_modifica,'0000') as serie_modifica,
                    COALESCE(LPAD(de.numero_documento_modifica,7,'0'),'000000') as correlativo_modifica,
                    de.estado_anulado,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', am.pago_efectivo , -1 * am_nota.pago_efectivo)) as pago_efectivo,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', am.pago_tarjeta , -1 * am_nota.pago_tarjeta)) as pago_tarjeta,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', am.pago_deposito , -1 * am_nota.pago_deposito)) as pago_deposito,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', am.pago_credito , -1 * am_nota.pago_credito)) as pago_credito,
                    de.cdr_estado,
                    IF (de.cdr_estado IS NULL, 'NO ENVIADO', (CASE de.cdr_estado WHEN '0' THEN 'ACEPTADO' WHEN '-1' THEN 'REVISAR' WHEN '' THEN 'REENVIAR' ELSE 'RECHAZADO' END)) as cdr_estado_descripcion,
                    IF (de.cdr_estado IS NULL, 'gray', (CASE de.cdr_estado WHEN '0' THEN 'green' WHEN '-1' THEN 'orange' WHEN '' THEN 'blue' ELSE 'red' END)) as cdr_estado_color,
                    de.cdr_descripcion              
                    FROM documento_electronico de
                    LEFT JOIN atencion_medica am ON de.id_atencion_medica = am.id_atencion_medica
                    LEFT JOIN banco b ON b.id_banco = am.id_banco
                    LEFT JOIN documento_electronico de_nota ON de_nota.serie  = de.serie_documento_modifica AND de_nota.numero_correlativo = de.numero_documento_modifica AND de_nota.estado_mrcb
                    LEFT JOIN atencion_medica am_nota ON am_nota.id_atencion_medica = de_nota.id_atencion_medica
                    WHERE (de.fecha_emision BETWEEN :0 AND :1) AND de.estado_mrcb  AND de.serie = :2
                    ORDER BY de.fecha_emision, de.serie, de.numero_correlativo";
            
            $total_gravadas = 0;
            $total_igv = 0;
            $importe_total = 0;

            $total_efectivo = 0;
            $total_credito = 0;
            $total_tarjeta = 0;
            $total_deposito = 0;

            $todos_comprobantes = [];
            foreach ($series as $key => $value) {
                $comprobantes = $this->consultarFilas($sqlDocumentos, [$fecha_inicio, $fecha_fin, $value["serie"]]);
                $series[$key]["comprobantes"] = $comprobantes;
                $total_gravadas = $total_gravadas + $value["total_gravadas"];
                $total_igv = $total_igv + $value["total_igv"];
                $importe_total = $importe_total +  $value["importe_total"];

                foreach ($comprobantes as $_k => $_v) {
                    $total_efectivo = $total_efectivo + $_v["pago_efectivo"];
                    $total_credito = $total_credito + $_v["pago_credito"];
                    $total_tarjeta = $total_tarjeta + $_v["pago_tarjeta"];
                    $total_deposito = $total_deposito + $_v["pago_deposito"];
                }

                $todos_comprobantes = array_merge($todos_comprobantes, $comprobantes);
            }

            $totales = [
                "total_gravadas"=>$total_gravadas,
                "total_igv"=>$total_igv,
                "importe_total"=>$importe_total
            ];

            $otros_totales = [
                "total_efectivo"=>$total_efectivo,
                "total_credito"=>$total_credito,
                "total_tarjeta"=>$total_tarjeta,
                "total_deposito"=>$total_deposito
            ];

            
            return ["series"=>$series, "todos_comprobantes"=>$todos_comprobantes, "totales"=>$totales, "otros_totales"=>$otros_totales];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function enviarComprobantesFactura($id_tipo_comprobante, $fecha_inicio, $fecha_fin, $forzado = false){
        try {   
            /*S?? es $forzado = true, enviar?? el documento aun ya haya sido enviado y tenga un ticket asociado.*/
            if ($forzado){
                $sql  = "SELECT iddocumento_electronico as id, xml_filename as nombre_archivo, fecha_emision 
                        FROM documento_electronico
                        WHERE estado_mrcb AND  (fecha_emision BETWEEN :1 AND :2) -- AND (cdr_estado IS NULL OR cdr_estado < 0)
                        AND idtipo_comprobante IN (:0) AND serie LIKE 'F%'";
            } else {
                $sql  = "SELECT de.iddocumento_electronico as id, de.xml_filename as nombre_archivo, de.fecha_emision
                        FROM documento_electronico de
                        LEFT JOIN documento_electronico fact ON fact.serie = de.serie_documento_modifica AND fact.numero_correlativo = de.numero_documento_modifica
                        WHERE de.estado_mrcb AND  (de.fecha_emision BETWEEN :1 AND :2) AND (de.cdr_estado IS NULL OR de.cdr_estado < -1) AND de.enviar_a_sunat = 0
                             AND de.idtipo_comprobante IN (:0) AND de.serie LIKE 'F%' AND ". ($id_tipo_comprobante == '01' ? "true" : " fact.cdr_estado = 0 ");
            }
            
            $comprobantes = $this->consultarFilas($sql, [$id_tipo_comprobante, $fecha_inicio, $fecha_fin]);

            $data_json = json_encode(["comprobantes"=>$comprobantes, "id_tipo_comprobante"=> ($id_tipo_comprobante == "01" ? "FA" : ($id_tipo_comprobante == "07" ?  "NC" : "ND")), "tipo_proceso"=>F_MODO_PROCESO]);

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
            $respuestasEnvioJSON  = curl_exec($ch);
            curl_close($ch);

            $respuestasEnvio = json_decode($respuestasEnvioJSON);

            $jsonEsValido = json_last_error() === JSON_ERROR_NONE;

            if( $jsonEsValido ){
                $this->beginTransaction();

                foreach ($respuestasEnvio as $key => $respuesta) {
                    $cdr_estado = NULL;
                    $cdr_descripcion = "";
                    $cdr_observaciones = NULL;
                    $cdr_hash = "";
                    $enviar_a_sunat = 0;
                    $estado_anulado = 0;
                    $motivo_anulacion = NULL;
                    $anulado_por_nota = "1";

                    if (isset($respuesta->respuesta)){
                        $cdr_estado  = $respuesta->cod_sunat;
                        $cdr_descripcion  = $respuesta->mensaje;
                        $cdr_hash  = $respuesta->hash_cdr;

                        if ($cdr_estado < 0){
                            $enviar_a_sunat = 0;
                            $cdr_observaciones = "ERROR POR NO CONEXION A SUNAT. REENVIAR XML NUEVAMENTE.";
                        } else {  
                            if ($cdr_estado >= 2000){
                                $enviar_a_sunat = 1;          
                                $estado_anulado = "1";
                                $anulado_por_nota = "0";
                                $motivo_anulacion = "RECHAZADO SUNAT: ".$respuesta->mensaje;
                            } else if ($cdr_estado > 0 && $cdr_estado < 2000) {
                                $enviar_a_sunat = 0; 
                                $cdr_observaciones = "ERROR POR EXCEPCION. GENERAR O REENVIAR XML NUEVAMENTE.";
                            }
                        }

                        $sql  = "UPDATE documento_electronico SET 
                                numero_veces_enviado = numero_veces_enviado + 1,
                                fecha_hora_envio = CURRENT_TIMESTAMP,
                                enviar_a_sunat = ".$enviar_a_sunat.",
                                cdr_descripcion = '".str_replace("'",' ', $cdr_descripcion)."',
                                cdr_observaciones = ".($cdr_observaciones == NULL ? "NULL" : "'".$cdr_observaciones."'") .",
                                cdr_hash = '".$cdr_hash."',
                                cdr_estado = '".$cdr_estado."',
                                estado_anulado = '".$estado_anulado."',
                                motivo_anulacion = ".($motivo_anulacion == NULL ? "NULL" : "'".$motivo_anulacion."'")."
                                WHERE  estado_mrcb AND iddocumento_electronico = '".$respuesta->id."'";

                        $this->consultaRaw($sql);
                    }

                }
        
                $this->commit();
            }

            return ["respuestas"=> (!$jsonEsValido ? $respuestasEnvioJSON : $respuestasEnvio)];
        } catch (Exception $exc) {
            throw new Exception($cdr_descripcion." ".$exc->getMessage(), 1);
        }
    }

    public function canjearComprobante(){
        try {

            $this->beginTransaction();

            $sql = "SELECT serie_boleta, serie_factura 
                                FROM caja
                                WHERE id_caja IN (SELECT id_caja 
                                                    FROM caja_instancia 
                                                    WHERE id_caja_instancia IN (SELECT id_caja_instancia FROM atencion_medica WHERE id_atencion_medica = :0))
                                    AND estado_mrcb";
            $objSerie = $this->consultarFila($sql, [$this->id_atencion_medica]);
            $this->serie = $objSerie[$this->id_tipo_comprobante == "01" ? "serie_factura" : "serie_boleta"];

            $sql  = "SELECT numero
                        FROM serie_documento 
                            WHERE idtipo_comprobante = :0 AND serie = :1";
            $this->numero_correlativo = $this->consultarValor($sql, [$this->id_tipo_comprobante, $this->serie]);

            if ($this->numero_correlativo == "" || $this->numero_correlativo == NULL){
                throw new Exception("Numero correlativo de comprobante no v??lido", 1);
            }

            $sql = "INSERT INTO documento_electronico(
                    idcliente,
                    idtipo_documento_cliente,
                    numero_documento_cliente,
                    descripcion_cliente,
                    direccion_cliente,
                    codigo_ubigeo_cliente,
                    idtipo_operacion,
                    fecha_emision,
                    fecha_vencimiento,
                    idtipo_moneda,
                    idtipo_comprobante,
                    serie, 
                    numero_correlativo,
                    total_gravadas,
                    total_inafectas,
                    total_exoneradas,
                    descuento_global,
                    porcentaje_descuento,
                    descuento_global_igv,
                    importe_total,
                    igv,
                    total_igv,
                    total_isc,
                    total_otro_imp,
                    total_letras,
                    id_usuario_registrado,
                    xml_filename,
                    id_atencion_medica
                    )
                    SELECT 
                        :1,
                        :2,
                        :3,
                        :4,
                        :5,
                        :6,
                        idtipo_operacion,
                        :7,
                        :8,
                        idtipo_moneda,
                        :9,
                        :10,
                        :11,
                        total_gravadas,
                        total_inafectas,
                        total_exoneradas,
                        descuento_global,
                        porcentaje_descuento,
                        descuento_global_igv,
                        importe_total,
                        igv,
                        total_igv,
                        total_isc,
                        total_otro_imp,
                        total_letras,
                        :12,
                        :13,
                        id_atencion_medica
                        FROM documento_electronico
                        WHERE iddocumento_electronico = :0";
        
            $this->ejecutarSimple($sql,[
                                        $this->id_documento_electronico_previo, 
                                        $this->Cliente["id_cliente"],
                                        $this->Cliente["id_tipo_documento"],
                                        $this->Cliente["numero_documento"],
                                        $this->Cliente["nombres_completos"],
                                        $this->Cliente["direccion"],
                                        $this->Cliente["codigo_ubigeo_distrito"],
                                        $this->fecha_emision,
                                        $this->fecha_emision,
                                        $this->id_tipo_comprobante,
                                        $this->serie,
                                        $this->numero_correlativo,
                                        $this->id_usuario_registrado,
                                        F_RUC."-07-".$this->serie."-".str_pad($this->numero_correlativo,6,'0',STR_PAD_LEFT)
                                    ]);

            $iddocumento_electronico = $this->getLastID();

            $sql = "INSERT INTO documento_electronico_detalle(
                iddocumento_electronico,
                idproducto,
                item,
                idunidad_medida,
                cantidad_item,
                descripcion_item,
                descripcion_detalle,
                peso_bruto_total,
                peso_neto_total,
                precio_venta_unitario,
                subtotal,
                valor_venta_unitario,
                valor_venta,
                total_igv,
                total_isc,
                idtipo_afectacion,
                idcodigo_precio,
                codigo_sunat,
                codigo_interno
                )
                SELECT 
                    :1,
                    idproducto,
                    item,
                    idunidad_medida,
                    cantidad_item,
                    descripcion_item,
                    descripcion_detalle,
                    peso_bruto_total,
                    peso_neto_total,
                    precio_venta_unitario,
                    subtotal,
                    valor_venta_unitario,
                    valor_venta,
                    total_igv,
                    total_isc,
                    idtipo_afectacion,
                    idcodigo_precio,
                    codigo_sunat,
                    codigo_interno
                    FROM documento_electronico_detalle
                    WHERE iddocumento_electronico = :0";

            $this->ejecutarSimple($sql, [$this->id_documento_electronico_previo, $iddocumento_electronico]);
                
            $this->id_documento_electronico = $iddocumento_electronico;   

            $this->numero_correlativo = $this->numero_correlativo + 1;
            $this->update("serie_documento", 
                        [ "numero"=>$this->numero_correlativo],
                        ["serie"=>$this->serie, "idtipo_comprobante"=>$this->id_tipo_comprobante]);

            $respuesta = [];
            $respuestafirma = [];
            $datosComprobante = null;
            $valor_firma = null; $valor_resumen = null; 
            $fue_generado = "0";
            $fue_firmado = "0";
            $xml_filename = NULL;

            if ($this->generar_xml){
                $objXMLComprobante = $this->generarXMLComprobante();
                $fue_generado = $objXMLComprobante["fue_generado"];
                $datosComprobante = $objXMLComprobante["datos_comprobante"];
                $respuesta = $objXMLComprobante["respuesta"];
                $xml_filename = $objXMLComprobante["xml_filename"];
            }

            if ($this->firmar_comprobante){
                $objXMLFirmaComprobante =  $this->firmarXMLComprobante($datosComprobante);
                $fue_firmado = "1";
                $valor_firma = $objXMLFirmaComprobante["valor_firma"];
                $valor_resumen = $objXMLFirmaComprobante["valor_resumen"];
                $respuestafirma = $objXMLFirmaComprobante["respuestafirma"];
            }

            $this->update("documento_electronico", 
                        [   "valor_firma"=>$valor_firma,
                            "valor_resumen"=>$valor_resumen,
                            "fue_generado"=>$fue_generado,
                            "xml_filename"=>$xml_filename,
                            "fue_firmado"=>$fue_firmado],
                        ["iddocumento_electronico"=>$this->id_documento_electronico]);

            $this->commit();
            return ["msj"=>"Registro realizado correctamente.", 
                            "r"=>$respuesta, 
                            "rfirma"=>$respuestafirma];  
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarComprobantesConvenio($fecha_inicio, $fecha_fin){
        try {

            $sql  = "SELECT  
                        de.iddocumento_electronico,
                        de.idtipo_comprobante,
                        DATE_FORMAT(de.fecha_emision, '%d/%m/%Y') as fecha_emision,
                        de.serie,
                        LPAD(de.numero_correlativo,6,'0') as comprobante,
                        de.idtipo_documento_cliente,
                        de.numero_documento_cliente,
                        de.descripcion_cliente as cliente,
                        de.total_gravadas,
                        de.total_igv,
                        de.importe_total,
                        de.estado_anulado,
                        de.cdr_estado, de.cdr_descripcion
                        FROM documento_electronico de 
                        WHERE de.es_convenio AND de.estado_mrcb AND  (de.fecha_emision BETWEEN :0 AND :1)
                        ORDER BY fecha_hora_registrado DESC";

            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerTipoMotivosNota($id_tipo_nota){
        try {

            $sql  = "SELECT  
                        idtipo_motivo_nota as id, descripcion 
                        FROM motivo_nota WHERE  idtipo_nota = :0";

            $datos = $this->consultarFilas($sql, [$id_tipo_nota]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function guardarPorConvenio($numeroTicket = NULL){
        try {
            $this->beginTransaction();

            require "Paciente.clase.php";
            $objPaciente = new Paciente($this->getDB());
            $objClienteCreado = $objPaciente->registrarClienteXRUC($this->Cliente["numero_documento"], $this->Cliente["nombres_completos"], $this->Cliente["direccion"]);
            $this->Cliente["id_cliente"] = $objClienteCreado["cliente"]["id"];

            if ($numeroTicket != NULL){
                $sql = "SELECT id_atencion_medica FROM atencion_medica WHERE numero_acto_medico = :0 AND estado_mrcb";
                $this->id_atencion_medica_convenio = $this->consultarValor($sql, [$numeroTicket]);
            }
            
            if ($this->id_tipo_comprobante == "07" || $this->id_tipo_comprobante == "08"){
                $sql = "SELECT iddocumento_electronico as id_documento_electronico, 
                        id_atencion_medica_convenio, idtipo_comprobante, importe_total
                        FROM documento_electronico
                         WHERE serie = :0 AND numero_correlativo = :1 AND idtipo_comprobante = '01' 
                            AND estado_anulado = '0' AND cdr_estado = '0' AND estado_mrcb";
                $objDocElectronico = $this->consultarFila($sql, [$this->serie_comprobante_previo, $this->numero_correlativo_comprobante_previo]);

                if ($objDocElectronico == false){
                    throw new Exception("El documento asociado a la nota no existe como enviado a SUNAT.", 1);
                } 

                if (($this->id_tipo_comprobante == "07")  && 
                    ($this->importe_total > $objDocElectronico["importe_total"]) 
                    ){
                    throw new Exception("La nota de cr??dito debe tener un monto menor igual al comprobante original.", 1);
                }

                $this->id_atencion_medica_convenio = $objDocElectronico["id_atencion_medica_convenio"];
                $this->id_documento_electronico_previo = $objDocElectronico["id_documento_electronico"];
                $this->id_tipo_comprobante_previo = $objDocElectronico["idtipo_comprobante"];

                if ($this->id_tipo_comprobante == "07" && ($this->cod_tipo_motivo_nota == "01" || $this->cod_tipo_motivo_nota == "02")){
                    $campos_valores = ["estado_anulado"=>"1", "motivo_anulacion"=>$this->motivo_anulacion, "fecha_hora_anulacion"=>date("Y-m-d H:i:s"), "id_usuario_registro_anulacion"=>$this->id_usuario_registrado];
                    $campos_valores_where = ["iddocumento_electronico"=>$this->id_documento_electronico_previo];

                    $this->update("documento_electronico", $campos_valores, $campos_valores_where);
                }

            }

            $obj = $this->generarComprobante($this->id_tipo_comprobante);


            $this->commit();

            return [
                        "msj"=>$obj["msj"], 
                        "r"=>$obj["r"], 
                        "id"=>$this->id_documento_electronico
                    ];  
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function _enviarComprobantesFactura($comprobantes){
        try{
            $data_json = json_encode(["comprobantes"=>$comprobantes, 
                                        "id_tipo_comprobante"=> ($this->id_tipo_comprobante == "01" ? "FA" : ($this->id_tipo_comprobante == "07" ?  "NC" : "ND")), 
                                        "tipo_proceso"=>F_MODO_PROCESO]);

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
            $respuestasEnvioJSON  = curl_exec($ch);
            curl_close($ch);

            $respuestasEnvio = json_decode($respuestasEnvioJSON);

            $jsonEsValido = json_last_error() === JSON_ERROR_NONE;

            if( $jsonEsValido ){
                $this->beginTransaction();

                foreach ($respuestasEnvio as $key => $respuesta) {
                    $cdr_estado = NULL;
                    $cdr_descripcion = "";
                    $cdr_observaciones = NULL;
                    $cdr_hash = "";
                    $enviar_a_sunat = 1;
                    $estado_anulado = 0;
                    $motivo_anulacion = NULL;
                    $anulado_por_nota = "1";

                    if (isset($respuesta->respuesta)){
                        $cdr_estado  = $respuesta->cod_sunat;
                        $cdr_descripcion  = $respuesta->mensaje;
                        $cdr_hash  = $respuesta->hash_cdr;

                        if ($cdr_estado < 0){
                            $enviar_a_sunat = 0;
                            $cdr_observaciones = "ERROR POR NO CONEXION A SUNAT. REENVIAR XML NUEVAMENTE.";
                        } else {  
                            if ($cdr_estado >= 2000){
                                $enviar_a_sunat = 1;          
                                $estado_anulado = "1";
                                $anulado_por_nota = "0";
                                $motivo_anulacion = "RECHAZADO SUNAT: ".$respuesta->mensaje;
                            } else if ($cdr_estado > 0 && $cdr_estado < 2000) {
                                $enviar_a_sunat = 0; 
                                $cdr_observaciones = "ERROR POR EXCEPCION. GENERAR O REENVIAR XML NUEVAMENTE.";
                            }
                        }

                        $sql  = "UPDATE documento_electronico SET 
                                numero_veces_enviado = numero_veces_enviado + 1,
                                fecha_hora_envio = CURRENT_TIMESTAMP,
                                enviar_a_sunat = ".$enviar_a_sunat.",
                                cdr_descripcion = '".str_replace("'",' ', $cdr_descripcion)."',
                                cdr_observaciones = ".($cdr_observaciones == NULL ? "NULL" : "'".$cdr_observaciones."'") .",
                                cdr_hash = '".$cdr_hash."',
                                cdr_estado = '".$cdr_estado."',
                                estado_anulado = '".$estado_anulado."',
                                anulado_por_nota = '".$anulado_por_nota."',
                                motivo_anulacion = ".($motivo_anulacion == NULL ? "NULL" : "'".$motivo_anulacion."'")."
                                WHERE  estado_mrcb AND iddocumento_electronico = '".$respuesta->id."'";

                        $this->consultaRaw($sql);
                    }

                }
        
                $this->commit();
            }

            return ["respuestas"=> (!$jsonEsValido ? $respuestasEnvioJSON : $respuestasEnvio)];
        } catch (Exception $exc) {
            throw new Exception($cdr_descripcion." ".$exc->getMessage(), 1);
        }
    }
    
    public function enviarSUNATPorId($forzado = false){
        try {   
            /*S?? es $forzado = true, enviar?? el documento aun ya haya sido enviado y tenga un ticket asociado.*/
            if ($forzado){
                $sql  = "SELECT iddocumento_electronico as id, idtipo_comprobante as id_tipo_comprobante, xml_filename as nombre_archivo, fecha_emision 
                            FROM documento_electronico
                            WHERE estado_mrcb AND cdr_estado IS NULL
                            AND iddocumento_electronico IN (:0)";
            } else {
                $sql  = "SELECT iddocumento_electronico as id, idtipo_comprobante as id_tipo_comprobante, xml_filename as nombre_archivo, fecha_emision 
                            FROM documento_electronico
                            WHERE estado_mrcb AND (cdr_estado IS NULL OR (cdr_estado <> 0  AND cdr_estado < 2000)) AND enviar_a_sunat = 0
                            AND iddocumento_electronico IN (:0)";
            }

            $comprobante = $this->consultarFila($sql, [$this->id_documento_electronico]);
            if($comprobante == false){
                throw new Exception("El ID del comprobante no es v??lido, no encontrado.", 1);
            }

            $this->id_tipo_comprobante = $comprobante["id_tipo_comprobante"];
            $objRespuestas =  $this->_enviarComprobantesFactura([$comprobante]);

            $respuesta = null;
            if ($objRespuestas && $objRespuestas["respuestas"]){
                $respuesta = $objRespuestas["respuestas"][0];
                $sql  = "SELECT  
                        de.iddocumento_electronico,
                        de.idtipo_comprobante,
                        DATE_FORMAT(de.fecha_emision, '%d/%m/%Y') as fecha_emision,
                        de.serie,
                        LPAD(de.numero_correlativo,6,'0') as comprobante,
                        de.idtipo_documento_cliente,
                        de.numero_documento_cliente,
                        de.descripcion_cliente as cliente,
                        de.total_gravadas,
                        de.total_igv,
                        de.importe_total,
                        de.estado_anulado,
                        de.cdr_estado, de.cdr_descripcion
                        FROM documento_electronico de 
                        WHERE de.es_convenio AND de.estado_mrcb AND iddocumento_electronico = :0
                        ORDER BY fecha_emision DESC";

                $respuesta->registro = $this->consultarFila($sql, [$this->id_documento_electronico]);
            }

            return $respuesta;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDocumentoElectronicoPorId(){
        try {

            $sql  = "SELECT  
                        de.iddocumento_electronico,
                        de.serie,
                        de.numero_correlativo,
                        de.numero_documento_cliente
                        FROM documento_electronico de 
                        WHERE de.es_convenio AND de.iddocumento_electronico = :0 AND de.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_documento_electronico]);
            
            if ($datos == false){
                throw new Exception("Comprobante no encontrado.", 1);
            }

            $sql = "SELECT 
                    cantidad_item as cantidad,
                    descripcion_item as descripcion,
                    precio_venta_unitario as precio,
                    idproducto as id
                    FROM documento_electronico_detalle
                    WHERE iddocumento_electronico = :0
                    ORDER BY item";

            $datos["detalle"] = $this->consultarFilas($sql, [$datos["iddocumento_electronico"]]);
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function validarCDRManual(){
        try {

            $sql  = "SELECT  
                        de.iddocumento_electronico,
                        de.idtipo_comprobante,
                        de.xml_filename
                        FROM documento_electronico de 
                        WHERE  de.iddocumento_electronico = :0 AND de.estado_mrcb";

            $comprobante_validar = $this->consultarFila($sql, [$this->id_documento_electronico]);
            
            if ($comprobante_validar == false){
                throw new Exception("Comprobante no encontrado.", 1);
            }

            $descripcion_comprobante_enviado_generico = "";
            $nombre_comprobante = "";
            switch($comprobante_validar["idtipo_comprobante"]){
                case "01":
                    $nombre_comprobante = "Factura";
                    break;
                case "03":
                    $nombre_comprobante = "Boleta";
                    break;
                case "07":
                    $nombre_comprobante = "Nota de Credito";
                    break;
                case "08":
                    $nombre_comprobante = "Nota de Debito";
                    break;
            }

            if ($nombre_comprobante == ""){
                throw new Exception("Tipo de comprobante no v??lido", 1);
            }
            $tempExplodeXmlFilname = explode("-", $comprobante_validar["xml_filename"]);
            $descripcion_comprobante_enviado_generico = "La ".$nombre_comprobante." n??mero ".$tempExplodeXmlFilname[2].'-'.$tempExplodeXmlFilname[3]." ha sido aceptada";

            $campos_valores = [
                "cdr_estado"=>"0",
                "cdr_descripcion"=>$descripcion_comprobante_enviado_generico,
                "cdr_observaciones"=>NULL,
                "enviar_a_sunat"=>"1"
            ];

            $campos_valores_where = [
                "iddocumento_electronico"=>$this->id_documento_electronico
            ];

            $this->update("documento_electronico", $campos_valores, $campos_valores_where);
            return ["msj"=>"Actualizado correctamente", "estado_color"=>"green", "estado"=>"ACEPTADO", "descripcion"=>$descripcion_comprobante_enviado_generico];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
}