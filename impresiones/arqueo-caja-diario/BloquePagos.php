<?php

class BloquePagos{
    private $pdf;
    private $ANCHO_TICKET;
    private $ALTO_LINEA;
    private $SALTO_LINEA;
    private $FONT;
    private $aumento_font;
    private $BORDES;
    private $COLS_DETALLE;

    function __construct($pdf, $ANCHO_TICKET, $ALTO_LINEA, $SALTO_LINEA, $FONT, $aumento_font, $BORDES, $COLS_DETALLE){
        $this->pdf = $pdf;
        $this->ANCHO_TICKET = $ANCHO_TICKET;
        $this->ALTO_LINEA = $ALTO_LINEA;
        $this->SALTO_LINEA = $SALTO_LINEA;
        $this->FONT = $FONT;
        $this->aumento_font = $aumento_font;
        $this->BORDES = $BORDES;
        $this->COLS_DETALLE = $COLS_DETALLE;
    }

    public function imprimir(string $rotulo, array $data){
        $this->pdf->SetFont($this->FONT,'B', 5.5 + $this->aumento_font); 
        $this->pdf->Cell($this->ANCHO_TICKET, $this->ALTO_LINEA + .75, utf8_decode($rotulo), $this->BORDES,1);  
        $this->pdf->Ln($this->SALTO_LINEA);

        $total_monto_efectivo = 0.00;
        $total_monto_deposito = 0.00;
        $total_monto_tarjeta = 0.00;
        $total_monto_vuelto = 0.00;
        $total_monto_saldo = 0.00;
        $this->pdf->SetFont($this->FONT,'', 5.25 + $this->aumento_font); 
        foreach ($data as $key => $value) {
            $i = 0;
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["fecha_registro"], $this->BORDES,0 ,"C");    
            //$this->pdf->CellFitScale($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode($value["cliente"]), $this->BORDES,0);    
            $this->pdf->CellFitScale($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode($value["descripcion"]), $this->BORDES,0);
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["recibo"] , $this->BORDES,0 ,"C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["comprobante"] , $this->BORDES,0 ,"C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_efectivo"], $this->BORDES,0, "C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_deposito"], $this->BORDES,0, "C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_tarjeta"], $this->BORDES,0, "C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_descuento"] > 0 ? "-".$value["monto_descuento"] : "0.00", $this->BORDES,0, "C");    
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_vuelto"], $this->BORDES,0, "C"); 
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, $value["monto_saldo"], $this->BORDES, 1, "C");    

            $total_monto_efectivo +=$value["monto_efectivo"];
            $total_monto_deposito +=$value["monto_deposito"];
            $total_monto_tarjeta +=$value["monto_tarjeta"];
            $total_monto_vuelto +=$value["monto_vuelto"];
            $total_monto_saldo +=$value["monto_saldo"];
            
            $this->pdf->SetFont($this->FONT,'I', 5.25 + $this->aumento_font); 

            if ($value["monto_tarjeta"] > 0.00){
            $i = 0;
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, "", $this->BORDES,0 ,"C");
            $this->pdf->CellFitScale($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode("TARJETA: ".$value["numero_tarjeta"]), $this->BORDES,0);   
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode("VOUCHER: ".$value["numero_voucher"]), $this->BORDES,1 ,"L");    
            }

            if ($value["monto_deposito"] > 0.00){
            $i = 0;
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, "", $this->BORDES,0 ,"C");
            $this->pdf->CellFitScale($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode("DEPÓSITO: ".$value["banco"]), $this->BORDES,0);   
            $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode("NÚM OP.: ".$value["numero_operacion"]), $this->BORDES,1 ,"L");    
            }

            $this->pdf->SetFont($this->FONT,'', 5.5 + $this->aumento_font); 
        }

        $this->pdf->SetFont($this->FONT,'B', 5.25 + $this->aumento_font); 
        $i = 0;
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, "", $this->BORDES,0 ,"C");    
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, "", $this->BORDES,0);
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, "", $this->BORDES,0);
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, utf8_decode("TOTAL $rotulo") , $this->BORDES,0 ,"R");
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, number_format($total_monto_efectivo,2), $this->BORDES,0, "C");    
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, number_format($total_monto_deposito,2), $this->BORDES,0, "C");    
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, number_format($total_monto_tarjeta,2), $this->BORDES,0, "C");    
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5,  "0.00", $this->BORDES,0, "C");    
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, number_format($total_monto_vuelto,2), $this->BORDES,0, "C"); 
        $this->pdf->Cell($this->COLS_DETALLE[$i++]["ancho"], $this->ALTO_LINEA + .5, number_format($total_monto_saldo,2), $this->BORDES, 1, "C"); 

        $this->pdf->Ln($this->SALTO_LINEA);


        return [
            "efectivo"=>$total_monto_efectivo,
            "deposito"=>$total_monto_deposito,
            "tarjeta"=>$total_monto_tarjeta,
            "vuelto"=>$total_monto_vuelto,
            "saldo"=>$total_monto_saldo
        ];
    }

}