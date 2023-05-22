<?php
require_once '../datos/local_config_web.php';
require_once '../tests/MedicoTest.clase.php';

$op = $_GET["op"];
$obj = new MedicoTest();

try {
    switch($op){
        case "testear_velocidad_comisiones_para_liquidacion_para_imprimir":
            $medicosTesteados = 20;
            $fecha_inicio = '2023-01-01';
            $fecha_fin = '2023-02-01';
            $totales_mayores_a = 0;
            
            $data = $obj->testear_velocidad_comisiones_para_liquidacion_para_imprimir($medicosTesteados, $fecha_inicio, $fecha_fin, $totales_mayores_a);
            echo Funciones::imprimeJSON("200", "OK", $data, true);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}