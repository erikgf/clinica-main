<?php

include_once 'config.php';

$endPoint = "documento.electronico.controlador.php?op=enviar_comprobante_nota_credito_factura";
$URL = BASE_URL_CONTROLADOR.$endPoint;
$baseRoute = DIR_ROBOTLOG;
$fileNameBase = "enviar_comprobante_nota_credito_factura";
$fileNameError = $baseRoute."error_".$fileNameBase."_{0}.txt";
$fileName = $baseRoute.$fileNameBase."_{0}.txt";

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
        $respuestaFinal = "No hay registros para enviar.";
    }
}

$fileRespuesta = fopen(str_replace("{0}",$numero_dia, $fileNameToWrite), "w") or die("Unable to open file!");
fwrite($fileRespuesta, $respuestaFinal);
fclose($fileRespuesta);

