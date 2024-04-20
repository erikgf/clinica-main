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

  $data = $obj->obtenerDatosParaExportarCancelacionesVentaCONCAR($fecha_inicio, $fecha_fin, $correlativo_inicio);

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
    $newExportedFile = 'exportar-concar-cancelacionesventa-'.str_replace("-","",$fecha_inicio).str_replace("-","",$fecha_fin).".xlsx";

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

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($newExportedFile).'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

