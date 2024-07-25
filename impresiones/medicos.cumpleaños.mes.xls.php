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

$mes = isset($_GET["m"]) ? $_GET["m"] : "";

if ($mes == NULL){
    echo "No se ha el MES a consultar";
    exit;
}

require "../negocio/Medico.clase.php";

$titulo_xls  = "";
try {
  $obj = new Medico();
  $data = $obj->listarCumpleañosPorMes($mes);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$mesDescripcion = Funciones::getMes((int) $mes);
$titulo_xls = "LISTA_CUMPLEANHOS_".$mesDescripcion;

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $actualFila = 1;
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "CUMPLEAÑOS MÉDICOS: ".$mesDescripcion);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>55,"rotulo"=>"NOMBRES Y APELLIDOS"],
                        ["ancho"=>18,"rotulo"=>"TELF / CELULAR"],
                        ["ancho"=>35,"rotulo"=>"PROMOTORA"],
                        ["ancho"=>10,"rotulo"=>"DÍA"],
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
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["nombres"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["telefono"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["promotora"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["dia"]);
        $actualFila++;
    }

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   