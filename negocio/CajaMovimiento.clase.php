<?php

require_once '../datos/Conexion.clase.php';

class CajaMovimiento extends Conexion {
    public $id_caja_instancia;
    public $id_caja_instancia_movimiento;
    public $id_cliente;
    public $id_paciente;
    public $id_registro_atencion;
    public $id_registro_atencion_relacionada;
    public $id_tipo_movimiento;
    public $monto_efectivo;
    public $monto_deposito;
    public $id_banco;
    public $numero_operacion;
    public $fecha_deposito;
    public $monto_tarjeta;
    public $numero_tarjeta;
    public $numero_voucher;
    public $fecha_transaccion;
    public $monto_credito;
    public $monto_descuento;
    public $correlativo_atencion;
    public $correlativo_egreso;
    public $correlativo_ingreso;

    public $id_atencion_medica;
    public $serie;
    public $fecha_atencion;
    public $servicios;
    public $observaciones;

    public $id_usuario_registrado;

    public $id_tipo_comprobante;
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

    public $monto_pago_realizado = 0.00;
    public $tipo_movimiento;
    public $descripcion_movimiento;
    public $fecha_hora_registrado;

    public $tipo_flujo_movimiento;

    public $ip_terminal;

    //Ingresos
    private $ID_ATENCION = "1";
    private $ID_SALDO = "4";
    private $ID_OTROS_INGRESOS = "10";

    //Egresos
    private $ID_GASTOS = "2";
    private $ID_VUELTOS_PACIENTE = "7";
    private $ID_DEVOLUCIONES_PACIENTE = "8";
    private $ID_OTROS_EGRESOS = "11";

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function registrarCajaMovimiento(){
        try {

            $this->beginTransaction();

            $this->monto_pago_realizado = $this->monto_efectivo + $this->monto_deposito + $this->monto_tarjeta;

            if ( $this->monto_pago_realizado < 0){
                throw new Exception("El monto pagado no es valido. Debe ser un monto mayor a 0", 1);
            }

            $sql = "SELECT  ci.estado_caja, 
                            c.serie_atencion,
                            ( SELECT numero FROM serie_documento WHERE serie = c.serie_atencion) as correlativo_atencion,
                            c.serie_ingresos,
                            ( SELECT numero FROM serie_documento WHERE serie = c.serie_ingresos) as correlativo_ingresos,
                            c.serie_egresos,
                            ( SELECT numero FROM serie_documento WHERE serie = c.serie_egresos) as correlativo_egresos
                            FROM caja_instancia ci
                            INNER JOIN caja c ON c.id_caja = ci.id_caja
                            WHERE ci.id_caja_instancia = :0 AND ci.estado_mrcb";
            $obj = $this->consultarFila($sql, [$this->id_caja_instancia]);

            if ($obj == false){
                throw new Exception("ID Caja no existe.", 1);
            }
            
            $estaCerrada = $obj["estado_caja"] == "C";

            if ($estaCerrada){
                throw new Exception("No puedo registrar movimientos en una caja CERRADA.", 1);
            }

            if ($this->id_tipo_movimiento == 1){
                $this->correlativo_atencion = $obj["correlativo_atencion"];
            } else {
                if ($this->tipo_flujo_movimiento === "E"){
                    $this->correlativo_egreso = $obj["correlativo_egresos"];
                } else {
                    $this->correlativo_ingreso = $obj["correlativo_ingresos"];
                }
            }

            $campos_valores = [
                "id_caja_instancia"=>$this->id_caja_instancia,
                "id_cliente"=>$this->id_cliente == "" ? NULL : $this->id_cliente,
                "id_registro_atencion"=>$this->id_registro_atencion == "" ? NULL : $this->id_registro_atencion,
                "id_registro_atencion_relacionada"=>$this->id_registro_atencion_relacionada == "" ? NULL : $this->id_registro_atencion_relacionada,
                "id_tipo_movimiento"=>$this->id_tipo_movimiento,
                "monto_efectivo"=>$this->monto_efectivo,
                "monto_deposito"=>$this->monto_deposito,
                "id_banco"=>$this->id_banco,
                "numero_operacion"=>$this->numero_operacion,
                "fecha_deposito"=>$this->fecha_deposito,
                "monto_tarjeta"=>$this->monto_tarjeta,
                "numero_tarjeta"=>$this->numero_tarjeta,
                "numero_voucher"=>$this->numero_voucher,
                "fecha_transaccion"=>$this->fecha_transaccion,
                "monto_credito"=>$this->monto_credito,
                "monto_descuento"=>$this->monto_descuento,
                "correlativo_atencion"=>$this->correlativo_atencion,
                "correlativo_ingreso"=>$this->correlativo_ingreso,
                "correlativo_egreso"=>$this->correlativo_egreso,
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$this->fecha_hora_registrado,
                "descripcion_movimiento"=>$this->descripcion_movimiento == "" ? NULL : $this->descripcion_movimiento,
                "ip_terminal"=> $_SERVER['REMOTE_ADDR']
            ];

            $this->insert("caja_instancia_movimiento",  $campos_valores);
            $id_caja_instancia_movimiento = $this->getLastID();

            if ($this->correlativo_atencion){
                $this->update("serie_documento", ["numero"=>$this->correlativo_atencion + 1], ["serie"=>$obj["serie_atencion"], "idtipo_comprobante"=>"CA"]);
            }
            if ($this->correlativo_ingreso){
                $this->update("serie_documento", ["numero"=>$this->correlativo_ingreso + 1], ["serie"=>$obj["serie_ingresos"], "idtipo_comprobante"=>"IN"]);
            }
            if ($this->correlativo_egreso){
                $this->update("serie_documento", ["numero"=>$this->correlativo_egreso + 1], ["serie"=>$obj["serie_egresos"], "idtipo_comprobante"=>"EG"]);
            }
            
            $this->commit();

            return ["msj"=>"Registro realizado", "id_caja_instancia_movimiento"=>$id_caja_instancia_movimiento];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarIngreso(){
        try {

            $this->beginTransaction();
            
            $this->fecha_hora_registrado = date("Y-m-d H:i:s");
            $r = ["msj"=>"Ingreso realizado correctamente."];

            $sql = "SELECT COUNT(id_tipo_movimiento) 
                    FROM tipo_movimiento 
                    WHERE tipo = 'I' AND id_tipo_movimiento = :0 AND id_tipo_movimiento <> 1 AND estado_mrcb";

            $existe = $this->consultarValor($sql, [$this->id_tipo_movimiento]);

            if ($existe <= 0){
                throw new Exception("Tipo de ingreso no encontrado.", 1);
            }

            $this->tipo_flujo_movimiento = "I";

            if ($this->id_tipo_movimiento == $this->ID_SALDO){
                $r = $this->registrarIngresoSaldo();
            }

            $this->registrarCajaMovimiento();
            $this->commit();

            return $r;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function registrarIngresoSaldo(){
        try {
            $this->beginTransaction();
        
            $sql = "SELECT am.id_atencion_medica, am.id_paciente,
                        am.importe_total,
                        am.observaciones,
                        (am.pago_credito - (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica)) as monto_deuda  
                        FROM atencion_medica am 
                        WHERE am.estado_mrcb AND am.id_atencion_medica = :0";
            $objAtencionMedica = $this->consultarFila($sql, [$this->id_registro_atencion_relacionada]);

            if ($objAtencionMedica == false){
                throw new Exception("Atención médica no encontrada", 1);
            }

            $deuda = $objAtencionMedica["monto_deuda"] - $this->monto_pago_realizado;
            if ($deuda < 0){
                throw new Exception("El monto pago de la deuda no es valido. Lo pagado superado lo adeudado.", 1);
            }

            $importe_total_atencion = $objAtencionMedica["importe_total"];
            $this->id_cliente = $objAtencionMedica["id_paciente"]; 

            //Generación de Comprobante
            // Setear Data
            $id_documento_electronico_registrado = "";
            if ($this->id_tipo_comprobante != "00"){
                /*La serie se saca de la caja donde se están pagando.*/
                require "AtencionMedica.clase.php";
                $objAtencionMedica = new AtencionMedica($this->getDB());

                $objAtencionMedica->id_atencion_medica = $this->id_registro_atencion_relacionada;
                $objAtencionMedica->fecha_atencion  = date("Y-m-d");

                $objAtencionMedica->id_paciente = $this->id_cliente;
                $objAtencionMedica->boleta_numero_documento = $this->boleta_numero_documento;
                $objAtencionMedica->factura_ruc = $this->factura_ruc;
                $objAtencionMedica->id_tipo_comprobante = $this->id_tipo_comprobante;
                $objAtencionMedica->factura_razon_social = $this->factura_razon_social;
                $objAtencionMedica->factura_direccion  = $this->factura_direccion;

                $objAtencionMedica->boleta_tipo_documento =  $this->boleta_tipo_documento;
                $objAtencionMedica->boleta_numero_documento =  $this->boleta_numero_documento;
                $objAtencionMedica->boleta_nombres  =  $this->boleta_nombres;
                $objAtencionMedica->boleta_apellido_paterno  =  $this->boleta_apellido_paterno;
                $objAtencionMedica->boleta_apellido_materno  =  $this->boleta_apellido_materno;
                $objAtencionMedica->boleta_sexo  =  $this->boleta_sexo;
                $objAtencionMedica->boleta_fecha_nacimiento  =  $this->boleta_fecha_nacimiento;
                $objAtencionMedica->id_usuario_registrado = $this->id_usuario_registrado;

                $objAtencionMedica->pago_efectivo = $this->monto_efectivo;
                $objAtencionMedica->pago_deposito = $this->monto_deposito;
                $objAtencionMedica->id_banco = $this->id_banco;
                $objAtencionMedica->numero_operacion = $this->numero_operacion;
                $objAtencionMedica->fecha_deposito = $this->fecha_deposito;
                $objAtencionMedica->pago_tarjeta = $this->monto_tarjeta;
                $objAtencionMedica->numero_tarjeta = $this->numero_tarjeta;
                $objAtencionMedica->numero_voucher = $this->numero_voucher;
                $objAtencionMedica->fecha_transaccion = $this->fecha_transaccion;
                $objAtencionMedica->pago_credito = $this->monto_credito;
                $objAtencionMedica->monto_descuento = 0.00;

                require "Paciente.clase.php";
                $objPaciente = new Paciente($this->getDB());
                $objPaciente->id_usuario_registrado = $this->id_usuario_registrado;

                if ($objAtencionMedica->boleta_numero_documento == "" && $objAtencionMedica->factura_ruc == ""){
                    $resPaciente = $objPaciente->obtenerPacienteXId($this->id_cliente);
                    $resPaciente  = $resPaciente["datos"];

                    $objPaciente->nombres_completos = $resPaciente["nombres_completos"];
                    $objPaciente->numero_documento = $resPaciente["numero_documento"];
                    $objPaciente->id_tipo_documento = $resPaciente["id_tipo_documento"];
                    $objPaciente->direccion = $resPaciente["direccion"];
                    $objPaciente->codigo_ubigeo_distrito = $resPaciente["codigo_ubigeo_distrito"];
                } else {
                    if ($this->id_tipo_comprobante == "01"){
                        $objClienteCreado = $objPaciente->registrarClienteXRUC($this->factura_ruc, $this->factura_razon_social, $this->factura_direccion);
                        $objAtencionMedica->factura_id_cliente = $objClienteCreado["cliente"]["id"];
                    } else{
                        $objClienteCreado = $objPaciente->registrarClienteXOTRO($this->boleta_tipo_documento, $this->boleta_numero_documento, $this->boleta_nombres, $this->boleta_apellido_paterno, $this->boleta_apellido_materno, $this->boleta_sexo, $this->boleta_fecha_nacimiento);
                        $objAtencionMedica->boleta_id_cliente = $objClienteCreado["cliente"]["id"];
                    }
                }                

                $sql = "SELECT serie_boleta, serie_factura 
                                FROM caja
                                WHERE id_caja IN (SELECT id_caja FROM caja_instancia WHERE id_caja_instancia = :0)
                                    AND estado_mrcb";
                $objSerie = $this->consultarFila($sql, [$this->id_caja_instancia]);

                $objAtencionMedica->serie = $objSerie[$this->id_tipo_comprobante == "01" ? "serie_factura" : "serie_boleta"];

                $sql = "SELECT ams.id_servicio, ams.nombre_servicio, ams.precio_unitario, 
                            s.idunidad_medida, s.idtipo_afectacion, ams.cantidad
                        FROM atencion_medica_servicio ams
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        WHERE ams.id_atencion_medica = :0 AND ams.estado_mrcb";

                $servicios = json_decode(json_encode($this->consultarFilas($sql, [$this->id_registro_atencion_relacionada])));
            
                $objAtencionMedica->servicios = $servicios;
                $resComprobante = $objAtencionMedica->generarComprobante($objPaciente, $importe_total_atencion);

                $id_documento_electronico_registrado = $resComprobante["id_documento_electronico_registrado"];
            }

            $this->commit();
            return ["msj"=>"Saldo pagado correctamente.", 
                    "id_documento_electronico_registrado"=>$id_documento_electronico_registrado];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function generarDocumentoElectronicoDesdeAtencion($id_tipo_comprobante, $atencion_medica){
        try {

            require "Paciente.clase.php";
            $objPaciente = new Paciente();

            $resPaciente = $objPaciente->obtenerPacienteXId($this->id_paciente);
            if (!$resPaciente["rpt"]){
                throw new Exception("No existe el paciente ingresado.", 1);
            }

            $resPaciente  = $resPaciente["datos"];
            $objPaciente->nombres_completos = $resPaciente["nombres_completos"];
            $objPaciente->numero_documento = $resPaciente["numero_documento"];
            $objPaciente->id_tipo_documento = $resPaciente["id_tipo_documento"];
            $objPaciente->direccion = $resPaciente["direccion"];
            $objPaciente->codigo_ubigeo_distrito = $resPaciente["codigo_ubigeo_distrito"];


            require "AtencionMedica.clase.php";
            $objAtencionMedica =  new AtencionMedica();

            $objAtencionMedica->factura_id_cliente = $atencion_medica["aux_factura_id_cliente"];
            $objAtencionMedica->factura_ruc = $atencion_medica["factura_ruc"];
            $objAtencionMedica->factura_razon_social = $atencion_medica["factura_razon_social"];
            $objAtencionMedica->factura_direccion = $atencion_medica["factura_direccion"];
            $objAtencionMedica->id_tipo_comprobante = $atencion_medica["id_tipo_comprobante"];

            $objAtencionMedica->generarComprobante($objPaciente);

            require "DocumentoElectronico.clase.php";
            $objComprobante = new DocumentoElectronico();

            $objComprobante->id_atencion_medica = $this->id_atencion_medica;
            $objComprobante->serie = $this->serie;
            $objComprobante->fecha_emision = $this->fecha_atencion;
            $objComprobante->descuento_global = $this->monto_descuento;
            $objComprobante->importe_total = $costo_total_atencion;
            $objComprobante->id_usuario_registrado = $this->id_usuario_registrado;

            $objComprobante->detalle = $this->servicios;
            $objComprobante->observaciones = $this->observaciones;
            $objComprobante->registrar_en_bbdd = true;
            $objComprobante->generar_xml = true;
            $objComprobante->firmar_comprobante = true;

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
                $objComprobante->Cliente = [
                    "id_cliente"=>$this->id_paciente,
                    "numero_documento"=>$objPaciente->numero_documento,
                    "nombres_completos"=>$objPaciente->nombres_completos,
                    "id_tipo_documento"=>$objPaciente->id_tipo_documento,
                    "direccion"=>$objPaciente->direccion,
                    "codigo_ubigeo_distrito"=>$objPaciente->codigo_ubigeo_distrito
                ];
            }

            $objComprobante->id_atencion_medica = $this->id_atencion_medica;
            $objComprobante->serie = $this->serie;
            $objComprobante->fecha_emision = $this->fecha_atencion;
            $objComprobante->descuento_global = $this->monto_descuento;
            $objComprobante->importe_total = $costo_total_atencion;
            $objComprobante->id_usuario_registrado = $this->id_usuario_registrado;

            $objComprobante->detalle = $this->servicios;
            $objComprobante->observaciones = $this->observaciones;
            $objComprobante->registrar_en_bbdd = true;
            $objComprobante->generar_xml = true;
            $objComprobante->firmar_comprobante = true;

            if ($this->id_tipo_comprobante == "01"){
                $r = $objComprobante->generarFactura();
            } else {
                $r = $objComprobante->generarBoleta();        
            }

            $id_documento_electronico_registrado = $objComprobante->id_documento_electronico;
                
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), 1);
        }
    }
    
    public function anularMovimiento($motivo_anulacion){
        try {
            
            $this->beginTransaction();
            //ver que la 
            if (!$this->id_caja_instancia_movimiento){
                throw new Exception("ID Caja Diaria no válido.", 1);
            }

            $sql = "SELECT 
                    cim.id_caja_instancia, id_registro_atencion as id_atencion_medica, id_tipo_movimiento,
                    (SELECT estado_caja = 'C' FROM caja_instancia WHERE id_caja_instancia = cim.id_caja_instancia) as es_caja_cerrada
                    FROM caja_instancia_movimiento cim
                    WHERE id_caja_instancia_movimiento = :0 AND estado_mrcb";
            $objCajaInstanciaMovimiento = $this->consultarFila($sql, [$this->id_caja_instancia_movimiento]);

            if ($objCajaInstanciaMovimiento == false){
                throw new Exception("Movimiento de caja no existe.", 1);
            }

            $sql = "SELECT c.id_rol 
                        FROM usuario u 
                        INNER JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                        WHERE u.id_usuario = :0";

            $id_rol = $this->consultarValor($sql, [$this->id_usuario_registrado]);

            if ($id_rol != "1"){
                throw new Exception("Solo un usuario ADMINISTRADOR puede anular movimientos.", 1);
            }


            $objR = ["msj"=>""];
            switch($objCajaInstanciaMovimiento["id_tipo_movimiento"]){
                case $this->ID_ATENCION:
                    include_once "AtencionMedica.clase.php";
                    $objAtencion = new AtencionMedica();
                    $objAtencion->id_usuario_registrado = $this->id_usuario_registrado;
                    $objAtencion->id_atencion_medica = $objCajaInstanciaMovimiento["id_atencion_medica"];
                    $objR = $objAtencion->anularAtencion($motivo_anulacion);
                break;
                default:
                    $this->update("caja_instancia_movimiento", [
                        "estado_mrcb"=>"0",
                        "fecha_hora_anulado"=>date("Y-m-d H:i:s"),
                        "fue_anulado_caja_cerrada"=> $objCajaInstanciaMovimiento["es_caja_cerrada"],
                        "motivo_anulado"=>$motivo_anulacion,
                        "id_usuario_anulado"=>$this->id_usuario_registrado
                    ], [
                        "id_caja_instancia_movimiento"=>$this->id_caja_instancia_movimiento
                    ]);

                    $objR["msj"] = "Movimiento Anulado correctamente.";
                break;
            }
            
            $this->commit();
            return ["msj"=>$objR["msj"], "id_caja_instancia"=>$objCajaInstanciaMovimiento["id_caja_instancia"]];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function registrarEgreso(){
        try {

            $this->beginTransaction();
            
            $this->fecha_hora_registrado = date("Y-m-d H:i:s");
            $r = ["msj"=>"Egreso realizado correctamente."];

            $sql = "SELECT COUNT(id_tipo_movimiento) 
                    FROM tipo_movimiento 
                    WHERE tipo = 'E' AND id_tipo_movimiento = :0 AND estado_mrcb";

            $existe = $this->consultarValor($sql, [$this->id_tipo_movimiento]);

            if ($existe <= 0){
                throw new Exception("Tipo de egreso no encontrado.", 1);
            }

            $this->tipo_flujo_movimiento = "E";
            $this->registrarCajaMovimiento();

            $this->commit();

            return $r;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }


}