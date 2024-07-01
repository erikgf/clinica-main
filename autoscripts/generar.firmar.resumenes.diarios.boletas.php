<?php
include_once 'config.php';

$endPoint = "resumen.diario.controlador.php?op=generar_firmar_resumenes_diarios";
$URL = BASE_URL_CONTROLADOR.$endPoint;
$baseRoute = DIR_ROBOTLOG;
$fileNameBase = "generar_firmar_resumenes_diarios";
$fileNameError = $baseRoute."error_".$fileNameBase."_{0}.txt";
$fileName = $baseRoute.$fileNameBase."_{0}.txt";

$hoy = date("Y-m-d"); 
$numero_dia = date("N", time());

$ch = curl_init();
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_URL, $URL);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POSTFIELDS, ["p_fi"=>$hoy, "p_ff"=> $hoy,"p_nota"=>0]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$respuestafirma  = curl_exec($ch);
curl_close($ch);

$respuestafirmaDecode = json_decode($respuestafirma);
if (json_last_error()){
    $fileRespuesta = fopen(str_replace("{0}", $numero_dia, $fileNameError), "w") or die("Unable to open file!");
} else {
    $fileRespuesta = fopen(str_replace("{0}", $numero_dia, $fileName), "w") or die("Unable to open file!");
}

fwrite($fileRespuesta, $respuestafirma);
fclose($fileRespuesta);

