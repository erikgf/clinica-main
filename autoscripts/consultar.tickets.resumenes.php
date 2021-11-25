<?php

$URL = "http://localhost/sistema_dpi/controlador/resumen.diario.controlador.php?op=consultar_tickets";

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
if (json_last_error()){
    $fileRespuesta = fopen("error_consultar_tickets_resumenes_diarios_".$numero_dia.".txt", "w") or die("Unable to open file!");
} else {
    $respuesta = $respuestafirmaDecode->respuestas[0];
    if ($respuesta->respuesta == "error"){
        $fileRespuesta = fopen("error_consultar_tickets_resumenes_diarios_".$numero_dia.".txt", "w") or die("Unable to open file!");
        $respuestafirma = $respuesta->mensaje;
    } else {
        $fileRespuesta = fopen("consultar_tickets_resumenes_diarios_".$numero_dia.".txt", "w") or die("Unable to open file!");
    }
}

fwrite($fileRespuesta, $respuestafirma);
fclose($fileRespuesta);

