<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
require_once "../negocio/Globals.clase.php";
require_once "../negocio/util/Funciones.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objUsuario = Sesion::obtenerSesion();

if (!$objUsuario){
    echo "No tiene permisos suficentes para ver esto.";
    exit;
}

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR, Globals::$ID_ROL_PROMOTORA])){
  echo "No tiene permisos para ver esto.";
  exit;
}

$login = $objUsuario["nombre_usuario"];

$id_promotora = NULL;
if (in_array($objUsuario["id_rol"], [Globals::$ID_ROL_PROMOTORA])){
  require "../negocio/Promotora.clase.php";
  $objPromotora = new Promotora();
  $objPromotora->id_usuario = $objUsuario["id_usuario_registrado"];
  $promotora = $objPromotora->getPromotoraFromUsuario();
  $id_promotora = $promotora["id_promotora"];
} else {
  $id_promotora = isset($_GET["idp"]) ? $_GET["idp"] : NULL;
}

if ($id_promotora == ""){
  $id_promotora = NULL;
}

$mes = isset($_GET["m"]) ? $_GET["m"] : NULL;
$año = isset($_GET["a"]) ? $_GET["a"] : NULL;

if ($mes == NULL){
    echo "No se ha ingresado parámetro de MES";
    exit;
}

if ($año == NULL){
    echo "No se ha ingresado parámetro de AÑO";
    exit;
}

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/Liquidacion.clase.php";

$titulo_xls  = "";

try {
  $obj = new Liquidacion();
  $data = $obj->obtenerLiquidacionesImprimir($id_promotora, $mes, $año);

  if (count($data) <= 0){
    echo "No se han encontrado LIQUIDACIONES calculadas.";
    exit;
  }

  $titulo_xls = "INFORLIQMED_".date("Ymd");

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

function impresionSheet($spreadsheet, $liquidacion_sede){
  $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $sheetActivo = $spreadsheet->getActiveSheet();

  $actualFila = 1;
  $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "INFORME DE LIQUIDACIÓN MÉDICOS: ".$liquidacion_sede["nombre_promotora"]. " - ".$liquidacion_sede["sede"]);	
  $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$liquidacion_sede["fecha_inicio"]." AL ".$liquidacion_sede["fecha_fin"]);	

  $actualFila++;

  $arregloCabecera = [
                      ["ancho"=>10,"rotulo"=>"CÓDIGO"],
                      ["ancho"=>70,"rotulo"=>"APELLIDOS NOMBRES MÉDICO"],
                      ["ancho"=>20,"rotulo"=>"TOTAL SIN IGV" ],
                      ["ancho"=>20,"rotulo"=>"COMISIÓN SIN IGV" ],
                      ["ancho"=>20,"rotulo"=>"CANTIDAD SERVICIOS" ]
                  ];

  foreach ($arregloCabecera as $i => $value) {
      $columna = $alfabeto[$i];
      $sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);			
      $sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
  }
  
  $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                              'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                              );
  $sheetActivo->getStyle('A'.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);

  $actualFila++;

  $primeraFila = $actualFila;

  $registros = $liquidacion_sede["medicos"];

  foreach ($registros as $registro) {
      $i = 0;
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["codigo"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_sin_igv"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comision_sin_igv"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["cantidad_servicios"]);
      $actualFila++;
  }

  $ultimaFila = $actualFila - 1;
  $actualFila = $actualFila + 3;

  $sheetActivo->setCellValue($alfabeto[1].$actualFila, "PAGO PROMOTORA");	
  $sheetActivo->setCellValue($alfabeto[1].$actualFila + 1, ($liquidacion_sede["porcentaje_comision"] / 100));	
  $sheetActivo->setCellValue($alfabeto[2].$actualFila, "TOTAL");	
  $sheetActivo->setCellValue($alfabeto[2].$actualFila + 1,"=SUM(C".$primeraFila.":C".$ultimaFila.")");	
  $sheetActivo->setCellValue($alfabeto[3].$actualFila, "COMISIÓN");	
  $sheetActivo->setCellValue($alfabeto[3].$actualFila + 1,"=PRODUCT(B".($actualFila + 1).",C".($actualFila + 1).")");	

  $estilosFinales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 12),
                              'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                              );

  $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[3].$actualFila)->applyFromArray($estilosFinales);

  $spreadsheet->getActiveSheet()->setTitle($liquidacion_sede["sede"]);	
}

try {
    $spreadsheet = new Spreadsheet();
    $sheets = $spreadsheet->getSheetCount();

    foreach ($data as $key => $liquidacion_sede) {
      if ($sheets > 1){
        $myWorkSheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'My Data');
        $spreadsheet->addSheet($myWorkSheet, $sheets - 1);
        $spreadsheet->setActiveSheetIndex($sheets - 1);
      }

      impresionSheet(
        $spreadsheet, $liquidacion_sede
      );
      $sheets++;
    }

    $spreadsheet->setActiveSheetIndex(0);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

