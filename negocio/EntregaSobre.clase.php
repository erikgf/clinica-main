<?php

require_once '../datos/Conexion.clase.php';

class EntregaSobre extends Conexion {
    public int $id_entrega_sobre;
    public int $id_usuario_registrado;

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function listarParaRegistrar(string $mes, string $año, $id_promotora = NULL, $monto_minimo) {
        try {
            
            $params = [$mes, $año, $monto_minimo];
            $sqlPromotora = "";

            if ($id_promotora != NULL){
                array_push($params, $id_promotora);
                $sqlPromotora = " AND l.id_promotora = :3 ";
            }

            $sql = "SELECT  
                    CONCAT(ld.id_medico,pr.id_promotora,l.mes,l.anio) as id,
                    m.id_medico,
                    m.nombres_apellidos as medico,
                    pr.id_promotora,
                    pr.descripcion as promotora,
                    ld.comision_sin_igv as monto,
                    l.mes as mes,
                    l.anio as anio,
                    COALESCE(((SELECT SUM(_ld.comision_sin_igv) From liquidacion _l
                        INNER JOIN liquidacion_detalle _ld ON _l.id_liquidacion = _ld.id_liquidacion
                        WHERE CONCAT(_l.mes,_l.anio) < CONCAT(l.mes,l.anio) AND _ld.entregado = '0'
                        AND _l.id_sede = l.id_sede AND _l.id_promotora = l.id_promotora AND _ld.id_medico = ld.id_medico
                        ORDER BY _l.anio,_l.mes
                    ) + ld.comision_sin_igv),0) as acumulado,
                    COALESCE((SELECT GROUP_CONCAT(CONCAT(_l.mes,'|',_l.anio,'|',_ld.comision_sin_igv)) From liquidacion _l
                        INNER JOIN liquidacion_detalle _ld ON _l.id_liquidacion = _ld.id_liquidacion
                        WHERE CONCAT(_l.mes,_l.anio) < CONCAT(l.mes,l.anio) AND _ld.entregado = '0'
                        AND _l.id_sede = l.id_sede AND _l.id_promotora = l.id_promotora AND _ld.id_medico = ld.id_medico
                        ORDER BY CONCAT(_l.anio,_l.mes)
                    ),0) as liquidaciones_anteriores
                    FROM liquidacion l
                    INNER JOIN liquidacion_detalle ld ON ld.id_liquidacion = l.id_liquidacion
                    INNER JOIN medico m ON m.id_medico = ld.id_medico
                    INNER JOIN promotora pr ON pr.id_promotora = l.id_promotora
                    WHERE l.mes = :0 and l.anio = :1 $sqlPromotora AND ld.entregado = '0'
                    HAVING acumulado >= :2
                    ORDER BY m.nombres_apellidos";

            return $this->consultarFilas($sql, $params);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function registrarSobres(array $data){
        try {

            $ahora = date("Y-m-d H:i:s");

            $this->beginTransaction();

            foreach ($data as $sobre) {
                $observaciones  = $sobre["observaciones"] != "" ? $sobre["observaciones"] : NULL;

                $campos_valores = [
                    "id_medico"=>$sobre["id_medico"],
                    "id_promotora"=>$sobre["id_promotora"],
                    "mes_principal"=>$sobre["mes"],
                    "anio_principal"=>$sobre["anio"],
                    "fecha_entregado"=>$sobre["fecha_entregado"],
                    "observaciones"=>$observaciones,
                    "id_usuario_registrado"=>$this->id_usuario_registrado,
                    "fecha_hora_registrado"=>$ahora
                ];

                $this->insert("entrega_sobre", $campos_valores);
                $id_entrega_sobre = $this->getLastId();

                $mesesAniosMarcarEntregados = [];
                $mesesSobre = $sobre["meses"];
                $campos_valores_detalle = [
                    "id_entrega_sobre"=>$id_entrega_sobre,
                    "mes"=>$sobre["mes"],
                    "anio"=>$sobre["anio"],
                    "monto"=>$sobre["monto"],
                    "es_registro_principal"=>1
                ];

                $this->insert("entrega_sobre_detalle", $campos_valores_detalle);
                array_push($mesesAniosMarcarEntregados, $sobre["anio"].$sobre["mes"]);

                foreach ($mesesSobre as $mesSobre) {
                    $campos_valores_detalle = [
                        "id_entrega_sobre"=>$id_entrega_sobre,
                        "mes"=>$mesSobre["mes"],
                        "anio"=>$mesSobre["anio"],
                        "monto"=>$mesSobre["monto"],
                        "es_registro_principal"=>0
                    ];
                    $this->insert("entrega_sobre_detalle", $campos_valores_detalle);
                    array_push($mesesAniosMarcarEntregados, $mesSobre["anio"].$mesSobre["mes"]);
                }

                $fueEntregado = 1;
                $this->actualizarColumnaEntregado($sobre["id_promotora"], $sobre["id_medico"], $mesesAniosMarcarEntregados, $fueEntregado);
            }

            $this->commit();

            return true;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function registrarActualizacionSobres(array $sobres){
        try {

            $ahora = date("Y-m-d H:i:s");
            $this->beginTransaction();
            
            foreach ($sobres as $sobre) {
                $id = $sobre["id"];
                $es_eliminar = $sobre["es_eliminar"];
                $fecha_entregado = $sobre["fecha_entregado"];
                $fecha_aceptado = $sobre["fecha_aceptado"] != "" ? $sobre["fecha_aceptado"] : NULL;
                $observaciones  = $sobre["observaciones"] != "" ? $sobre["observaciones"] : NULL;

                if ($es_eliminar == 1){

                    $sql = "SELECT id_promotora, id_medico,
                                (SELECT GROUP_CONCAT(CONCAT(anio,mes) ORDER BY mes, anio) as anio_mes 
                                    FROM entrega_sobre_detalle 
                                    WHERE id_entrega_sobre = es.id_entrega_sobre AND fecha_hora_eliminado IS NULL) as anio_meses
                                FROM entrega_sobre es
                                WHERE es.id_entrega_sobre = :0";
                    $dataSobreEntrega = $this->consultarFila($sql, [$id]);

                    $mesesAniosMarcarEntregados = [];


                    $dataSobreEntrega["anio_meses"] = explode(",", $dataSobreEntrega["anio_meses"]);

                    foreach ($dataSobreEntrega["anio_meses"] as $anio_mes) {
                        array_push($mesesAniosMarcarEntregados, $anio_mes);
                    }

                    $fueEntregado = 0;
                    $this->actualizarColumnaEntregado($dataSobreEntrega["id_promotora"], $dataSobreEntrega["id_medico"], $mesesAniosMarcarEntregados, $fueEntregado);

                    $this->update("entrega_sobre", ["fecha_hora_eliminado"=>$ahora], ["id_entrega_sobre"=>$id]);
                    $this->update("entrega_sobre_detalle", ["fecha_hora_eliminado"=>$ahora], ["id_entrega_sobre"=>$id]);
                }

                if ($fecha_entregado === "" || $fecha_entregado === NULL){
                    throw new Exception("No se puede enviar una entrega de sobre sin fecha de entrega.", 1);
                }

                $this->update("entrega_sobre", [
                    "fecha_entregado"=>$fecha_entregado,
                    "fecha_aceptado"=>$fecha_aceptado,
                    "observaciones"=>$observaciones
                ], ["id_entrega_sobre"=>$id]);

            }

            $this->commit();

            return true;
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    public function listarSobresEntrega(string $mes, string $año, $id_promotora = NULL) {
        try {
            $añoMes = $año.$mes;
            $params = [$añoMes];
            $sqlPromotora = "";

            if ($id_promotora != NULL){
                array_push($params, $id_promotora);
                $sqlPromotora = " AND es.id_promotora = :1 ";
            }

            $sql = "SELECT  
                    es.id_entrega_sobre as id,
                    m.id_medico,
                    m.nombres_apellidos as medico,
                    pr.id_promotora,
                    pr.descripcion as promotora,
                    fecha_entregado,
                    fecha_aceptado,
                    es.observaciones,
                    (SELECT SUM(monto)
                            FROM entrega_sobre_detalle 
                            WHERE id_entrega_sobre = es.id_entrega_sobre 
                                AND fecha_hora_eliminado IS NULL) as acumulado,
                    (SELECT GROUP_CONCAT(CONCAT(mes,'|',anio,'|',monto,'|',es_registro_principal) ORDER BY mes, anio) as anio_mes 
                            FROM entrega_sobre_detalle 
                            WHERE id_entrega_sobre = es.id_entrega_sobre 
                                AND fecha_hora_eliminado IS NULL ) as anio_meses
                    FROM entrega_sobre es
                    INNER JOIN medico m ON m.id_medico = es.id_medico
                    INNER JOIN promotora pr ON pr.id_promotora = es.id_promotora
                    WHERE DATE_FORMAT(es.fecha_entregado,'%Y%m') = :0 $sqlPromotora AND es.fecha_hora_eliminado IS NULL
                    ORDER BY m.nombres_apellidos";

            return $this->consultarFilas($sql, $params);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    private function actualizarColumnaEntregado($id_promotora, $id_medico, $mesesAniosMarcarEntregados, $fueEntregado){
        try{
            $sql = "UPDATE liquidacion_detalle ld
                    INNER JOIN liquidacion l ON l.id_liquidacion = ld.id_liquidacion
                    SET ld.entregado = :2
                    WHERE CONCAT(l.anio,l.mes) IN (".join(",", $mesesAniosMarcarEntregados).") AND l.id_promotora = :0 AND ld.id_medico = :1";

            return $this->ejecutarSimple($sql, [$id_promotora, $id_medico, $fueEntregado]);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

}

