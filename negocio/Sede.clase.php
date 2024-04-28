<?php

require_once '../datos/Conexion.clase.php';

class Sede extends Conexion {
    public $id_sede;
    public $descripcion;

    public $id_usuario_registrado;

    public function listar(){
        try {
            $sql = "SELECT 
                        id_sede as id,
                        nombre as descripcion
                    FROM sede 
                    WHERE estado_mrcb
                    ORDER BY nombre";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }   

}