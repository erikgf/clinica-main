<?php

require_once '../negocio/Medico.clase.php';

class MedicoTest{

        public function testear_velocidad_comisiones_para_liquidacion_para_imprimir($medicosTesteados = 1, $fecha_inicio, $fecha_fin, $totales_mayores_a){
        
        $obj = new Medico();
        $medicosRandom = $obj->getMedicosOrdenantesRandom($medicosTesteados, $fecha_inicio, $fecha_fin);
        /*
        $medicosRandom = [
            ["id_medico"=>2070],
            ["id_medico"=>1470],
            ["id_medico"=>1519],
            ["id_medico"=>1636],
            ["id_medico"=>1981],
            ["id_medico"=>1099],
            ["id_medico"=>2172],
            ["id_medico"=>2403],
            ["id_medico"=>1349],
            ["id_medico"=>1182],
            ["id_medico"=>934],
            ["id_medico"=>559],
            ["id_medico"=>1869],
            ["id_medico"=>1979],
            ["id_medico"=>2174],
            ["id_medico"=>295],
            ["id_medico"=>1476],
            ["id_medico"=>2182],
            ["id_medico"=>348]
        ];
        */
        
        $resultados = [];
        $time_start_total = microtime(true);

        foreach ($medicosRandom as $key => $value) {
            $time_start = microtime(true);
            $obj->id_medico = $value["id_medico"];
            $data_executed = $obj->listarAtencionesComisionParaLiquidacionXMedicoImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a);
            $time_end = microtime(true);
            
            $execution_time = ($time_end - $time_start);

            array_push($resultados,[
                "segundos"=>$execution_time,
                "minutos"=>$execution_time / 60,
                "medico"=>$obj->id_medico,
                "bytes"=>strlen(json_encode($data_executed))
            ]);
        }

        $time_end_total = microtime(true); 
        $execution_time_total = ($time_end_total - $time_start_total);
        
        return ["execution_total"=>$execution_time_total, $resultados];
    }


}