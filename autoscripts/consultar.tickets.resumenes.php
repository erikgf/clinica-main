<?php

include_once 'config.php';

$endPoint = "resumen.diario.controlador.php?op=consultar_tickets";
$URL = BASE_URL_CONTROLADOR.$endPoint;
$baseRoute = DIR_ROBOTLOG;
$fileNameError = $baseRoute."error_consultar_tickets_resumenes_diarios_{0}.txt";
$fileName = $baseRoute."consultar_tickets_resumenes_diarios_{0}.txt";

$hoy = date("Y-m-d");
$numero_dia = date("N", time());

$ch = curl_init();
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_URL, $URL);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, ["p_fi"=>$hoy, "p_ff"=> $hoy]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuestafirma  = curl_exec($ch);
curl_close($ch);


$respuestafirmaDecode = json_decode($respuestafirma);
$jsonLastError = json_last_error();
$fileNameToWrite = $fileNameError;

if ($jsonLastError){
    $respuesta = $jsonLastError;
} else {
    if (count($respuestafirmaDecode->respuestas) > 0){
        $respuesta = $respuestafirmaDecode->respuestas[0];
        
        if ($respuesta && $respuesta->respuesta != "error"){
            $fileNameToWrite = $fileName;
            $respuestaFinal = $respuestafirma;
        } else {
            $respuestaFinal = $respuesta->mensaje;
        }
    } else {
        $fileNameToWrite = $fileName;
        $respuestaFinal = "No hay registros para consultar.";
    }
}
/*
$respuestafirmaDecode = json_decode($respuestafirma);
if (json_last_error()){
    $fileRespuesta = fopen(str_replace("{0}",$numero_dia, $fileNameError), "w") or die("Unable to open file!");
} else {
    $respuesta = $respuestafirmaDecode->respuestas[0];
    if ($respuesta->respuesta == "error"){
        $fileRespuesta = fopen(str_replace("{0}",$numero_dia, $fileNameError), "w") or die("Unable to open file!");
        $respuestafirma = $respuesta->mensaje;
    } else {
        $fileRespuesta = fopen(str_replace("{0}",$numero_dia, $fileName), "w") or die("Unable to open file!");
    }
}
*/
$fileRespuesta = fopen(str_replace("{0}",$numero_dia, $fileNameToWrite), "w") or die("Unable to open file!");
fwrite($fileRespuesta, $respuestaFinal);
fclose($fileRespuesta);

