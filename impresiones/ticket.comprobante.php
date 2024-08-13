 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
//require '../plugins/bs4/vendor/autoload.php';
require "../negocio/Sesion.clase.php";
//require "TicketPDF_AutoPrint.clase.php";
require "PDF.clase.php";
require "../plugins/bigfish-pdf417/vendor/autoload.php";

use BigFish\PDF417\PDF417;
use BigFish\PDF417\Renderers\ImageRenderer;
use BigFish\PDF417\Renderers\SvgRenderer;

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficentes para ver esto.";
  exit;
}

$login = utf8_decode(Sesion::obtenerSesion()["nombre_usuario"]);

$id = isset($_GET["id"]) ? $_GET["id"] : NULL;

if ($id == NULL){
   echo "No se ha recibido un ID de comprobante válido.";
   exit;
}

$fecha_impresion = date('d/m/Y');
$fecha_qr =  date('Y-m-d');

$empresa = F_RAZON_SOCIAL_IMPRESIONES;
$ruc = "R.U.C.: ".F_RUC;
$direccion = F_DIRECCION;
$direccion_2 = F_DIRECCION_2;
$lugar = F_URBANIZACION;
$ubigeo = F_DIRECCION_DISTRITO."-".F_DIRECCION_PROVINCIA."-".F_DIRECCION_DEPARTAMENTO;
$telefono = "Telf.: ".F_TELEFONO;

require "../negocio/DocumentoElectronico.clase.php";

try {
  $objDocumentoElectronico = new DocumentoElectronico();
  $datos = $objDocumentoElectronico->obtenerDatosParaImpresionTicket($id);
} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$pdf = new PDF($orientation='P', $unit='mm', array(80,260));
$pdf->AddPage();

$pdf->AddFont('IckyTicketMono','','IckyTicketMono.php');
$pdf->AddFont('IckyTicketMono','B','IckyTicketMono.php');

$FONT = "IckyTicketMono";
$aumento_font = 2.35;

$MARGENES_LATERALES = 5.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 

$ANCHO_TICKET = $pdf->GetPageWidth();
$ALTO_LINEA = 3;
$BORDES = 0;
$SALTO_LINEA = .65;

/*CABECERA*/
$idtipo_comprobante = $datos["idtipo_comprobante"];
$serie = $datos["serie"];
$numero_correlativo = utf8_decode($datos["numero_correlativo"]);
$fecha_emision = $datos["fecha_emision"];
$fecha_emision_raw = $datos["fecha_emision_raw"];
$hora_emision = $datos["hora_emision"];
$numero_recibo = $datos["numero_recibo"];

$id_tipo_documento_cliente = $datos["id_tipo_documento_cliente"];
$numero_documento_cliente = $datos["numero_documento_cliente"];
$cliente = utf8_decode($datos["cliente"]);
$direccion_cliente = utf8_decode($datos["direccion_cliente"] ?? "");
$paciente = utf8_decode($datos["paciente"] ?? "");
$tipo_paciente = utf8_decode($datos["tipo_paciente"]);
$empresa_aseguradora = "";
$total_letras = $datos["total_letras"];
$observaciones = $datos["observaciones"];

$total_igv = $datos["total_igv"];
$total_gravadas = $datos["total_gravadas"];
$importe_total = $datos["importe_total"];
$descuento_global = $datos["descuento_global"];
$monto_saldo = $datos["monto_saldo"];
$condicion_pago = $datos["condicion_pago"];

$valor_resumen = $datos["valor_resumen"]; //DigestValue
$valor_firma = $datos["valor_firma"]; //SignatureValue

$usuario_atendido = utf8_decode($datos["usuario_atendido"]);

switch ($idtipo_comprobante) {
  case '01' :  $rotuloTipoComprobante='FACTURA ELECTRÓNICA'; break;
  case '03' :  $rotuloTipoComprobante='BOLETA ELECTRÓNICA '; break;
  case '07' :  $rotuloTipoComprobante='NOTA DE CRÉDITO ELECTRÓNICA '; break;
  case '08' :  $rotuloTipoComprobante='NOTA DE DÉBITO ELECTRÓNICA '; break;
}


$pdf->Image('logo_dpi.jpg', 25 , 0 ,30,0);
$pdf->SetY(25 + 5);

$ANCHO_TICKET_MENOS_MARGENES = $ANCHO_TICKET - ($MARGENES_LATERALES * 2);

$aumento_font = 1.5;

$pdf->SetFont($FONT,'B', 13.5 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES,$ALTO_LINEA , F_NOMBRE_COMERCIAL,$BORDES,1,"C");

$pdf->SetFont($FONT,'', 10 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES,$ALTO_LINEA * 1.4 , $ruc ,$BORDES,1,"C");

$pdf->Ln(1.5);

$pdf->SetFont($FONT,'',7 + $aumento_font); 
$ALTO_LINEA_LV1 = 3;
$ALTO_LINEA_LV2 = $ALTO_LINEA - 1;
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,utf8_decode($direccion),$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,utf8_decode($direccion_2), $BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,$ubigeo,$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES , $ALTO_LINEA_LV1,$telefono,$BORDES,1,"C");

$pdf->Ln(2);

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());
$pdf->Ln(1);
$pdf->SetFont($FONT,'B',9.25 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA * 1.2 , utf8_decode($rotuloTipoComprobante),$BORDES,1,"C");
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA * 1.2, $serie.' - '.str_pad($numero_correlativo,6,'0',STR_PAD_LEFT),$BORDES,1,"C");
$pdf->Ln(1);
$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());

$pdf->Ln(3);

$pdf->SetFont($FONT,'', 7.2 + $aumento_font); 
$ANCHO_COLS = [22, 2, 0];

$ANCHO_COLS[2] = $ANCHO_TICKET - ($ANCHO_COLS[0] + $ANCHO_COLS[1]) - ($MARGENES_LATERALES * 2);
$SALTO_LINEA += .5;

/*
if ($numero_recibo != ""){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "N. RECIBO", $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
  $pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5, str_pad($numero_recibo,6,'0',STR_PAD_LEFT), $BORDES,1);
  
  $pdf->Ln($SALTO_LINEA);
}
*/

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("FECHA EMISIÓN"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $fecha_emision, $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("HORA EMISIÓN"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $hora_emision, $BORDES,1);

$pdf->Ln($SALTO_LINEA);


$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "CLIENTE", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5, utf8_decode($cliente), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "DNI/RUC/CE", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5, utf8_decode($numero_documento_cliente), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("DIRECCIÓN"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->MultiCell($ANCHO_COLS[2], $ALTO_LINEA + .5, utf8_decode(mb_strtoupper($direccion_cliente == "" ? "-" : $direccion_cliente, 'UTF-8')), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("PACIENTE"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5, $paciente, $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("TIPO PACIENTE"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5, utf8_decode(mb_strtoupper($tipo_paciente, 'UTF-8')), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("FORMA PAGO"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5, utf8_decode(mb_strtoupper($condicion_pago == "1" ? "CONTADO" : "CRÉDITO", 'UTF-8')), $BORDES,1);

$pdf->Ln($SALTO_LINEA);

if (strlen($empresa_aseguradora) > 0){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "EMP. ASEGURADORA", $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,1);
  $pdf->CellFitScale($ANCHO_TICKET, $ALTO_LINEA + .5,  utf8_decode(mb_strtoupper($empresa_aseguradora, 'UTF-8')) , $BORDES,1);
  $pdf->Ln($SALTO_LINEA);
}

$detalle = $datos["detalle"];

$TAMAÑO_MAXIMO_NOMBRE_SERVICIO = 40;
$ANCHO_COLS_DETALLE = [5, $TAMAÑO_MAXIMO_NOMBRE_SERVICIO, 12.5, 0];
$ANCHO_COLS_DETALLE[3] = $ANCHO_TICKET - ($ANCHO_COLS_DETALLE[0] + $ANCHO_COLS_DETALLE[1] + $ANCHO_COLS_DETALLE[2] + ($MARGENES_LATERALES * 2));
$ALTO_LINEA = 3.35;

$pdf->Ln($SALTO_LINEA * 1.25); 
$pdf->SetFont($FONT,'B', 5 + $aumento_font); 
$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "CNT", $BORDES,0,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, utf8_decode("DESCRIPCIÓN"), $BORDES,0);
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "", $BORDES,0,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, "TOTAL" , $BORDES,1 ,"C");
$pdf->SetFont($FONT,'', 6.5 + $aumento_font); 

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());

$pdf->Ln($SALTO_LINEA * 1.25); 

foreach ($detalle as $key => $value) {
  $subtotal = round($value["precio_venta_unitario"] * $value["cantidad_item"],2);
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, $value["cantidad_item"], $BORDES,0 ,"C");
  $pdf->CellFitScale($ANCHO_COLS_DETALLE[1] + $ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5,  utf8_decode($value["descripcion_item"]), $BORDES,0);
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($subtotal,2) , $BORDES,1,"R");
}

$pdf->Ln($SALTO_LINEA); 

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());
$pdf->SetFont($FONT,'B', 6.5 + $aumento_font); 

$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "OP. GRAVADA", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total_gravadas, 2) , $BORDES,1, "R");

$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "I.G.V.", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($total_igv, 2) , $BORDES,1, "R");

if ($descuento_global > 0){
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "DESCUENTO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, "-".number_format($descuento_global, 2) , $BORDES,1, "R");  
}

$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "IMPORTE TOTAL", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
$pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, number_format($importe_total, 2) , $BORDES,1, "R");

/*
if ($monto_saldo > 0){
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "", $BORDES,0 ,"C");
  $pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "DESCUENTO", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[2], $ALTO_LINEA + .5, "S/", $BORDES,0,"R");
  $pdf->Cell($ANCHO_COLS_DETALLE[3], $ALTO_LINEA + .5, "-".number_format($monto_saldo, 2) , $BORDES,1, "R");  
}
*/

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());

$pdf->SetFont($FONT,'', 6 + $aumento_font); 
$pdf->MultiCell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA + .5, utf8_decode("SON: ".$total_letras) , $BORDES,1);

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());

if (strlen($observaciones) > 0){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("OBSERVACIONES"), $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,1);
  $pdf->MultiCell($ANCHO_TICKET, $ALTO_LINEA + .5,  $observaciones , $BORDES,1);
  $pdf->Ln($SALTO_LINEA);
}

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());
$pdf->Ln($SALTO_LINEA * 2.5);

$pdf->SetFont( $FONT, "", 7 + $aumento_font);
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA + .5, "Hash: ".$valor_resumen, $BORDES,1);

$cadena_pdf417 = F_RUC."|".$idtipo_comprobante."|".$serie."|".$numero_correlativo."|".$total_igv."|".$importe_total."|".$fecha_emision_raw."|".$id_tipo_documento_cliente."|".$numero_documento_cliente."|".$valor_resumen."|".$valor_firma;
$altura_pdf417 = 23;
$pdf417 = new PDF417();
$pdf417->setColumns(8); 
$renderer = new ImageRenderer([
    'format' => 'png',
    'scale' => 10,
]);
$ruta_pdf417 = "pdf417".getHostByName($_SERVER['REMOTE_ADDR'] == "::1" ? "localhost" : $_SERVER["REMOTE_ADDR"]).".png";
$image = $renderer->render($pdf417->encode($cadena_pdf417));
$image->save($ruta_pdf417);

$pdf->Image($ruta_pdf417, $pdf->GetX(), $pdf->GetY(), $ANCHO_TICKET_MENOS_MARGENES, $altura_pdf417);

$pdf->SetY($pdf->GetY() + $altura_pdf417);

$pdf->SetFont( $FONT, "", 6 + $aumento_font);
$pdf->CellFitScale($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA, utf8_decode("Representación Impresa de la ".mb_strtoupper($rotuloTipoComprobante,'UTF-8')), $BORDES,1,"C");
$pdf->CellFitScale($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA, utf8_decode("AUTORIZADO MEDIANTE LA RESOLUCIÓN DE SUPERINTENDENCIA ".F_RESOLUCION), $BORDES,1,"C");

$pdf->Ln($SALTO_LINEA * 2.5);

$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET_MENOS_MARGENES, $pdf->GetY());

$pdf->SetFont( $FONT, "B", 7.25 + $aumento_font);
$pdf->Cell($ANCHO_TICKET_MENOS_MARGENES, $ALTO_LINEA + .5, "GRACIAS POR SU PREFERENCIA", $BORDES,1,"C");

$pdf->Ln($SALTO_LINEA * 2); 

$pdf->SetFont( $FONT, "", 6 + $aumento_font);

$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "ATENDIDO POR: ".$usuario_atendido, $BORDES,1);
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "IMPRESO POR: ".$login, $BORDES,1);

$pdf->Ln($SALTO_LINEA * 10); 

$pdf->SetFont($FONT,'', 6.5 + $aumento_font); 
$pdf->MultiCell($ANCHO_TICKET - ($MARGENES_LATERALES * 2),$ALTO_LINEA - 1, utf8_decode("Se le recomienda conservar este TICKET. ".F_NOMBRE_COMERCIAL_TICKET." no se hace responsable de la pérdida de este y es de carácter OBLIGATORIO que sea presentado para gestionar devoluciones y/u otros procesos requeridos por el cliente."),$BORDES,"C");


$pdf->output();
ob_end_flush();
exit;