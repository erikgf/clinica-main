 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require "../negocio/Sesion.clase.php";
require '../negocio/Globals.clase.php';
require "../negocio/util/Funciones.php";
require "PDF.clase.php";

$objUsuario = Sesion::obtenerSesion();

if (!$objUsuario){
    echo "No tiene permisos suficentes para ver esto.";
    exit;
}

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR, Globals::$ID_ROL_PROMOTORA])){
    echo "No tiene permisos para ver esto.";
    exit;
}

$login = $objUsuario["nombre_usuario"];

$id_promotora = NULL;
if (in_array($objUsuario["id_rol"], [Globals::$ID_ROL_PROMOTORA])){
    require "../negocio/Promotora.clase.php";
    $objPromotora = new Promotora();
    $objPromotora->id_usuario = $objUsuario["id_usuario_registrado"];
    $promotora = $objPromotora->getPromotoraFromUsuario();
    $id_promotora = $promotora["id_promotora"];
} else {
    $id_promotora = isset($_GET["idp"]) ? $_GET["idp"] : NULL;
}

$mes = isset($_GET["m"]) ? $_GET["m"] : NULL;
$año = isset($_GET["a"]) ? $_GET["a"] : NULL;

if ($mes == NULL){
    echo "No se ha ingresado parámetro de MES";
    exit;
}

if ($año == NULL){
    echo "No se ha ingresado parámetro de AÑO";
    exit;
}

$FONT = isset($_GET["f"]) ? $_GET["f"] : 1;
$FONT = $FONT == 1 ? "Arial" : "Courier";

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/Liquidacion.clase.php";

try {
  $obj = new Liquidacion();
  $data = $obj->obtenerLiquidacionesImprimir($id_promotora, $mes, $año);

  if (count($data) <= 0){
    echo "No se han encontrado LIQUIDACIONES calculadas.";
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

foreach ($data as $key => $liquidacion_sede) {
    $pdf->AddPage();
    /*Init - Zona superior */
    $pdf->SetFont($FONT,'', 5 + $aumento_font); 

    $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Fecha de Impresión: ").$fecha_impresion,$BORDES,1);
    $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Hora de Impresión: ".$hora_impresion),$BORDES,1);

    /*Fin - Zona superior */

    $ALTO_LINEA = 5.5;
    $SALTO_LINEA = 2;

    $pdf->Ln($SALTO_LINEA * 1.5);

    $pdf->SetFont($FONT,'', 9.5+ $aumento_font); 
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + 1, utf8_decode("INFORME DE LIQUIDACIÓN DE MÉDICOS"),$BORDES,1,"C");

    $pdf->SetFont($FONT,'', 11.5+ $aumento_font); 
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + 1, utf8_decode($liquidacion_sede["sede"]),$BORDES,1,"C");

    $pdf->SetFont($FONT,'', 11.5+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode($liquidacion_sede["nombre_promotora"]),$BORDES,1,"C");

    $pdf->SetFont($FONT,'B', 7.5+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, "DEL ".$liquidacion_sede["fecha_inicio"]." AL ".$liquidacion_sede["fecha_fin"],$BORDES,1,"C");

    $pdf->Ln($SALTO_LINEA * 1.5);
    /*inicio - cabecera */
    $pdf->Ln($SALTO_LINEA * 2);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA * 2);
    /*fin - cabecera */
    $COLS_DETALLE = [
        ["rotulo"=>"CÓDIGO", "ancho"=>12.00, "alineacion"=>"C"],
        ["rotulo"=>"APELLIDOS Y NOMBRES MÉDICO", "ancho"=>105.00, "alineacion"=>"L"],
        ["rotulo"=>"TOTAL SIN IGV", "ancho"=>28.00, "alineacion"=>"R"],
        ["rotulo"=>"COMISIÓN SIN IGV", "ancho"=>28.00, "alineacion"=>"R"],
        ["rotulo"=>"CANT. SERVICIOS", "ancho"=>25.00, "alineacion"=>"R"],
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

    $registros = $liquidacion_sede["medicos"];

    $total_comisiones = 0.00;
    $total_medicos = count($registros);
    $total_subtotales =0.00;
    $comision_promotora = $liquidacion_sede["porcentaje_comision"];

    foreach ($registros as $key => $value) {
        $i = 0;
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $value["codigo"], $BORDES,0 ,"C");    
        $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($value["medico"]), $BORDES,0);    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".number_format(round($value["monto_sin_igv"], 2),2), $BORDES,0 ,"R");   
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".number_format(round($value["comision_sin_igv"], 2),2), $BORDES,0 ,"R");   
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $value["cantidad_servicios"], $BORDES,1 ,"R");   

        $total_comisiones +=$value["comision_sin_igv"];
        $total_subtotales += $value["monto_sin_igv"];
    }

    $pdf->Ln($SALTO_LINEA);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA);

    $pdf->SetFont($FONT,'B', 8.55 + $aumento_font); 
    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, utf8_decode("Total Médicos: ".$total_medicos), $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "S/ ".number_format(round($total_subtotales,2),2) , $BORDES,0 ,"R");
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".number_format(round($total_comisiones,2),2), $BORDES,0, "R");    

    $pdf->Ln($SALTO_LINEA * 6);


    $ALTO_LINEA += 2;

    $COLS_DETALLE = [
        ["rotulo"=>"PAGO DE PROMOTORA", "ancho"=>80.00, "alineacion"=>"C"],
        ["rotulo"=>"TOTAL", "ancho"=>30.00, "alineacion"=>"C"],
        ["rotulo"=>"COMISIÓN", "ancho"=>30.00, "alineacion"=>"C"]
    ];
    $NUMERO_COLS = count($COLS_DETALLE);

    $pdf->SetFont($FONT,'B',7.55 + $aumento_font); 

    foreach ($COLS_DETALLE as $key => $value) {
        if ($key < ($NUMERO_COLS - 1)){
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), 1,0, $value["alineacion"]);    
        } else {
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), 1, 1, $value["alineacion"]);    
        }
    }

    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, $comision_promotora." %" , 1,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "S/ ".number_format(round($total_subtotales,2),2) , 1,0 ,"C");
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".number_format(round($total_subtotales * $comision_promotora / 100,2),2), 1,1, "C");    

    $pdf->Ln($SALTO_LINEA);
}


$pdf->output();
ob_end_flush();
exit;