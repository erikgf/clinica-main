<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficientes para ver esto.";
  exit;
}
$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_promotora = isset($_GET["idp"]) ? $_GET["idp"] : "";
$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;

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
    $obj->id_promotora = $id_promotora;
    $data = $obj->listarMedicosLiquidacionXPromotoraImprimir($fecha_inicio, $fecha_fin);

  if (count($data) <= 0){
    echo "Sin datos encontrado.";
    exit;
  }
  $titulo_xls = "INFORLIQMED_".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $actualFila = 1;
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "INFORME DE LIQUIDACIÓN MÉDICOS: ".$data["nombre_promotora"]);	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : "."DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>10,"rotulo"=>"CÓDIGO"],
                        ["ancho"=>70,"rotulo"=>"APELLIDOS NOMBRES MÉDICO"],
                        ["ancho"=>20,"rotulo"=>"TOTAL SIN IGV" ],
                        ["ancho"=>20,"rotulo"=>"COMISIÓN SIN IGV" ],
                        ["ancho"=>20,"rotulo"=>"CANTIDAD SERVICIOS" ]
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
    $registros = $data["registros"];

    $primeraFila = $actualFila;
    foreach ($registros as $key => $registro) {
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["codigo"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medicos"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["subtotal_sin_igv"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comision_sin_igv"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["cantidad_servicios"]);
        $actualFila++;
    }
    $ultimaFila = $actualFila - 1;
    $actualFila = $actualFila + 3;

    $sheetActivo->setCellValue($alfabeto[1].$actualFila, "PAGO PROMOTORA");	
    $sheetActivo->setCellValue($alfabeto[1].$actualFila + 1, ($data["porcentaje_comision"] / 100));	
    $sheetActivo->setCellValue($alfabeto[2].$actualFila, "TOTAL");	
    $sheetActivo->setCellValue($alfabeto[2].$actualFila + 1,"=SUM(C".$primeraFila.":C".$ultimaFila.")");	
    $sheetActivo->setCellValue($alfabeto[3].$actualFila, "COMISIÓN");	
    $sheetActivo->setCellValue($alfabeto[3].$actualFila + 1,"=PRODUCT(B".($actualFila + 1).",C".($actualFila + 1).")");	

    $estilosFinales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 12),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );

    $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[3].$actualFila)->applyFromArray($estilosFinales);                            

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);	

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

