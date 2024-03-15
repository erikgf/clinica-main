<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/

error_reporting(E_ALL);
ini_set('display_errors', 1);
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
$id_medico_realizante = isset($_GET["mr"]) ? $_GET["mr"] : NULL;
$id_medico_atendido = isset($_GET["ma"]) ? $_GET["ma"] : NULL;
$area = isset($_GET["a"]) ? $_GET["a"] : NULL;
$estado = isset($_GET["est"]) ? $_GET["est"] : "*";

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

require "../negocio/AtencionMedicaServicio.clase.php";

$titulo_xls  = "";
try {
  $obj = new AtencionMedicaServicio();
  $obj->id_medico_realizante = $id_medico_realizante;
  $obj->id_medico_atendido = $id_medico_atendido;
  $obj->fue_atendido = $estado;
  $data = $obj->listarExamenesAdministrador($fecha_inicio, $fecha_fin, $area);

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
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "REPORTE DE EXÁMENES");	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>12,"rotulo"=>"ESTADO"],
                        ["ancho"=>12,"rotulo"=>"FECHA"],
                        ["ancho"=>8,"rotulo"=>"RECIBO"],
                        ["ancho"=>42,"rotulo"=>"PACIENTE"],
                        ["ancho"=>16,"rotulo"=>"AREA"],
                        ["ancho"=>30,"rotulo"=>"EXAMEN"],
                        ["ancho"=>15,"rotulo"=>"MONTO EXAMEN"],
                        ["ancho"=>15,"rotulo"=>"MONTO RECIBO"],
                        ["ancho"=>15,"rotulo"=>"DEUDA"],
                        ["ancho"=>15,"rotulo"=>"MEDIO PAGO"],
                        ["ancho"=>15,"rotulo"=>"FECHA REALIZADO"],
                        ["ancho"=>15,"rotulo"=>"HORA REALIZADO"],
                        ["ancho"=>30,"rotulo"=>"MÉDICO REALIZANTE"],
                        ["ancho"=>30,"rotulo"=>"MÉDICO INFORMANTE"],
                        ["ancho"=>30,"rotulo"=>"OBSERVACIONES"],
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

    $colorCancelado = array('font' => array('color' => ['argb' => 'EB2B02']));
    $colorRealizado = array('font' => array('color' => ['argb' => '1b663e']));




    foreach ($data as $key => $registro) {
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["rotulo_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atencion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["recibo"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["nombre_paciente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["area"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["examen"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_examen"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["monto_deuda"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["metodo_pago"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["hora_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico_realizado"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["medico_atendido"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["observaciones_atendido"]);

        if ($registro["fue_atendido"] == "1"){
            $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[$i - 1].$actualFila)->applyFromArray($colorRealizado);
        } else if($registro["fue_atendido"] == "2"){
            $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[$i - 1].$actualFila)->applyFromArray($colorCancelado);
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

