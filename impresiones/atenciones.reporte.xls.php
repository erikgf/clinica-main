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
  $data = $obj->listarAtencionesGeneral($fecha_inicio, $fecha_fin);

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

    $actualFila = 1;
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "INFORME DE ATENCIONES + COMPROBANTES");	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>12,"rotulo"=>"FECHA"],
                        ["ancho"=>8,"rotulo"=>"RECIBO"],
                        ["ancho"=>16,"rotulo"=>"COMPROBANTE"],
                        ["ancho"=>42,"rotulo"=>"CLIENTE"],
                        ["ancho"=>42,"rotulo"=>"PACIENTE"],
                        ["ancho"=>15,"rotulo"=>"MTO. EFECTIVO"],
                        ["ancho"=>15,"rotulo"=>"MTO. DEPÓSITO"],
                        ["ancho"=>15,"rotulo"=>"MTO. TARJETA"],
                        ["ancho"=>15,"rotulo"=>"MTO. CRÉDITO"],
                        ["ancho"=>15,"rotulo"=>"MTO. TOTAL"],
                        ["ancho"=>16,"rotulo"=>"COMPROBANTE NOTA"],
                        ["ancho"=>40,"rotulo"=>"MOTIVO NOTA"],
                        ["ancho"=>12,"rotulo"=>"ESTADO"],
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
        $anulado = $registro["estado_anulado"] == "1";
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["numero_acto_medico"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comprobante"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["cliente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["paciente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_efectivo"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_deposito"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_tarjeta"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_credito"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_total"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comprobante_nota"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["motivo_nota"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $anulado ? "ANULADO" : "ACTIVO");

        if ($anulado){
            $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[$i - 1].$actualFila)->applyFromArray($colorAnulado);
        }

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

