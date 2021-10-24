<?php

require_once '../datos/Conexion.clase.php';

class Ubigeo extends Conexion {

    public function obtenerDepartamentos($cadenaBuscar){
        try {

            $sql = "SELECT id, name as text FROM ubigeo_peru_departments WHERE name LIKE '%".$cadenaBuscar."%' ";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerProvincias($cadenaBuscar, $idDepartamento){
        try {

            $sql = "SELECT id, name as text FROM ubigeo_peru_provinces WHERE name LIKE '%".$cadenaBuscar."%' AND department_id = :0";
            $data =  $this->consultarFilas($sql, [$idDepartamento]);
            return array("rpt"=>true,"datos"=>$data);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerDistritos($cadenaBuscar, $idProvincia){
        try {

            $sql = "SELECT id, name as text FROM ubigeo_peru_districts WHERE name LIKE '%".$cadenaBuscar."%' AND province_id = :0 ";
            $data =  $this->consultarFilas($sql, [$idProvincia]);
            return array("rpt"=>true,"datos"=>$data);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}