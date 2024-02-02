<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
require_once "../negocio/util/Funciones.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficientes para ver esto.";
  exit;
}
$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_medico = isset($_GET["idm"]) ? $_GET["idm"] : "";
$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;
$totales_mayores_a =  isset($_GET["tt"]) ? $_GET["tt"] : "0";
$id_sede =  isset($_GET["s"]) ? $_GET["s"] : "";

if ($fecha_inicio == NULL){
    echo "No se ha ingresado parámetro de FECHA DE INICIO";
    exit;
}

if ($fecha_fin == NULL){
    echo "No se ha ingresado parámetro de FECHA DE FIN";
    exit;
}
$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/Medico.clase.php";

$titulo_xls  = "";
try {
  $obj = new Medico();
  $data = $obj->listarLiquidacionesMedicosImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);

  if (count($data) <= 0){
    echo "Sin datos encontrado.";
    exit;
  }
  $titulo_xls = "LIQMEDICOS_".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

function impresionSheet($spreadsheet, $titulo_xls, $sede, $fecha_inicio, $fecha_fin, $data){
    $sheetActivo = $spreadsheet->getActiveSheet();
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    $actualFila = 1;
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "INFORME DE LIQUIDACIÓN MÉDICOS - ".$sede);	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>10,"rotulo"=>"CÓDIGO"],
                        ["ancho"=>70,"rotulo"=>"APELLIDOS NOMBRES"],
                        ["ancho"=>16,"rotulo"=>"TOTAL" ]
                    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = $alfabeto[$key];
        $sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
    }
    
    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );
    $sheetActivo->getStyle('A'.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);

    $actualFila++;
    foreach ($data as $key => $registro) {
        $i = 0;
        foreach ($registro as $key_celda => $celda) {
            $columna = $alfabeto[$i];
            $sheetActivo->setCellValue($columna.$actualFila, $celda);
            $i++;			
        }

        $actualFila++;
    }

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);	
}

try {
    $spreadsheet = new Spreadsheet();
    $sheets = $spreadsheet->getSheetCount();

    foreach ($data as $key => $sede) {
      if ($sheets > 1){
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'My Data');
        $spreadsheet->addSheet($myWorkSheet, $sheets - 1);
        $spreadsheet->setActiveSheetIndex($sheets - 1);
      }

      $nombreSede = $sede["sede"];
      impresionSheet(
        $spreadsheet, $nombreSede, $nombreSede, $fecha_fin, $fecha_fin, $sede["medicos"]
      );
      $sheets ++;
    }

    $spreadsheet->setActiveSheetIndex(0);
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

