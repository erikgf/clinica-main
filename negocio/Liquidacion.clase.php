<?php

require_once '../datos/Conexion.clase.php';

class Liquidacion extends Conexion {
    public int $id_liquidacion;
    public int $id_usuario_registrado;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function calcular(int $id_promotora, string $mes, string $anio){
        try {
            $ahora = date("Y-m-d H:i:s");

            $fecha_inicio =  $anio.'-'.$mes.'-01'; 
            $fecha_fin  = date("Y-m-t", strtotime($fecha_inicio)); 
            $fecha_hoy = date("Y-m-d");

            if ($fecha_hoy < $fecha_fin){
                $fecha_fin = $fecha_hoy;
            }
            
            $this->beginTransaction();

            $sql = "SELECT id_liquidacion as id, (SELECT COUNT(id_liquidacion_detalle) 
                        FROM liquidacion_detalle WHERE id_liquidacion = l.id_liquidacion AND entregado = 1) as existe_entregados 
                    FROM liquidacion l 
                    WHERE mes = :0 AND anio = :1 AND id_promotora = :2";
            $liquidacionesAntiguas = $this->consultarFilas($sql, [$mes, $anio, $id_promotora]);
            
            foreach ($liquidacionesAntiguas as $liquidacion) {
                if ($liquidacion["existe_entregados"] == 1){
                    $this->rollBack();
                    throw new Exception("No puedo recalcular una liquidación de la que ya se entregaron SOBRES.", 500);
                }

                $this->delete("liquidacion", ["id_liquidacion"=>$liquidacion["id"]]);
                $this->delete("liquidacion_detalle", ["id_liquidacion"=>$liquidacion["id"]]);
            }

            $data = $this->obtenerMedicosLiquidacionXPromotoraXFechas($id_promotora, $fecha_inicio, $fecha_fin);

            $porcentaje_promotora = $data["porcentaje_comision"];
            $sedes = $data["sedes"];

            foreach ($sedes as $sede) {
                $id_sede = $sede["id_sede_ordenante"];
                $medicos = $sede["medicos"];

                $campos_valores = [
                    "anio"=>$anio,
                    "mes"=>$mes,
                    "id_promotora"=>$id_promotora,
                    "id_sede"=>$id_sede,
                    "porcentaje_promotora"=>$porcentaje_promotora,
                    "fecha_inicio"=>$fecha_inicio,
                    "fecha_fin"=>$fecha_fin,
                    "id_usuario_registrado"=>$this->id_usuario_registrado,
                    "fecha_hora_registrado"=>$ahora
                ];

                $this->insert("liquidacion", $campos_valores);
                $this->id_liquidacion = $this->getLastId();

                foreach ($medicos as $medico) {
                    $id_medico = $medico["id_medico"];
                    $cantidad_servicios = $medico["cantidad_servicios"];
                    $monto_sin_igv = $medico["monto_sin_igv"];
                    $comision_sin_igv = $medico["comision_sin_igv"];
                    $comision_con_igv = $medico["comision_con_igv"];

                    $campos_valores_detalle = [
                        "id_liquidacion"=>$this->id_liquidacion,
                        "id_medico"=>$id_medico,
                        "monto_sin_igv"=>$monto_sin_igv,
                        "comision_con_igv"=>$comision_con_igv,
                        "comision_sin_igv"=>$comision_sin_igv,
                        "cantidad_servicios"=>$cantidad_servicios
                    ];

                    $this->insert("liquidacion_detalle", $campos_valores_detalle);
                }

            }

            $this->commit();
            return count($sedes) > 0;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerMedicosLiquidacionXPromotoraXFechas($id_promotora, $fecha_inicio, $fecha_fin){
        try {

            $sql = "SELECT porcentaje_comision 
                    FROM promotora_porcentaje_comision
                    WHERE estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL AND id_promotora = :0";
            $cabecera = $this->consultarFila($sql, [$id_promotora]);

            if ($cabecera == false){
                throw new Exception("ID Promotora no encontrado.", 404);
            }

            $sql = "SELECT 
                        am.id_medico_ordenante as id_medico,
                        COUNT(ams.id_servicio) as cantidad_servicios,
                        ROUND((SUM(sub_total) / 1.18),2)  as monto_sin_igv,
                        ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as comision_sin_igv,
                        ROUND(SUM(ams.monto_comision_categoria),2) as comision_con_igv,
                        am.id_sede_ordenante
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) 
                                AND id_medico_ordenante NOT IN (1,2) AND id_promotora_ordenante = :2  AND am.id_sede_ordenante IS NOT NULL
                        GROUP BY am.id_sede_ordenante, am.id_medico_ordenante
                        HAVING comision_sin_igv > 0.00
                        ORDER BY am.id_sede_ordenante, am.id_medico_ordenante";
            $registros =  $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $id_promotora]);

            $cabecera["sedes"] = Funciones::reagruparArregloPorKeys($registros, ["id_sede_ordenante"], "medicos");

            return $cabecera;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerLiquidacionesImprimir(int $id_promotora, string $mes, string $anio){
        try {
            
            $sql = "SELECT 
                        l.id_liquidacion,
                        pr.descripcion as nombre_promotora,
                        porcentaje_promotora as porcentaje_comision,
                        s.nombre as sede,
                        DATE_FORMAT(fecha_inicio, '%d-%m-%Y') as fecha_inicio,
                        DATE_FORMAT(fecha_fin, '%d-%m-%Y') as fecha_fin
                        FROM liquidacion l
                        INNER JOIN sede s ON s.id_sede = l.id_sede
                        INNER JOIN promotora pr ON pr.id_promotora = l.id_promotora
                        WHERE mes = :0 AND anio = :1 AND l.id_promotora = :2";

            $resultados = $this->consultarFilas($sql, [$mes, $anio, $id_promotora]);

            foreach ($resultados as $key => $liquidacion_sede) {
                
                $sql = "SELECT 
                            LPAD(m.id_medico, 5, '0')  as codigo,
                            m.nombres_apellidos as medico,
                            monto_sin_igv,
                            comision_sin_igv,
                            comision_con_igv,
                            cantidad_servicios
                            FROM liquidacion_detalle ld
                            INNER JOIN medico m ON m.id_medico = ld.id_medico 
                            WHERE ld.id_liquidacion  = :0
                            ORDER BY m.nombres_apellidos";

                $resultados[$key]["medicos"]  = $this->consultarFilas($sql, [$liquidacion_sede["id_liquidacion"]]);
            }

            return $resultados;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    /*
    public function calcularHastaMayo2024(){
        try {
            
            $sql = "SELECT id_promotora FROM promotora WHERE estado_mrcb";
            $promotoras =$this->consultarFilas($sql);
            $mes_inicial = 1;
            $mes_final = 5;

            $this->beginTransaction();

            $año = '2024';

            for ($i=$mes_inicial; $i <= $mes_final ; $i++) { 
                $mes = '0'.$i;
                foreach ($promotoras as $promotora) {
                    $this->calcular($promotora["id_promotora"], $mes, $año);
                }
            }
            
            $this->commit();
            return true;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    */

}

