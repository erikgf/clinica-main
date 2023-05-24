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
$id_sede = isset($_GET["s"]) ? $_GET["s"] : "*";
$id_estado = isset($_GET["e"]) ? $_GET["e"] : "*";
$id_areas = isset($_GET["ar"]) ? $_GET["ar"] : "[*]";

if ($fecha_inicio == NULL){
    echo "No se ha ingresado parámetro de FECHA FIN";
    exit;
}

if ($fecha_fin == NULL){
    echo "No se ha ingresado parámetro de FECHA FIN";
    exit;
}
$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/AtencionMedicaServicio.clase.php";

$titulo_xls  = "";
try {
  $obj = new AtencionMedicaServicio();

  $data = $obj->listarExamenesAtencionesPorSede($fecha_inicio, $fecha_fin, $id_estado, json_decode($id_areas), $id_sede);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }

  $titulo_xls = "RPTE_ATN_SEDES_".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);

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

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

