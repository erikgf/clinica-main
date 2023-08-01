 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require "../negocio/Sesion.clase.php";
require "./arqueo-caja-diario/BloquePagos.php";

require "PDF.clase.php";

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficentes para ver esto.";
  exit;
}

$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_caja_instancia = isset($_GET["id"]) ? $_GET["id"] : NULL;

if ($id_caja_instancia == NULL){
    echo "No se ha recibido un ID de comprobante válido.";
    exit;
}

$FONT = isset($_GET["f"]) ? $_GET["f"] : 1;
$FONT = $FONT == 1 ? "Arial" : "Courier";

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

$empresa = F_RAZON_SOCIAL;
$ruc = "R.U.C.: ".F_RUC;
$direccion = F_DIRECCION;
$lugar = F_URBANIZACION;
$ubigeo = F_DIRECCION_DISTRITO."-".F_DIRECCION_PROVINCIA."-".F_DIRECCION_DEPARTAMENTO;
$telefono = "Telf.: ".F_TELEFONO;

require "../negocio/Caja.clase.php";

try {
  $obj = new Caja();
  $obj->id_caja_instancia = $id_caja_instancia;
  $data = $obj->obtenerFormatoArqueoCajaDiarioImpresion();

  if ($data == false){
    echo "Caja no válida.";
    exit;
  }

  $caja_instancia = $data["nombre_caja"].' '.$data["codigo"];
  $usuario_apertura = utf8_decode($data["usuario_apertura"]);
  $fecha_apertura =$data["fecha_apertura"];
  $hora_apertura = $data["hora_apertura"];
  $fecha_cierre = $data["fecha_cierre"];
  $hora_cierre = $data["hora_cierre"];

  $esta_cerrada = $data["esta_cerrada"];
  $atenciones = $data["atenciones"];
  $saldos = $data["saldos"];
  $amortizaciones = $data["amortizaciones"];
  $notasCredito =  $data["notas_credito"];
  $ingresos_tickets = $data["ingresos_tickets"];

  $egresos = $data["egresos"];

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$ALTO_LINEA = 4;
$BORDES = 0;
$SALTO_LINEA = 1.4;

$pdf = new PDF($orientation='L', $unit='mm', 'A4');


$MARGENES_LATERALES = 5.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 
$pdf->AliasNbPages();
$pdf->show_footer = true;

$pdf->AddPage();
$ANCHO_TICKET_FULL = $pdf->GetPageWidth();
$ANCHO_TICKET = $ANCHO_TICKET_FULL - ($MARGENES_LATERALES * 2);
$ANCHO_TICKET_MITAD = $ANCHO_TICKET / 2;

$aumento_font = 2;


/*Init - Zona superior */
$pdf->SetFont($FONT,'', 7 + $aumento_font); 

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("RAZÓN SOCIAL: ").$empresa,$BORDES,0);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Fecha de Impresión: ").$fecha_impresion,$BORDES,1);

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("DIRECCIÓN: ".$direccion),$BORDES,0);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("Hora de Impresión: ".$hora_impresion),$BORDES,1);

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, $ruc,$BORDES,1);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, $telefono,$BORDES,1);

/*Fin - Zona superior */
$pdf->Ln($SALTO_LINEA * 1.5);

$pdf->SetFont($FONT,'B', 12 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .5, "ARQUEO DE CAJA DIARIO".($esta_cerrada == "0" ? " (PRELIMINAR) ": ""),$BORDES,1,"C");

$pdf->Ln($SALTO_LINEA * 2.5);

/*inicio - cabecera */

$pdf->SetFont($FONT,'B', 6.5 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("CAJA: ").$caja_instancia,$BORDES,0);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("USUARIO APERTURA  :   ").$usuario_apertura,$BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("FECHA DE APERTURA :   ").$fecha_apertura,$BORDES,0);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("FECHA DE CIERRE :   ").$fecha_cierre,$BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("HORA DE APERTURA :   ").$hora_apertura,$BORDES,0);
$pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .5, utf8_decode("HORA DE CIERRE :   ").$hora_cierre,$BORDES,1);

$pdf->EXTRA_FOOTER_TEXT  = "CAJA: ".$caja_instancia; 

$pdf->Ln($SALTO_LINEA * 2);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
$pdf->Ln($SALTO_LINEA * 2);
/*fin - cabecera */

$COLS_DETALLE = [
    ["rotulo"=>"FECHA", "ancho"=>12.50, "alineacion"=>"C"],
    ["rotulo"=>"DESCRIPCIÓN", "ancho"=>81.00, "alineacion"=>"C"],
    ["rotulo"=>"DOCUMENTO", "ancho"=>25.00, "alineacion"=>"C"],
    ["rotulo"=>"COMPROBANTE", "ancho"=>25.00, "alineacion"=>"C"],
    ["rotulo"=>"EFECTIVO", "ancho"=>25, "alineacion"=>"C"],
    ["rotulo"=>"DEPÓSITO", "ancho"=>25, "alineacion"=>"C"],
    ["rotulo"=>"TARJETA", "ancho"=>25, "alineacion"=>"C"],
    ["rotulo"=>"DESCUENTO", "ancho"=>25, "alineacion"=>"C"],
    ["rotulo"=>"VUELTO", "ancho"=>25, "alineacion"=>"C"],
    ["rotulo"=>"SALDO", "ancho"=>25, "alineacion"=>"C"]
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

$ALTO_LINEA = $ALTO_LINEA + 2;

$bloquePagos = new BloquePagos($pdf, $ANCHO_TICKET, $ALTO_LINEA, $SALTO_LINEA, $FONT, $aumento_font, $BORDES, $COLS_DETALLE);

$total_monto_efectivo = 0.00;
$total_monto_deposito = 0.00;
$total_monto_tarjeta = 0.00;
$total_monto_descuento = 0.00;
$total_monto_vuelto = 0.00;
$total_monto_saldo = 0.00;

$totalAtenciones = $bloquePagos->imprimir("ATENCIONES", $atenciones);

$totalSaldos = $bloquePagos->imprimir("SALDOS", $saldos);

$totalNotas = $bloquePagos->imprimir("NOTAS DE CRÉDITO", $notasCredito);

$totalAmortizaciones = $bloquePagos->imprimir("AMORTIZACIONES", $amortizaciones);

$totalIngresosTickets = $bloquePagos->imprimir("TICKETS Y OTROS", $ingresos_tickets);

$ingresos = [];

$pdf->SetFont($FONT,'B', 5.5 + $aumento_font); 
$total_monto_efectivo_ingreso = $totalAtenciones["efectivo"] + $totalSaldos["efectivo"] + $totalNotas["efectivo"] + $totalAmortizaciones["efectivo"] + $totalIngresosTickets["efectivo"];
$total_monto_deposito_ingreso = $totalAtenciones["deposito"] + $totalSaldos["deposito"] + $totalNotas["deposito"] + $totalAmortizaciones["deposito"] + $totalIngresosTickets["deposito"];
$total_monto_tarjeta_ingreso = $totalAtenciones["tarjeta"] + $totalSaldos["tarjeta"] + $totalNotas["tarjeta"] + $totalAmortizaciones["tarjeta"] + $totalIngresosTickets["tarjeta"];
$total_monto_vuelto_ingreso = $totalAtenciones["vuelto"] + $totalSaldos["vuelto"] + $totalNotas["vuelto"] + $totalAmortizaciones["vuelto"] + $totalIngresosTickets["vuelto"];
$total_monto_saldo_ingreso = $totalAtenciones["saldo"] + $totalSaldos["saldo"] + $totalNotas["saldo"] + $totalAmortizaciones["saldo"] + $totalIngresosTickets["saldo"];

$pdf->SetFont($FONT,'B', 6.5 + $aumento_font); 
$i = 0;
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "TOTAL INGRESOS" , $BORDES,0 ,"R");
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_efectivo_ingreso,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_deposito_ingreso,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_tarjeta_ingreso,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_vuelto_ingreso,2), $BORDES,0, "C"); 
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_saldo_ingreso,2), $BORDES, 1, "C"); 

$pdf->Cell($ANCHO_TICKET, $ALTO_LINEA + .75, utf8_decode("EGRESOS"), $BORDES,1);    

$total_monto_efectivo_egreso = 0.00;

$pdf->SetFont($FONT,'', 6.5 + $aumento_font); 
foreach ($egresos as $key => $value) {
    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["fecha_registro"], $BORDES,0 ,"C");    
    $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, utf8_decode($value["descripcion"]), $BORDES,0);    
    $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["recibo"], $BORDES,0 ,"C");    
    $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["comprobante"], $BORDES,0 ,"C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($value["monto_efectivo"] * -1, 2), $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["monto_deposito"], $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["monto_tarjeta"], $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["monto_descuento"] > 0 ? "-".$value["monto_descuento"] : "0.00", $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["monto_vuelto"], $BORDES,0, "C"); 
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $value["monto_saldo"], $BORDES, 1, "C");    

    $total_monto_efectivo_egreso +=$value["monto_efectivo"];
}

$pdf->SetFont($FONT,'B', 6.5 + $aumento_font); 
$i = 0;
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "TOTAL EGRESOS" , $BORDES,0 ,"R");
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, $total_monto_efectivo_egreso > 0 ? "-".number_format($total_monto_efectivo_egreso, 2): "0.00", $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, "C"); 
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES, 1, "C"); 

$pdf->Ln($SALTO_LINEA  * 2.5);

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
$pdf->Ln($SALTO_LINEA);

$pdf->SetFont($FONT,'B', 8 + $aumento_font); 

foreach ($COLS_DETALLE as $key => $value) {
    if ($key < ($NUMERO_COLS - 1)){ 
        if ($key <= 3){
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, "", $BORDES,0, $value["alineacion"]);        
        } else {
            $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES,0, $value["alineacion"]);    
        }
    } else {
        $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES, 1, $value["alineacion"]);    
    }
}

$i = 0;
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, utf8_decode("TOTALES"), $BORDES,0 ,"R");
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_efectivo + $total_monto_efectivo_ingreso - $total_monto_efectivo_egreso ,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_deposito + $total_monto_deposito_ingreso ,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_tarjeta + $total_monto_tarjeta_ingreso ,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_descuento ,2), $BORDES,0, "C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_vuelto + $total_monto_vuelto_ingreso ,2), $BORDES,0, "C"); 
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($total_monto_saldo + $total_monto_saldo_ingreso ,2), $BORDES, 1, "C"); 

$pdf->Ln($SALTO_LINEA);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
$pdf->Ln($SALTO_LINEA * 2);


$pdf->SetFont($FONT,'B', 12 + $aumento_font);

$balance_general = $total_monto_efectivo + $total_monto_efectivo_ingreso - $total_monto_efectivo_egreso +  
                    $total_monto_deposito + $total_monto_deposito_ingreso + 
                    $total_monto_tarjeta + $total_monto_tarjeta_ingreso +
                    $total_monto_saldo + $total_monto_saldo_ingreso;

$i = 0;
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, "", $BORDES,0);    
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, utf8_decode("BALANCE TOTAL"), $BORDES,0 ,"R");
$pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + .5, number_format($balance_general,2), $BORDES,0, "C");    

$pdf->output();
ob_end_flush();
exit;