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
$correlativo_inicio = isset($_GET["c"]) ? $_GET["c"] : "*";

if ($fecha_inicio == NULL){
    echo "No se ha ingresado parámetro de FECHA INICIO.";
    exit;
}

if (!Funciones::checkValidDate($fecha_inicio)){
    echo "El valor de  FECHA INICIO no es válido.";
    exit;
}

if ($fecha_fin == NULL){
    echo "No se ha ingresado parámetro de FECHA FIN.";
    exit;
}

if (!Funciones::checkValidDate($fecha_fin)){
    echo "El valor de  FECHA FIN no es válido.";
    exit;
}

//Mismo mes

if (!(date("F Y", strtotime($fecha_inicio)) == date("F Y", strtotime($fecha_fin)))){
    echo "Las fechas ingresadas debe estar en el mismo MES.";
    exit;
}

require "../negocio/DocumentoElectronico.clase.php";

$titulo_xls  = "";
try {
  $obj = new DocumentoElectronico();

  $data = $obj->obtenerDatosParaExportarCONCAR($fecha_inicio, $fecha_fin, $correlativo_inicio);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }


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

    

    $directory = "./exportar-concar/";
    $fileTemplate = $directory.'main-template.xlsx';
    $newExportedFile = 'exportar-concar-'.str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin).".xlsx";

    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTemplate);
    $sheetActivo = $spreadsheet->getActiveSheet();

    $columnaInicio = 'A';
    $filaInicio = 4;

    $actualFila = $filaInicio;

    foreach ($data as $i => $registro) {
        $j = 0;
        foreach ($registro as $key => $registro_item) {
            $sheetActivo->setCellValue(getColumnaPorNumero($j).$actualFila, $registro_item);
            $j++;
        }

        $actualFila++;
    }

    /*
    $spreadsheet->setActiveSheetIndex(0);
    $sheetActivo = $spreadsheet->getActiveSheet();

    $tituloEstilos = array('font' => array( 'name' => 'Arial','size' => 22));

    $actualFila = 1;
    $sheetActivo->getStyle('A'.$actualFila)->applyFromArray($tituloEstilos);
    $sheetActivo->setCellValue('A'.$actualFila++, "REPORTE DE EXAMENES POR SEDE");
    
    $tituloEstilos = array('font' => array( 'name' => 'Arial','size' => 16));

    $timestamp_inicio = strtotime($fecha_inicio);
    $diaInicio = date("d", $timestamp_inicio);
    $anioInicio = date("Y", $timestamp_inicio);
    $mesInicio = Funciones::getMes(date("m", $timestamp_inicio));

    $timestamp_fin = strtotime($fecha_fin);
    $diaFin = date("d", $timestamp_fin);
    $anioFin = date("Y", $timestamp_fin);
    $mesFin = Funciones::getMes(date("m", $timestamp_fin));

    $sheetActivo->setCellValue('A'.$actualFila, "SEDE");	
    $sheetActivo->setCellValue('B'.$actualFila++, $id_sede === "*" ? "CHICLAYO/LAMBAYEQUE" : ($id_sede === "1" ? "CHICLAYO" : "LAMBAYEQUE"));	
    $sheetActivo->setCellValue('A'.$actualFila, "DESDE");	
    $sheetActivo->setCellValue('B'.$actualFila++, $diaInicio." DE ".$mesInicio." DEL ".$anioInicio);
    $sheetActivo->setCellValue('A'.$actualFila, "HASTA");	
    $sheetActivo->setCellValue('B'.$actualFila++, $diaFin." DE ".$mesFin." DEL ".$anioFin);

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

    */
    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($newExportedFile).'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

