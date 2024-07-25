 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require '../plugins/bs4/vendor/autoload.php';
require "../negocio/Sesion.clase.php";
//require "TicketPDF_AutoPrint.clase.php";
require "PDF.clase.php";
//use Endroid\QrCode\QrCode;

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


require "../negocio/AtencionMedica.clase.php";

try {
  $objAtencion = new AtencionMedica();
  $datos = $objAtencion->obtenerDatosParaImpresion($id);
} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$pdf = new PDF($orientation='P', $unit='mm', array(80,260));
$pdf->AddPage();

$pdf->AddFont('IckyTicketMono','','IckyTicketMono.php');
$pdf->AddFont('IckyTicketMono','B','IckyTicketMono.php');

$FONT = "IckyTicketMono";
$aumento_font = 2.65;

$MARGENES_LATERALES = 5.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 

$ANCHO_TICKET = $pdf->GetPageWidth();
$ALTO_LINEA = 3;
$BORDES = 0;
$SALTO_LINEA = .65;

/*CABECERA*/
$fecha_atencion = $datos["fecha_atencion"];
$hora_atencion = $datos["hora_atencion"];
$numero_atencion_medica = $datos["numero_acto_medico"];
$nombre_paciente = utf8_decode($datos["nombres_completos"]);
$fecha_nacimiento_formateada = $datos["fecha_nacimiento_formateada"];
$edad = $datos["edad"];
$numero_documento = $datos["numero_documento"];
$telefono = $datos["telefonos"];
$medico_ordenante = utf8_decode($datos["medico_ordenante"]);
$observaciones = $datos["observaciones"];
$total_credito = $datos["total_credito"];
$total_vuelto = $datos["total_vuelto"];
$descuento_global = $datos["descuento_global"];

$usuario_atendido = utf8_decode($datos["usuario_atendido"]);
$empresa_convenio = utf8_decode($datos["empresa_convenio"]);

$pdf->Image('logo_dpi.jpg', 25 , 0 ,30,0);
$pdf->SetY(30 + 5);

$pdf->SetFont($FONT,'B',13 + $aumento_font + $aumento_font); 
$pdf->Cell($ANCHO_TICKET - ($MARGENES_LATERALES * 2),$ALTO_LINEA + .5, F_NOMBRE_COMERCIAL_TICKET,$BORDES,1,"C");
$pdf->Ln(1);
$pdf->SetFont($FONT,'B', 10 + $aumento_font); 
$pdf->Cell($ANCHO_TICKET - ($MARGENES_LATERALES * 2),$ALTO_LINEA + .5, "TICKET PARA CONSULTA",$BORDES,1,"C");

$pdf->Ln(4.5);

$pdf->SetFont($FONT,'', 7 + $aumento_font); 
$ANCHO_COLS = [19, 2, 0];

$ANCHO_COLS[2] = $ANCHO_TICKET - ($ANCHO_COLS[0] + $ANCHO_COLS[1]) - ($MARGENES_LATERALES * 2);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "N. RECIBO", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  str_pad($numero_atencion_medica,6,'0',STR_PAD_LEFT) , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Fecha", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5,  $fecha_atencion , $BORDES,0);

$pdf->Cell($ANCHO_COLS[0] / 2, $ALTO_LINEA + .5, "Hora: ", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5, $hora_atencion, $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Paciente", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5,  $nombre_paciente , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Fecha Nac.", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $fecha_nacimiento_formateada , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Edad", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $edad , $BORDES,1);


$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("DNI/RUC/CE"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $numero_documento , $BORDES,1);


$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, "Telf/Cel.", $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->Cell($ANCHO_COLS[2], $ALTO_LINEA + .5,  $telefono , $BORDES,1);


$pdf->Ln($SALTO_LINEA);

$pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("Médico"), $BORDES,0);
$pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
$pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5,  utf8_decode($medico_ordenante) , $BORDES,1);

$pdf->Ln($SALTO_LINEA);

if ($empresa_convenio != ""){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("CONVENIO"), $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,0);
  $pdf->CellFitScale($ANCHO_COLS[2], $ALTO_LINEA + .5,  $empresa_convenio , $BORDES,1);

  $pdf->Ln($SALTO_LINEA);
}

if (strlen($observaciones) > 0){
  $pdf->Cell($ANCHO_COLS[0], $ALTO_LINEA + .5, utf8_decode("Observaciones"), $BORDES,0);
  $pdf->Cell($ANCHO_COLS[1], $ALTO_LINEA + .5, ":", $BORDES,1);
  $pdf->Cell($ANCHO_TICKET, $ALTO_LINEA + .5,  $observaciones , $BORDES,1);
  $pdf->Ln($SALTO_LINEA);
}

$servicios = $datos["servicios"];
$ANCHO_COLS_DETALLE = [5, 0];
$ANCHO_COLS_DETALLE[1] = $ANCHO_TICKET - ($ANCHO_COLS_DETALLE[0]  + ($MARGENES_LATERALES * 2));
$ALTO_LINEA = 3.35;

$pdf->Ln($SALTO_LINEA * 5); 
$pdf->SetFont($FONT,'B', 5 + $aumento_font); 
$pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, "CNT", $BORDES,0,"C");
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5, "SERVICIO", $BORDES,1);
$pdf->SetFont($FONT,'', 6.5 + $aumento_font); 

$pdf->Cell($ANCHO_TICKET - ($MARGENES_LATERALES * 2), .15, "------------------------------------------------------" , $BORDES,1);

$pdf->Ln($SALTO_LINEA * 2.5); 

foreach ($servicios as $key => $value) {
  $pdf->SetFont($FONT,'', 6.5 + $aumento_font); 
  $subtotal = round($value["precio_unitario"] * $value["cantidad"],2);
  $pdf->Cell($ANCHO_COLS_DETALLE[0], $ALTO_LINEA + .5, $value["cantidad"], $BORDES,0 ,"C");
  $pdf->CellFitScale($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .5,  utf8_decode($value["nombre_servicio"]), $BORDES,1);

  if (isset($value["servicios_paquete"])){
    $serviciosPaquete = explode(",", $value["servicios_paquete"]);
    $pdf->SetFont($FONT,'', 5.5 + $aumento_font); 
    foreach ($serviciosPaquete as $i => $servPaquete) {
      $pdf->SetX($pdf->GetX()+ 5.00);
      $pdf->CellFitScale($ANCHO_COLS_DETALLE[1], $ALTO_LINEA + .25,  "* ".utf8_decode($servPaquete), $BORDES, 1);
    }
  }
}

$pdf->SetFont($FONT,'', 6 + $aumento_font); 

$pdf->Ln($SALTO_LINEA * 10); 

$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "ATENDIDO POR: ".$usuario_atendido, $BORDES,1);
$pdf->Cell($ANCHO_COLS_DETALLE[1], $ALTO_LINEA, "IMPRESO POR: ".$login, $BORDES,1);

$pdf->Ln($SALTO_LINEA * 10); 

$pdf->SetFont($FONT,'', 5.5 + $aumento_font); 
$pdf->MultiCell($ANCHO_TICKET - ($MARGENES_LATERALES * 2),$ALTO_LINEA - 1, utf8_decode("Se le recomienda conservar este TICKET. ".F_NOMBRE_COMERCIAL_TICKET." no se hace responsable de la pérdida de este y es de carácter OBLIGATORIO que sea presentado para gestionar devoluciones y/u otros procesos requeridos por el cliente."),$BORDES,"C");


//$pdf->AutoPrint();
$pdf->Output();

ob_end_flush();
exit;
