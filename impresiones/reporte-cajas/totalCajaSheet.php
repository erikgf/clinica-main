<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

function imprimirCajaTotales(Worksheet $sheet, int $filaInicio, int $columnaInicio, array $data) : int {
    $actualFila = $filaInicio;
    $cabeceraEstilos = array( 'font' => array('bold' => true, 'name' => 'Calibri','size' => 17),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER)
                                );

    $arregloCabecera = [
        ["ancho"=>25,"rotulo"=>""],
        ["ancho"=>40,"rotulo"=>""],
        ["ancho"=>25,"rotulo"=>"EFECTIVO"],
        ["ancho"=>25,"rotulo"=>"EFEC. AMORTIZA"],
        ["ancho"=>25,"rotulo"=>"TICKET"],
        ["ancho"=>25,"rotulo"=>"TOTAL EFECTIVO"],
        ["ancho"=>25,"rotulo"=>"DEPOSITO"],
        ["ancho"=>25,"rotulo"=>"AMORTIZA. DEPÓSITO"],
        ["ancho"=>25,"rotulo"=>"TOTAL DEPÓSITO"],
        ["ancho"=>25,"rotulo"=>"TARJETA"],
        ["ancho"=>25,"rotulo"=>"AMORTIZACIÓN TARJETA"],
        ["ancho"=>25,"rotulo"=>"TOTAL TARJETAS"],
        ["ancho"=>25,"rotulo"=>"SOBRANTES / FALTANTES"],
        ["ancho"=>25,"rotulo"=>"SALDOS"],
        ["ancho"=>25,"rotulo"=>"TOTAL DE VENTAS"],
    ];

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        $sheet->setCellValue($columna.$actualFila, $value["rotulo"]);			
        $sheet->getColumnDimension($columna)->setWidth($value["ancho"]);
        $sheet->getRowDimension($actualFila)->setRowHeight(55);
    }

    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->applyFromArray($cabeceraEstilos);
    $sheet->getStyle(letraAlfabeto($columnaInicio).$actualFila.':'.$columna.$actualFila)->getAlignment()->setWrapText(true);
    $actualFila++;

    $primeraFila = $actualFila;
    $primeraColumna = $columnaInicio;
    foreach ($data as $key => $value) {
        $columnaInicio = $primeraColumna;
        $caja = $value["caja"];
        $totales = $value["totales"];
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $caja["descripcion"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $caja["nombre_usuario"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["efectivo"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["efectivo_amortizacion"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["ticket"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, "=+C".$actualFila."+D".$actualFila."+E".$actualFila);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["deposito"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["deposito_amortizacion"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, "=+G".$actualFila."+H".$actualFila);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["tarjeta"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["tarjeta_amortizacion"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, "=+J".$actualFila."+K".$actualFila);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, "0.00");
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, $totales["saldos"]);
        $sheet->setCellValue(letraAlfabeto($columnaInicio++).$actualFila, "=+F".$actualFila."+I".$actualFila."+L".$actualFila."+N".$actualFila);

        $actualFila++;
    }
    $ultimaFila = $actualFila - 1;
    $ultimaColumna = $columnaInicio - 1;

    $cuerpoEstilos = array( 'font' => array('bold' => false, 'name' => 'Calibri','size' => 18));
    $sheet->getStyle(letraAlfabeto($primeraColumna).$primeraFila.':'.letraAlfabeto($ultimaColumna).$ultimaFila)->applyFromArray($cuerpoEstilos);

    $sheet->getStyle(letraAlfabeto($primeraColumna + 2).$primeraFila.':'.letraAlfabeto($ultimaColumna).($ultimaFila + 1))
            ->getNumberFormat()
            ->setFormatCode('_-[$S/-es-PE] * #,##0.00_-;-[$S/-es-PE] * #,##0.00_-;_-[$S/-es-PE] * "-"??_-;_-@_-');

    foreach ($arregloCabecera as $key => $value) {
        $columna = letraAlfabeto($key);
        if ($key === 1){
            $sheet->setCellValue(letraAlfabeto($key).$actualFila, "TOTALES");	
            $sheet->getStyle(letraAlfabeto($key).$actualFila)->applyFromArray($cabeceraEstilos);	
        }

        if ($key > 1){
            $sheet->setCellValue($columna.$actualFila, "=SUM(".$columna.$primeraFila.":".$columna.$ultimaFila.")");
        }
    }
    
    $ultimaFila = $actualFila;

    $totalesEstilos = array('font' => array('bold' => true, 'name' => 'Arial','size' => 18),
                            'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT)
    );
    $sheet->getStyle("C".$ultimaFila.":O".$ultimaFila)->applyFromArray($totalesEstilos);	

    $estiloTotalEfectivo = array('font' => array('bold' => true, 'name' => 'Arial','size' => 18),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                                'fill' => array('fillType' => Fill::FILL_SOLID, "startColor"=> array('rgb' => 'B7DEE8'))
    );
    $sheet->getStyle("F".$primeraFila.":F".$ultimaFila)->applyFromArray($estiloTotalEfectivo);	

    $estiloTotalDeposito = array('font' => array('bold' => true, 'name' => 'Arial','size' => 18),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                                'fill' => array('fillType' => Fill::FILL_SOLID, "startColor"=> array('rgb' => 'B8CCE4'))
    );
    $sheet->getStyle("I".$primeraFila.":I".$ultimaFila)->applyFromArray($estiloTotalDeposito);	

    $estiloTotalTarjetas = array('font' => array('bold' => true, 'name' => 'Arial','size' => 18),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_RIGHT),
                                'fill' => array('fillType' => Fill::FILL_SOLID, "startColor"=> array('rgb' => 'CCC0DA'))
    );
    $sheet->getStyle("L".$primeraFila.":L".$ultimaFila)->applyFromArray($estiloTotalTarjetas);	

    return $actualFila;
}

function imprimirCajaTotalesSheet(Worksheet $sheet, array $data, string $fecha){
    $actualFila = 1;

    $primeraColumna = "A";
    $ultimaColumna = "O";

    $sheet->setCellValue($primeraColumna.$actualFila, "RENDICIÓN DE CAJA $fecha");	

    $tituloEstilos = array( 'font' => array('bold' => true, 'name' => 'Calibri','size' => 22),
                                'alignment' => array('horizontal' => Alignment::HORIZONTAL_CENTER)
                                );

    $sheet->getStyle($primeraColumna.$actualFila)->applyFromArray($tituloEstilos);

    $sheet->mergeCells($primeraColumna.$actualFila.":".$ultimaColumna.$actualFila);

    $actualFila++;

    imprimirCajaTotales($sheet, $actualFila, 0, $data);

    $sheet->setTitle("TOTAL");
}

