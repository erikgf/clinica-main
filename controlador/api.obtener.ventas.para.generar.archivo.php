<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_reporting(E_ALL ^ E_NOTICE);
date_default_timezone_set('America/Lima');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

require_once '../datos/local_config_web.php';
require "../negocio/AtencionMedica.clase.php";

try{
    $objAtencionMedica = new AtencionMedica();
    $bodyRequest = file_get_contents("php://input");
    // Decodificamos y lo guardamos en un array
    $data = json_decode($bodyRequest, true);
    $fi = isset($data["p_fi"]) ? $data["p_fi"] : "";
    $ff = isset($data["p_ff"]) ? $data["p_ff"] : "";
    
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

    $ventas = $objAtencionMedica->obtenerVentasExportacionContab($fi, $ff);
    Funciones::imprimeJSON("200", "OK", $ventas);
    exit; 
} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR", mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}