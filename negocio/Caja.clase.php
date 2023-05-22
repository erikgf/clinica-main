<?php

require_once '../datos/Conexion.clase.php';

class Caja extends Conexion {

    public $id_caja;
    public $id_caja_instancia;
    public $numero_caja_dia;
    public $monto_apertura;
    public $fecha_apertura;
    public $monto_cierre;
    public $monto_cierre_efectivo;

    public $id_usuario_registrado;

    public function obtenerCajas(){
        try {
            $sql = "SELECT  
                    ca.id_caja,
                    ca.descripcion
                    FROM caja ca
                    WHERE ca.estado_mrcb
                    ORDER BY ca.codigo";
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerCajasAbiertasValidas(){
        try {
            $sql = "SELECT 
                    distinct ci.id_caja_instancia as id,
                    CONCAT(ca.codigo,' | ', CONCAT(co.nombres,' ',co.apellido_paterno)) as descripcion
                    FROM caja_instancia ci 
                    INNER JOIN caja ca ON ca.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.estado_caja = 'A' AND ci.estado_mrcb AND ca.estado_mrcb AND ci.fecha_apertura = CURRENT_DATE
                    ORDER BY ca.codigo";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerCajasAbiertasValidasRegistrarAtencion(){
        try {
            $sql = "SELECT 
                    distinct ci.id_caja_instancia as id,
                    CONCAT(ca.codigo,' | ', CONCAT(co.nombres,' ',co.apellido_paterno)) as descripcion
                    FROM caja_instancia ci 
                    INNER JOIN caja ca ON ca.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.estado_caja = 'A' AND ci.estado_mrcb AND ca.estado_mrcb AND ca.id_caja = :0
                    ORDER BY ca.codigo, ci.id_caja_instancia DESC";
            $data =  $this->consultarFilas($sql, $this->id_caja);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function esValidaInstanciaCaja($id_caja_instancia, $fecha_atencion){
        try {
            $sql = "SELECT 
                    ci.id_caja_instancia,
                    c.bloquear_efectivo
                    FROM caja_instancia ci 
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    WHERE ci.estado_caja = 'A' AND ci.estado_mrcb AND ci.id_caja_instancia = :0 AND ci.fecha_apertura = :1";
            $data =  $this->consultarFila($sql, [$id_caja_instancia, $fecha_atencion]);

            return  array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }


    public function obtenerInstanciaValidaFecha($fecha_atencion){
        try {
            $sql = "SELECT 
                    ci.id_caja_instancia
                    FROM caja_instancia ci 
                    WHERE ci.estado_caja = 'A' AND ci.estado_mrcb AND fecha_apertura = :0 AND";
            $data =  $this->consultarFila($sql, [$fecha_atencion]);

            return  array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerInstancias($fecha_inicio, $fecha_fin){
        try {
            $sql = "SELECT 
                    ci.id_caja_instancia as id,
                    ca.codigo as descripcion,
                    fecha_apertura,
                    monto_apertura,
                    CONCAT(co.nombres,' ',co.apellido_paterno) as usuario_caja,
                    COALESCE(fecha_cierre,'NO CERRADA') as fecha_cierre,
                    COALESCE(monto_cierre,'NO CERRADA') as monto_cierre
                    FROM caja_instancia ci 
                    INNER JOIN caja ca ON ca.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.estado_mrcb AND ca.estado_mrcb AND ca.id_caja = :0 AND (fecha_apertura >= :1 AND fecha_apertura <= :2)
                    ORDER BY ci.fecha_apertura DESC, ci.numero_caja_dia DESC, ca.codigo";
            $data =  $this->consultarFilas($sql, [$this->id_caja, $fecha_inicio, $fecha_fin]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function seleccionarMovimientosCajaInstancia(){
        try {

            $sql = "SELECT 
                    c.codigo as nombre_caja,
                    CONCAT(REPLACE(fecha_apertura,'-',''),LPAD(numero_caja_dia, 4,'0')) as codigo,
                    monto_apertura,
                    CONCAT(co.nombres,' ',co.apellido_paterno) as usuario_caja,
                    COALESCE(monto_cierre,'') as monto_cierre,
                    IF(estado_caja = 'C', '1', '0') as esta_cerrada
                    FROM caja_instancia ci                    
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.estado_mrcb  AND ci.id_caja_instancia = :0";    

            $data =  $this->consultarFila($sql, [$this->id_caja_instancia]);

            $ingresos = $this->obtenerIngresosXIdCajaInstancia();
            $egresos =  $this->obtenerEgresosXIdCajaInstancia();
            $movimientos =  $this->obtenerMovimientosXIdCajaInstancia();

            $data["ingresos"] = $ingresos;
            $data["egresos"] = $egresos;
            $data["movimientos"] = $movimientos;

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
        
    public function abrirCaja($es_fecha_anterior = "0", $es_fecha_repetida = "0", $clave_admin = ""){
        try {

            $this->beginTransaction();
            //ver que la 
            if (!$this->id_caja){
                throw new Exception("ID Caja no valida.", 1);
            }
            
            if ($es_fecha_anterior == "0"){
                $sql = "SELECT CURRENT_DATE < :0";
                $fechaPasada = $this->consultarValor($sql, [$this->fecha_apertura]);
                if ($fechaPasada == "1"){
                    return ["rpt"=>"0", "es_fecha_anterior"=>"1"];
                }
            }	

            if ($this->id_caja_instancia != NULL){
                $sql = "SELECT estado_caja
                            FROM caja_instancia 
                            WHERE estado_mrcb AND id_caja_instancia = :0";
                $cajaInstanciaVerificacion = $this->consultarFila($sql, [$this->id_caja_instancia]);
                if ($cajaInstanciaVerificacion == false){
                    throw new Exception("La caja editandose no existe.", 1);
                }

                if ($clave_admin == ""){
                    throw new Exception("No se ha enviado una clave de un usuario administrador..", 1);
                }

                $sql = "SELECT COUNT(u.id_usuario) 
                                FROM usuario u
                                INNER JOIN colaborador c ON u.id_colaborador = u.id_colaborador
                                INNER JOIN rol r ON r.id_rol = c.id_rol
                                WHERE u.clave = md5(:0) AND u.estado_mrcb AND c.es_gestion_caja 
                                        AND c.estado_mrcb AND r.estado_mrcb";
                $valido = $this->consultarValor($sql, [$clave_admin]);

                if ($valido <= 0){
                    throw new Exception("Clave ingresada no válida.", 1);
                }

                $campos_valores = [
                    "monto_apertura"=>$this->monto_apertura,
                    "id_usuario_registrado"=>$this->id_usuario_registrado
                ];

                $campos_valores_where = [
                    "id_caja_instancia"=>$this->id_caja_instancia
                ];  
                
                $this->update("caja_instancia", $campos_valores, $campos_valores_where);
                
                return ["rpt"=>"1", "msj"=>"Caja editada correctamente", "id_caja_instancia"=>$this->id_caja_instancia];
            }
            
            $sql = "SELECT id_caja_instancia, estado_caja FROM caja_instancia
                         WHERE estado_mrcb AND fecha_apertura = :0 AND id_caja = :1";
            $cajaInstanciaVerificacion = $this->consultarFila($sql, [$this->fecha_apertura, $this->id_caja]);
            if ($cajaInstanciaVerificacion != false){
                if ($cajaInstanciaVerificacion["estado_caja"] == "A"){
                    throw new Exception("Ya existe una caja abierta con esta fecha.");
                }	
            
                if ($es_fecha_repetida == "0"){
                    return ["rpt"=>"0", "es_fecha_repetida"=>"1"];
                }
            }

            $this->obtenerSiguienteNumeroCajaDia();
            
            $campos_valores = [
                "id_caja"=>$this->id_caja,
                "monto_apertura"=>$this->monto_apertura,
                "fecha_apertura"=>$this->fecha_apertura,
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "numero_caja_dia"=>$this->numero_caja_dia
            ];
            
            $this->insert("caja_instancia", $campos_valores);
            $this->id_caja_instancia = $this->getLastID();


            $this->commit();
            return ["rpt"=>"1", "msj"=>"Caja abierta correctamente", "id_caja_instancia"=>$this->id_caja_instancia];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    private function obtenerMovimientosXIdCajaInstancia(){
        try {

            $sql = "SELECT 
                    cim.id_caja_instancia_movimiento as id,
                    cim.id_registro_atencion as id_atencion_medica,
                    am.DE_ID as iddocumento_electronico,
                    am.DE_NOTA_ID as iddocumento_electronico_relacionado,
                    tm.descripcion as movimiento,
                    IF(tm.tipo = 'I', '1','0') as es_ingreso,
                    COALESCE(p.razon_social, CONCAT(p.nombres,' ',p.apellidos_paterno,' ',p.apellidos_materno),cim.descripcion_movimiento,'') as cliente,
                    cim.monto_efectivo,
                    cim.monto_deposito,
                    cim.monto_tarjeta,
                    cim.monto_credito,
                    (cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito - cim.monto_descuento) as monto_total,
                    cim.id_tipo_movimiento,
                    (cim.id_tipo_movimiento  <> 1) es_anulable,
                    COALESCE(am.DE_ESTADO_ANULADO, NOT cim.estado_mrcb) as estado_anulado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON cim.id_caja_instancia = ci.id_caja_instancia
                    INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    LEFT JOIN atencion_medica am ON am.id_atencion_medica = cim.id_registro_atencion
                    LEFT JOIN paciente p ON p.id_paciente = cim.id_cliente
            /*
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = cim.id_registro_atencion
                    LEFT JOIN documento_electronico de_relacionado ON de_relacionado.id_atencion_medica = cim.id_registro_atencion_relacionada
                                                                        AND  (de.estado_anulado = 0 OR de_relacionado.estado_anulado  = 0) */
                    WHERE cim.estado_mrcb  AND cim.id_caja_instancia = :0
                    ORDER BY cim.id_caja_instancia_movimiento DESC";

            $movimientos =  $this->consultarFilas($sql, [$this->id_caja_instancia]);

            return $movimientos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerIngresosXIdCajaInstancia(){
        try {

            $sql = "SELECT 
                            COALESCE(SUM(cim.monto_efectivo),'0.00') as monto_efectivo,
                            COALESCE(SUM(cim.monto_deposito),'0.00') as monto_deposito,
                            COALESCE(SUM(cim.monto_tarjeta),'0.00') as monto_tarjeta,
                            COALESCE(SUM(cim.monto_credito),'0.00') as monto_credito,
                            COALESCE(SUM(cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito ), '0.00') as monto_total
                            FROM caja_instancia_movimiento cim
                            INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento 
                            WHERE tm.tipo = 'I' AND tm.estado_mrcb AND cim.id_caja_instancia = :0 AND cim.estado_mrcb";
            $ingresos =  $this->consultarFila($sql, [$this->id_caja_instancia]);
            return $ingresos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerEgresosXIdCajaInstancia(){
        try {

            $sql = "SELECT 
                            COALESCE(SUM(cim.monto_efectivo),'0.00') as monto_efectivo,
                            COALESCE(SUM(cim.monto_deposito),'0.00') as monto_deposito,
                            COALESCE(SUM(cim.monto_tarjeta),'0.00') as monto_tarjeta,
                            COALESCE(SUM(cim.monto_credito),'0.00') as monto_credito,
                            COALESCE(SUM(cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito ), '0.00') as monto_total
                            FROM caja_instancia_movimiento cim
                            INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento 
                            WHERE tm.tipo = 'E' AND tm.estado_mrcb AND cim.id_caja_instancia = :0 AND cim.estado_mrcb";
            $egresos =  $this->consultarFila($sql, [$this->id_caja_instancia]);
            return $egresos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerSiguienteNumeroCajaDia(){
        try {

            $sql = "SELECT 
                            COALESCE(MAX(numero_caja_dia) + 1, 1)
                            FROM caja_instancia ci
                            WHERE ci.id_caja = :0 AND ci.estado_mrcb AND ci.fecha_apertura = :1";
            $numero_caja_dia =  $this->consultarValor($sql, [$this->id_caja, $this->fecha_apertura]);
            $this->numero_caja_dia = $numero_caja_dia;
            return $numero_caja_dia;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cerrarCaja(){
        try {
            
            $this->beginTransaction();
            //ver que la 
            if (!$this->id_caja_instancia){
                throw new Exception("ID Caja no válida.", 1);
            }
            
            $sql = "SELECT estado_caja
                        FROM caja_instancia 
                        WHERE estado_mrcb AND id_caja_instancia = :0";
            $cajaInstanciaVerificacion = $this->consultarFila($sql, [$this->id_caja_instancia]);
            if ($cajaInstanciaVerificacion == false){
                throw new Exception("La caja a cerrar no existe.", 1);
            }
            
            if ( $cajaInstanciaVerificacion["estado_caja"] == "C"){
                throw new Exception("Esta caja ya fue cerrada.", 1);
            }

            $ingresos = $this->obtenerIngresosXIdCajaInstancia();

            $this->monto_cierre = $ingresos["monto_efectivo"] + $ingresos["monto_deposito"] + $ingresos["monto_tarjeta"] + $ingresos["monto_credito"];
            $this->monto_cierre_efectivo = $ingresos["monto_efectivo"];

            $campos_valores = [
                "estado_caja"=>"C",
                "monto_cierre"=>$this->monto_cierre,
                "monto_cierre_efectivo"=>$this->monto_cierre_efectivo,
                "fecha_cierre"=>date('Y-m-d'),
                "fecha_hora_cierre"=>date("Y-m-d H:i:s"),
                "id_usuario_cierre"=>$this->id_usuario_registrado,

            ];

            $campos_valores_where = [
                "id_caja_instancia"=>$this->id_caja_instancia
            ];  
            
            $this->update("caja_instancia", $campos_valores, $campos_valores_where);
            $this->commit();
            return ["msj"=>"Caja cerrada correctamente", "id_caja_instancia"=>$this->id_caja_instancia];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerFormatoArqueoCajaDiarioImpresion(){
        try {

            $sql = "SELECT 
                    c.codigo as nombre_caja,
                    CONCAT(REPLACE(fecha_apertura,'-',''),LPAD(numero_caja_dia, 4,'0')) as codigo,
                    monto_apertura,
                    COALESCE(monto_cierre,'') as monto_cierre,
                    IF(estado_caja = 'C', '1', '0') as esta_cerrada,
                    DATE_FORMAT(fecha_hora_registrado, '%d/%m/%Y') as fecha_apertura, 
                    DATE_FORMAT(fecha_hora_registrado, '%H:%i:%s') as hora_apertura,
                    DATE_FORMAT(fecha_hora_cierre, '%d/%m/%Y') as fecha_cierre, 
                    DATE_FORMAT(fecha_hora_cierre, '%H:%i:%s') as hora_cierre,
                    CONCAT(co.numero_documento,' - ', co.apellido_paterno,' ',co.apellido_materno,' ',co.nombres) as usuario_apertura
                    FROM caja_instancia ci
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.estado_mrcb  AND ci.id_caja_instancia = :0";    

            $data =  $this->consultarFila($sql, [$this->id_caja_instancia]);

            $sql = "SELECT 
                    DATE_FORMAT(am.fecha_atencion, '%d/%m/%Y') as fecha_registro,
                    cim.id_registro_atencion as id_atencion_medica,
                    COALESCE(am.nombre_paciente,'') as cliente,
                    'RI' as tipo_documento,
                    am.numero_acto_medico as numero_documento,
                    '1' as es_ingreso,
                    cim.monto_efectivo,
                    cim.monto_deposito,
                    cim.monto_tarjeta,
                    cim.monto_credito as monto_saldo,
                    cim.monto_descuento,
                    am.monto_vuelto,
                    (cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito - cim.monto_descuento) as monto_total,
                    ban.descripcion as banco,
                    cim.numero_operacion,
                    cim.numero_voucher,
                    cim.numero_tarjeta
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON cim.id_caja_instancia = ci.id_caja_instancia
                    INNER JOIN atencion_medica am ON cim.id_registro_atencion = am.id_atencion_medica
                    LEFT JOIN banco ban ON ban.id_banco = cim.id_banco
                    WHERE cim.estado_mrcb AND am.estado_mrcb AND cim.id_caja_instancia = :0 AND cim.id_tipo_movimiento IN (1)
                    ORDER BY am.fecha_atencion, am.hora_atencion";

            $data["atenciones"] =  $this->consultarFilas($sql, [$this->id_caja_instancia]);

            $sql = "SELECT 
                    DATE_FORMAT(COALESCE(am.fecha_atencion, cim.fecha_hora_registrado), '%d/%m/%Y') as fecha_registro,
                    cim.id_registro_atencion as id_atencion_medica,
                    COALESCE(cim.descripcion_movimiento, am.nombre_paciente,'') as cliente,
                    'IN' as tipo_documento,
                    COALESCE(tm.descripcion,'-') as descripcion,
                    '1' as es_ingreso,
                    cim.monto_efectivo,
                    cim.monto_deposito,
                    cim.monto_tarjeta,
                    cim.monto_credito as monto_saldo,
                    cim.monto_descuento,
                    COALESCE(am.monto_vuelto,'0.00') as monto_vuelto,
                    (cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito - cim.monto_descuento) as monto_total,
                    ban.descripcion as banco,
                    cim.numero_operacion,
                    cim.numero_voucher,
                    cim.numero_tarjeta
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON cim.id_caja_instancia = ci.id_caja_instancia
                    LEFT JOIN atencion_medica am ON cim.id_registro_atencion_relacionada = am.id_atencion_medica AND am.estado_mrcb
                    LEFT JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    LEFT JOIN banco ban ON ban.id_banco = cim.id_banco
                    WHERE cim.estado_mrcb  AND cim.id_caja_instancia = :0 AND tm.tipo = 'I' AND cim.id_tipo_movimiento NOT IN (1)
                    ORDER BY cim.fecha_hora_registrado";

            $data["ingresos"] =  $this->consultarFilas($sql, [$this->id_caja_instancia]);

            $sql = "SELECT 
                    DATE_FORMAT(am.fecha_atencion, '%d/%m/%Y') as fecha_registro,
                    cim.id_registro_atencion as id_atencion_medica,
                    COALESCE(cim.descripcion_movimiento, am.nombre_paciente,'') as cliente,
                    'EG' as tipo_documento,
                    COALESCE(tm.descripcion,'-') as descripcion,
                    '0' as es_ingreso,
                    cim.monto_efectivo,
                    cim.monto_deposito,
                    cim.monto_tarjeta,
                    cim.monto_credito as monto_saldo,
                    cim.monto_descuento,
                    am.monto_vuelto,
                    (cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito - cim.monto_descuento) as monto_total
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON cim.id_caja_instancia = ci.id_caja_instancia
                    LEFT JOIN atencion_medica am ON cim.id_registro_atencion_relacionada = am.id_atencion_medica
                    LEFT JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    WHERE cim.estado_mrcb AND cim.id_caja_instancia = :0 AND tm.tipo = 'E'
                    ORDER BY am.fecha_atencion, am.hora_atencion";

            $data["egresos"] =  $this->consultarFilas($sql, [$this->id_caja_instancia]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerMovimientosXIdCaja($fecha_inicio, $fecha_fin){
        try {
            $sqlCaja = "true";
            $params =  [$fecha_inicio, $fecha_fin];
            if ($this->id_caja != ""){
                $sqlCaja = " ci.id_caja = :2 ";
                array_push($params, $this->id_caja);
            }

            $sql = "SELECT 
                    cim.id_caja_instancia_movimiento as id,
                    COALESCE(am.numero_acto_medico,'-') as numero_acto_medico,
                    CONCAT(c.codigo,' ',REPLACE(ci.fecha_apertura,'-',''),LPAD(ci.numero_caja_dia, 4,'0')) as caja_instancia,
                    DATE_FORMAT(cim.fecha_hora_registrado, '%d/%m/%Y %H:%i:%s') as fecha_registro,
                    cim.id_registro_atencion as id_atencion_medica,
                    am.DE_ID as iddocumento_electronico,
                    am.DE_NOTA_ID as iddocumento_electronico_relacionado,
                    tm.descripcion as movimiento,
                    IF(tm.tipo = 'I', '1','0') as es_ingreso,
                    COALESCE(p.razon_social, CONCAT(p.nombres,' ',p.apellidos_paterno,' ',p.apellidos_materno),'') as cliente,
                    cim.monto_efectivo,
                    cim.monto_deposito,
                    cim.monto_tarjeta,
                    cim.monto_credito,
                    (cim.monto_efectivo + cim.monto_deposito + cim.monto_tarjeta + cim.monto_credito) as monto_total,
                    cim.id_tipo_movimiento,
                    IF (am.DE_ID IS NULL, NOT am.estado_mrcb, COALESCE(am.DE_ESTADO_ANULADO, NOT am.estado_mrcb)) as estado_anulado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON cim.id_caja_instancia = ci.id_caja_instancia
                    INNER JOIN caja c ON ci.id_caja = c.id_caja
                    INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    INNER JOIN paciente p ON p.id_paciente = cim.id_cliente
                    /*
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = cim.id_registro_atencion AND de.estado_anulado = 0
                    LEFT JOIN documento_electronico de_relacionado ON de_relacionado.id_atencion_medica = cim.id_registro_atencion_relacionada
                                                                        AND  (de.estado_anulado = 0 OR de_relacionado.estado_anulado  = 0) 
                                                                        */
                    LEFT JOIN atencion_medica am ON am.id_atencion_medica = cim.id_registro_atencion
                    WHERE cim.estado_mrcb  AND (DATE(cim.fecha_hora_registrado) >= :0 AND DATE(cim.fecha_hora_registrado) <= :1) AND $sqlCaja
                    ORDER BY cim.fecha_hora_registrado DESC";
                
            $movimientos =  $this->consultarFilas($sql, $params);

            return $movimientos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }


    public function listarMovimientosGeneral($fecha_inicio, $fecha_fin){
        try {
            $data =  $this->obtenerMovimientosXIdCaja($fecha_inicio, $fecha_fin);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
}