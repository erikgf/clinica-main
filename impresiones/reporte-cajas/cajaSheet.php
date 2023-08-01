<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

function letraAlfabeto($index){
    $alfabeto = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    return $alfabeto[$index];
}

function imprimirAtenciones(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data): int {
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["ancho"=>14,"rotulo"=>"FECHA"],
        ["ancho"=>14,"rotulo"=>"RECIBO"],
        ["ancho"=>18,"rotulo"=>"COMPROBANTE"],
        ["ancho"=>45,"rotulo"=>"CLIENTE"],
        ["ancho"=>45,"rotulo"=>"PACIENTE"],
        ["ancho"=>18,"rotulo"=>"MTO. EFECTIVO"],
        ["ancho"=>18,"rotulo"=>"MTO. DEPÓSITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TARJETA"],
        ["ancho"=>18,"rotulo"=>"MTO. CRÉDITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TOTAL"],
        ["ancho"=>55,"rotulo"=>"DETALLE"],
        ["ancho"=>12,"rotulo"=>"ESTADO"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
    }

    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );

    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    $actualFila++;

    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $anulado = $registro["estado"] === "ANULADA";
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"]);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);
        $columnaEstado = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEstado.$actualFila, $registro["estado"]);

        if ($anulado){
            $sheet->getStyle($columnaFecha.$actualFila.':'.$columnaEstado.$actualFila)->applyFromArray($colorAnulado);
        }

        $actualFila++;
    }

    return $actualFila;
}

function imprimirSaldos(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data) : int{
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["rotulo"=>"SALDOS"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
    }

    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );

    $sheet->mergeCells(letraAlfabeto($key).$actualFila.":".letraAlfabeto($key + 1).$actualFila);
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    
    $actualFila++;

    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $anulado = $registro["estado"] === "ANULADA";
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"]);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);
        $columnaEstado = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEstado.$actualFila, $registro["estado"]);

        if ($anulado){
            $sheet->getStyle($columnaFecha.$actualFila.':'.$columnaEstado.$actualFila)->applyFromArray($colorAnulado);
        }

        $actualFila++;
    }

    return $actualFila;
}

function imprimirSumatoriaAtencionesSaldos(Worksheet $sheet, int $filaInicio, int $columnaInicio, int $filaSumatoriaInicio, int $filaSumatoriaFinal) : int {
    $columnaEfectivo = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaEfectivo.$filaInicio, "=SUM(".$columnaEfectivo.$filaSumatoriaInicio.":".$columnaEfectivo.$filaSumatoriaFinal.")");
    $columnaDeposito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaDeposito.$filaInicio, "=SUM(".$columnaDeposito.$filaSumatoriaInicio.":".$columnaDeposito.$filaSumatoriaFinal.")");
    $columnaTarjeta = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTarjeta.$filaInicio, "=SUM(".$columnaTarjeta.$filaSumatoriaInicio.":".$columnaTarjeta.$filaSumatoriaFinal.")");
    $columnaCredito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaCredito.$filaInicio, "=SUM(".$columnaCredito.$filaSumatoriaInicio.":".$columnaCredito.$filaSumatoriaFinal.")");
    $columnaTotal = letraAlfabeto($columnaInicio);
    $sheet->setCellValue($columnaTotal.$filaInicio, "=SUM(".$columnaTotal.$filaSumatoriaInicio.":".$columnaTotal.$filaSumatoriaFinal.")");

    
    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
                                );

    $sheet->getStyle($columnaEfectivo.$filaInicio.':'.$columnaTotal.$filaInicio)->applyFromArray($subCabeceraEstilos);

    return $filaInicio;
}

function imprimirNotasCredito(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data){
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["rotulo"=>"NOTAS CRÉDITO"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
    }

    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );

    $sheet->mergeCells(letraAlfabeto($key).$actualFila.":".letraAlfabeto($key + 1).$actualFila);
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    
    $actualFila++;

    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $anulado = $registro["estado"] === "ANULADA";
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"] * -1);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"] * -1);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"] * -1);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"] * -1);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);
        $columnaEstado = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEstado.$actualFila, $registro["estado"]);

        if ($anulado){
            $sheet->getStyle($columnaFecha.$actualFila.':'.$columnaEstado.$actualFila)->applyFromArray($colorAnulado);
        }

        $actualFila++;
    }

    return $actualFila;
}

function imprimirTotalVentasContable(Worksheet $sheet, int $filaInicio, int $columnaInicio, int $filaSumatoria, int $filaInicialSumatoriaCredito, int $filaFinalSumatoriaCredito) : int {
    $columnaDescripcion = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaDescripcion.$filaInicio, "TOTAL VENTAS CONTABLES");

    $estiloDescripcion = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                            'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
    );
    $estiloTotales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );

    $columnaEfectivo = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaEfectivo.$filaInicio, "=SUM(".$columnaEfectivo.$filaSumatoria.",".$columnaEfectivo.$filaInicialSumatoriaCredito.":".$columnaEfectivo.$filaFinalSumatoriaCredito.")");
    $columnaDeposito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaDeposito.$filaInicio, "=SUM(".$columnaDeposito.$filaSumatoria.",".$columnaDeposito.$filaInicialSumatoriaCredito.":".$columnaDeposito.$filaFinalSumatoriaCredito.")");
    $columnaTarjeta = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTarjeta.$filaInicio, "=SUM(".$columnaTarjeta.$filaSumatoria.",".$columnaTarjeta.$filaInicialSumatoriaCredito.":".$columnaTarjeta.$filaFinalSumatoriaCredito.")");
    $columnaCredito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaCredito.$filaInicio, "=SUM(".$columnaCredito.$filaSumatoria.",".$columnaCredito.$filaInicialSumatoriaCredito.":".$columnaCredito.$filaFinalSumatoriaCredito.")");
    $columnaTotal = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTotal.$filaInicio, "=SUM(".$columnaTotal.$filaSumatoria.",".$columnaTotal.$filaInicialSumatoriaCredito.":".$columnaTotal.$filaFinalSumatoriaCredito.")");

    $sheet->getStyle($columnaDescripcion.$filaInicio)->applyFromArray($estiloDescripcion);
    $sheet->getStyle($columnaEfectivo.$filaInicio.":".$columnaTotal.$filaInicio)->applyFromArray($estiloTotales);


    return $filaInicio;
}

function imprimirTicketOtros(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data){
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["rotulo"=>"TICKET Y OTROS INGRESOS"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
    }
    
    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );

    $sheet->mergeCells(letraAlfabeto($key).$actualFila.":".letraAlfabeto($key + 2).$actualFila);
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    
    $actualFila++;

    $arregloCabecera = [
        ["ancho"=>14,"rotulo"=>"FECHA"],
        ["ancho"=>14,"rotulo"=>"RECIBO"],
        ["ancho"=>18,"rotulo"=>"COMPROBANTE"],
        ["ancho"=>45,"rotulo"=>"CLIENTE"],
        ["ancho"=>45,"rotulo"=>"PACIENTE"],
        ["ancho"=>18,"rotulo"=>"MTO. EFECTIVO"],
        ["ancho"=>18,"rotulo"=>"MTO. DEPÓSITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TARJETA"],
        ["ancho"=>18,"rotulo"=>"MTO. CRÉDITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TOTAL"],
        ["ancho"=>55,"rotulo"=>"MOTIVO NOTA"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
    }

    $subCabeceraTotales= array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
                        );  

    $sheet->getStyle( letraAlfabeto(0).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    $actualFila++;

    $primeraFila = $actualFila;

    foreach ($data as $key => $registro) {
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"]);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);

        $actualFila++;
    }

    $ultimaFila = $actualFila - 1;
    
    $columnaRotulo = 4;
    $columnaDescripcion = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaDescripcion.$actualFila, "TOTAL TICKET Y OTROS INGRESOS");

    $columnaRotulo++;
    $columnaEfectivo = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaEfectivo.$actualFila, "=SUM(".$columnaEfectivo.$primeraFila.":".$columnaEfectivo.$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $columnaTotal = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaTotal.$actualFila, "=SUM(".$columnaTotal.$primeraFila.":".$columnaTotal.$ultimaFila.")");

    $sheet->getStyle($columnaDescripcion.$actualFila)->applyFromArray($subCabeceraEstilos);
    $sheet->getStyle($columnaEfectivo.$actualFila.":".$columnaTotal.$actualFila)->applyFromArray($subCabeceraTotales);

    return $actualFila;
}

function imprimirAmortizaciones(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data){
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["rotulo"=>"AMORTIZACIONES"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
    }

    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );
    $sheet->mergeCells(letraAlfabeto($key).$actualFila.":".letraAlfabeto($key + 1).$actualFila);
    $sheet->getStyle($columna.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);

    $actualFila++;

    $arregloCabecera = [
        ["ancho"=>14,"rotulo"=>"FECHA"],
        ["ancho"=>14,"rotulo"=>"RECIBO"],
        ["ancho"=>18,"rotulo"=>"COMPROBANTE"],
        ["ancho"=>45,"rotulo"=>"CLIENTE"],
        ["ancho"=>45,"rotulo"=>"PACIENTE"],
        ["ancho"=>18,"rotulo"=>"MTO. EFECTIVO"],
        ["ancho"=>18,"rotulo"=>"MTO. DEPÓSITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TARJETA"],
        ["ancho"=>18,"rotulo"=>"MTO. CRÉDITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TOTAL"],
        ["ancho"=>55,"rotulo"=>"MOTIVO NOTA"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
    }

    $subCabeceraTotales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
                                );
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    $actualFila++;

    $primeraFila = $actualFila;


    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"]);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);
        $sheet->getStyle($columnaCredito.$actualFila)->applyFromArray($colorAnulado);
        
        $actualFila++;
    }

    $ultimaFila = $actualFila - 1;
    
    $columnaRotulo = 4;
    $columnaDescripcion = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "TOTAL AMORTIZACIONES");

    $columnaRotulo++;
    $columnaEfectivo =  letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaEfectivo.$actualFila, "=SUM(".$columnaEfectivo.$primeraFila.":".$columnaEfectivo.$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $columnaCredito = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaCredito.$actualFila, "=SUM(".$columnaCredito.$primeraFila.":".$columnaCredito.$ultimaFila.")");
    $sheet->getStyle($columnaCredito.$actualFila)->applyFromArray($colorAnulado);
    $columnaRotulo++;
    $columnaTotal =  letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaTotal.$actualFila, "=SUM(".$columnaTotal.$primeraFila.":".$columnaTotal.$ultimaFila.")");

    $sheet->getStyle($columnaDescripcion.$actualFila)->applyFromArray($subCabeceraEstilos);
    $sheet->getStyle($columnaEfectivo.$actualFila.":".$columnaTotal.$actualFila)->applyFromArray($subCabeceraTotales);

    return $actualFila;
}

function imprimirTotalIngresos(Worksheet $sheet, int $filaInicio, int $columnaInicio, int $filaTotalVentasContable, int $filaTotalTicket, int $filaTotalAmortizaciones) : int {
    $columnaDescripcion = letraAlfabeto($columnaInicio);
    $sheet->setCellValue($columnaDescripcion.$filaInicio, "TOTAL INGRESOS");
    
    $columnaInicio = $columnaInicio + 4;
    $sheet->mergeCells($columnaDescripcion.$filaInicio.":".letraAlfabeto($columnaInicio).$filaInicio);

    $estiloDescripcion = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                            'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
    );
    $sheet->getStyle($columnaDescripcion.$filaInicio)->applyFromArray($estiloDescripcion);

    $estiloTotales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );


    $columnaInicio++;
    $columnaEfectivo = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaEfectivo.$filaInicio, "=SUM(".$columnaEfectivo.$filaTotalVentasContable.",".$columnaEfectivo.$filaTotalTicket.",".$columnaEfectivo.$filaTotalAmortizaciones.")");
    $columnaDeposito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaDeposito.$filaInicio, "=SUM(".$columnaDeposito.$filaTotalVentasContable.",".$columnaDeposito.$filaTotalTicket.",".$columnaDeposito.$filaTotalAmortizaciones.")");
    $columnaTarjeta = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTarjeta.$filaInicio, "=SUM(".$columnaTarjeta.$filaTotalVentasContable.",".$columnaTarjeta.$filaTotalTicket.",".$columnaTarjeta.$filaTotalAmortizaciones.")");
    $columnaCredito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaCredito.$filaInicio, "=SUM(".$columnaCredito.$filaTotalVentasContable.",".$columnaCredito.$filaTotalTicket.",".$columnaCredito.$filaTotalAmortizaciones.")");
    $columnaTotal = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTotal.$filaInicio, "=SUM(".$columnaTotal.$filaTotalVentasContable.",".$columnaTotal.$filaTotalTicket.",".$columnaTotal.$filaTotalAmortizaciones.")");

    
    $sheet->getStyle($columnaEfectivo.$filaInicio.":".$columnaTotal.$filaInicio)->applyFromArray($estiloTotales);

    return $filaInicio;
}

function imprimirEgresos(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data){
    $actualFila = $filaInicio;

    $arregloCabecera = [
        ["rotulo"=>"EGRESOS"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
    }

    $subCabeceraEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );
    $sheet->mergeCells(letraAlfabeto($key).$actualFila.":".letraAlfabeto($key + 1).$actualFila);
    $sheet->getStyle($columna.$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);

    $actualFila++;

    $arregloCabecera = [
        ["ancho"=>14,"rotulo"=>"FECHA"],
        ["ancho"=>14,"rotulo"=>"RECIBO"],
        ["ancho"=>18,"rotulo"=>"COMPROBANTE"],
        ["ancho"=>45,"rotulo"=>"CLIENTE"],
        ["ancho"=>45,"rotulo"=>"PACIENTE"],
        ["ancho"=>18,"rotulo"=>"MTO. EFECTIVO"],
        ["ancho"=>18,"rotulo"=>"MTO. DEPÓSITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TARJETA"],
        ["ancho"=>18,"rotulo"=>"MTO. CRÉDITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TOTAL"],
        ["ancho"=>55,"rotulo"=>"MOTIVO NOTA"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
    }

    $subCabeceraTotales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 10),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
                                );
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($subCabeceraEstilos);
    $actualFila++;

    $primeraFila = $actualFila;

    $colorAnulado = array('font' => array('color' => ['argb' => 'EB2B02']));

    foreach ($data as $key => $registro) {
        $i = $columnaInicio;
        $columnaFecha = letraAlfabeto($i++);
        $sheet->setCellValue($columnaFecha.$actualFila, $registro["fecha"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["recibo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["comprobante"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["cliente"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["paciente"]);
        $columnaEfectivo = letraAlfabeto($i++);
        $sheet->setCellValue($columnaEfectivo.$actualFila, $registro["monto_efectivo"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_deposito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["monto_tarjeta"]);
        $columnaCredito = letraAlfabeto($i++);
        $sheet->setCellValue($columnaCredito.$actualFila, $registro["monto_credito"]);
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, "=SUM(".$columnaEfectivo.$actualFila.":".$columnaCredito.$actualFila.")");
        $sheet->setCellValue(letraAlfabeto($i++).$actualFila, $registro["detalle"]);
        $sheet->getStyle($columnaFecha.$actualFila.':'.$columnaCredito.$actualFila)->applyFromArray($colorAnulado);
        
        $actualFila++;
    }

    $ultimaFila = $actualFila - 1;
    
    $actualFila++;

    $columnaRotulo = 0;
    $columnaDescripcion = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaDescripcion.$actualFila, "TOTAL EGRESOS");
    $columnaRotulo = $columnaRotulo + 4;
    $sheet->mergeCells($columnaDescripcion.$actualFila.":".letraAlfabeto($columnaRotulo).$actualFila);

    $columnaRotulo++;
    $columnaEfectivo =  letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaEfectivo.$actualFila, "=SUM(".$columnaEfectivo.$primeraFila.":".$columnaEfectivo.$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $sheet->setCellValue(letraAlfabeto($columnaRotulo).$actualFila, "=SUM(".letraAlfabeto($columnaRotulo).$primeraFila.":".letraAlfabeto($columnaRotulo).$ultimaFila.")");
    $columnaRotulo++;
    $columnaCredito = letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaCredito.$actualFila, "=SUM(".$columnaCredito.$primeraFila.":".$columnaCredito.$ultimaFila.")");
    $columnaRotulo++;
    $columnaTotal =  letraAlfabeto($columnaRotulo);
    $sheet->setCellValue($columnaTotal.$actualFila, "=SUM(".$columnaTotal.$primeraFila.":".$columnaTotal.$ultimaFila.")");

    $sheet->getStyle($columnaDescripcion.$actualFila)->applyFromArray($subCabeceraEstilos);
    $sheet->getStyle($columnaEfectivo.$actualFila.":".$columnaTotal.$actualFila)->applyFromArray($subCabeceraTotales);

    return $actualFila;
}

function imprimirTotales(Worksheet $sheet, int $filaInicio, int $columnaInicio, int $filaTotalIngresos, int $filaTotalEgresos) : int {
    $subCabeceraEstilos= array('font' => array('bold' => true, 'name' => 'Arial','size' => 11),
                        'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                        );  

    $arregloCabecera = [
        ["ancho"=>18,"rotulo"=>"MTO. EFECTIVO"],
        ["ancho"=>18,"rotulo"=>"MTO. DEPÓSITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TARJETA"],
        ["ancho"=>18,"rotulo"=>"MTO. CRÉDITO"],
        ["ancho"=>18,"rotulo"=>"MTO. TOTAL"]
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key + 5);
        $sheet->setCellValue($columna.$filaInicio, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
    }

    $sheet->getStyle(letraAlfabeto($columnaInicio).$filaInicio.':'.$columna.$filaInicio)->applyFromArray($subCabeceraEstilos);
    $filaInicio++;

    $columnaDescripcion = letraAlfabeto($columnaInicio);
    $sheet->setCellValue($columnaDescripcion.$filaInicio, "TOTALES");
    $columnaInicio = $columnaInicio + 4;
    $sheet->mergeCells($columnaDescripcion.$filaInicio.":".letraAlfabeto($columnaInicio).$filaInicio);

    $estiloDescripcion = array('font' => array('bold' => true, 'name' => 'Arial','size' => 12, 'color' => ['argb' => '002060']),
                            'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
    );
    $sheet->getStyle($columnaDescripcion.$filaInicio)->applyFromArray($estiloDescripcion);

    $estiloTotales = array('font' => array('bold' => true, 'name' => 'Arial','size' => 12, 'color' => ['argb' => '002060']),
        'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );

    $columnaInicio++;
    $columnaEfectivo = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaEfectivo.$filaInicio, "=SUM(".$columnaEfectivo.$filaTotalIngresos.",".$columnaEfectivo.$filaTotalEgresos." * -1)");
    $columnaDeposito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaDeposito.$filaInicio, "=SUM(".$columnaDeposito.$filaTotalIngresos.",".$columnaDeposito.$filaTotalEgresos." * -1)");
    $columnaTarjeta = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTarjeta.$filaInicio, "=SUM(".$columnaTarjeta.$filaTotalIngresos.",".$columnaTarjeta.$filaTotalEgresos." * -1)");
    $columnaCredito = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaCredito.$filaInicio, "=SUM(".$columnaCredito.$filaTotalIngresos.",".$columnaCredito.$filaTotalEgresos." * -1)");
    $columnaTotal = letraAlfabeto($columnaInicio++);
    $sheet->setCellValue($columnaTotal.$filaInicio, "=SUM(".$columnaTotal.$filaTotalIngresos.",".$columnaTotal.$filaTotalEgresos." * -1)");

    
    $sheet->getStyle($columnaEfectivo.$filaInicio.":".$columnaTotal.$filaInicio)->applyFromArray($estiloTotales);

    return $filaInicio;
}

function imprimirCajaSheet(Worksheet $sheet, array $data){
    $actualFila = 1;

    $caja = $data["caja"];
    $sheet->setCellValue(letraAlfabeto(0).$actualFila, "INFORME DE ATENCIONES + COMPROBANTES");	

    $actualFila++;
    $sheet->setCellValue(letraAlfabeto(0).$actualFila, "Fecha: ".$caja["fecha"]);
    $sheet->setCellValue(letraAlfabeto(4).$actualFila, "CAJA:");
    $sheet->setCellValue(letraAlfabeto(5).$actualFila, $caja["descripcion"]);
    $sheet->setCellValue(letraAlfabeto(7).$actualFila, "USUARIO:");
    $sheet->setCellValue(letraAlfabeto(8).$actualFila, $caja["nombre_usuario"]);

    $actualFila = $actualFila + 2;
    $filaInicialSumatoria = $actualFila + 1;
    $actualFila = imprimirAtenciones($sheet, $actualFila, 0, $data["atenciones"]);
    $actualFila++;
    $filaFinalSumatoria = imprimirSaldos($sheet, $actualFila, 0, $data["saldos"]);
    $actualFila = $filaFinalSumatoria;
    $actualFila++;
    $filaSumatoria = imprimirSumatoriaAtencionesSaldos($sheet, $actualFila, 5, $filaInicialSumatoria, $filaFinalSumatoria);
    $actualFila = $filaSumatoria;
    $actualFila++;
    $filaInicialSumatoriaCredito = $actualFila + 1;
    $filaFinalSumatoriaCredito = imprimirNotasCredito($sheet, $actualFila, 0, $data["notas_credito"]);
    $actualFila = $filaFinalSumatoriaCredito;
    $actualFila++;
    $filaTotalVentasContable = imprimirTotalVentasContable($sheet, $actualFila, 4, $filaSumatoria, $filaInicialSumatoriaCredito, $filaFinalSumatoriaCredito);
    $actualFila = $filaTotalVentasContable;
    $actualFila++;
    $filaTotalTicket = imprimirTicketOtros($sheet, $actualFila, 0, $data["tickets_e_ingresos"]);
    $actualFila = $filaTotalTicket;
    $actualFila++;
    $filaTotalAmortizaciones = imprimirAmortizaciones($sheet, $actualFila, 0, $data["amortizaciones"]);
    $actualFila = $filaTotalAmortizaciones;
    $actualFila = $actualFila + 2;
    $filaTotalIngresos = imprimirTotalIngresos($sheet, $actualFila, 0, $filaTotalVentasContable, $filaTotalTicket, $filaTotalAmortizaciones);
    $actualFila = $filaTotalIngresos;
    $actualFila = $actualFila + 2;
    $filaTotalEgresos = imprimirEgresos($sheet, $actualFila, 0, $data["egresos"]);
    $actualFila = $filaTotalEgresos;
    $actualFila = $actualFila + 2;
    $actualFila = imprimirTotales($sheet, $actualFila, 0, $filaTotalIngresos, $filaTotalEgresos);

    $sheet->getStyle("F1:J".$actualFila)
            ->getNumberFormat()
            ->setFormatCode('_-[$S/-es-PE] * #,##0.00_-;-[$S/-es-PE] * #,##0.00_-;_-[$S/-es-PE] * "-"??_-;_-@_-');

    $sheet->setTitle($caja["descripcion"]."-".$caja["dni_usuario"]);
}

