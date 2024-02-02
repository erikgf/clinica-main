 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
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
    echo "No se ha ingresado parámeteo de FECHA DE INICIO";
    exit;
}

if ($fecha_fin == NULL){
    echo "No se ha ingresado parámeteo de FECHA DE FIN";
    exit;
}

$FONT = isset($_GET["f"]) ? $_GET["f"] : 1;
$FONT = $FONT == 1 ? "Arial" : "Courier";

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");
/*
$empresa = F_RAZON_SOCIAL;
$ruc = "R.U.C.: ".F_RUC;
$direccion = F_DIRECCION;
$lugar = F_URBANIZACION;
$ubigeo = F_DIRECCION_DISTRITO."-".F_DIRECCION_PROVINCIA."-".F_DIRECCION_DEPARTAMENTO;
$telefono = "Telf.: ".F_TELEFONO;
*/

require "../negocio/Medico.clase.php";

try {
  $obj = new Medico();
  $obj->id_medico = $id_medico;
  $data = $obj->listarAtencionesComisionParaLiquidacionXMedicoImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);

  if (count($data) <= 0){
    echo "Sin datos encontrado.";
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

foreach ($data as $key => $data_medico) {
    $ALTO_LINEA = 4;
    $SALTO_LINEA = 1.6;

    $ANCHO_TICKET_FULL = $pdf->GetPageWidth();
    $ANCHO_TICKET = $ANCHO_TICKET_FULL - ($MARGENES_LATERALES * 2);
    $ANCHO_TICKET_MITAD = $ANCHO_TICKET / 2;

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
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode("LIQUIDACIÓN DETALLADA DE MÉDICO"),$BORDES,1,"C");

    $pdf->SetFont($FONT,'', 7+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .5, utf8_decode($data_medico["sede"]),$BORDES,1,"C");

    $pdf->SetFont($FONT,'', 11.5+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, utf8_decode($data_medico["nombres_apellidos"]),$BORDES,1,"C");

    $pdf->SetFont($FONT,'B', 7.5+ $aumento_font);
    $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, "DEL ".$fecha_inicio." AL ".$fecha_fin,$BORDES,1,"C");

    $pdf->Ln($SALTO_LINEA * 1.5);
    /*inicio - cabecera */
    $pdf->Ln($SALTO_LINEA * 2);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA * 2);
    /*fin - cabecera */
    $COLS_DETALLE = [
        ["rotulo"=>"FECHA", "ancho"=>16.00, "alineacion"=>"C"],
        ["rotulo"=>"PACIENTE", "ancho"=>75.00, "alineacion"=>"C"],
        ["rotulo"=>"SERVICIO", "ancho"=>85.00, "alineacion"=>"C"],
       // ["rotulo"=>"IMPORTE", "ancho"=>22.5, "alineacion"=>"R"],
       // ["rotulo"=>"COMISIÓN %", "ancho"=>22.5, "alineacion"=>"R"],
        ["rotulo"=>"S/ COMISIÓN", "ancho"=>16.5, "alineacion"=>"R"]
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

    $total_total_importe = 0.00;
    $total_total_comisiones = 0.00;

    $categorias = $data_medico["categorias"];
    $total_pacientes = $data_medico["total_pacientes"];
    
    foreach ($categorias as $key => $categoria) {
        $total_importe = 0.00;
        $total_comisiones = 0.00;
        $i = 0;
        $pdf->SetFont($FONT,'B', 5.55 + $aumento_font);
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, ($key + 1), $BORDES,0 ,"C");    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($categoria["categoria"]), $BORDES,1);   

        $pdf->SetFont($FONT,'', 5 + $aumento_font);
        $atenciones = $categoria["atenciones"];

        foreach ($atenciones as $key => $value) {
            $i = 0;
            $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $value["fecha_atencion"], $BORDES,0 ,"C");    
            $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($value["nombre_paciente"]), $BORDES,0);    
            $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($value["nombre_servicio"]), $BORDES,0);    
           // $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".round($value["subtotal_sin_igv"], 3), $BORDES,0 ,"R");    
           // $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, number_format(round($value["porcentaje_comision_categoria"] * 100,2),2). "%", $BORDES,0 ,"R");   
            $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, "S/ ".round($value["sin_igv"],3), $BORDES,1 ,"R");   

            $total_importe += $value["subtotal"];
            $total_comisiones +=$value["sin_igv"];
        }

        $total_importe = round($total_importe / 1.18,2);
        $pdf->SetFont($FONT,'B', 5.55 + $aumento_font); 
        $i = 0;
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0 ,"C");    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0);    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "TOTAL ATENCIONES", $BORDES,0 ,"R");
     //  $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".round($total_importe,3), $BORDES,0, "R");    
     //   $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0,"R");    
        $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".round($total_comisiones,3), $BORDES,1, "R"); 

        $total_total_importe += $total_importe;
        $total_total_comisiones +=$total_comisiones;
    }

    $pdf->Ln($SALTO_LINEA);
    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
    $pdf->Ln($SALTO_LINEA);

    $pdf->SetFont($FONT,'B', 6.55 + $aumento_font); 
    $i = 0;
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0 ,"C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "Total Pacientes: ".$total_pacientes, $BORDES,0, "C");    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, utf8_decode("TOTAL LIQUIDACIÓN") , $BORDES,0 ,"R");
   // $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".round($total_total_importe,3), $BORDES,0, "R");    
   // $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5, "", $BORDES,0);    
    $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 2.5,  "S/ ".round($total_total_comisiones,3), $BORDES,0, "R");    

    $pdf->Ln($SALTO_LINEA);

}

$pdf->output();
ob_end_flush();
exit;