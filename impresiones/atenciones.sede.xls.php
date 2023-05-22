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

$mes = isset($_GET["m"]) ? $_GET["m"] : NULL;
$año = isset($_GET["a"]) ? $_GET["a"] : NULL;
$id_sede = isset($_GET["sede"]) ? $_GET["sede"] : "*";

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

require "../negocio/AtencionMedicaServicio.clase.php";

$titulo_xls  = "";
try {
  $obj = new AtencionMedicaServicio();
  $data = $obj->listarExamenesAtencionesPorSede($mes, $año, $id_sede);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
  $titulo_xls = "RPTE_ATN_SEDES_".$mes."_".$año;

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

function getMes($mes){
  switch($mes){
    case 1:
    return "ENERO";
    case 2:
    return "FEBRERO";
    case 3:
    return "MARZO";
    case 4:
    return "ABRIL";
    case 5:
    return "MAYO";
    case 6:
    return "JUNIO";
    case 7:
    return "JULIO";
    case 8:
    return "AGOSTO";
    case 9:
    return "SETIEMBRE";
    case 10:
    return "OCTUBRE";
    case 11:
    return "NOVIEMBRE";
    case 12:
    return "DICIEMBRE";
  }
}

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $tituloEstilos = array('font' => array( 'name' => 'Arial','size' => 22));

    $actualFila = 1;
    $sheetActivo->getStyle('A'.$actualFila)->applyFromArray($tituloEstilos);
    $sheetActivo->setCellValue('A'.$actualFila++, "REPORTE DE EXAMENES POR SEDE");
    
    $tituloEstilos = array('font' => array( 'name' => 'Arial','size' => 16));

    $sheetActivo->setCellValue('A'.$actualFila, "SEDE");	
    $sheetActivo->setCellValue('B'.$actualFila++, $id_sede === "*" ? "CHICLAYO/LAMBAYEQUE" : ($id_sede === "1" ? "CHICLAYO" : "LAMBAYEQUE"));	
    $sheetActivo->setCellValue('A'.$actualFila, "MES");	
    $sheetActivo->setCellValue('B'.$actualFila++,getMes($mes));	
    $sheetActivo->setCellValue('A'.$actualFila, "AÑO");	
    $sheetActivo->setCellValue('B'.$actualFila++, $año);	

    $sheetActivo->getStyle('A2:B4')->applyFromArray($tituloEstilos);

    $actualFila++;
    
    $arregloCabecera = [
                        ["ancho"=>12,"rotulo"=>"SEDE"],
                        ["ancho"=>12,"rotulo"=>"ESTADO"],
                        ["ancho"=>14,"rotulo"=>"FECHA"],
                        ["ancho"=>12,"rotulo"=>"RECIBO"],
                        ["ancho"=>18,"rotulo"=>"COMPROBANTE"],
                        ["ancho"=>42,"rotulo"=>"PACIENTE"],
                        ["ancho"=>30,"rotulo"=>"AREA"],
                        ["ancho"=>45,"rotulo"=>"EXAMEN"],
                        ["ancho"=>20,"rotulo"=>"MONTO EXAMEN"],
                        ["ancho"=>20,"rotulo"=>"MONTO RECIBO POR EXAMEN"],
                        ["ancho"=>20,"rotulo"=>"MONTO DESCUENTO"],
                        ["ancho"=>35,"rotulo"=>"MEDICO REALIZANTE"],
                        ["ancho"=>35,"rotulo"=>"MEDICO INFORMARTE"],
                        ["ancho"=>35,"rotulo"=>"MEDICO ORDENANTE"],
                        ["ancho"=>62,"rotulo"=>"OBSERVACIONES DEL TICKET"],
                    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = $alfabeto[$key];
        $sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
    }
    
    $subCabeceraEstilos = array('font' => array('bold'=>true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
                                );

    $sheetActivo->getStyle('A'.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    $actualFila++;

    foreach ($data as $key => $registro) {
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["sede"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["estado"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["recibo"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comprobante"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["nombre_paciente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["area"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["examen"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_examen"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_total_recibo"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_descuento"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico_realizado"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico_ordenante"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["observaciones_ticket"]);

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

