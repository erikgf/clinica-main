 
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

$fecha_impresion = strftime("%d/%m/%Y");
$fecha_qr = strftime( "%Y-%m-%d" );


$razon_social = F_RAZON_SOCIAL;
$ruc =  F_RUC;
$direccion = F_DIRECCION;
$direccion_2 = F_DIRECCION_2;
$lugar = F_URBANIZACION;
$ubigeo = F_DIRECCION_DISTRITO." - ".F_DIRECCION_PROVINCIA." - ".F_DIRECCION_DEPARTAMENTO;
$telefono = "Telf.: ".F_TELEFONO;

class PDFComprobante extends PDF{
  protected $B = 0;
  protected $I = 0;
  protected $U = 0;
  protected $HREF = '';

  private $MARGEN_IZQ = 10;
  private $MARGEN_DER = 10;
  private $ANCHO_TOTAL = 190;
  private $ALTO_TOTAL = 200;
  private $ALTURA_BASE_DETALLE = 150;

  function imprimirLogo($x, $y, $data){
      $IMAGEN_ANCHO = 35;
      $IMAGEN_ALTO = 30;

      $LOGO = $data["img_logo"]; //'img/logo_acopiadora.png';
      $this->Image($LOGO,$x,$y, $IMAGEN_ANCHO, $IMAGEN_ALTO);
  }

  function imprimirInfoEmpresa($x, $y, $data){
      $CELDA_ALTO = 7;
      $CELDA_ANCHO = 80;
      $actualX = $x;
      $actualY = $y;
      $dataEmpresa  = $data["empresa"];
      $alineacion = '';

      $this->setXY($actualX,$actualY);
      $this->SetFont('Arial','B',15.5);
      $this->Cell($CELDA_ANCHO,$CELDA_ALTO, utf8_decode($dataEmpresa["razon_social"]),0,0,$alineacion);    
      $actualY  = $actualY + $CELDA_ALTO;
      $this->setXY($actualX, $actualY);

      $CELDA_ALTO = 6;
      $this->SetFont('Arial','',10);
      $this->Cell($CELDA_ANCHO,$CELDA_ALTO, utf8_decode($dataEmpresa["direccion"]),0,0,$alineacion);
      $actualY  = $actualY + $CELDA_ALTO - 2;
      $this->setXY($actualX, $actualY);

      $this->SetFont('Arial','',10);
      $this->Cell($CELDA_ANCHO,$CELDA_ALTO, utf8_decode($dataEmpresa["direccion_2"]),0,0,$alineacion);
      $actualY  = $actualY + $CELDA_ALTO - 2;
      $this->setXY($actualX, $actualY);

      $this->SetFont('Arial','',10);
      $this->Cell($CELDA_ANCHO,$CELDA_ALTO, utf8_decode($dataEmpresa["ubigeo"]),0,0,$alineacion);
      $actualY  = $actualY + $CELDA_ALTO - 2;
      $this->setXY($actualX, $actualY);

      $this->SetFont('Arial','',10);
      $this->Cell($CELDA_ANCHO,$CELDA_ALTO, utf8_decode($dataEmpresa["telefono"]),0,0,$alineacion);
      $actualY  = $actualY + $CELDA_ALTO - 2;
      $this->setXY($actualX, $actualY);

      return $CELDA_ANCHO;
  }

  function imprimirComprobanteCuadrado($x, $y, $data){
      $RECT_ANCHO = 55;
      $RECT_ALTO = 24.5;  
      $actualX = $x;
      $actualY = $y;
      $fontSize = 12;

      $this->Rect($x,$y,$RECT_ANCHO,$RECT_ALTO);

      /*NUMERO DOCUMENTO*/
      $this->SetFont('Arial','', $fontSize);
      $this->setXY($actualX,$actualY);
      $alturaCelda = 8;
      $this->Cell($RECT_ANCHO,$alturaCelda, "RUC ".$data["numero_documento"],0,0,'C');    
      $actualY = $actualY + $alturaCelda;

      $this->SetFillColor(235,235,235);
      $this->SetFont('Arial','B', $fontSize + 1);
      $this->setXY($actualX,$actualY);
      /*ROTULO COMPROBANTE*/
     
      $actualY = $actualY + $alturaCelda;

      $this->CellFitScale($RECT_ANCHO, $alturaCelda, utf8_decode($data["rotulo_comprobante"]),'LR',0,'C',1);   
      $this->SetFillColor(255,255,255);

      $this->SetFont('Arial','', $fontSize);
      $this->setXY($actualX,$actualY);
      $this->Cell($RECT_ANCHO, $alturaCelda, $data["numero_comprobante"],0,0,'C');  
  }

  function imprimirCabeceraComprobante($objCabecera){
      $this->imprimirLogo(10,10, ["img_logo" => $objCabecera["img_logo"]]);
      $this->imprimirInfoEmpresa(45,15, 
        ["empresa" => $objCabecera["empresa"]]);
      $this->imprimirComprobanteCuadrado(145,10, 
        [ "numero_comprobante"=>$objCabecera["comprobante"]["serie"].' - '.$objCabecera["comprobante"]["correlativo"],
           "rotulo_comprobante"=>$objCabecera["comprobante"]["rotulo"],
           "numero_documento"=>$objCabecera["empresa"]["numero_documento"]]);
  }

  function imprimirMainComprobante($obj){
      $ANCHO_COLUMNAS =  [20, 115, 35, 0]; /*TOTAL 190 + 20 (MARGENES)*/
      $ANCHO_COLUMNAS = $this->CalcularUltimaColumna($ANCHO_COLUMNAS);
      $ALTO_FILA = 6;
      $BORDES = 0;

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[0], $ALTO_FILA, "RUC/DNI",$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[1], $ALTO_FILA, $obj["numero_documento_cliente"],$BORDES,0);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[2], $ALTO_FILA, utf8_decode("FECHA EMISIÓN"),$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[3], $ALTO_FILA,  $obj["fecha_emision"],$BORDES,1);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[0], $ALTO_FILA, "CLIENTE",$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[1], $ALTO_FILA, $obj["razon_social_cliente"],$BORDES,0);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[2], $ALTO_FILA, "FECHA VENCIMIENTO",$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[3], $ALTO_FILA,  $obj["fecha_vencimiento"],$BORDES,1);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[0], $ALTO_FILA, utf8_decode("DIRECCIÓN"),$BORDES,0);

      $this->SetFont("Arial","");
      $tmpY = $this->GetY();
      $tmpX = $this->GetX();
      $extraAlturaMultiCell = $this->GetStringWidth( $obj["direccion_cliente"]) > $ANCHO_COLUMNAS[1] ? 2 : 0;
      $this->MultiCell($ANCHO_COLUMNAS[1], $ALTO_FILA - $extraAlturaMultiCell,  $obj["direccion_cliente"],$BORDES,'');
      $this->SetXY($tmpX + $ANCHO_COLUMNAS[1], $tmpY);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[2], $ALTO_FILA, "MONEDA",$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[3], $ALTO_FILA,  $obj["moneda"],$BORDES,1);    

      $this->Cell($ANCHO_COLUMNAS[0], $ALTO_FILA, "",$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[1], $ALTO_FILA, "",$BORDES,0);

      $this->SetFont("Arial","B");
      $this->Cell($ANCHO_COLUMNAS[2], $ALTO_FILA, utf8_decode("FORMA DE PAGO"),$BORDES,0);
      $this->SetFont("Arial","");
      $this->Cell($ANCHO_COLUMNAS[3], $ALTO_FILA, utf8_decode($obj["condicion_pago"] == "1" ? "CONTADO" : "CRÉDITO"),$BORDES,1);   

      $this->Ln($extraAlturaMultiCell + 2);
  }

  function CalcularUltimaColumna($arregloInicial){
      $cantidadColumnas = count($arregloInicial);

      $ANCHO_COLUMNA_FINAL = 0;
      for ($i=0; $i < $cantidadColumnas - 1; $i++) { 
        $ANCHO_COLUMNA_FINAL  = $ANCHO_COLUMNA_FINAL + $arregloInicial[$i];
      }
      $ANCHO_COLUMNA_FINAL = $this->ANCHO_TOTAL - $ANCHO_COLUMNA_FINAL;
      $arregloInicial[$cantidadColumnas] = $ANCHO_COLUMNA_FINAL;
      $ANCHO_COLUMNA_FINAL = null;

      return $arregloInicial;
  }

  function imprimirTablaDetalle($items_factura, $total_letras){
      $BORDES = 0;
      $ALTO_FILA = 6;

      $INICIO_DETALLE_ALTURA = $this->GetY();

      $ITEMS_COLUMNAS = [
        ["alineacion"=>"C", "valor"=>"N°"],
        ["alineacion"=>"C", "valor"=>"UNID."],
        ["alineacion"=>"", "valor"=>"DESCRIPCIÓN"],
        ["alineacion"=>"C", "valor"=>"CANT."],
        ["alineacion"=>"R", "valor"=>"V.V. UNIT."],
        ["alineacion"=>"R", "valor"=>"V.V. TOTAL"]
      ];
      $ANCHO_COLUMNAS =  [10, 15, 110, 18, 18, 0];
      $ANCHO_COLUMNAS = $this->CalcularUltimaColumna($ANCHO_COLUMNAS);

      $cantidadCeldas = count($ITEMS_COLUMNAS) - 1;

      $this->SetFont("Arial","B");
      $this->SetFillColor(0,0,0);
      $this->SetTextColor(255,255,255);
      foreach ($ITEMS_COLUMNAS as $key => $columna) {
        $saltoCelda = ($key >= $cantidadCeldas) ? 1 : 0;
        $this->Cell($ANCHO_COLUMNAS[$key], $ALTO_FILA, utf8_decode($columna["valor"]), $BORDES,$saltoCelda,$columna["alineacion"],1);
      }
      $this->SetFillColor(60,60,60);
      $this->SetTextColor(0,0,0);

      $this->SetFont('Arial','');
      
      foreach ($items_factura as $j => $item) {
        $i = 0;
        $x = 0;
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["item"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["idunidad_medida"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->CellFitScale($ANCHO_COLUMNAS[$i++], $ALTO_FILA, utf8_decode(mb_strtoupper($item["descripcion_item"],'UTF-8')), '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["cantidad_item"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["valor_venta_unitario"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["valor_venta"], '', 1, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
      }
      $FIN_DETALLE_ALTURA = $this->GetY();
      $ALTURA_DETALLE = $FIN_DETALLE_ALTURA - $INICIO_DETALLE_ALTURA;

      /*FIN - AGREGAR LAS LINEAS AL DETALLE*/
      $this->SetXY($this->GetX(), $INICIO_DETALLE_ALTURA);

      $i = 0;
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 1, 1); 

      $this->SetFont('Arial','');
      $this->CellFitScale($this->ANCHO_TOTAL, $ALTO_FILA, "SON: ".utf8_decode($total_letras),1,1,"L");      

      /*Dibujando cuadrado final*/
      //$this->SetY($Y_LINEAS_TOTALES);xx
      //$this->Cell($this->ANCHO_TOTAL, $tmpY - $Y_LINEAS_TOTALES, "",1,1,"");
  }

  function imprimirCuotas($cuotas, $fecha_emision){
      $BORDES = 0;
      $ALTO_FILA = 4;

      $this->SetFont("Arial","B");
      $this->Cell(50, $ALTO_FILA, utf8_decode("DETALLE DE PAGO AL CRÉDITO"),$BORDES,1);

      $INICIO_DETALLE_ALTURA = $this->GetY();

      $ITEMS_COLUMNAS = [
        ["alineacion"=>"C", "valor"=>"CUOTA"],
        ["alineacion"=>"C", "valor"=>"IMPORTE DE CUOTA"],
        ["alineacion"=>"C", "valor"=>"FECHA PAGO"]
      ];
      $ANCHO_COLUMNAS =  [27.5, 35, 35];
      $cantidadCeldas = count($ITEMS_COLUMNAS) - 1;

      $this->SetFont("Arial","B");

      $this->SetFillColor(100,100,100);
      $this->SetTextColor(255,255,255);
      foreach ($ITEMS_COLUMNAS as $key => $columna) {
        $saltoCelda = ($key >= $cantidadCeldas) ? 1 : 0;
        $this->Cell($ANCHO_COLUMNAS[$key], $ALTO_FILA, utf8_decode($columna["valor"]), $BORDES,$saltoCelda,$columna["alineacion"],1);
      }
      $this->SetFillColor(60,60,60);
      $this->SetTextColor(0,0,0);

      $this->SetFont('Arial','');
      
      foreach ($cuotas as $j => $item) {
        $i = 0;
        $x = 0;
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["numero_cuota"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["monto_cuota"], '', 0, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
        $this->Cell($ANCHO_COLUMNAS[$i++], $ALTO_FILA, $item["fecha_vencimiento"], '', 1, $ITEMS_COLUMNAS[$x++]["alineacion"]); 
      }
      $FIN_DETALLE_ALTURA = $this->GetY();
      $ALTURA_DETALLE = $FIN_DETALLE_ALTURA - $INICIO_DETALLE_ALTURA;
      $this->SetXY($this->GetX(), $INICIO_DETALLE_ALTURA);

      $i = 0;
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 0, 1); 
      $this->Cell($ANCHO_COLUMNAS[$i++],  $ALTURA_DETALLE, "", 1, 1, 1); 

      $date1 = date_create_from_format('d/m/Y', $item["fecha_vencimiento"]);
      $date2 = date_create_from_format('d/m/Y', $fecha_emision);
      $interval = date_diff($date1, $date2);

      $cantidad_dias_credito =  $interval->format("%a");
     // $cantidad_dias_credito =  date_diff($date1, $date2)->d;
      $this->Cell(50, $ALTO_FILA, utf8_decode("Condiciones de pago: ".$cantidad_dias_credito." días de CRÉDITO"),$BORDES,1);

  }

  function imprimirFinalComprobante($obj){
      $ANCHO_COLUMNAS = [40, 150];
      $BORDES = 0;

      $NUMERO_DECIMALES = 2;
      $ANCHO_ZONA_DERECHA = 120;
      $ALTO_FILA = 4.5;

      $simbolo_moneda = $obj["moneda"] == "PEN" ? "S/" : "$";
      $totales = $obj["totales"];
      $rotuloTipoComprobante = $obj["rotuloTipoComprobante"];

      $this->SetFont("Arial","B");
      $this->Cell(30, $ALTO_FILA, "USUARIO",$BORDES,0,'');
      $this->SetFont("Arial","");
      $this->Cell(90, $ALTO_FILA, $obj["usuario"],$BORDES,0,'');

      $descuento_global = $totales["descuento_global"];

      if ($descuento_global > 0.00){          
        $this->SetFont('Arial',''); 
        $this->Cell(30, $ALTO_FILA, "DESCUENTOS",0,0,"R");      
        $this->Cell(15, $ALTO_FILA, $simbolo_moneda,0,0,"R");
        $this->Cell(25, $ALTO_FILA, number_format($descuento_global * -1, $NUMERO_DECIMALES,".",","), $BORDES,1,"R");

        $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      }

      $this->SetFont('Arial',''); 
      $this->Cell(30, $ALTO_FILA, "OP. GRAVADA",$BORDES,0,"R");      
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,$BORDES,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format($totales["total_gravadas"], $NUMERO_DECIMALES,".",","),$BORDES,1,"R");

      $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      $this->Cell(30, $ALTO_FILA, "OP. INAFECTA",$BORDES,0,"R");      
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,$BORDES,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format($totales["total_inafectas"], $NUMERO_DECIMALES,".",","),$BORDES,1,"R");

      $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      $this->Cell(30, $ALTO_FILA, "OP. EXONERADA",$BORDES,0,"R");      
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,$BORDES,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format($totales["total_exoneradas"], $NUMERO_DECIMALES,".",","),$BORDES,1,"R");

      $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      $this->Cell(30, $ALTO_FILA, "OP. GRATUITAS",$BORDES,0,"R");      
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,$BORDES,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format("0.00", $NUMERO_DECIMALES,".",","),$BORDES,1,"R");


      $es_nota = $obj["idtipo_comprobante"] == "07" || $obj["idtipo_comprobante"] == "08";

      if ($es_nota){
        $this->SetFont("Arial","B");
        $this->Cell(30, $ALTO_FILA, "DOC. AFECTADO",$BORDES,0,'');
        $this->SetFont("Arial","");
        $this->Cell(90, $ALTO_FILA, $obj["documento_afectado"],$BORDES,0,'');
      } else {
        $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      } 

      $this->Cell(30, $ALTO_FILA, "I.G.V. 18%",0,0,"R");      
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,0,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format($totales["total_igv"], $NUMERO_DECIMALES,".",","), $BORDES,1,"R");
  
      if ($es_nota){
       $this->SetFont("Arial","B");
        $this->Cell(30, $ALTO_FILA, "MOTIVO DE LA NOTA",$BORDES,0,'');
        $this->SetFont("Arial","");
        $this->CellFitScale(90, $ALTO_FILA, utf8_decode($obj["motivo_nota"]),$BORDES,0,'');
      } else {
        $this->Cell($ANCHO_ZONA_DERECHA, $ALTO_FILA, '',0,0,"L");
      }          
   
      $this->SetFont("Arial", "B");
      $this->Cell(30, $ALTO_FILA, "IMPORTE TOTAL",0,0,"R");     
      $this->Cell(15, $ALTO_FILA, $simbolo_moneda,0,0,"R");
      $this->Cell(25, $ALTO_FILA, number_format($totales["total_importe"], $NUMERO_DECIMALES,".",","), $BORDES, 1,"R");

      $this->Ln(2);

      $this->SetFont("Arial","B");
      $this->Cell(30, $ALTO_FILA, "OBSERVACIONES",$BORDES,0,'');
      $this->SetFont("Arial","");

      $observaciones = utf8_decode(mb_strtoupper($obj["observaciones"] == "" ? "-" : $obj["observaciones"],'UTF-8'));
      $extraAlturaMultiCell = $this->GetStringWidth( $observaciones ) > $ANCHO_COLUMNAS[1] ? 1.5: 0;
      $this->MultiCell($ANCHO_COLUMNAS[1], $ALTO_FILA - $extraAlturaMultiCell, $observaciones ,$BORDES,'');
    
      $this->Ln(2);
  
      if ($obj["respuesta_sunat"] != ""){
        $this->SetFont("Arial","B");
        $this->Cell(30, $ALTO_FILA, "RESPUESTA SUNAT",$BORDES,0,'');
        $this->SetFont("Arial","");
        $this->Cell($ANCHO_COLUMNAS[1], $ALTO_FILA,  $obj["respuesta_sunat"],$BORDES,1,'');
      }
      
      $this->Ln(2.75);
      $this->SetFont("Arial","",5.5);
      $this->CellFitScale($ANCHO_COLUMNAS[1], $ALTO_FILA * .85, utf8_decode("Representación Impresa de la ".mb_strtoupper($rotuloTipoComprobante,'UTF-8')), $BORDES,1,"L");
      $this->CellFitScale($ANCHO_COLUMNAS[1], $ALTO_FILA * .85, utf8_decode("AUTORIZADO MEDIANTE LA RESOLUCIÓN DE INTENDENCIA ".F_RESOLUCION), $BORDES,1,"L");
      $this->SetFont("Arial","B",7.5);
      $this->CellFitScale($ANCHO_COLUMNAS[1], $ALTO_FILA, utf8_decode("Resumen: ".$obj["hash"]), $BORDES,1,"L");

  }

}


require "../negocio/DocumentoElectronico.clase.php";

try {
  $objDocumentoElectronico = new DocumentoElectronico();
  $datos = $objDocumentoElectronico->obtenerDatosParaImpresionTicket($id);
} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

$pdf = new PDFComprobante();
$pdf->AddPage();

/*
$pdf->AddFont('IckyTicketMono','','IckyTicketMono.php');
$pdf->AddFont('IckyTicketMono','B','IckyTicketMono.php');

*/
$FONT = "Arial";
$aumento_font = 1.00;
/*
$MARGENES_LATERALES = 10.00;
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES, $MARGENES_LATERALES); 
*/

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
$fecha_vencimiento = $datos["fecha_vencimiento"];
$fecha_vencimiento_raw = $datos["fecha_vencimiento_raw"];

$id_tipo_documento_cliente = $datos["id_tipo_documento_cliente"];
$numero_documento_cliente = $datos["numero_documento_cliente"];
$razon_social_cliente = utf8_decode($datos["cliente"]);
$direccion_cliente = utf8_decode($datos["direccion_cliente"]);
$paciente = utf8_decode($datos["paciente"]);
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
$tipo_moneda = $datos["idtipo_moneda"];

$valor_resumen = $datos["valor_resumen"]; //DigestValue
$valor_firma = $datos["valor_firma"]; //SignatureValue
$respuesta_sunat = utf8_decode($datos["respuesta_sunat"]);

$documento_afectado = $datos["documento_afectado"];
$motivo_nota  = $datos["motivo_nota"];

$usuario_atendido = utf8_decode($datos["usuario_atendido"]);

$detalle = $datos["detalle"];
$cuotas = $datos["cuotas"];

switch ($idtipo_comprobante) {
  case '01' :  $rotuloTipoComprobante='FACTURA ELECTRÓNICA'; break;
  case '03' :  $rotuloTipoComprobante='BOLETA ELECTRÓNICA '; break;
  case '07' :  $rotuloTipoComprobante='NOTA DE CRÉDITO ELECTRÓNICA '; break;
  case '08' :  $rotuloTipoComprobante='NOTA DE DÉBITO ELECTRÓNICA '; break;
}

$img_logo = "logo_dpi.jpg";

$pdf->imprimirCabeceraComprobante([
    "img_logo"=>$img_logo,
    "empresa"=>[
      "numero_documento"=>$ruc,
      "razon_social"=>$razon_social,
      "direccion"=>$direccion,
      "direccion_2"=>$direccion_2,
      "ubigeo"=>$ubigeo,      
      "telefono"=>$telefono
    ],
    "comprobante"=>[
      "rotulo"=>$rotuloTipoComprobante,
      "serie"=>$serie,
      "correlativo"=>str_pad($numero_correlativo,6,'0',STR_PAD_LEFT)
    ]
  ]);

$INICIAL_X = 10;
$INICIAL_Y = 45;

$pdf->setXY($INICIAL_X, $INICIAL_Y);
$pdf->SetFontSize(8);

$totales = [
    "descuento_global"=>$descuento_global,
    "total_gravadas"=>$total_gravadas,
    "total_inafectas"=>"0.00",
    "total_exoneradas"=>"0.00",
    "total_igv"=>$total_igv,
    "total_importe"=>$importe_total
  ];

$objMainComprobante = [
  "numero_documento_cliente"=>$numero_documento_cliente,
  "razon_social_cliente"=>$razon_social_cliente,
  "direccion_cliente"=>$direccion_cliente,
  "fecha_emision"=>$fecha_emision,
  "fecha_vencimiento"=>$fecha_vencimiento,
  "condicion_pago"=>$condicion_pago,
  "moneda"=>$tipo_moneda
];

$pdf->imprimirMainComprobante($objMainComprobante);

$pdf->Ln(2);  

$pdf->imprimirTablaDetalle($detalle, $total_letras);

$pdf->Ln(3.5);

if (count($cuotas) > 0){
  $pdf->imprimirCuotas($cuotas, $fecha_emision);
  $pdf->Ln(2.5);
}


$objFinalComprobante = [
    "moneda"=>$tipo_moneda,
    "totales"=>$totales,
    "idtipo_comprobante"=>$idtipo_comprobante,
    "rotuloTipoComprobante"=>$rotuloTipoComprobante,
    "documento_afectado"=>$documento_afectado,
    "motivo_nota"=>$motivo_nota,
    "usuario"=>$usuario_atendido.' - '.$fecha_emision." ".$hora_emision,
    "observaciones"=>$observaciones,
    "guias_referencia"=>"",
    "respuesta_sunat"=>$respuesta_sunat,
    "es_detraccion"=>"0",
    "hash"=>$valor_resumen
  ];

$pdf->imprimirFinalComprobante($objFinalComprobante);

$cadena_pdf417 = F_RUC."|".$idtipo_comprobante."|".$serie."|".$numero_correlativo."|".$total_igv."|".$importe_total."|".$fecha_emision_raw."|".$id_tipo_documento_cliente."|".$numero_documento_cliente."|".$valor_resumen."|".$valor_firma;
$altura_pdf417 = 15;

$pdf417 = new PDF417();
$pdf417->setColumns(8); 
$renderer = new ImageRenderer([
    'format' => 'png',
    'scale' => 10,
]);
$ruta_pdf417 = "pdf417".getHostByName($_SERVER['REMOTE_ADDR'] == "::1" ? "localhost" : $_SERVER["REMOTE_ADDR"]).".png";
$image = $renderer->render($pdf417->encode($cadena_pdf417));
$image->save($ruta_pdf417);

$pdf->Image($ruta_pdf417, $pdf->GetX() + 118, $pdf->GetY() - 7.5, 75, $altura_pdf417);

$pdf->output("I", $serie."-".$numero_correlativo.".pdf");
ob_end_flush();
exit;
