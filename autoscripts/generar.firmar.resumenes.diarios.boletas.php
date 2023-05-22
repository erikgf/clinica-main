<?php

$URL = "http://localhost/sistema_dpi/controlador/resumen.diario.controlador.php?op=generar_firmar_resumenes_diarios";

//$hoy = date("Y-m-d", strtotime("-1 days")); //ayer
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
    $fileRespuesta = fopen("error_generar_firmar_resumenes_diarios_boletas_".$numero_dia.".txt", "w") or die("Unable to open file!");
} else {
    $fileRespuesta = fopen("generar_firmar_resumenes_diarios_boletas_".$numero_dia.".txt", "w") or die("Unable to open file!");
}

fwrite($fileRespuesta, $respuestafirma);
fclose($fileRespuesta);

