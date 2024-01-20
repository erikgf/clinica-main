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

$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;
$id_sede = "*";

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

require "../negocio/AtencionMedica.clase.php";

$titulo_xls  = "";
try {
  $obj = new AtencionMedica();
  $data = $obj->obtenerReporteAtencionesDescuentos($fecha_inicio, $fecha_fin);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
  $titulo_xls = str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $tituloEstilos = array('font' => array( 'name' => 'Arial','size' => 22));

    $actualFila = 1;
    $sheetActivo->getStyle('A'.$actualFila)->applyFromArray($tituloEstilos);
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "INFORME DESCUENTOS DE ATENCIONES");	
    
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;
    
    $arregloCabecera = [
                        ["ancho"=>14,"rotulo"=>"CAJA"],
                        ["ancho"=>10,"rotulo"=>"ID RECIBO"],
                        ["ancho"=>12,"rotulo"=>"FECHA"],
                        ["ancho"=>12,"rotulo"=>"HORA"],
                        ["ancho"=>45,"rotulo"=>"PACIENTE"],
                        ["ancho"=>30,"rotulo"=>"U. REGISTRO"],
                        ["ancho"=>30,"rotulo"=>"U. VALIDADOR"],
                        ["ancho"=>45,"rotulo"=>"MOTIVO DESCUENTO"],
                        ["ancho"=>50,"rotulo"=>"SERVICIO"],
                        ["ancho"=>18,"rotulo"=>"IMPORTE TOTAL"],
                        ["ancho"=>18,"rotulo"=>"MONTO DESCUENTO"],
                        ["ancho"=>18,"rotulo"=>"MONTO CANCELADO"],
                        ["ancho"=>18,"rotulo"=>"MONTO DEUDA"],
                        ["ancho"=>15,"rotulo"=>"SEDE"],
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

    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["caja_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["id_atencion_medica"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["hora_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["paciente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["usuario_registro"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["usuario_validador"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["motivo_descuento"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["servicio_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["importe_total"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_descuento"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["importe_total"] - $registro["monto_descuento"] - $registro["monto_adeuda"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_adeuda"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["sede"]);
        

        $actualFila++;
    }

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode("descuentos_".$titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

