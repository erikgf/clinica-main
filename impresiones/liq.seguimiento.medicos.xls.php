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
$monto =  isset($_GET["monto"]) ? $_GET["monto"] : 0.00;

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
  $data = $obj->listarLiquidacionesSeguimientoMedico($fecha_inicio, $fecha_fin, $monto);

  if (count($data["data"]) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
  $titulo_xls = "LIQ_SGTO_MEDICOS_".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $sheetActivo->setCellValue('A1', "REPORTE GENERAL DE SEGUIMIENTO DE MEDICOS");

    $sheetActivo->setCellValue('A2', "FECHA");
    $sheetActivo->setCellValue('B2', "DEL x al y");
    $sheetActivo->setCellValue('A3', "PROMOTOR");
    $sheetActivo->setCellValue('B3', "SUSANA/JUAN CARLOS/DMI/NO TIENE/TODOS");
    $sheetActivo->setCellValue('A4', "MONTO");
    $sheetActivo->setCellValue('B4', "TOTAL O MAYOR A ".$monto);
    $sheetActivo->setCellValue('A5', "ÁREA");
    $sheetActivo->setCellValue('B5', "ECO/DENSI/MAMO/RESO/TOMO/LAB/BIOPSIA/RAYOS X/OTROS");
    $sheetActivo->setCellValue('A6', "SEDE");
    $sheetActivo->setCellValue('B6', "CHICLAYO/LAMBAYEQUE");

    $actualFila = 7;

    $arregloCabecera = [
                        ["ancho"=>45,"rotulo"=>"APELLIDOS NOMBRES"],
                        ["ancho"=>60,"rotulo"=>"PROMOTOR"],
                    ];

    foreach ($data["fechas"] as $key => $value) {
        array_push($arregloCabecera, [
            "ancho"=>16, "rotulo"=>$value
        ]);
    }

    foreach ($arregloCabecera as $key => $value) {
        $columna = $alfabeto[$key];
        $sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
    }
    
    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );

    $sheetActivo->getStyle('A'.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);


    $dataRegistros = $data["data"];

    /*
    $actualFila++;

    foreach ($data as $key => $registro) {
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["id_atencion_medica"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_descuento"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["importe_total"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["usuario_registro"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["usuario_validador"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["paciente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["servicio_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["sede"]);

        $actualFila++;
    }
    */

    $spreadsheet->getActiveSheet()->setTitle("LIQ_SGTO_MEDICOS");

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}