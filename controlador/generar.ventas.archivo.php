<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require_once '../datos/local_config_web.php';
require "../negocio/AtencionMedica.clase.php";

$objAtencionMedica = new AtencionMedica();
$URL_ARCHIVO_IMPORTACIONES_VENTAS =  'C:\Siscontab\ImportacionesVentas';

try{
    
    $fi = isset($_POST["txt-fechainicio"]) ? $_POST["txt-fechainicio"] : "";
    $ff = isset($_POST["txt-fechafin"]) ? $_POST["txt-fechafin"] : "";
    
    if ($fi == ""){
        throw new Exception("No se ha ingresado fecha de inicio", 1);
    } 

    if ($ff == ""){
        throw new Exception("No se ha ingresado fecha de fin", 1);
    }

    $mesaniofi = date("mY", strtotime($fi));
    $mesanioff = date("mY", strtotime($ff));

    if ($mesanioff != $mesaniofi){
        throw new Exception("Solo se permite data del mismo período, mismo mes y año.");
    }

    $base_archivo = $mesanioff.F_RUC.".txt";
    $conArchivoPrevio = true;
    $nombre_archivo = $URL_ARCHIVO_IMPORTACIONES_VENTAS."/".$base_archivo;
    if (!file_exists($nombre_archivo)){
        $conArchivoPrevio = false;
    }

    $ventas = $objAtencionMedica->obtenerVentasExportacionContab($fi, $ff);
    if (!$gestor = fopen($nombre_archivo, 'a')) {
        throw new Exception( "No se puede abrir el archivo ($base_archivo)");
    }

    $contenido = "";
    foreach ($ventas as $key => $registro) {
        $i=0;
        if ($key > 0){
            $contenido .= "\n";
        }
        foreach ($registro as $_ => $__) {
            if ($i > 0){
                $contenido .= "|";	
            }
            $contenido .= $__;	
            $i++;
        }
    }

    if (fwrite($gestor, $contenido) === FALSE) {
        throw new Exception( "No se puede crear/escribir en el archivo ($base_archivo)");
    }

    fclose($gestor);
    Funciones::imprimeJSON("200", "OK", ["msj"=>"Archivo $base_archivo generado/actualizado correctamente.","nombre_archivo"=>$base_archivo]);
    exit; 
} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}