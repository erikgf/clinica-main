 
<?php
ob_start();

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require "../negocio/Sesion.clase.php";
require "PDF.clase.php";

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficentes para ver esto.";
  exit;
}

$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_atencion_medica = isset($_GET["id"]) ? $_GET["id"] : "";
$mostrar_logo = isset($_GET["logo"]) ? $_GET["logo"] : "0";
$firma_img = "patologo_firma.png";
$id_atenciones_servicios = isset($_GET["idams"]) ? $_GET["idams"] : "[]";
$imprimir_todo_junto =  isset($_GET["tj"]) ? $_GET["tj"] : "0";

$FONT = isset($_GET["f"]) ? $_GET["f"] : 1;
$FONT = $FONT == 1 ? "Arial" : "Courier";

$fecha_impresion = date("d/m/Y");
$hora_impresion = date("H:i:s");

require "../negocio/AtencionMedica.clase.php";

try {
  $obj = new AtencionMedica();
  $obj->id_atencion_medica = $id_atencion_medica;
  $data = $obj->obtenerResultadoExamenesLaboratorioAtencionMedica(json_decode($id_atenciones_servicios));

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
$pdf->SetMargins($MARGENES_LATERALES, $MARGENES_LATERALES * 8, $MARGENES_LATERALES); 
$pdf->AliasNbPages();
//$pdf->show_footer = true;
$aumento_font = 1.65;
$BORDES = 0;

$ALTO_LINEA = 3.75;
$SALTO_LINEA = 1.5;

$ANCHO_TICKET_FULL = $pdf->GetPageWidth();
$ANCHO_TICKET = $ANCHO_TICKET_FULL - ($MARGENES_LATERALES * 2);
$ANCHO_TICKET_MITAD = $ANCHO_TICKET / 2;
$ANCHO_TICKET_CUARTA = $ANCHO_TICKET / 4.6;
$ANCHO_TICKET_MITAD_MITAD = $ANCHO_TICKET_MITAD / 2;
$ANCHO_TICKET_MITAD_MITAD_MITAD = $ANCHO_TICKET_MITAD_MITAD / 2;

$ESPACIO_HORIZONTAL = 10;

$secciones = $data["secciones"];

$hojas_generadas = 0;
foreach ($secciones as $_ => $seccion) {
    $muestras = $seccion["muestras"];
    foreach ($muestras as $key => $muestra) {
        if ($imprimir_todo_junto == "1" && $hojas_generadas >= 1){
        } else {
          $pdf->AddPage();
          $hojas_generadas++;
          /*Init - Zona superior */
          if ($mostrar_logo == "1"){
            $pdf->Image("laboratorio_logo_superior.png", 0, 0, $ANCHO_TICKET_FULL);
          }

          $ALTO_LINEA = 5;
          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Ln($SALTO_LINEA * 1.5);
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, utf8_decode("N° RECIBO: "),$BORDES,0);
          
          $pdf->SetFont($FONT,'', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_MITAD_MITAD_MITAD,$ALTO_LINEA + .75, $data["numero_recibo"],$BORDES, 0);
          
          $pdf->Cell($ESPACIO_HORIZONTAL * 2,$ALTO_LINEA + .75, "",$BORDES,0);
          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, utf8_decode("FECHA DE ORDEN:"),$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_MITAD_MITAD_MITAD,$ALTO_LINEA + .75, $data["fecha_orden"],$BORDES, 1);
          
          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, "EDAD:",$BORDES,0);
          $edad = "";
          if ($data["edad_anios"] > 0){
            $edad = $data["edad_anios"]." AÑOS";
          } else {
            $edad = $data["edad_meses"]." MESES";
          }
          $pdf->SetFont($FONT,'', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_MITAD_MITAD_MITAD,$ALTO_LINEA + .75, utf8_decode($edad),$BORDES, 1);

          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, "PACIENTE:",$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .75, utf8_decode($data["nombre_paciente"]),$BORDES, 1);

          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, "SEXO:",$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .75, $data["sexo"] == "F" ? "FEMENINO" : "MASCULINO",$BORDES, 1);
          
          $pdf->SetFont($FONT,'B', 7 + $aumento_font); 
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, utf8_decode("MÉDICO:"),$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .75, utf8_decode($data["nombre_medico"]),$BORDES, 1);
          
          $pdf->SetFont($FONT,'B', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, "PROCEDENCIA:",$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .75, utf8_decode($data["procedencia"]),$BORDES, 1);
          
          $pdf->SetFont($FONT,'B', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_CUARTA,$ALTO_LINEA + .75, "FECHA ENTREGA:",$BORDES,0);
          $pdf->SetFont($FONT,'', 7 + $aumento_font);
          $pdf->Cell($ANCHO_TICKET_MITAD,$ALTO_LINEA + .75, $seccion["fecha_entrega"], $BORDES, 1);
          
          $ALTO_LINEA = 3.25;
          $pdf->Ln($SALTO_LINEA * 2.75);

          $aumento_font = 1.25;

          $COLS_DETALLE = [
              ["rotulo"=>"Prueba", "ancho"=>60.00, "alineacion"=>"L"],
              ["rotulo"=>"Resultado", "ancho"=>43.00, "alineacion"=>"L"],
              ["rotulo"=>"Unidades", "ancho"=>25.50, "alineacion"=>"L"],
              ["rotulo"=>"Valores de Referencia", "ancho"=>40.00, "alineacion"=>"L"],
              ["rotulo"=>"Método", "ancho"=>20.00, "alineacion"=>"L"],
          ];
          
          $NUMERO_COLS = count($COLS_DETALLE);
          
          $acumulado_cols_detalle = 0.00;
          foreach ($COLS_DETALLE as $key => $value) {
              if ($key < ($NUMERO_COLS - 1)){
                  $acumulado_cols_detalle += $value["ancho"];
              }
          }
          
          $COLS_DETALLE[$NUMERO_COLS - 1]["ancho"] = $ANCHO_TICKET - $acumulado_cols_detalle;
          $ALTO_LINEA = 2.75;

          $pdf->SetFont($FONT,'B', 6 + $aumento_font);
          
          foreach ($COLS_DETALLE as $key => $value) {
              if ($key < ($NUMERO_COLS - 1)){
                  $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES,0, $value["alineacion"]);    
              } else {
                  $pdf->Cell($value["ancho"], $ALTO_LINEA + .5, utf8_decode($value["rotulo"]), $BORDES, 1, $value["alineacion"]);    
              }
          }
          $pdf->Ln($SALTO_LINEA * 1.5);
          $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX()+ $ANCHO_TICKET, $pdf->GetY());
          $pdf->Ln($SALTO_LINEA);

        }
        
        $pdf->SetFont($FONT,'B', 6 + $aumento_font);
        $pdf->Cell($ANCHO_TICKET,$ALTO_LINEA + .75, $seccion["descripcion"],$BORDES, 1);
        $pdf->Ln($SALTO_LINEA * 1.5);

        $pdf->SetFont($FONT,'', 5.5 + $aumento_font); 
        $ALTO_LINEA = $ALTO_LINEA * 1;

        $servicios = $muestra["servicios"];

        $espacio_nivel_base = ["[ ]    ","    *  ","          "];
        $tempLastY = null;
        $tempLastY2 = null;
        foreach ($servicios as $__ => $servicio) {
            $resultados = $servicio["resultados"];
            foreach ($resultados as $___ => $resultado) {
              if ($tempLastY != null){
                $pdf->SetXY($pdf->GetX(), $tempLastY);
              }
              
              $nivel = $resultado["nivel"];
              if ($nivel == "99" || strlen($resultado["descripcion"]) <= 0){
                $espacio_nivel = "";
              } else {
                $espacio_nivel = $espacio_nivel_base[$nivel];
              }

              $i = 0;
              $pdf->CellFitScale($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $espacio_nivel.utf8_decode($resultado["descripcion"]), $BORDES, 0);    
              $tempX = $pdf->GetX();
              $tempInitY = $pdf->GetY();
              $pdf->MultiCell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($resultado["resultado"]), $BORDES,"L",0);
              $tempLastY = $pdf->GetY();
              $pdf->SetXY($tempX + $COLS_DETALLE[$i - 1]["ancho"], $tempInitY);
          

              $pdf->Cell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, $resultado["unidad"], $BORDES,0);
              $tempX = $pdf->GetX();
              $pdf->MultiCell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($resultado["valor_referencial"]), $BORDES, "L", 0);
              $tempLastY2 = $pdf->GetY();
              if ($tempLastY < $tempLastY2){
                $tempLastY = $tempLastY2;
              }
              $pdf->SetXY($tempX + $COLS_DETALLE[$i - 1]["ancho"], $tempInitY);
              $tempX = $pdf->GetX();
              $pdf->MultiCell($COLS_DETALLE[$i++]["ancho"], $ALTO_LINEA + 1.5, utf8_decode($resultado["metodo"]), $BORDES, "L");
              $tempLastY2 = $pdf->GetY();
              if ($tempLastY < $tempLastY2){
                $tempLastY = $tempLastY2;
              }
            }

            $pdf->Ln($SALTO_LINEA * 2);
        }

        $pdf->Image($firma_img, 140, 239, 55);

        if ($mostrar_logo == "1"){
          $pdf->Image("laboratorio_logo_inferior.png", 0, 270, $ANCHO_TICKET_FULL);
        }
    }
}

$pdf->output("I",$data["numero_recibo"]."_".$data["nombre_paciente"].".pdf");
ob_end_flush();
exit;