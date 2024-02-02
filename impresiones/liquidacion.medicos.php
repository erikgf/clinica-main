 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require '../negocio/util/Funciones.php';
//require '../plugins/bs4/vendor/autoload.php';
require "../negocio/Sesion.clase.php";
//require "TicketPDF_AutoPrint.clase.php";
require "PDF.clase.php";

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficentes para ver esto.";
  exit;
}

$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_medico = isset($_GET["idm"]) ? $_GET["idm"] : "";
$fecha_inicio = isset($_GET["fi"]) ? $_GET["fi"] : NULL;
$fecha_fin = isset($_GET["ff"]) ? $_GET["ff"] : NULL;
$totales_mayores_a =  isset($_GET["tt"]) ? $_GET["tt"] : "0";
$id_sede =  isset($_GET["s"]) ? $_GET["s"] : "";

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

require "../negocio/Medico.clase.php";

try {
  $obj = new Medico();
  $data = $obj->listarLiquidacionesMedicosImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);

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
$aumento_font = 2.6;
$BORDES = 0;

$ALTO_LINEA = 4;
$SALTO_LINEA = 1.6;

$ANCHO_TICKET_FULL = $pdf->GetPageWidth();
$ANCHO_TICKET = $ANCHO_TICKET_FULL - ($MARGENES_LATERALES * 2);
$ANCHO_TICKET_MITAD = $ANCHO_TICKET / 2;

foreach ($data as $key => $sede) {
    $pdf->AddPage();
    /*Init - Zona superior */
    $pdf->SetFont($FONT,'', 5 + $aumento_font); 

    $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Fecha de Impresión: ").$fecha_impresion,$BORDES,1);
    $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Hora de Impresión: ".$hora_impresion),$BORDES,1);

    /*Fin - Zona superior */

    $ALTO_LINEA = 5.5;
    $SALTO_LINEA = 2;

    $pdf->Ln($SALTO_LINEA * 1.5);

    $pdf->SetFont($FONT,'', 10.5+ $aumento_font); 
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode("INFORME DE LIQUIDACIÓN DE MÉDICOS"),$BORDES,1,"C");
    $pdf->SetFont($FONT,'', 12.5+ $aumento_font); 
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode($sede["sede"]),$BORDES,1,"C");

    $pdf->SetFont($FONT,'B', 7.5+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, "DEL ".$fecha_inicio." AL ".$fecha_fin,$BORDES,1,"C");

    $pdf->Ln($SALTO_LINEA * 1.5);
    /*inicio - cabecera */
    $pdf->Ln($SALTO_LINEA * 2);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA * 2);
    /*fin - cabecera */
    $COLS_DETALLE = [
        ["rotulo"=>"CÓDIGO", "ancho"=>16.00, "alineacion"=>"C"],
        ["rotulo"=>"APELLIDOS Y NOMBRES", "ancho"=>120.00, "alineacion"=>"L"],
        ["rotulo"=>"TOTAL", "ancho"=>55.00, "alineacion"=>"R"],
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
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES,0, $value["alineacion"]);    
        } else {
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES, 1, $value["alineacion"]);    
        }
    }

    $pdf->Ln($SALTO_LINEA);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA);

    $pdf->SetFont($FONT,'', 6.5 + $aumento_font); 
    $ALTO_LINEA = $ALTO_LINEA + 2;

    $total_comisiones = 0.00;

    $medicos = $sede["medicos"];
    foreach ($medicos as $key => $value) {
        $i = 0;
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $value["codigo"], $BORDES,0 ,"C");    
        $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($value["medicos"]), $BORDES,0);    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".round($value["comision_sin_igv"], 2), $BORDES,1 ,"R");   

        $total_comisiones +=$value["comision_sin_igv"];
    }

    $pdf->Ln($SALTO_LINEA);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA);

    $pdf->SetFont($FONT,'B', 8.55 + $aumento_font); 
    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, utf8_decode("TOTAL LIQUIDACIONES") , $BORDES,0 ,"R");
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".round($total_comisiones,3), $BORDES,0, "R");    

    $pdf->Ln($SALTO_LINEA);
}

        

$pdf->output();
ob_end_flush();
exit;