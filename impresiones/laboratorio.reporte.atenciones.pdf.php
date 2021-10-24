 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
//require '../plugins/bs4/vendor/autoload.php';
require "../negocio/Sesion.clase.php";
//require "TicketPDF_AutoPrint.clase.php";
require "PDF.clase.php";

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

$FONT = isset($_GET["f"]) ? $_GET["f"] : 1;
$FONT = $FONT == 1 ? "Arial" : "Courier";

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/AtencionMedicaServicio.clase.php";

try {
  $obj = new AtencionMedicaServicio();
  $data = $obj->obtenerReporteExamenesRealizado($fecha_inicio, $fecha_fin);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$pdf = new PDF($orientation='P', $unit='mm', 'A4');

$MARGENES_LATERALES = 5.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 
$pdf->AliasNbPages();
//$pdf->show_footer = true;
$aumento_font = 1.5;
$BORDES = 0;

$ALTO_LINEA = 2.5;
$SALTO_LINEA = 1.5;

$ANCHO_TICKET_FULL = $pdf->GetPageWidth();
$ANCHO_TICKET = $ANCHO_TICKET_FULL - ($MARGENES_LATERALES * 2);
$ANCHO_TICKET_MITAD = $ANCHO_TICKET / 2;

$pdf->AddPage();
/*Init - Zona superior */
$pdf->SetFont($FONT,'', 5 + $aumento_font); 

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Fecha de Impresión: ").$fecha_impresion,$BORDES,1);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Hora de Impresión: ".$hora_impresion),$BORDES,1);

/*Fin - Zona superior */

$ALTO_LINEA = 3.5;
$SALTO_LINEA = 3.5;

$pdf->Ln($SALTO_LINEA * 1.5);

$pdf->SetFont($FONT,'', 10.5+ $aumento_font); 
$pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode("REPORTE RESUMIDO DE ATENCIONES"),$BORDES,1,"L");

$pdf->SetFont($FONT,'B', 7.5+ $aumento_font);
$pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, "DEL ".$fecha_inicio." AL ".$fecha_fin,$BORDES,1,"L");

$pdf->Ln($SALTO_LINEA * 1.5);

$COLS_DETALLE = [
    ["rotulo"=>utf8_decode("SECCION"), "ancho"=>35.00, "alineacion"=>"L"],
    ["rotulo"=>utf8_decode("NOMBRE DEL EXAMEN"), "ancho"=>105.00, "alineacion"=>"L"],
    ["rotulo"=>utf8_decode("COSTO UNIT."), "ancho"=>20.00, "alineacion"=>"R"],
    ["rotulo"=>utf8_decode("CANTIDAD"), "ancho"=>20.00, "alineacion"=>"R"],
    ["rotulo"=>utf8_decode("IMPORTE TOTAL"), "ancho"=>55.00, "alineacion"=>"R"],
];
$NUMERO_COLS = count($COLS_DETALLE);

$acumulado_cols_detalle = 0.00;
foreach ($COLS_DETALLE as $key => $value) {
    if ($key < ($NUMERO_COLS - 1)){
        $acumulado_cols_detalle += $value["ancho"];
    }
}

$COLS_DETALLE[$NUMERO_COLS - 1]["ancho"] = $ANCHO_TICKET - $acumulado_cols_detalle;
$ALTO_LINEA = 1.00;

$pdf->SetFont($FONT,'B', 5.25 + $aumento_font);

foreach ($COLS_DETALLE as $key => $value) {
    if ($key < ($NUMERO_COLS - 1)){
        $pdf->Cell($value["ancho"], $ALTO_LINEA + 3.5, utf8_decode($value["rotulo"]), $BORDES,0, $value["alineacion"]);    
    } else {
        $pdf->Cell($value["ancho"], $ALTO_LINEA + 3.5, utf8_decode($value["rotulo"]), $BORDES, 1, $value["alineacion"]);    
    }
}

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
$pdf->Ln($SALTO_LINEA *.75);

//Inicio de registros.
$ALTO_LINEA = $ALTO_LINEA + 2;



foreach ($data as $key => $value) {
    $nombre_seccion = $value["seccion"];
    $examenes = $value["examenes"];
    $pdf->SetFont($FONT,'B', 6.25 + $aumento_font);

    $pdf->Cell($COLS_DETALLE[0]["ancho"], $ALTO_LINEA + 1.5, $nombre_seccion, $BORDES, 1, "L");    

    $pdf->SetFont($FONT,'', 5.5 + $aumento_font); 

    $total_cantidad = 0;
    $total_importe_total = 0.00;

    foreach ($examenes as $key => $value) {
        $i = 0;
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "", $BORDES,0 ,"C");    
        $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($value["nombre_servicio"]), $BORDES,0);    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".number_format(round($value["precio_unitario"], 3),2), $BORDES,0,"R");    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $value["cantidad"], $BORDES, 0 ,"R"); 
        $importe_total = $value["cantidad"] * $value["precio_unitario"];
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".number_format(round($importe_total, 3),2), $BORDES, 1 ,"R");   
    
        $total_cantidad += $value["cantidad"];
        $total_importe_total += $importe_total;
    }

    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA * .5);

    $pdf->SetFont($FONT,'B', 5.55 + $aumento_font); 
    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, utf8_decode("TOTAL SECCIÓN") , $BORDES,0 ,"R");
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "", $BORDES,0, "R");   
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  $total_cantidad, $BORDES,0, "R");   
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".number_format(round($total_importe_total, 3),2), $BORDES, 1, "R");    

}


$pdf->Ln($SALTO_LINEA);

$pdf->output();
ob_end_flush();
exit;