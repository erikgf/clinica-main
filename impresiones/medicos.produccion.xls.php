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

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR])){
  echo "No tiene permisos para ver esto.";
  exit;
}

$login = $objUsuario["nombre_usuario"];

$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;
$id_medico = isset($_GET["m"]) ? $_GET["m"] : NULL;

if ($fecha_inicio == NULL){
    echo "No se ha ingresado parámetro de FECHA DE INICIO";
    exit;
}

if ($fecha_fin == NULL){
    echo "No se ha ingresado parámetro de FECHA DE FIN";
    exit;
}

if ($id_medico == NULL){
    echo "No se ha ingresado parámetro de MÉDICO";
    exit;
}

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/AtencionMedicaServicio.clase.php";

$titulo_xls  = "";

try {
  $obj = new AtencionMedicaServicio();
  $data = $obj->listarProduccionMedicos($fecha_inicio, $fecha_fin, $id_medico);

  $datos = $data["datos"];
  $medico = $data["medico"];

  if (count($datos) <= 0){
    echo "No se han encontrado registros que mostrar.";
    exit;
  }

  $titulo_xls = $medico;

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

function impresionSheet($spreadsheet, $fecha_inicio, $fecha_fin, $medico, $registros){
  $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $sheetActivo = $spreadsheet->getActiveSheet();

  $actualFila = 1;

  $arregloCabecera = [
        ["ancho"=>12,"rotulo"=>"ESTADO"],
        ["ancho"=>12,"rotulo"=>"FECHA"],
        ["ancho"=>14,"rotulo"=>"RECIBO"],
        ["ancho"=>60,"rotulo"=>"PACIENTE" ],
        ["ancho"=>20,"rotulo"=>"ÁREA" ],
        ["ancho"=>30,"rotulo"=>"EXAMEN" ],
        ["ancho"=>14,"rotulo"=>"MTO PROD."],
    ];
    
  $filaInicioCabecera = $actualFila;
  $columnaInicioCabecera = $alfabeto[0];
  $columnaFinalCabecera = $alfabeto[count($arregloCabecera) - 1];
  $sheetActivo->setCellValue($columnaInicioCabecera.$actualFila, "REPORTE DE EXÁMENES");
  $sheetActivo->mergeCells("{$columnaInicioCabecera}{$actualFila}:{$columnaFinalCabecera}{$actualFila}");
  $actualFila++;
  $sheetActivo->setCellValue($columnaInicioCabecera.$actualFila, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	
  $sheetActivo->mergeCells("{$columnaInicioCabecera}{$actualFila}:{$columnaFinalCabecera}{$actualFila}");
  $actualFila++;
  $sheetActivo->setCellValue($columnaInicioCabecera.$actualFila, $medico);
  $sheetActivo->mergeCells("{$columnaInicioCabecera}{$actualFila}:{$columnaFinalCabecera}{$actualFila}");
  $filaFinalCabecera = $actualFila;

  $actualFila++;

  $cabeceraEstilos = array('font' => array('name' => 'Arial','size' => 13),
    'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
  );

  $sheetActivo->getStyle("{$columnaInicioCabecera}{$filaInicioCabecera}:{$columnaFinalCabecera}{$filaFinalCabecera}")->applyFromArray($cabeceraEstilos);

  $actualFila++;

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

  foreach ($registros as $registro) {
      $i = 0;
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["rotulo_atendido"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atencion"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["recibo"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["nombre_paciente"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["area"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["examen"]);
      $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto"]);
      $actualFila++;
  }

}

try {
    $spreadsheet = new Spreadsheet();

    impresionSheet(
        $spreadsheet, $fecha_inicio, $fecha_fin, $medico, $datos
    );

    $spreadsheet->setActiveSheetIndex(0);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   
