<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/variables.php';

class CategoriaProduccionMedico extends Conexion {

    public $id_medico;
    public $id_sub_categoria_servicio;
    public $valor;
    public $tipo_valor;
    
    const TIPO_VALOR_MONTO_FIJO = "M";
    const TIPO_VALOR_MONTO_FIJO_SIMBOLO = "S/";
    const TIPO_VALOR_MONTO_FIJO_ROTULO = "Monto Fijo";
    const TIPO_VALOR_PORCENTAJE = "P";
    const TIPO_VALOR_PORCENTAJE_SIMBOLO = "%";
    const TIPO_VALOR_PORCENTAJE_ROTULO = "Porcentaje";

    public function listar(){
        try {

            $sql = "SELECT id_sub_categoria_servicio as id, descripcion
                    FROM sub_categoria_servicio scs
                    WHERE estado_mrcb
                    ORDER BY id_sub_categoria_servicio";

            $categorias = $this->consultarFilas($sql);

            $sql = "SELECT cpm.id_medico, m.nombres_apellidos, 
                        (
                            SELECT
                            GROUP_CONCAT(CONCAT(scc.id_sub_categoria_servicio,'|',COALESCE(_cpm.valor,'0.00'),'|', COALESCE(_cpm.tipo_valor,'M')))
                            FROM sub_categoria_servicio scc
                            LEFT JOIN categoria_produccion_medico _cpm ON _cpm.id_sub_categoria_servicio = scc.id_sub_categoria_servicio AND _cpm.id_medico = cpm.id_medico
                            WHERE scc.estado_mrcb
                            ORDER BY scc.id_sub_categoria_servicio
                        ) as data_categorias
                    FROM categoria_produccion_medico cpm
                    INNER JOIN medico m ON m.id_medico = cpm.id_medico
                    GROUP BY cpm.id_medico, m.nombres_apellidos";
                                        
            $medicos =  $this->consultarFilas($sql);

            $id_medicos_ocupados = "0";

            if (count($medicos) > 0){
                $id_medicos_ocupados = [];
                foreach ($medicos as $i => $medico) {
                    $data_categorias = explode(",", $medico["data_categorias"]);

                    $valores_categoria = array_map(function($categoria){
                        $ar_categoria = explode("|", $categoria);
                        $tipo_valor = $ar_categoria[2];
                        $tipo_valor = [
                            "key"=> $tipo_valor == CategoriaProduccionMedico::TIPO_VALOR_MONTO_FIJO ? CategoriaProduccionMedico::TIPO_VALOR_MONTO_FIJO_SIMBOLO :  CategoriaProduccionMedico::TIPO_VALOR_PORCENTAJE_SIMBOLO,
                            "value"=> $tipo_valor,
                            "desc"=>$tipo_valor == CategoriaProduccionMedico::TIPO_VALOR_MONTO_FIJO ? CategoriaProduccionMedico::TIPO_VALOR_MONTO_FIJO_ROTULO : CategoriaProduccionMedico::TIPO_VALOR_PORCENTAJE_ROTULO
                        ];

                        return [
                            "id_sub_categoria_servicio"=>$ar_categoria[0],
                            "valor"=>$ar_categoria[1],
                            "tipo_valor"=>$tipo_valor
                        ];
                    }, $data_categorias); 

                    $medicos[$i]["valores"] = $valores_categoria;
                    array_push($id_medicos_ocupados, $medico["id_medico"]);
                }

                $id_medicos_ocupados = join(",", $id_medicos_ocupados);
            }

            $sql = "SELECT m.id_medico, m.nombres_apellidos
                    FROM medico m 
                    WHERE m.id_medico NOT IN ({$id_medicos_ocupados}) AND m.estado_mrcb
                    ORDER BY m.nombres_apellidos ";

            $medicosLibres = $this->consultarFilas($sql);

            return ["categorias"=>$categorias, "medicos"=> $medicos, "medicosLibres" => $medicosLibres];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function eliminarMedico(){
        try {
            
            $this->delete("categoria_produccion_medico", [
                "id_medico"=> $this->id_medico
            ]);
            
            return $this->id_medico;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function registrar(){
        try {

            $this->beginTransaction();

            if ($this->valor === "" || $this->valor == 0){
                //eliminar
                $this->eliminar();
            }

            $this->insert("categoria_produccion_medico", [
                "id_medico"=>$this->id_medico,
                "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio,
                "valor"=>$this->valor,
                "tipo_valor"=>$this->tipo_valor
            ]);

            $this->commit();

            return [
                "id_medico"=>$this->id_medico,
                "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio
            ];

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function eliminar(){
        try {

            $this->beginTransaction();

            $this->delete("categoria_produccion_medico", [
                "id_medico"=> $this->id_medico,
                "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio
            ]);

            $this->commit();

            return [
                "id_medico"=>$this->id_medico,
                "id_sub_categoria_servicio"=>$this->id_sub_categoria_servicio
            ];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

}