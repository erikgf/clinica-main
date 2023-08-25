<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../plugins/phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$RUTA_SERVER = "http://192.168.1.100/sistema_dpi/controlador/api.documento.electronico.controlador.php";
//$RUTA_SERVER = "http://localhost/dpi/controlador/api.documento.electronico.controlador.php";

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

$titulo_xls  = "";
try {

  $data_json = json_encode( ["p_fecha_inicio"=>$fecha_inicio, "p_fecha_fin"=>$fecha_fin]);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $RUTA_SERVER."?op=listar_atenciones_comprobantes");
  curl_setopt(
      $ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
      )
  );
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $respuesta  = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($respuesta, true);

  if (is_string($data)){
      throw new Exception($data);
  }

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }

  $series = $data["series"];
  $totales = $data["totales"];
  $comprobantes =  $data["todos_comprobantes"];

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
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Reporte COMPROBANTES");	
    $sheetActivo->setCellValue($alfabeto[0].$actualFila++, "Fechas : DEL ".$fecha_inicio." AL ".$fecha_fin);	

    $actualFila++;

    $arregloCabecera = [
                        ["ancho"=>8,"rotulo"=>"Tipo Doc"],
                        ["ancho"=>12,"rotulo"=>"Serie Doc"],
                        ["ancho"=>13,"rotulo"=>"Numero Doc"],
                        ["ancho"=>13,"rotulo"=>"Fecha Doc"],
                        ["ancho"=>42,"rotulo"=>"Nombre o Razón Social"],
                        ["ancho"=>13,"rotulo"=>"R.U.C."],
                        ["ancho"=>13,"rotulo"=>"Condición Pago"],
                        ["ancho"=>13,"rotulo"=>"Fecha Vence"],
                        ["ancho"=>12,"rotulo"=>"Forma Pago"],
                        ["ancho"=>13,"rotulo"=>"Tipo de Tarjeta"],
                        ["ancho"=>18,"rotulo"=>"Banco"],
                        ["ancho"=>16,"rotulo"=>"N° Operación"],
                        ["ancho"=>14,"rotulo"=>"Tasa IGV %"],
                        ["ancho"=>14,"rotulo"=>"Sub Total"],
                        ["ancho"=>14,"rotulo"=>"I.G.V."],
                        ["ancho"=>14,"rotulo"=>"Total Importe"],
                        ["ancho"=>12,"rotulo"=>"Estado"],
                        ["ancho"=>15,"rotulo"=>"Fecha Doc Modifica"],
                        ["ancho"=>12,"rotulo"=>"Tipo Doc Modificado"],
                        ["ancho"=>12,"rotulo"=>"Serie Doc Modifica"],
                        ["ancho"=>15,"rotulo"=>"Numero Doc Modifica"]
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

    $primeraFila = $actualFila;
    foreach ($comprobantes as $key => $registro) {
        $anulado = $registro["estado_anulado"] == "1";
        $i = 0;
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["idtipo_comprobante"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["serie"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["comprobante"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_emision"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["cliente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["numero_documento_cliente"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, "Contado");
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_exportacion"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["metodo_pago"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["tipo_tarjeta"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["codigo_entidad"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["numero_operacion_banco"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["porcentaje_igv"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["total_gravadas"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["total_igv"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["importe_total"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, ($anulado ? "Anulado" : "Emitido"));
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["fecha_modificado"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["td_modifica"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["serie_modifica"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, $registro["correlativo_modifica"]);
        $sheetActivo->setCellValue($alfabeto[$i++].$actualFila, "02");
        if ($anulado){
            $sheetActivo->getStyle('A'.$actualFila.':'.$alfabeto[$i - 1].$actualFila)->applyFromArray($colorAnulado);
        }

        $actualFila++;
       // var_dump('<pre>',$registro,'</pre>'); exit;
    }

    $celda_gravadas = "N";
    $celda_igv = "O";
    $celda_total = "P";

    $ultimaFila = $actualFila;
    $sheetActivo->setCellValue($celda_gravadas.$actualFila, "=SUM(".$celda_gravadas.$primeraFila.":".$celda_gravadas.$ultimaFila.")");
    $sheetActivo->setCellValue($celda_igv.$actualFila, "=SUM(".$celda_igv.$primeraFila.":".$celda_igv.$ultimaFila.")");
    $sheetActivo->setCellValue($celda_total.$actualFila, "=SUM(".$celda_total.$primeraFila.":".$celda_total.$ultimaFila.")");

    $spreadsheet->getActiveSheet()->setTitle($titulo_xls);

    $writer = new Xlsx($spreadsheet);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="'. urlencode($titulo_xls.".xlsx").'"');
    $writer->save('php://output');

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

