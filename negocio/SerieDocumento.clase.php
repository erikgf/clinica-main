<?php

require_once '../datos/Conexion.clase.php';

class SerieDocumento extends Conexion {
    public function obtenerSeries($tipoComprobantes){
        try {

            $strTipoComprobantes = "(";
            $cantidadTipoComprobantes = count($tipoComprobantes);

            for ($i=0; $i < $cantidadTipoComprobantes; $i++) { 
                $strTipoComprobantes .= "'".$tipoComprobantes[$i]."'";

                if ($i + 1 <  $cantidadTipoComprobantes){
                    $strTipoComprobantes .= ",";
                }
            }

            $strTipoComprobantes .= ")";

            $sql = "SELECT serie, numero, sd.idtipo_comprobante, ci.id_caja_instancia
                    FROM serie_documento sd
                    INNER JOIN caja c ON sd.serie = c.serie_boleta OR sd.serie = c.serie_factura
                    LEFT JOIN caja_instancia ci ON ci.id_caja = c.id_caja
                    WHERE ci.estado_caja = 'A' AND ci.estado_mrcb AND c.estado_mrcb AND sd.idtipo_comprobante IN $strTipoComprobantes 
                    ORDER BY idtipo_comprobante DESC, serie";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);

        } catch (Exception $exc) {
            return array("rpt"=>false,"msj"=>$exc->getMessage());
        }
    }

}