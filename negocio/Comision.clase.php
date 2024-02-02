<?php

require_once '../datos/Conexion.clase.php';

class Comision extends Conexion {

    /*@Desechado*/
    public function obtenerComisionEspecialidadXMedico($id_medico){
        try {

            $sql = "SELECT ecc.id_especialidad_medico, ecc.porcentaje_comision
                        FROM especialidad_porcentaje_comision ecc
                        WHERE ecc.id_especialidad_medico IN (SELECT id_especialidad_medico FROM medico WHERE id_medico = :0) 
                                AND estado_validez = 'A' AND fecha_fin IS NULL AND ecc.estado_mrcb";
            $objEspecialidadComision = $this->consultarFila($sql, $id_medico);

            if ($objEspecialidadComision == false){
                $sql = "SELECT id_especialidad_medico, '0.00' as porcentaje_comision FROM medico WHERE id_medico = :0 AND estado_mrcb";
                $objEspecialidadComision = $this->consultarFila($sql, $id_medico);

                if ($objEspecialidadComision == false){
                    throw new Exception("Médico no válido.", 1);
                }
            }

            return $objEspecialidadComision;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerComisionPromotoraXMedico($id_medico){
        try {

            $sql = "SELECT ppc.id_promotora, ppc.porcentaje_comision
                        FROM promotora_porcentaje_comision ppc
                        WHERE ppc.id_promotora IN (SELECT id_promotora FROM medico WHERE id_medico = :0) 
                                AND estado_validez = 'A' AND fecha_fin IS NULL AND ppc.estado_mrcb";
            $objComision = $this->consultarFila($sql, $id_medico);

            if ($objComision == false){
                $sql = "SELECT id_promotora, '0.00' as porcentaje_comision FROM medico WHERE id_medico = :0  AND estado_mrcb";
                $objComision = $this->consultarFila($sql, $id_medico);

                if ($objComision == false){
                    throw new Exception("Médico no válido.", 1);
                }
            }

            return $objComision;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerComisionCategoriaServicio($id_servicio, $id_sede_ordenante = 1){
        try {

            $sql = "SELECT id_categoria_servicio, comision  as porcentaje_comision 
                        FROM servicio WHERE id_servicio = :0";
            $objServicioComision = $this->consultarFila($sql, [$id_servicio]);

            if ($objServicioComision == false){
                throw new Exception("Servicio no válido.", 1);
            }

            if ($objServicioComision["porcentaje_comision"] <= 0.00){
                $sql = "SELECT ecc.id_categoria_servicio, ecc.porcentaje_comision
                        FROM categoria_porcentaje_comision ecc
                        WHERE ecc.id_categoria_servicio IN (:0) AND ecc.id_sede = :1
                                AND estado_validez = 'A' AND fecha_fin IS NULL AND ecc.estado_mrcb";
                $objCategoriaComision = $this->consultarFila($sql, [$objServicioComision["id_categoria_servicio"], $id_sede_ordenante]);
                if ($objCategoriaComision != false){
                    return $objCategoriaComision;
                }
            }

            return $objServicioComision;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}