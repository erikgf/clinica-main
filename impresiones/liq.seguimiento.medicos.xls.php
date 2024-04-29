<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
include_once "../negocio/util/Funciones.php";

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficientes para ver esto.";
  exit;
}
$login = Sesion::obtenerSesion()["nombre_usuario"];

$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;
$monto =  isset($_GET["m"]) ? $_GET["m"] : 0.00;
$areas =  isset($_GET["area"]) ? $_GET["area"] : '[*]';
$promotoras =  isset($_GET["promo"]) ? $_GET["promo"] : '[*]';
$sedes =  isset($_GET["sede"]) ? $_GET["sede"] : '[*]';

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
  $data = $obj->listarLiquidacionesSeguimientoMedico($fecha_inicio, $fecha_fin,json_decode($sedes), json_decode($promotoras), json_decode($areas), $monto);

  if (count($data["data"]) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
  $titulo_xls = "LIQ_SGTO_MEDICOS_".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

function getColumnaPorNumero(int $columna){
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $limiteAlfabeto = strlen($alfabeto); // 26

    if ($columna < $limiteAlfabeto){
        return $alfabeto[$columna];
    }

    $primerColumna =  (int) floor($columna/$limiteAlfabeto) - 1;
    $segundaColumna = $columna - $limiteAlfabeto;
    return $alfabeto[$primerColumna].$alfabeto[$segundaColumna];
}

try {
    $spreadsheet = new Spreadsheet();

    $spreadsheet->setActiveSheetIndex(0);
    //$alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $sheetActivo = $spreadsheet->getActiveSheet();

    $sheetActivo->setCellValue('A1', "REPORTE GENERAL DE SEGUIMIENTO DE MEDICOS");

    $cabeceraEstilos = array('font' => array('name' => 'Arial Narrow','size' => 20),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );
    $sheetActivo->getStyle('A1')->applyFromArray($cabeceraEstilos);
    $sheetActivo->mergeCells('A1:E1');

    $timestamp_inicio = strtotime($fecha_inicio);
    $diaInicio = date("d", $timestamp_inicio);
    $anioInicio = date("Y", $timestamp_inicio);
    $mesInicio = Funciones::getMes(date("m", $timestamp_inicio));

    $timestamp_fin = strtotime($fecha_fin);
    $diaFin = date("d", $timestamp_fin);
    $anioFin = date("Y", $timestamp_fin);
    $mesFin = Funciones::getMes(date("m", $timestamp_fin));


    $sheetActivo->setCellValue('A2', "FECHA");
    $sheetActivo->setCellValue('B2', "DEL ".$diaInicio." DE ".$mesInicio." DEL ".$anioInicio." AL ".$diaFin." DE ".$mesFin." DEL ".$anioFin."");
    $sheetActivo->mergeCells('B2:E2');

    $sheetActivo->setCellValue('A3', "PROMOTOR");
    $sheetActivo->setCellValue('B3', $data["promotoras"]);
    $sheetActivo->mergeCells('B3:E3');

    $sheetActivo->setCellValue('A4', "MONTO");
    $sheetActivo->setCellValue('B4', "TOTAL O MAYOR A ".$monto);
    $sheetActivo->mergeCells('B4:E4');

    $sheetActivo->setCellValue('A5', "ÁREA");
    $sheetActivo->setCellValue('B5', $data["areas"]);
    $sheetActivo->mergeCells('B5:E5');

    $sheetActivo->setCellValue('A6', "SEDE");
    $sheetActivo->setCellValue('B6', $data["sedes"]);
    $sheetActivo->mergeCells('B6:E6');

    $actualFila = 7;

    $arregloCabecera = [
                        ["ancho"=>40,"rotulo"=>"APELLIDOS NOMBRES"],
                        ["ancho"=>40,"rotulo"=>"PROMOTOR"],
                    ];

    foreach ($data["fechas"] as $key => $value) {
        array_push($arregloCabecera, [
            "ancho"=>16, "rotulo"=>$value
        ]);
    }

    foreach ($arregloCabecera as $key => $value) {
        $columna = getColumnaPorNumero($key);
        $sheetActivo->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheetActivo->getColumnDimension($columna)->setWidth($value["ancho"]);
    }
    
    $subCabeceraEstilos = array('font' => array('name' => 'Arial Narrow','size' => 14),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );
    $sheetActivo->getStyle('A2:E6')->applyFromArray($subCabeceraEstilos);


    $dataRegistros = $data["data"];
    $fechasTemp;
   // $actualFila++;

    $last_medico = NULL;
    $i = 0;

    foreach ($dataRegistros as $key => $registro) {
        $medico = $registro["medico"];
        if ($last_medico == NULL ||  $last_medico != $medico){
            $i = 0;
            $actualFila++;

            $promotora = $registro["promotora"];
            $fechasTemp = $data["fechas"];

            $sheetActivo->setCellValue(getColumnaPorNumero($i++).$actualFila, $medico);
            $sheetActivo->setCellValue(getColumnaPorNumero($i++).$actualFila, $promotora);

            //var_dump("medico, promo, ondex set ", $actualFila, "med ", $medico);
           // echo "</br>";
        }

        $mes_anio_atencion = $registro["mes_anio_atencion"];

        foreach ($fechasTemp as $_key => $value) {
            if ($value == $mes_anio_atencion){
                $sheetActivo->setCellValue(getColumnaPorNumero($i++).$actualFila, $registro["comision_sin_igv"]);
                array_splice($fechasTemp, 0, $_key + 1);
                break;
            }
            $sheetActivo->setCellValue(getColumnaPorNumero($i++).$actualFila, "");
        }

        $last_medico = $medico;
    }
    
    $cabeceraEstilos = array('font' => array('bold'=>true, 'name' => 'Arial Narrow','size' => 12),
                                'alignment' => array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                );
    $sheetActivo->getStyle('A7:'.getColumnaPorNumero($i++).'E7')->applyFromArray($cabeceraEstilos);
    
    $spreadsheet->getActiveSheet()->setTitle("LIQ_SGTO_MEDICOS");

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}