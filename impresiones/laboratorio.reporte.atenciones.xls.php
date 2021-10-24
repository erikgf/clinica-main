<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$objUsuario = Sesion::obtenerSesion();
if (!$objUsuario){
  echo "No tiene permisos suficentes para ver esto.";
  exit;
}

$login = $objUsuario["nombre_usuario"];

require_once "../views/pages/Template.php";
$objTemplate = new Template();
if (!$objTemplate->validarPermisoRoles($objUsuario, [$objTemplate->ID_ROL_COORDINADOR_LABORATORIO, $objTemplate->ID_ROL_ADMINISTRADOR, $objTemplate->ID_ROL_LOGISTICA])){
    echo "No tiene permisos suficientes para ver esto.";
    exit;
}

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

$titulo_xls  = "";

require "../negocio/AtencionMedicaServicio.clase.php";

try {
  $obj = new AtencionMedicaServicio();
  $data = $obj->obtenerReporteExamenesRealizado($fecha_inicio, $fecha_fin);

  $titulo_xls = "REPORTE_LAB".str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin);
  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }

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
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "REPORTE RESUMIDO DE ATENCIONES");	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>25,"rotulo"=>"SECCION"],
                        ["ancho"=>60,"rotulo"=>"NOMBRE DEL EXAMEN"],
                        ["ancho"=>16,"rotulo"=>"COSTO UNIT"],
                        ["ancho"=>16,"rotulo"=>"CANTIDAD"],
                        ["ancho"=>16,"rotulo"=>"IMPORTE TOTAL"]
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

    foreach ($data as $key => $seccion) {
        $nombre_seccion = $seccion["seccion"];
        $examenes = $seccion["examenes"];
        foreach ($examenes as $key => $registro) {
            $i = 0;
            $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $nombre_seccion);
            $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["nombre_servicio"]);
            $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["precio_unitario"]);
            $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["cantidad"]);
            $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["precio_unitario"] * $registro["cantidad"]);

            $actualFila++;
        }
    }

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

