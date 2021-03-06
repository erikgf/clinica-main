<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/variables.php';

class AtencionMedica extends Conexion {
    public $id_atencion_medica;
    public $id_paciente;
    public $nombres_completos;
    public $numero_acto_medico;
    public $numero_documento;
    public $id_medico_realizante;
    public $id_medico_ordenante;
    public $fecha_atencion;
    public $hora_atencion;
    public $observaciones;
    public $id_caja_instancia;
    public $pago_efectivo;
    public $pago_deposito;
    public $id_banco;
    public $numero_operacion;
    public $pago_tarjeta;
    public $pago_credito;
    public $monto_vuelto;
    public $numero_tarjeta;
    public $numero_voucher;
    public $servicios;

    public $monto_descuento;
    public $id_usuario_validador;
    public $motivo_descuento;
    public $es_gratuito_descuento;
    public $total;

    public $id_tipo_comprobante;
    public $serie;
    public $id_usuario_registrado;
    public $ID_TIPO_MOVIMIENTO_ATENCION = "1";

    public $id_usuario_validador_descuento_sin_efectivo;
    public $clave_descuento_sin_efectivo;

    public $factura_id_cliente;
    public $factura_ruc;
    public $factura_razon_social;
    public $factura_direccion;

    public $boleta_id_cliente;
    public $boleta_tipo_documento;
    public $boleta_numero_documento;
    public $boleta_nombres;
    public $boleta_apellido_paterno;
    public $boleta_apellido_materno;
    public $boleta_sexo;
    public $boleta_fecha_nacimiento;

    public $id_atencion_medica_servicio;
    public $id_medico_atendido;
    public $observaciones_atendido;
    public $fue_atendido;
    public $id_convenio_empresa;
    public $convenio_porcentaje;


    private $MAX_CREDITO = 0.50;

    private $ID_CATEGORIA_LABORATORIO = 14;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }
    
    public function registrar(){
        try {

            $this->beginTransaction();

            $fecha_ahora = date("Y-m-d H:i:s");

            require "Paciente.clase.php";
            $objPaciente = new Paciente();

            $resPaciente = $objPaciente->obtenerPacienteXId($this->id_paciente);
            if (!$resPaciente["rpt"]){
                throw new Exception("No existe el paciente ingresado.", 1);
            }

            if ($this->id_tipo_comprobante == "01" && $this->factura_ruc != ""){
                $objClienteCreado = $objPaciente->registrarClienteXRUC($this->factura_ruc, $this->factura_razon_social, $this->factura_direccion);
                $this->factura_id_cliente = $objClienteCreado["cliente"]["id"];
            }

            if ($this->id_tipo_comprobante == "03" && $this->boleta_numero_documento != ""){
                $objClienteCreado = $objPaciente->registrarClienteXOTRO($this->boleta_tipo_documento, $this->boleta_numero_documento, $this->boleta_nombres, $this->boleta_apellido_paterno, $this->boleta_apellido_materno, $this->boleta_sexo, $this->boleta_fecha_nacimiento);
                $this->boleta_id_cliente = $objClienteCreado["cliente"]["id"];
            }

            $resPaciente  = $resPaciente["datos"];

            if ($this->servicios == NULL || $this->servicios == ""){
                throw new Exception("No hay servicios v??lidos enviados.", 1);
            }

            $this->servicios = json_decode($this->servicios);

            $objPaciente->nombres_completos = $resPaciente["nombres_completos"];
            $objPaciente->numero_documento = $resPaciente["numero_documento"];
            $objPaciente->id_tipo_documento = $resPaciente["id_tipo_documento"];
            $objPaciente->direccion = $resPaciente["direccion"];
            $objPaciente->codigo_ubigeo_distrito = $resPaciente["codigo_ubigeo_distrito"];

            if ($objPaciente->numero_documento == "" || $objPaciente->numero_documento == NULL || $objPaciente->numero_documento == "SD"){                
                if (($this->boleta_id_cliente == NULL && $this->id_tipo_comprobante == "03") ||
                    ($this->factura_id_cliente == NULL && $this->id_tipo_comprobante == "01")
                    ){
                    throw new Exception("Necesita ingresar un n??mero documento para emitir comprobante. El paciente original no tiene n??mero documento v??lido.", 1);
                }
            }

            $cantidad_servicios = count($this->servicios);
            if ($cantidad_servicios <= 0){
                throw new Exception("No se ha enviado servicios en esta atenci??n.", 1);
            }
            
            if ($this->id_medico_realizante == NULL || $this->id_medico_realizante == ""){
                throw new Exception("M??dico realizante no v??lido.", 1);
            }

            if ($this->id_medico_ordenante == NULL || $this->id_medico_ordenante == ""){
                throw new Exception("M??dico ordenante no v??lido.", 1);
            }

            require "Comision.clase.php";
            $objComision = new Comision();
            $objPromotoraComision = $objComision->obtenerComisionPromotoraXMedico($this->id_medico_ordenante);
            $sql = "SELECT id_promotora FROM medico WHERE id_medico = :0 AND estado_mrcb";
            $id_promotora_realizante = $this->consultarValor($sql, [$this->id_medico_realizante]);

            require "Caja.clase.php";
            $objCaja = new Caja();
            
            if (!$objCaja->esValidaInstanciaCaja($this->id_caja_instancia, $this->fecha_atencion)){
                throw new Exception("Caja no v??lida.", 1);
            }

            $costo_total_atencion = 0.00;
            for ($i=0; $i < $cantidad_servicios; $i++) { 
                $costo_total_atencion += $this->servicios[$i]->precio_unitario;
            }

            /*Descuento forzado basado en que el CONVENIO s?? es un descuento.*/
            if ($this->monto_descuento > 0 && $this->id_convenio_empresa != NULL){
                throw new Exception("No se puede registrar una atenci??n con CONVENIO y DESCUENTO.", 1);
            }

            if ($this->id_convenio_empresa != NULL){
                $this->monto_descuento = round($costo_total_atencion * ($this->convenio_porcentaje / 100), 2, PHP_ROUND_HALF_UP);
            }


            
            $costo_total_atencion = round($costo_total_atencion -  (float) $this->monto_descuento, 4, PHP_ROUND_HALF_UP);



            if ($this->pago_deposito > 0){
                if ($this->id_banco == NULL  || $this->id_banco == ""){
                    throw new Exception("Banco ingresado no v??lido.", 1);
                }

                if ($this->numero_operacion == NULL || $this->numero_operacion == ""){
                    throw new Exception("N??mero de operaci??n de transacci??n no v??lida.", 1);
                }
            } else {
                $this->id_banco = NULL;
                $this->numero_operacion = NULL;
            }

            if ($this->pago_tarjeta > 0){
                if ($this->numero_tarjeta == NULL || $this->numero_tarjeta == ""){
                    throw new Exception("Banco ingresado no v??lido.", 1);
                }

                if ($this->numero_voucher == NULL || $this->numero_voucher == ""){
                    throw new Exception("N??mero de operaci??n de transacci??n no v??lida.", 1);
                }
            } else {
                $this->numero_tarjeta = NULL;
                $this->numero_voucher = NULL;
            }   

            $pago_totalizado = (float) $this->pago_tarjeta + (float) $this->pago_efectivo + (float) $this->pago_deposito;
            $diferencia_entre_costo_y_pagado = round(($costo_total_atencion - $pago_totalizado), 4, PHP_ROUND_HALF_UP);

            if ($diferencia_entre_costo_y_pagado > 0){
                $this->pago_credito = $diferencia_entre_costo_y_pagado;
                $this->monto_vuelto = 0.00;
            } else {
                $this->monto_vuelto = abs($diferencia_entre_costo_y_pagado);
                $this->pago_credito = 0.00;
            }

            if ($this->id_convenio_empresa != NULL &&            
                ($this->pago_credito > 0.00 || $this->monto_vuelto > 0.00)
                ){
                throw new Exception("No se puede registrar un SALDO en una atencion con CONVENIO.", 1);
            }

            $objCreditoValido = $this->validarMontoCreditoValido();
            if ($objCreditoValido["r"] == false){
                throw new Exception("Esta atencion contiene un monto de credito SUPERIOR al ".($this->MAX_CREDITO * 100)."% del total de la venta. Maximo permitido: ".number_format($objCreditoValido["monto_maximo"],2)." soles");
            }
            
            $this->obtenerNumeroActoMedicoCorrelativo();

            $campos_valores = [
                "id_paciente"=>$this->id_paciente,
                "nombre_paciente"=>$objPaciente->nombres_completos,
                "numero_documento"=>$objPaciente->numero_documento,
                "numero_acto_medico"=>$this->numero_acto_medico,
                "id_medico_realizante"=>$this->id_medico_realizante,
                "id_promotora_realizante"=>$id_promotora_realizante,
                "id_medico_ordenante"=>$this->id_medico_ordenante,
                //"id_especialidad_medico_ordenante"=>$objEspecialidadComision["id_especialidad_medico"],
                //"comision_especialidad_medico_ordenante"=>$objEspecialidadComision["porcentaje_comision"],
                "id_promotora_ordenante"=>$objPromotoraComision["id_promotora"],
                "comision_promotora_ordenante"=>$objPromotoraComision["porcentaje_comision"],
                "fecha_atencion"=>$this->fecha_atencion,
                "hora_atencion"=>$this->hora_atencion,
                "observaciones"=>$this->observaciones,
                "id_caja_instancia"=>$this->id_caja_instancia,
                "pago_efectivo"=>$this->pago_efectivo,
                "pago_deposito"=>$this->pago_deposito,
                "id_banco"=>$this->id_banco,
                "numero_operacion"=>$this->numero_operacion,
                "pago_tarjeta"=>$this->pago_tarjeta,
                "numero_tarjeta"=>$this->numero_tarjeta,
                "numero_voucher"=>$this->numero_voucher,
                "pago_credito"=>$this->pago_credito,
                "monto_vuelto"=>$this->monto_vuelto,
                "importe_total"=>$costo_total_atencion,
                "ip_terminal"=>$_SERVER['REMOTE_ADDR'],
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "monto_descuento"=>$this->monto_descuento,
                "id_usuario_validador"=>$this->id_usuario_validador == "" ? NULL : $this->id_usuario_validador,
                "motivo_descuento"=>$this->motivo_descuento == "" ? NULL : $this->motivo_descuento,
                "es_gratuito_descuento"=>$this->es_gratuito_descuento,
                "id_usuario_validador_descuento_sin_efectivo"=>$this->id_usuario_validador_descuento_sin_efectivo == "" ? NULL : $this->id_usuario_validador_descuento_sin_efectivo,
                "id_empresa_convenio"=>$this->id_convenio_empresa,
                "porcentaje_convenio"=>$this->convenio_porcentaje
            ];

            $this->insert("atencion_medica",  $campos_valores);
            $this->id_atencion_medica = $this->getLastID();


            $campos_servicios = [
                "id_atencion_medica",
                "id_servicio",
                "nombre_servicio",
                "precio_unitario",
                "cantidad",
                "sub_total",
                "porcentaje_comision_categoria",
                "monto_comision_categoria",
                "monto_comision_categoria_sin_igv"
            ];

            $valores_servicios = [];
            for ($i=0; $i < $cantidad_servicios; $i++) { 
                $objServicio = $this->servicios[$i];
                $objServicio->cantidad = $objServicio->cantidad == NULL ? "1" : $objServicio->cantidad;
                $subtotal = $objServicio->precio_unitario * $objServicio->cantidad;
                $objCategoriaComision = $objComision->obtenerComisionCategoriaServicio($objServicio->id_servicio);
                $monto_comision_categoria = round($objCategoriaComision["porcentaje_comision"] * $subtotal,3);
                $monto_comision_categoria_sin_igv = round($monto_comision_categoria / (1+IGV),3);

                array_push($valores_servicios,
                    [
                        $this->id_atencion_medica,
                        $objServicio->id_servicio,
                        $objServicio->nombre_servicio,
                        $objServicio->precio_unitario,
                        $objServicio->cantidad,
                        $subtotal,
                        $objCategoriaComision["porcentaje_comision"],
                        $monto_comision_categoria,
                        $monto_comision_categoria_sin_igv
                    ]);
            }
            
            $this->insertMultiple("atencion_medica_servicio", $campos_servicios, $valores_servicios);

            if ($this->monto_descuento > 0.00 && 
                ($this->pago_tarjeta > 0.00 || $this->pago_deposito > 0.00 || $this->pago_credito > 0.00) &&
                ($this->id_convenio_empresa == NULL)
                ){

                $objValidacion = $this->verificarUsuarioValidarDescuentoSinEfectivo();

                if (!$objValidacion["r"]){
                    throw new Exception($objValidacion["msj"], 1);
                }

                $campos_valores = [
                    "id_usuario_validador"=>$this->id_usuario_validador_descuento_sin_efectivo,
                    "id_atencion_medica"=>$this->id_atencion_medica,
                    "clave_ingresada"=>$this->clave_descuento_sin_efectivo,
                ];
    
                $this->insert("atencion_medica_permiso_descuento",  $campos_valores);
            }

            $id_documento_electronico_registrado = "";

            $generar_comprobante = true;
            $MONTO_MAXIMO_GENERAR_COMPROBANTE = 5.00;
            if ($this->pago_credito > 0.00 || 
                    ($pago_totalizado < $MONTO_MAXIMO_GENERAR_COMPROBANTE && $this->id_convenio_empresa != NULL) ||
                    (($objPaciente->numero_documento == "" || $objPaciente->numero_documento == NULL || $objPaciente->numero_documento == "SD") && $this->id_convenio_empresa != NULL)
                    ){
                $generar_comprobante = false;
            }

            $r = [];
            if ($generar_comprobante){
                $objComprobante = $this->generarComprobante($objPaciente, $costo_total_atencion);
                $id_documento_electronico_registrado = $objComprobante["id_documento_electronico_registrado"];
                $r = $objComprobante["r"];
            }

            include 'CajaMovimiento.clase.php';
            $objCajaMovimiento = new CajaMovimiento();

            $objCajaMovimiento->id_caja_instancia = $this->id_caja_instancia;
            $objCajaMovimiento->id_cliente = $this->id_paciente;
            $objCajaMovimiento->id_registro_atencion = $this->id_atencion_medica;
            $objCajaMovimiento->id_tipo_movimiento = $this->ID_TIPO_MOVIMIENTO_ATENCION;
            $objCajaMovimiento->monto_efectivo = $this->pago_efectivo;
            $objCajaMovimiento->monto_deposito = $this->pago_deposito;
            $objCajaMovimiento->id_banco = $this->id_banco;
            $objCajaMovimiento->numero_operacion = $this->numero_operacion;
            $objCajaMovimiento->monto_tarjeta = $this->pago_tarjeta;
            $objCajaMovimiento->numero_tarjeta = $this->numero_tarjeta;
            $objCajaMovimiento->numero_voucher = $this->numero_voucher;
            $objCajaMovimiento->monto_credito = $this->pago_credito;
            $objCajaMovimiento->monto_descuento = $this->monto_descuento;
            $objCajaMovimiento->id_usuario_registrado = $this->id_usuario_registrado;
            $objCajaMovimiento->fecha_hora_registrado = $fecha_ahora;            
            
            $objCajaMovimiento->registrarCajaMovimiento();

            $this->update("atencion_medica", ["numero_acto_medico"=>$this->id_atencion_medica], ["id_atencion_medica"=>$this->id_atencion_medica]);

            $this->commit();

            return ["msj"=>"Registro realizado correctamente.",
                            "id_atencion_medica"=>$this->id_atencion_medica,
                            "id_documento_electronico"=>$id_documento_electronico_registrado,
                            "numero_acto_medico"=>$this->numero_acto_medico,
                        "r"=>$r];  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function obtenerNumeroActoMedicoCorrelativo(){
        try {

            $sql  = "SELECT COALESCE(MAX(numero_acto_medico) + 1, 1) FROM atencion_medica";
            $numero_acto_medico = $this->consultarValor($sql);

            if ($numero_acto_medico == NULL){
                throw new Exception($exc->getMessage(), 1); 
            }

            $this->numero_acto_medico = $numero_acto_medico;
            return ["rpt"=>true];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDatosParaImpresion($id_atencion_medica){
        try {

            $this->id_atencion_medica = $id_atencion_medica;

            $sql  = "SELECT 
                    am.numero_acto_medico,
                    DATE_FORMAT(fecha_atencion, '%d/%m/%Y') as fecha_atencion,
                    hora_atencion,
                    am.nombre_paciente as nombres_completos,
                    DATE_FORMAT(pa.fecha_nacimiento, '%d/%m/%Y') as fecha_nacimiento_formateada,
                    calcularEdad(pa.fecha_nacimiento) as edad,
                    am.numero_documento,
                    CONCAT(COALESCE(pa.telefono_fijo,''),COALESCE(CONCAT(' ', pa.celular_uno),''),COALESCE(CONCAT(' ', pa.celular_dos),'')) as telefonos,
                    mo.nombres_apellidos as medico_ordenante,
                    COALESCE(am.observaciones,'') as observaciones,
                    pago_credito as total_credito,
                    monto_vuelto as total_vuelto,
                    monto_descuento as descuento_global,
                    pago_deposito as total_deposito,
                    pago_efectivo as total_efectivo,
                    pago_tarjeta as total_tarjeta,
                    CONCAT(co.nombres,' ',co.apellido_paterno,' ',co.apellido_materno) as usuario_atendido,
                    COALESCE(eco.razon_social,'') as empresa_convenio
                    FROM atencion_medica am
                    INNER JOIN medico mo ON mo.id_medico = am.id_medico_ordenante
                    INNER JOIN paciente pa ON pa.id_paciente = am.id_paciente
                    INNER JOIN usuario u ON u.id_usuario = am.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    LEFT JOIN empresa_convenio eco ON eco.id_empresa_convenio = am.id_empresa_convenio
                    WHERE id_atencion_medica = :0 AND am.estado_mrcb";
            $datos = $this->consultarFila($sql, [$id_atencion_medica]);

            if ($datos == NULL){
                throw new Exception("No existe una atenci??n con el ID ingresado."); 
            }

            $sql = "SELECT  
                        ams.nombre_servicio, 
                        ams.precio_unitario,
                        ams.cantidad
                        FROM atencion_medica_servicio ams
                        WHERE ams.estado_mrcb AND ams.id_atencion_medica  = :0";
            $datos["servicios"] = $this->consultarFilas($sql, [$id_atencion_medica]);
            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function validarMontoCreditoValido(){
        try {

            if ($this->pago_credito > 0.00){
                $monto_maximo = ($this->total - $this->monto_descuento) * $this->MAX_CREDITO;
                if ($this->pago_credito <= $monto_maximo){
                    return ["r"=>true];
                } else {
                    return ["r"=>false, "monto_maximo"=>$monto_maximo];
                }
            }

            return ["r"=>true];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function verificarUsuarioValidarDescuentoSinEfectivo(){
        try {

            if ($this->id_usuario_validador_descuento_sin_efectivo == NULL || $this->id_usuario_validador_descuento_sin_efectivo == ""){
                return ["r"=>false, "msj"=>"No se ha obtenido un usuario autorizante correctamente."];
            }


            if ($this->clave_descuento_sin_efectivo == NULL || $this->clave_descuento_sin_efectivo == ""){
                return ["r"=>false, "msj"=>"No se ha obtenido una clave del usuario autorizante correctamente."];
            }

            $this->clave_descuento_sin_efectivo = md5($this->clave_descuento_sin_efectivo);

            $sql  = "SELECT clave FROM usuario WHERE estado_acceso ='A' AND id_usuario = :0";
            $r = $this->consultarFila($sql, [$this->id_usuario_validador_descuento_sin_efectivo]);

            if ($r == false){
                return ["r"=>false, "msj"=>"No se ha encontrado un usuario autorizante v??lido."];
            }

            if ($r["clave"] != $this->clave_descuento_sin_efectivo){
                return ["r"=>false, "msj"=>"Clave no v??lido en el usuario autorizante."];
            }

            return ["r"=>true];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    
    public function anularComprobanteAtencion($motivo_anulacion, $incluye_atencion = true){
        try {

            $this->beginTransaction();


            $sql = "SELECT fecha_atencion, ci.estado_caja
                    FROM atencion_medica am
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = am.id_caja_instancia AND ci.estado_mrcb
                    WHERE am.id_atencion_medica = :0 AND am.estado_mrcb";
            $obj = $this->consultarFila($sql, [$this->id_atencion_medica]);

            if ($obj == false){
                throw new Exception("ID Atenci??n m??dica no existe.", 1);
            }
            
            $estaCerrada = $obj["estado_caja"] == "C";

            $idUsuarioAnulado = $this->id_usuario_registrado;
            $fueAnuladoCajaCerrada = "0";

            if ($estaCerrada){
                $fueAnuladoCajaCerrada = "1";
            }

            $this->fecha_hora_hoy = date("Y-m-d H:i:s");

            if ($incluye_atencion == true){
                $this->update("atencion_medica", ["estado_mrcb"=>"0", "id_usuario_anulado"=>$idUsuarioAnulado, "fecha_hora_anulado"=>$this->fecha_hora_hoy, "motivo_anulado"=>$motivo_anulacion], 
                                ["id_atencion_medica"=>$this->id_atencion_medica]);
                $this->update("atencion_medica_servicio", ["estado_mrcb"=>"0"], ["id_atencion_medica"=>$this->id_atencion_medica]);

                $this->update("caja_instancia_movimiento", 
                            ["estado_mrcb"=>"0", 
                            "id_usuario_anulado"=>$idUsuarioAnulado,
                            "fecha_hora_anulado"=>$this->fecha_hora_hoy,
                            "fue_anulado_caja_cerrada"=>$fueAnuladoCajaCerrada], 
                            ["id_registro_atencion"=>$this->id_atencion_medica]);
            }

            $sql = "SELECT iddocumento_electronico, id_atencion_medica, fecha_emision 
                            FROM documento_electronico 
                            WHERE id_atencion_medica = :0 AND estado_mrcb AND estado_anulado = 0";
            $existeComprobanteAsociado = $this->consultarFila($sql, [$this->id_atencion_medica]);


            $nota_credito_comprobante  = "";
            if ($existeComprobanteAsociado != false){
                include_once 'DocumentoElectronico.clase.php';
                $objComprobante = new DocumentoElectronico($this->getDB());  
                $objComprobante->id_usuario_registrado = $idUsuarioAnulado;

                $objComprobante->anularComprobanteDesdeAtencionMedica($existeComprobanteAsociado, $motivo_anulacion, $idUsuarioAnulado);                
                $nota_credito_comprobante = ($objComprobante->serie."-".$objComprobante->numero_correlativo);
            }

            $this->commit();
            return ["msj"=>"Anulado correctamente.", "nota_credito"=>$nota_credito_comprobante, "obj"=>$objComprobante];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function anularAtencion($motivo_anulacion){
        try {

            $this->beginTransaction();

            $sql = "SELECT fecha_atencion, ci.estado_caja
                    FROM atencion_medica am
                    INNER JOIN caja_instancia_movimiento cim ON cim.id_caja_instancia = am.id_caja_instancia AND cim.estado_mrcb
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia AND ci.estado_mrcb
                    WHERE am.id_atencion_medica = :0 AND am.estado_mrcb";
            $obj = $this->consultarFila($sql, [$this->id_atencion_medica]);

            if ($obj == false){
                throw new Exception("ID Atenci??n m??dica no existe.", 1);
            }
            
            $estaCerrada = $obj["estado_caja"] == "C";

            $idUsuarioAnulado = $this->id_usuario_registrado;
            $fueAnuladoCajaCerrada = "0";

            if ($estaCerrada){
                $fueAnuladoCajaCerrada = "1";
            }

            $fechaHoraHoy = date("Y-m-d H:i:s");

            $this->update("atencion_medica", ["estado_mrcb"=>"0", "id_usuario_anulado"=>$idUsuarioAnulado, "fecha_hora_anulado"=>$fechaHoraHoy, "motivo_anulado"=>$motivo_anulacion], ["id_atencion_medica"=>$this->id_atencion_medica]);
            $this->update("atencion_medica_servicio", ["estado_mrcb"=>"0"], ["id_atencion_medica"=>$this->id_atencion_medica]);
            $this->update("caja_instancia_movimiento", 
                            ["estado_mrcb"=>"0", 
                            "id_usuario_anulado"=>$idUsuarioAnulado,
                            "fecha_hora_anulado"=>$fechaHoraHoy,
                            "fue_anulado_caja_cerrada"=>$fueAnuladoCajaCerrada], 
                            ["id_registro_atencion"=>$this->id_atencion_medica]);

            $sql = "SELECT iddocumento_electronico, id_atencion_medica, fecha_emision FROM documento_electronico WHERE id_atencion_medica = :0 AND estado_mrcb AND estado_anulado = 0";
            $existeComprobanteAsociado = $this->consultarFila($sql, [$this->id_atencion_medica]);

            $nota_credito_comprobante  = "";
            if ($existeComprobanteAsociado != false){
                include_once 'DocumentoElectronico.clase.php';
                $objComprobante = new DocumentoElectronico();  
                $objComprobante->id_usuario_registrado = $idUsuarioAnulado;

                $objComprobante->anularComprobanteDesdeAtencionMedica($existeComprobanteAsociado, $motivo_anulacion, $idUsuarioAnulado);                
                $nota_credito_comprobante = ($objComprobante->serie."-".$objComprobante->numero_correlativo);
            }

            $this->commit();
            return ["msj"=>"Anulado correctamente.", "nota_credito"=>$nota_credito_comprobante];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarComprobante($objPaciente, $costo_total_atencion, $id_documento_electronico_canje = NULL){
        try {

            $id_documento_electronico_registrado = "";
            if ($this->id_convenio_empresa != NULL){
                //marcar boleta cuando sea convenio con un monto comprobante
                $this->id_tipo_comprobante = "03";
                $sqlSerie = "SELECT c.serie_boleta 
                                        FROM caja c
                                        INNER JOIN caja_instancia ci ON ci.id_caja = c.id_caja AND ci.estado_mrcb
                                        WHERE ci.id_caja_instancia = :0 AND c.estado_mrcb";
                $this->serie = $this->consultarValor($sqlSerie, $this->id_caja_instancia);
            }

            switch($this->id_tipo_comprobante){
                case "01":
                case "03":
                    include_once 'DocumentoElectronico.clase.php';
                    $objComprobante = new DocumentoElectronico($this->getDB());

                    if ($this->id_tipo_comprobante == "01"){
                        $objComprobante->Cliente = [
                            "id_cliente"=>$this->factura_id_cliente,
                            "numero_documento"=>$this->factura_ruc,
                            "nombres_completos"=>$this->factura_razon_social,
                            "id_tipo_documento"=>"6",
                            "direccion"=>$this->factura_direccion,
                            "codigo_ubigeo_distrito"=>NULL
                        ];
                    } else {

                        if ($this->boleta_numero_documento == ""){
                            $objComprobante->Cliente = [
                                "id_cliente"=>$this->id_paciente,
                                "numero_documento"=>$objPaciente->numero_documento,
                                "nombres_completos"=>$objPaciente->nombres_completos,
                                "id_tipo_documento"=>$objPaciente->id_tipo_documento,
                                "direccion"=>$objPaciente->direccion,
                                "codigo_ubigeo_distrito"=>$objPaciente->codigo_ubigeo_distrito
                            ];
                        }  else {
                            $objComprobante->Cliente = [
                                "id_cliente"=>$this->boleta_id_cliente,
                                "numero_documento"=>$this->boleta_numero_documento,
                                "nombres_completos"=>$this->boleta_apellido_paterno." ".$this->boleta_apellido_materno." ".$this->boleta_nombres,
                                "id_tipo_documento"=>$this->boleta_tipo_documento,
                                "direccion"=>"",
                                "codigo_ubigeo_distrito"=>NULL
                            ];
                        }
                        
                    }
                    
                    $objComprobante->id_tipo_comprobante = $this->id_tipo_comprobante;
                    $objComprobante->id_usuario_registrado = $this->id_usuario_registrado;
                    $objComprobante->fecha_emision = $this->fecha_atencion;
                    $objComprobante->id_atencion_medica = $this->id_atencion_medica;
                    $objComprobante->es_convenio = $this->id_convenio_empresa != NULL;
                    $objComprobante->registrar_en_bbdd = true;
                    $objComprobante->generar_xml = true;
                    $objComprobante->firmar_comprobante = true;

                    if ($id_documento_electronico_canje != NULL){
                        $objComprobante->id_documento_electronico_previo = $id_documento_electronico_canje;

                        $this->anularComprobanteAtencion("CANJEO DE COMPROBANTE", false);
                        $r = $objComprobante->canjearComprobante();
                    } else {

                        $objComprobante->serie = $this->serie;
                        $sql = "SELECT 
                                    am.monto_descuento,
                                    am.importe_total
                                FROM 
                                atencion_medica am 
                                WHERE am.id_atencion_medica = :0 AND am.estado_mrcb";

                        $resComprobante = $this->consultarFila($sql, [$this->id_atencion_medica]);

                        if ($resComprobante == false){
                            throw new Exception("Atenci??n no encontrada.", 1);
                        }

                        $objComprobante->descuento_global = $resComprobante["monto_descuento"];
                        $objComprobante->importe_total = $resComprobante["importe_total"];

                        $sql = "SELECT 
                                    ams.nombre_servicio,
                                    ams.precio_unitario,
                                    ams.cantidad,
                                    ams.sub_total,
                                    ams.id_servicio,
                                    s.idtipo_afectacion,
                                    s.idunidad_medida
                                FROM 
                                atencion_medica_servicio ams
                                INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                                WHERE ams.id_atencion_medica = :0 AND ams.estado_mrcb";

                        $objComprobante->detalle = $this->consultarFilas($sql, [$this->id_atencion_medica]);
                        $objComprobante->detalle = json_decode(json_encode($objComprobante->detalle), FALSE);

                        $objComprobante->observaciones = "";

                        if ($objComprobante->id_tipo_comprobante == "01"){
                            $r = $objComprobante->generarFactura();
                        } else {
                            $r = $objComprobante->generarBoleta();        
                        }        
                    }

                    $id_documento_electronico_registrado = $objComprobante->id_documento_electronico;
                break;

                default:
                $r = [];
                break;
            }

            return ["r"=>$r, "id_documento_electronico_registrado"=>$id_documento_electronico_registrado];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerVentasExportacionContab($fi, $ff){
        try {

            $sql  = "SELECT 
                    '02' as codigo_servicio,
                    de.idtipo_comprobante,
                    de.serie,
                    LPAD(de.numero_correlativo,7,'0') as numero_correlativo,
                    DATE_FORMAT(de.fecha_emision,'%d/%m/%Y') as fecha_emision,
                    TRIM(de.descripcion_cliente) as cliente,
                    TRIM(de.numero_documento_cliente) as numero_documento_cliente,
                    TRIM(de.direccion_cliente) as direccion_cliente,
                    '0' as tipo_pago,
                    DATE_FORMAT(de.fecha_emision,'%d/%m/%Y') as fecha_exportacion,
                    COALESCE(IF (am.pago_deposito > 0.00, 'DP', (IF(am.pago_tarjeta > 0.00, 'TJ', 'EF'))),'') as metodo_pago,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF(de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_gravadas,'0.00') , -1 * de.total_gravadas)) as total_gravadas,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.total_igv,'0.00') , -1 * de.total_igv)) as total_igv,
                    IF (de.idtipo_comprobante IN ('07','08') AND de.estado_anulado = '1', '0.00', IF (de.idtipo_comprobante <> '07', IF(de.anulado_por_nota = 1,de.importe_total,'0.00') , -1 * de.importe_total)) as importe_total,
                    '0' as item_modifica, 
                    COALESCE(DATE_FORMAT(de_nota.fecha_emision,'%d/%m/%Y'),'00/00/0000') as fecha_modifica,
                    COALESCE(de_nota.idtipo_comprobante,'00') as td_modifica,
                    COALESCE(de_nota.serie,'0000') as serie_modifica,
                    COALESCE(LPAD(de_nota.numero_correlativo,7,'0'),'000000') as correlativo_modifica,
                    '0.00' as monto_uno,
                    '0.00' as monto_dos,
                    COALESCE(b.codigo_contab,'') as codigo_entidad,
                    COALESCE(am.numero_operacion,'') as numero_operacion_banco,
                    IF(am.numero_voucher IS NOT NULL,'02','') as tipo_tarjeta
                    FROM documento_electronico de
                    LEFT JOIN atencion_medica am ON de.id_atencion_medica = am.id_atencion_medica AND am.estado_mrcb
                    LEFT JOIN banco b ON b.id_banco = am.id_banco
                    LEFT JOIN documento_electronico de_nota ON de_nota.serie  = de.serie_documento_modifica AND de_nota.numero_correlativo = de.numero_documento_modifica
                    WHERE de.estado_mrcb AND  (de.fecha_emision BETWEEN :0 AND :1)";
            $datos = $this->consultarFilas($sql, [$fi, $ff]);

            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function verAtencion(){
        try {

            $sql  = "SELECT 
                    am.id_atencion_medica,
                    numero_acto_medico,
                    TRIM(de.descripcion_cliente) as factura_razon_social,
                    TRIM(de.numero_documento_cliente) as factura_ruc,
                    TRIM(de.direccion_cliente) as factura_direccion,
                    am.numero_documento as numero_documento_paciente,
                    am.nombre_paciente as paciente,
                    COALESCE(CONCAT(de.serie,'-',LPAD(de.numero_correlativo,7,'0')),'') comprobante,
                    am.fecha_atencion, fecha_atencion, de.idtipo_documento_cliente,
                    am.monto_descuento,
                    am.importe_total,
                    med_r.nombres_apellidos as medico_realizante,
                    am.id_medico_realizante,
                    med_o.nombres_apellidos as medico_ordenante,
                    am.id_medico_ordenante,
                    am.pago_efectivo,
                    am.pago_deposito,
                    b.descripcion as pago_deposito_banco,
                    am.numero_operacion as pago_deposito_numerooperacion,
                    am.pago_tarjeta,
                    am.numero_voucher as numero_voucher_tarjeta,
                    COALESCE(am.numero_tarjeta,'') as numero_tarjeta,
                    am.pago_credito as monto_saldo,
                    am.observaciones,
                    am.motivo_descuento,
                    CONCAT(c.nombres,' ',apellido_paterno,' ',apellido_materno) as usuario_descuento
                    FROM atencion_medica am
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = am.id_atencion_medica AND de.estado_mrcb AND de.estado_anulado = 0
                    LEFT JOIN banco b ON b.id_banco = am.id_banco
                    LEFT JOIN medico med_r ON med_r.id_medico = am.id_medico_realizante
                    LEFT JOIN medico med_o ON med_o.id_medico = am.id_medico_ordenante
                    LEFT JOIN usuario u ON u.id_usuario = am.id_usuario_validador
                    LEFT JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                    WHERE am.estado_mrcb AND am.id_atencion_medica = :0";
            $datos = $this->consultarFila($sql, [$this->id_atencion_medica]);

            if ($datos == false){
                throw new Exception("Atenci??n no encontrada.", 1);
            }

            $sql = "SELECT nombre_servicio, 
                            s.descripcion_detallada as descripcion, 
                            sub_total as subtotal 
                            FROM atencion_medica_servicio ams 
                            LEFT JOIN servicio s ON s.id_servicio = ams.id_servicio
                            WHERE ams.id_atencion_medica = :0 AND ams.estado_mrcb";
            $datos["servicios"] = $this->consultarFilas($sql, [$this->id_atencion_medica]);

            return  $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cambiarMedicoAtencion(){
        try {
            $this->beginTransaction();

            require "Comision.clase.php";
            $objComision = new Comision();
            $objPromotoraComision = $objComision->obtenerComisionPromotoraXMedico($this->id_medico_ordenante);

            $sql = "SELECT id_promotora FROM medico WHERE id_medico = :0 AND estado_mrcb";
            $id_promotora_realizante = $this->consultarValor($sql, [$this->id_medico_realizante]);

            $campos_valores = [
                "id_medico_realizante"=>$this->id_medico_realizante,
                "id_promotora_realizante"=>$id_promotora_realizante,
                "id_medico_ordenante"=>$this->id_medico_ordenante,
                "id_promotora_ordenante"=>$objPromotoraComision["id_promotora"],
                "comision_promotora_ordenante"=>$objPromotoraComision["porcentaje_comision"]
            ];

            $campos_valores_where = [
                "id_atencion_medica"=>$this->id_atencion_medica
            ];

            $this->update("atencion_medica", $campos_valores, $campos_valores_where);

            $this->commit();
            return ["msj"=>"M??dicos cambiados."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    public function listarAtencionesGeneral($fecha_inicio, $fecha_fin){
        try {
            $sql  = "SELECT 
                    am.id_atencion_medica,
                    COALESCE(am.numero_acto_medico,'-') as numero_acto_medico,
                    DATE_FORMAT(COALESCE(de.fecha_emision, am.fecha_atencion), '%d/%m/%Y') as fecha_registro,
                    DATE_FORMAT(COALESCE(de.fecha_emision, am.fecha_atencion), '%d/%m/%Y') as fecha,
                    de.iddocumento_electronico as iddocumento_electronico,
                    COALESCE(CONCAT(de.serie,'-',LPAD(de.numero_correlativo,7,'0')),'S/C') comprobante,
                    nombre_paciente as paciente,
                    COALESCE(p.razon_social, CONCAT(p.nombres,' ',p.apellidos_paterno,' ',p.apellidos_materno),'') as cliente,
                    am.pago_efectivo as monto_efectivo,
                    am.pago_deposito as monto_deposito,
                    am.pago_tarjeta as monto_tarjeta,
                    am.pago_credito as monto_credito,
                    (am.pago_efectivo + am.pago_deposito + am.pago_tarjeta + am.pago_credito) as monto_total,
                    COALESCE(de.estado_anulado, NOT am.estado_mrcb) as estado_anulado,
                    de_nota.iddocumento_electronico as iddocumento_electronico_nota,
                    CONCAT(de_nota.serie,'-',LPAD(de_nota.numero_correlativo,7,'0')) as  comprobante_nota,
                    COALESCE(de_nota.descripcion_motivo_nota, am.motivo_anulado) as motivo_nota
                    FROM atencion_medica am
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = am.id_atencion_medica AND de.estado_mrcb
                    LEFT JOIN paciente p ON p.id_paciente = am.id_paciente
                    LEFT JOIN documento_electronico de_nota ON de_nota.serie_documento_modifica = de.serie 
                                        AND de_nota.numero_documento_modifica = de.numero_correlativo 
                                        AND de_nota.idtipo_comprobante_modifica = de.idtipo_comprobante
                                        AND de_nota.estado_mrcb AND de_nota.estado_anulado = 0
                    WHERE (COALESCE(de.fecha_emision, am.fecha_atencion) BETWEEN :0 AND :1)
                    ORDER BY am.numero_acto_medico";
            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerAtencionMedicaParaSaldos(){
        try {

            $sql  = "SELECT 
                    am.id_atencion_medica,
                    am.nombre_paciente,
                    am.importe_total,
                    am.pago_credito as monto_credito,
                    am.fecha_atencion,
                    (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) 
                                    AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica) as monto_pagado  
                    FROM atencion_medica am
                    WHERE am.estado_mrcb AND am.numero_acto_medico = :0";
            $data = $this->consultarFila($sql, [$this->numero_acto_medico]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerAtencionMedicaParaEgreso(){
        try {

            $sql  = "SELECT 
                    am.id_atencion_medica,
                    am.numero_acto_medico as numero_recibo,
                    am.id_paciente,
                    am.nombre_paciente,
                    am.importe_total,
                    am.fecha_atencion,
                    am.estado_mrcb as estado_valido,
                    am.monto_vuelto,
                    (SELECT COALESCE(SUM(cim.monto_efectivo),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (7) 
                                    AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica) as monto_vueltos_entregados,  
                    (SELECT COALESCE(SUM(cim.monto_efectivo),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (8) 
                                    AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica) as monto_devuelto
                    FROM atencion_medica am
                    WHERE am.numero_acto_medico = :0";
            $data = $this->consultarFila($sql, [$this->numero_acto_medico]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function mostrarPagosDeAtencion(){
        try {

            $sql  = "SELECT 
                    cim.monto_efectivo, cim.monto_tarjeta, cim.monto_deposito,
                    COALESCE(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito,'0.00') as monto_total,
                    DATE_FORMAT(cim.fecha_hora_registrado, '%d-%m-%Y %H:%i:%s') as fecha_hora_registrado,
                    cj.codigo as caja,
                    CONCAT(c.nombres,' ',c.apellido_paterno) as usuario_caja
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja cj ON cj.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                    WHERE cim.id_tipo_movimiento IN (4) AND cim.id_registro_atencion_relacionada = :0 AND cim.estado_mrcb";
            $data = $this->consultarFilas($sql, [$this->id_atencion_medica]);
            
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarAtencionesLaboratorio($fecha_inicio, $fecha_fin, $tipo_filtro){
        try {
            $sqlFiltro = " true ";
            $sqlFiltroImpresos = " ";
            if ($tipo_filtro == "M"){
                $sqlFiltro = " am.fecha_hora_muestra IS NULL ";
            } else if($tipo_filtro == "R"){
                $sqlFiltro = " (am.fecha_hora_resultado IS NULL AND am.fecha_hora_muestra IS NOT NULL) ";
            } else if ($tipo_filtro == "V"){
                $sqlFiltro = " (am.fecha_hora_validado IS NULL AND am.fecha_hora_resultado IS NOT NULL AND am.fecha_hora_muestra IS NOT NULL) ";
            } else if ($tipo_filtro == "C"){
                $sqlFiltro = " (am.fecha_hora_validado IS NOT NULL AND am.fecha_hora_resultado IS NOT NULL AND am.fecha_hora_muestra IS NOT NULL) ";
            } else if ($tipo_filtro == "I"){
                $sqlFiltro = " (am.fecha_hora_validado IS NOT NULL AND am.fecha_hora_resultado IS NOT NULL AND am.fecha_hora_muestra IS NOT NULL) ";
                $sqlFiltroImpresos = " AND servicios_lab_no_impresos > 0";
            }

            $sql  = "SELECT 
                    am.id_atencion_medica,
                    COALESCE(am.numero_acto_medico,'-') as numero_recibo,
                    DATE_FORMAT(am.fecha_atencion, '%d/%m/%Y') as fecha_registro,
                    nombre_paciente as paciente,
                    COALESCE(DATE_FORMAT(am.fecha_hora_muestra,'%d/%m/%Y %H:%i:%s'),'-') as fecha_hora_muestra,
                    COALESCE(DATE_FORMAT(am.fecha_hora_resultado,'%d/%m/%Y %H:%i:%s'),'-') as fecha_hora_resultado,
                    COALESCE(DATE_FORMAT(am.fecha_hora_validado,'%d/%m/%Y %H:%i:%s'),'-') as fecha_hora_validado,
                    (SELECT COUNT(*) 
                        FROM atencion_medica_servicio ams
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = s.id_categoria_servicio
                        WHERE ams.id_atencion_medica = am.id_atencion_medica AND cs.id_grupo_servicio IN (2)) as servicios_lab,
                    (SELECT COUNT(*) 
                        FROM atencion_medica_servicio ams
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = s.id_categoria_servicio
                        WHERE ams.id_atencion_medica = am.id_atencion_medica AND cs.id_grupo_servicio IN (2) AND ams.numero_impresiones_laboratorio <= 0) as servicios_lab_no_impresos,
                    p.sexo,
                    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad
                    FROM atencion_medica am
                    LEFT JOIN paciente p ON p.id_paciente = am.id_paciente
                    WHERE (am.fecha_atencion BETWEEN :0 AND :1) AND am.estado_mrcb AND $sqlFiltro
                    HAVING servicios_lab > 0 ".$sqlFiltroImpresos."
                    ORDER BY am.numero_acto_medico";
            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarAtencionDetalleLaboratorio(){
        try {

            $sql = "SELECT nombre_paciente as paciente,
                    numero_acto_medico as numero_recibo,
                    IF( fecha_hora_muestra IS NULL, '0' ,'1') as ya_fue_registrado_muestra,
                    IF( fecha_hora_resultado IS NULL, '0' ,'1') as ya_fue_registrado_resultado,
                    IF( fecha_hora_validado IS NULL, '0' ,'1') as ya_fue_registrado_validado
                    FROM atencion_medica 
                    WHERE id_atencion_medica = :0 AND estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_atencion_medica]);

            if ($datos == false){
                throw new Exception("Atenci??n no encontrada.", 1);
            }

            $sql  = "SELECT 
                    ams.id_atencion_medica_servicio,
                    ams.id_servicio,
                    ams.nombre_servicio,
                    ams.numero_impresiones_laboratorio,
                    ams.fue_muestreado,
                    COALESCE(DATE_FORMAT(ams.fecha_hora_muestra, '%d-%m-%Y %H:%i')) as fecha_hora_muestra,
                    COALESCE(CONCAT(c_mue.nombres,' ',c_mue.apellido_paterno,' ',c_mue.apellido_materno),'-') as usuario_muestra,
                    COALESCE(DATE_FORMAT(ams.fecha_hora_entrega, '%d-%m-%Y %H:%i')) as fecha_hora_entrega,
                    COALESCE(CONCAT(c_ent.nombres,' ',c_ent.apellido_paterno,' ',c_ent.apellido_materno),'-') as usuario_entrega,
                    COALESCE(DATE_FORMAT(ams.fecha_hora_resultado, '%d-%m-%Y %H:%i'),'-') as fecha_hora_resultado,
                    COALESCE(CONCAT(c_res.nombres,' ',c_res.apellido_paterno,' ',c_res.apellido_materno),'-') as usuario_resultado,
                    COALESCE(DATE_FORMAT(ams.fecha_hora_validado, '%d-%m-%Y %H:%i'),'-') as fecha_hora_validado,
                    COALESCE(CONCAT(c_val.nombres,' ',c_val.apellido_paterno,' ',c_val.apellido_materno),'-') as usuario_validado,
                    DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%dT%H:%i') as fecha_hora_hoy_muestra,
                    DATE_FORMAT(CURRENT_TIMESTAMP, '%Y-%m-%dT18:00') as fecha_hora_hoy_resultado,
                    IF (ams.fecha_hora_validado IS NULL, '0', '1') as fue_validado
                    FROM atencion_medica_servicio ams
                    LEFT JOIN usuario u_mue ON u_mue.id_usuario = ams.id_usuario_muestra
                    LEFT JOIN colaborador c_mue ON c_mue.id_colaborador = u_mue.id_colaborador
                    LEFT JOIN usuario u_ent ON u_ent.id_usuario = ams.id_usuario_entrega
                    LEFT JOIN colaborador c_ent ON c_ent.id_colaborador = u_ent.id_colaborador
                    LEFT JOIN usuario u_res ON u_res.id_usuario = ams.id_usuario_resultado
                    LEFT JOIN colaborador c_res ON c_res.id_colaborador = u_res.id_colaborador
                    LEFT JOIN usuario u_val ON u_val.id_usuario = ams.id_usuario_validado
                    LEFT JOIN colaborador c_val ON c_val.id_colaborador = u_val.id_colaborador
                    WHERE ams.estado_mrcb AND ams.id_atencion_medica = :0";
            $datos["detalle"] = $this->consultarFilas($sql, [$this->id_atencion_medica]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerResultadoExamenesLaboratorioAtencionMedica($arregloAtencionesServicios = []){
        try {
            if (count($arregloAtencionesServicios) <= 0){
                $sqlAtencionesServicios = " true ";
            } else {
                $sqlAtencionesServicios = "ams.id_atencion_medica_servicio IN (".implode(",", $arregloAtencionesServicios).")";
            }

            $sql  = "SELECT
                        numero_acto_medico  as numero_recibo,
                        DATE_FORMAT(am.fecha_hora_registrado ,'%d/%m/%Y') as fecha_orden,
                        am.nombre_paciente as nombre_paciente,
                        TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad_anios,
                        TIMESTAMPDIFF(MONTH, p.fecha_nacimiento, CURDATE()) as edad_meses,
                        p.sexo as sexo,
                        m.nombres_apellidos as nombre_medico,
                        'CHICLAYO' as procedencia,
                        COALESCE(DATE_FORMAT(MAX(fecha_hora_muestra),'%d/%m/%Y'),'') as fecha_entrega
                        FROM atencion_medica am
                        INNER JOIN paciente p ON p.id_paciente = am.id_paciente
                        INNER JOIN medico m ON m.id_medico = am.id_medico_ordenante
                        WHERE am.id_atencion_medica = :0";
            $datos = $this->consultarFila($sql, [$this->id_atencion_medica]);

            /*obtener los detalles validados por seccion*/
            $sql = "SELECT distinct le.id_lab_seccion as id_lab_seccion, ls.descripcion, 
                        DATE_FORMAT(MAX(fecha_hora_entrega),'%d/%m/%Y') as fecha_entrega
                    FROM atencion_medica_servicio ams
                    INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                    INNER JOIN lab_examen le ON le.id_servicio = s.id_servicio
                    INNER JOIN lab_seccion ls ON ls.id_lab_seccion = le.id_lab_seccion
                    WHERE ams.id_atencion_medica = :0 AND ams.fecha_hora_validado IS NOT NULL AND s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.") AND $sqlAtencionesServicios
                    GROUP BY le.id_lab_seccion";
            $secciones = $this->consultarFilas($sql, [$this->id_atencion_medica]);

            foreach ($secciones as $key => $seccion) {
                $sql = "SELECT distinct id_lab_muestra as id_lab_muestra
                        FROM atencion_medica_servicio ams
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        INNER JOIN lab_examen le ON le.id_servicio = s.id_servicio 
                        WHERE le.id_lab_seccion = :0 AND ams.id_atencion_medica = :1 AND s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.") AND $sqlAtencionesServicios";
                $muestras = $this->consultarFilas($sql, [$seccion["id_lab_seccion"], $this->id_atencion_medica ]);
                
                foreach ($muestras as $key2 => $muestra) {
                    $sql = "SELECT id_atencion_medica_servicio
                            FROM atencion_medica_servicio ams
                            INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                            INNER JOIN lab_examen le ON le.id_servicio = s.id_servicio 
                            WHERE le.id_lab_seccion = :0 AND id_lab_muestra = :1 AND ams.id_atencion_medica = :2 AND s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.")  AND $sqlAtencionesServicios";
                    $servicios = $this->consultarFilas($sql, [$seccion["id_lab_seccion"], $muestra["id_lab_muestra"], $this->id_atencion_medica ]);

                    foreach ($servicios as $key3 => $servicio) {
                        $sql  = "SELECT 
                                nivel,
                                descripcion,
                                resultado,
                                unidad,
                                valores_referencia as valor_referencial,
                                metodo
                                FROM atencion_medica_servicio_laboratorio_resultados
                                WHERE id_atencion_medica_servicio = :0 AND estado_mrcb
                                ORDER BY orden_ubicacion";

                        $servicios[$key3]["resultados"] = $this->consultarFilas($sql, [$servicio["id_atencion_medica_servicio"]]);
                    }

                    $muestras[$key2]["servicios"] = $servicios;
                }

                $secciones[$key]["muestras"] = $muestras;
            }

            $datos["secciones"] = $secciones;
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarRecepcionLaboratorioResultados($fecha_inicio, $fecha_fin){
        try {        

            $sql  = "SELECT
                        am.id_atencion_medica,
                        numero_acto_medico  as numero_recibo,
                        am.nombre_paciente as nombre_paciente,
                        TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) as edad_anios,
                        TIMESTAMPDIFF(MONTH, p.fecha_nacimiento, CURDATE()) as edad_meses,
                        DATE_FORMAT(fecha_atencion, '%d/%m/%Y') as fecha_atencion,
                        IF (p.sexo = 'F', 'FEMENINO', 'MASCULINO') as sexo,
                        (SELECT COUNT(ams_1.id_atencion_medica_servicio) 
                            FROM atencion_medica_servicio ams_1 
                            INNER JOIN servicio s ON s.id_servicio = ams_1.id_servicio
                            WHERE ams_1.id_atencion_medica = am.id_atencion_medica AND ams_1.estado_mrcb AND s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.")) as cantidad_total,
                        (SELECT COUNT(ams_2.id_atencion_medica_servicio) 
                            FROM atencion_medica_servicio ams_2 
                            INNER JOIN servicio s ON s.id_servicio = ams_2.id_servicio
                            WHERE ams_2.id_atencion_medica = am.id_atencion_medica AND ams_2.estado_mrcb AND 
                            s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.")
                            AND ams_2.fecha_hora_validado IS NOT NULL) as cantidad_validados
                        FROM atencion_medica am
                        INNER JOIN paciente p ON p.id_paciente = am.id_paciente
                        WHERE am.estado_mrcb AND  (am.fecha_atencion BETWEEN :0 AND :1)
                        HAVING cantidad_validados > 0";
            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarRecepcionLaboratorioResultadosDetalle(){
        try {

            $sql  = "SELECT
                        ams.id_atencion_medica_servicio,
                        ams.nombre_servicio,
                        DATE_FORMAT(fecha_hora_muestra, '%d/%m/%Y %H:%i:%S') as fecha_hora_muestra,
                        DATE_FORMAT(fecha_hora_entrega, '%d/%m/%Y %H:%i:%S') as fecha_hora_entrega,
                        IF (fecha_hora_validado IS NULL, '0', '1') as esta_validado,
                        numero_impresiones_laboratorio
                        FROM atencion_medica_servicio ams
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        WHERE ams.estado_mrcb  AND ams.id_atencion_medica = :0 
                                AND s.id_categoria_servicio IN (".$this->ID_CATEGORIA_LABORATORIO.")";
            $datos = $this->consultarFilas($sql, [$this->id_atencion_medica]);
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDatosAtencionComprobante(){
        try {

            $sql  = "SELECT de.iddocumento_electronico,
                            COALESCE(CONCAT(de.serie,'-',de.numero_correlativo),'SIN COMPROBANTE') as comprobante
                        FROM  atencion_medica am
                        LEFT JOIN documento_electronico de ON de.id_atencion_medica = am.id_atencion_medica  AND de.estado_anulado = 0
                        WHERE am.id_atencion_medica = :0 AND am.estado_mrcb";

            $datos = $this->consultarFila($sql, [$this->id_atencion_medica]);

            if ($datos != false){
                $sql = "SELECT nombre_servicio, 
                                '' as descripcion,
                                sub_total  as subtotal
                        FROM atencion_medica_servicio
                        WHERE id_atencion_medica = :0";
                $datos["servicios"] = $this->consultarFilas($sql, [$this->id_atencion_medica]);
            }

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function canjearComprobante(){
        try {
            $sql = "SELECT iddocumento_electronico
                    FROM documento_electronico 
                    WHERE id_atencion_medica = :0 AND estado_mrcb  AND estado_anulado = 0";
            $id_documento_electronico_canje = $this->consultarFila($sql, $this->id_atencion_medica);

            if ($id_documento_electronico_canje == false){
                $id_documento_electronico_canje = NULL;
            } else {
                $id_documento_electronico_canje = $id_documento_electronico_canje["iddocumento_electronico"];
            }

            $this->beginTransaction();

            $fecha_ahora = date("Y-m-d H:i:s");

            require "Paciente.clase.php";
            $objPaciente = new Paciente();

            if ($this->id_tipo_comprobante == "01" && $this->factura_ruc != ""){
                $objClienteCreado = $objPaciente->registrarClienteXRUC($this->factura_ruc, $this->factura_razon_social, $this->factura_direccion);
                $this->factura_id_cliente = $objClienteCreado["cliente"]["id"];
            }

            if ($this->id_tipo_comprobante == "03" && $this->boleta_numero_documento != ""){
                $objClienteCreado = $objPaciente->registrarClienteXOTRO($this->boleta_tipo_documento, $this->boleta_numero_documento, $this->boleta_nombres, $this->boleta_apellido_paterno, $this->boleta_apellido_materno, $this->boleta_sexo, $this->boleta_fecha_nacimiento);
                $this->boleta_id_cliente = $objClienteCreado["cliente"]["id"];
            }

            $costo_total_atencion = 0.00;

            if ($id_documento_electronico_canje == NULL){
                 $sql = "SELECT serie_boleta, serie_factura 
                                FROM caja
                                WHERE id_caja IN (SELECT id_caja 
                                                    FROM caja_instancia 
                                                    WHERE id_caja_instancia IN (SELECT id_caja_instancia FROM atencion_medica WHERE id_atencion_medica = :0))
                                    AND estado_mrcb";
                $objSerie = $this->consultarFila($sql, [$this->id_atencion_medica]);
                $this->serie = $objSerie[$this->id_tipo_comprobante == "01" ? "serie_factura" : "serie_boleta"];

            }
            
            $resComprobante = $this->generarComprobante($objPaciente, $costo_total_atencion, $id_documento_electronico_canje);
            $id_documento_electronico_registrado = $resComprobante["id_documento_electronico_registrado"];

            $this->commit();

            return ["msj"=>"Registro realizado correctamente.",
                        "id_atencion_medica"=>$this->id_atencion_medica,
                        "id_documento_electronico"=>$id_documento_electronico_registrado];  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarAtencionesConSaldo($fecha_inicio, $fecha_fin, $filtro_saldo = "*"){
        try {

            $sqlFiltro = " true ";

            if ($filtro_saldo != "*"){
                if ($filtro_saldo == "P"){
                    $sqlFiltro = " monto_saldo > 0.00";
                } else {
                    $sqlFiltro = " monto_saldo <= 0.00";
                }
            }

            $sql  = "SELECT  
                        id_atencion_medica,
                        numero_recibo,
                        paciente,
                        fecha_registro,
                        tipo_comprobante,
                        comprobante,
                        monto_saldo,
                        monto_acuenta,
                        veces_amortizacion,
                        monto_total,
                        CONCAT(caja_monto_acuenta,' | ', usuario_caja) as caja_monto_acuenta,
                        iddocumento_electronico
                    FROM
                    (SELECT 
                    am.id_atencion_medica,
                    COALESCE(am.numero_acto_medico,'-') as numero_recibo,
                    DATE_FORMAT(COALESCE(am.fecha_atencion), '%d/%m/%Y') as fecha_registro,
                    de.iddocumento_electronico as iddocumento_electronico,
                    COALESCE((CASE de.idtipo_comprobante WHEN '01' THEN 'FACTURA' WHEN '03' THEN 'BOLETA DE VENTA' ELSE '' END),'') as tipo_comprobante,
                    COALESCE(CONCAT(de.serie,'-',LPAD(de.numero_correlativo,7,'0')),'-') as comprobante,
                    nombre_paciente as paciente,
                    (am.pago_credito - (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica)) as monto_saldo,
                    (SELECT COUNT(*) FROM  caja_instancia_movimiento cim
                        WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb 
                        AND cim.id_registro_atencion_relacionada = am.id_atencion_medica) as veces_amortizacion,
                    (am.pago_efectivo + am.pago_deposito + am.pago_tarjeta) as monto_acuenta, 
                    am.importe_total as monto_total,
                    cj.codigo as caja_monto_acuenta,
                    CONCAT(c.nombres,' ',c.apellido_paterno) as usuario_caja
                    FROM atencion_medica am
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = am.id_atencion_medica AND de.estado_mrcb AND de.estado_anulado = 0
                    LEFT JOIN paciente p ON p.id_paciente = am.id_paciente
                    LEFT JOIN caja_instancia_movimiento cm ON am.id_atencion_medica = cm.id_registro_atencion
                    LEFT JOIN caja_instancia ci ON ci.id_caja_instancia = cm.id_caja_instancia
                    LEFT JOIN caja cj ON cj.id_caja = ci.id_caja
                    LEFT JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    LEFT JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                    WHERE am.fecha_atencion BETWEEN :0 AND :1 AND am.estado_mrcb AND am.pago_credito > 0.00
                    ORDER BY am.numero_acto_medico
                    ) t1
                    WHERE $sqlFiltro";

            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarAtencionesConvenio($fecha_inicio, $fecha_fin){
        try {

            $sql  = "SELECT  
                        em.razon_social,
                        am.nombre_paciente,
                        am.porcentaje_convenio,
                        am.numero_acto_medico  as numero_atencion,
                        am.importe_total 
                        FROM atencion_medica am
                        INNER JOIN empresa_convenio ec ON ec.id_empresa_convenio = am.id_empresa_convenio
                        WHERE am.id_empresa_convenio IS NOT NULL AND 
                                am.estado_mrcb AND  (am.fecha_atencion BETWEEN :0 AND :1)
                        ORDER BY am.fecha_atencion DESC";

            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}