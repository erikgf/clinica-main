<?php

require_once '../datos/Conexion.clase.php';

class CajaReporte extends Conexion {

    private function obtenerDataReporteCajaExcel(int $idCajaInstancia){
        try {

            $sql = "SELECT c.descripcion, u.nombre_usuario as dni_usuario,
                    CONCAT(co.nombres,' ',co.apellido_paterno) as nombre_usuario, ci.fecha_apertura as fecha, c.serie_boleta, c.serie_factura
                    From caja c
                    INNER JOIN caja_instancia ci ON ci.id_caja = c.id_caja
                    INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                    INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                    WHERE ci.id_caja_instancia = :0";

            $caja = $this->consultarFila($sql, [$idCajaInstancia]);

            $sql = "SELECT 
                    DATE(cim.fecha_hora_registrado) as fecha,
                    CONCAT(c.serie_atencion,'-',cim.correlativo_atencion) as recibo,
                    CONCAT(de.serie,'-',de.numero_correlativo) as comprobante,
                    de.descripcion_cliente as cliente,
                    CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres) as paciente,
                    cim.monto_efectivo,cim.monto_deposito,cim.monto_tarjeta,cim.monto_credito,
                    CONCAT(
                       IF (cim.monto_deposito > 0,CONCAT(' DEPÓSITO: ',b.descripcion,' OP.: ',cim.numero_operacion),''),
                       IF (cim.monto_tarjeta > 0,CONCAT(' TARJETA: ',cim.numero_tarjeta,' VOUCHER: ',cim.numero_voucher),'')
                    ) as detalle,
                    IF(de.estado_anulado = 1, 'ANULADA', 'ACTIVO') as estado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    LEFT JOIN banco b ON b.id_banco = cim.id_banco
                    INNER JOIN documento_electronico de ON de.id_atencion_medica = cim.id_registro_atencion
                    INNER JOIN paciente p ON p.id_paciente = cim.id_cliente
                    WHERE ci.id_caja_instancia = :0 AND cim.id_tipo_movimiento = 1";
            
            $atenciones = $this->consultarFilas($sql, [$idCajaInstancia]);

            $sql = "SELECT 
                    DATE(cim.fecha_hora_registrado) as fecha,
                    CONCAT(c_r.serie_atencion,'-',cim_r.correlativo_atencion) as recibo,
                    CONCAT(de.serie,'-',de.numero_correlativo) as comprobante,
                    de.descripcion_cliente as cliente,
                    CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres) as paciente,
                    cim.monto_efectivo, cim.monto_deposito, cim.monto_tarjeta, cim.monto_credito,
                    CONCAT('CANCELÓ ',
                                IF(cim_r.monto_efectivo > 0, CONCAT(' EFECTIVO ',cim_r.monto_efectivo),''),
                                IF(cim_r.monto_deposito > 0, CONCAT(' DEPÓSITO ',cim_r.monto_deposito),''),
                                IF(cim_r.monto_tarjeta > 0, CONCAT(' TARJETA ',cim_r.monto_tarjeta),''),
                        ' EL ',DATE(cim_r.fecha_hora_registrado)) as detalle,
                    'SALDO' as estado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia_movimiento cim_r ON cim.id_registro_atencion_relacionada = cim_r.id_registro_atencion
                    INNER JOIN paciente p ON p.id_paciente = cim_r.id_cliente
                    INNER JOIN caja_instancia ci_r ON ci_r.id_caja_instancia = cim_r.id_caja_instancia
                    INNER JOIN caja c_r ON c_r.id_caja = ci_r.id_caja
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = cim_r.id_registro_atencion
                    WHERE ci.id_caja_instancia = :0  AND cim.id_tipo_movimiento = 4 AND cim.estado_mrcb";
            $saldos = $this->consultarFilas($sql, [$idCajaInstancia]);

            //corregir
            $sql = "SELECT 
                    de.fecha_emision as fecha,
                    CONCAT(c.serie_atencion,'-',cim.correlativo_atencion) as recibo,
                    CONCAT(de.serie,'-',de.numero_correlativo) as comprobante,
                    de.descripcion_cliente as cliente
                    ,CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres) as paciente,
                    cim.monto_efectivo, cim.monto_deposito, cim.monto_tarjeta, monto_credito,
                    CONCAT(de_r.serie,'-',de_r.numero_correlativo) as detalle,
                    'ANULADA' as estado
                    FROM documento_electronico de
                    INNER JOIN documento_electronico de_r ON de_r.iddocumento_electronico = de.id_documento_electronico_previo
                    LEFT JOIN caja_instancia_movimiento cim ON cim.id_registro_atencion = de_r.id_atencion_medica
                    LEFT JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    LEFT JOIN caja c ON c.id_caja = ci.id_caja
                    LEFT JOIN paciente p ON p.id_paciente = cim.id_cliente
                    WHERE de.idtipo_comprobante = '07' AND de.fecha_emision = :0 AND de.serie IN (:1, :2)";
            $notas_credito = $this->consultarFilas($sql, [$caja["fecha"], $caja["serie_boleta"], $caja["serie_factura"]]);

            $sql = "SELECT 
                    ci.fecha_apertura as fecha,
                    COALESCE(CONCAT(c.serie_atencion,'-',cim.correlativo_atencion), CONCAT(c.serie_ingresos,'-',cim.correlativo_ingreso)) as recibo,
                    'S/C' as comprobante,
                    COALESCE(CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres),'-')  as cliente,
                    COALESCE(CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres),'-') as paciente,
                    cim.monto_efectivo, cim.monto_deposito, cim.monto_tarjeta, cim.monto_credito,
                    cim.descripcion_movimiento as detalle,
                    '' as estado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    LEFT JOIN paciente p ON p.id_paciente = cim.id_cliente
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = cim.id_registro_atencion
                    WHERE ci.id_caja_instancia = :0 AND tm.tipo = 'I' AND cim.id_tipo_movimiento NOT IN (4) AND de.iddocumento_electronico IS NULL";
            $tickets_e_ingresos = $this->consultarFilas($sql, [$idCajaInstancia]);
            

            $sql = "SELECT 
                    ci.fecha_apertura as fecha,
                    COALESCE(CONCAT(c.serie_atencion,'-',cim.correlativo_atencion), CONCAT(c.serie_ingresos,'-',cim.correlativo_ingreso)) as recibo,
                    'S/C' as comprobante,
                    CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres) as cliente,
                    CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres) as paciente,
                    cim.monto_efectivo, cim.monto_deposito, cim.monto_tarjeta, cim.monto_credito,
                    CONCAT(
                        IF (cim.monto_deposito > 0,CONCAT(' DEPÓSITO: ',b.descripcion,' OP.: ',cim.numero_operacion),''),
                        IF (cim.monto_tarjeta > 0,CONCAT(' TARJETA: ',cim.numero_tarjeta,' VOUCHER: ',cim.numero_voucher),'')
                    ) as detalle,
                    '' as estado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    LEFT JOIN banco b ON b.id_banco = cim.id_banco
                    INNER JOIN paciente p ON p.id_paciente = cim.id_cliente
                    LEFT JOIN documento_electronico de ON de.id_atencion_medica = cim.id_registro_atencion
                    WHERE ci.id_caja_instancia = :0 AND cim.id_tipo_movimiento = 1 AND de.iddocumento_electronico IS NULL AND cim.monto_credito > 0";
            $amortizaciones = $this->consultarFilas($sql, [$idCajaInstancia]);

            $sql = "SELECT 
                    ci.fecha_apertura as fecha,
                    CONCAT(c.serie_egresos,'-',cim.correlativo_egreso) as recibo,
                    'S/C' as comprobante,
                    COALESCE(CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres),'-')  as cliente,
                    COALESCE(CONCAT(p.apellidos_paterno,' ',p.apellidos_materno,' ',p.nombres),'-') as paciente,
                    cim.monto_efectivo, cim.monto_deposito, cim.monto_tarjeta, cim.monto_credito,
                    cim.descripcion_movimiento as detalle,
                    '' as estado
                    FROM caja_instancia_movimiento cim
                    INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                    INNER JOIN caja c ON c.id_caja = ci.id_caja
                    INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                    LEFT JOIN paciente p ON p.id_paciente = cim.id_cliente
                    WHERE ci.id_caja_instancia = :0 AND tm.tipo = 'E'";
            $egresos = $this->consultarFilas($sql, [$idCajaInstancia]);

            return  [   
                        "caja"=>$caja, 
                        "atenciones"=> $atenciones, "saldos"=>$saldos, "notas_credito"=>$notas_credito, 
                        "tickets_e_ingresos" => $tickets_e_ingresos, "egresos"=>$egresos, "amortizaciones"=>$amortizaciones
                    ];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDataReporteFechaCajasExcel(array $arregloIdInstanciaCajas){
        try {
            
            $data = [];
            foreach ($arregloIdInstanciaCajas as $key => $idCajaInstancia) {
                $datos = $this->obtenerDataReporteCajaExcel($idCajaInstancia);

                $atencionesEfectivo = 0.00;
                $atencionesDeposito = 0.00;
                $atencionesTarjeta = 0.00;
                $atencionesCredito = 0.00;
                foreach ($datos["atenciones"] as $key => $value) {
                    $atencionesEfectivo += $value["monto_efectivo"];
                    $atencionesDeposito += $value["monto_deposito"];
                    $atencionesTarjeta += $value["monto_tarjeta"];
                    $atencionesCredito += $value["monto_credito"];
                }

                $saldosEfectivo = 0.00;
                $saldosDeposito = 0.00;
                $saldosTarjeta = 0.00;
                $saldosCredito = 0.00;
                foreach ($datos["saldos"] as $key => $value) {
                    $saldosEfectivo += $value["monto_efectivo"];
                    $saldosDeposito += $value["monto_deposito"];
                    $saldosTarjeta += $value["monto_tarjeta"];
                    $saldosCredito += $value["monto_credito"];
                }
                
                $amortizacionesEfectivo = 0.00;
                $amortizacionesDeposito = 0.00;
                $amortizacionesTarjeta = 0.00;
                $amortizacionesCredito = 0.00;
                foreach ($datos["amortizaciones"] as $key => $value) {
                    $amortizacionesEfectivo += $value["monto_efectivo"];
                    $amortizacionesDeposito += $value["monto_deposito"];
                    $amortizacionesTarjeta += $value["monto_tarjeta"];
                    $amortizacionesCredito += $value["monto_credito"];
                }

                $notaCreditoEfectivo = 0.00;
                $notaCreditoDeposito = 0.00;
                $notaCreditoTarjeta = 0.00;
                $notaCreditoCredito = 0.00;
                foreach ($datos["notas_credito"] as $key => $value) {
                    $notaCreditoEfectivo += $value["monto_efectivo"];
                    $notaCreditoDeposito += $value["monto_deposito"];
                    $notaCreditoTarjeta += $value["monto_tarjeta"];
                    $notaCreditoCredito += $value["monto_credito"];
                }

                $ticketsIngresosEfectivo = 0.00;
                $ticketsIngresosDeposito = 0.00;
                $ticketsIngresosTarjeta = 0.00;
                $ticketsIngresosCredito = 0.00;
                foreach ($datos["tickets_e_ingresos"] as $key => $value) {
                    $ticketsIngresosEfectivo += $value["monto_efectivo"];
                    $ticketsIngresosDeposito += $value["monto_deposito"];
                    $ticketsIngresosTarjeta += $value["monto_tarjeta"];
                    $ticketsIngresosCredito += $value["monto_credito"];
                }

                $ticketsIngresosEfectivo = 0.00;
                $ticketsIngresosDeposito = 0.00;
                $ticketsIngresosCredito = 0.00;
                foreach ($datos["tickets_e_ingresos"] as $key => $value) {
                    $ticketsIngresosEfectivo += $value["monto_efectivo"];
                    $ticketsIngresosDeposito += $value["monto_deposito"];
                    $ticketsIngresosCredito += $value["monto_credito"];
                }

                $egresosEfectivo = 0.00;
                foreach ($datos["egresos"] as $key => $value) {
                    $egresosEfectivo += $value["monto_efectivo"];
                }

                $totalEfectivo = $atencionesEfectivo + $saldosEfectivo - $notaCreditoEfectivo - $egresosEfectivo;
                $totalEfectivoAmortizacion = $amortizacionesEfectivo;
                $totalTickets = $ticketsIngresosEfectivo;

                $totalDeposito = $atencionesDeposito  + $saldosDeposito  - $notaCreditoDeposito;
                $totalDepositoAmortizacion = $amortizacionesDeposito;

                $totalTarjeta = $atencionesTarjeta + $saldosTarjeta - $notaCreditoTarjeta;
                $totalTarjetaAmortizacion = $amortizacionesTarjeta;

                $totalSaldos =  $amortizacionesCredito + 
                                $atencionesCredito + $saldosCredito - $notaCreditoCredito + 
                                $ticketsIngresosCredito;

                $datos["totales"] = [
                    "efectivo"=>$totalEfectivo,
                    "efectivo_amortizacion"=>$totalEfectivoAmortizacion,
                    "ticket"=>$totalTickets,
                    "deposito"=>$totalDeposito,
                    "deposito_amortizacion"=>$totalDepositoAmortizacion,
                    "tarjeta"=>$totalTarjeta,
                    "tarjeta_amortizacion"=>$totalTarjetaAmortizacion,
                    "saldos"=>$totalSaldos
                ];

                array_push($data, $datos);
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerCajaInstanciaPorFecha(string $fecha){
        try {
            $sql = "SELECT ci.id_caja_instancia as id, CONCAT(c.descripcion,' - ', co.nombres,' ',co.apellido_paterno) as descripcion
                        FROM caja_instancia ci 
                        INNER JOIN caja c ON c.id_caja = ci.id_caja
                        INNER JOIN usuario u ON u.id_usuario = ci.id_usuario_registrado
                        INNER JOIN colaborador co ON co.id_colaborador = u.id_colaborador
                        WHERE ci.fecha_apertura = :0 AND c.estado_mrcb
                        ORDER BY c.descripcion";
            
            return $this->consultarFilas($sql, [$fecha]);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}