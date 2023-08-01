<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
include_once './reporte-cajas/cajaSheet.php';
include_once './reporte-cajas/totalCajaSheet.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/*
if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficientes para ver esto.";
  exit;
}
$login = Sesion::obtenerSesion()["nombre_usuario"];
*/

$fecha = isset($_GET["f"]) ? $_GET["f"] : NULL;

if ($fecha == NULL){
    echo "No se ha ingresado parámetro de FECHA";
    exit;
}

$idCajas = isset($_GET["cs"]) ? $_GET["cs"] : NULL;

if ($idCajas == NULL || $idCajas == ""){
    echo "No se ha ingresado parámetro de CAJAS";
    exit;
}

$idCajas = json_decode($idCajas);

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/CajaReporte.clase.php";

$titulo_xls  = "";
try {
  $obj = new CajaReporte();
  $data = $obj->obtenerDataReporteFechaCajasExcel($idCajas);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
  $titulo_xls = "REPCAJAS".str_replace("-","",$fecha);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

try {
    $spreadsheet = new Spreadsheet();
    $cantidadBloquesData = count($data);
    foreach ($data as $key => $bloqueData) {
        $sheetActivo = $spreadsheet->setActiveSheetIndex($key);
        imprimirCajaSheet($sheetActivo, $bloqueData);
        if ($cantidadBloquesData > ($key + 1)){
          $spreadsheet->createSheet();
        }
    }

    $sheetActivo = $spreadsheet->createSheet();
    imprimirCajaTotalesSheet($sheetActivo, $data, $fecha);

    $spreadsheet->setActiveSheetIndex(0);
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

