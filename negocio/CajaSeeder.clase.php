<?php

require_once '../datos/Conexion.clase.php';

class CajaSeeder extends Conexion {

    public function actualizarCorrelativoProactivo(){
        try {

            // 1.- obtengo todos los registros
            //for each => actualizo con la serie y valores respectivos


            $sql = "SELECT id_caja, serie_atencion, serie_ingresos, serie_egresos 
                            FROM caja WHERE estado_mrcb";
            $cajas = $this->consultarFilas($sql);
            
            $this->beginTransaction();

            $this->update("caja_instancia_movimiento", ["correlativo_atencion"=>NULL, "correlativo_egreso"=>NULL, "correlativo_ingreso"=>NULL]);

            foreach ($cajas as $key => $caja) {
                $sql  = "SELECT cim.id_caja_instancia_movimiento as id
                            FROM caja_instancia_movimiento cim
                            INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                            WHERE ci.id_caja = :0 AND cim.id_tipo_movimiento = 1
                            ORDER BY cim.fecha_hora_registrado";

                $registros = $this->consultarFilas($sql, [$caja["id_caja"]]);

                $numero_correlativo = 0;
                foreach ($registros as $i => $registro) {
                    $numero_correlativo = $i + 1;
                    $this->update("caja_instancia_movimiento", 
                            [
                                "correlativo_atencion"=>$numero_correlativo
                            ],
                            [
                                "id_caja_instancia_movimiento"=>$registro["id"]
                            ]);
                }

                $this->update("serie_documento", [
                    "numero"=>$numero_correlativo + 1
                ], [
                    "serie"=>$caja["serie_atencion"],
                    "idtipo_comprobante"=>"CA"
                ]);

                echo "\nCommited, registros atenciones: $numero_correlativo";

                $sql  = "SELECT cim.id_caja_instancia_movimiento as id
                            FROM caja_instancia_movimiento cim
                            INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                            INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                            WHERE ci.id_caja = :0 AND tm.tipo = 'I' AND cim.id_tipo_movimiento NOT IN (1, 4)
                            ORDER BY cim.fecha_hora_registrado";

                $registros = $this->consultarFilas($sql, [$caja["id_caja"]]);

                $numero_correlativo = 0;
                foreach ($registros as $i => $registro) {
                    $numero_correlativo = $i + 1;
                    $this->update("caja_instancia_movimiento", 
                            [
                                "correlativo_ingreso"=>$numero_correlativo
                            ],
                            [
                                "id_caja_instancia_movimiento"=>$registro["id"]
                            ]);
                }

                $this->update("serie_documento", [
                    "numero"=>$numero_correlativo + 1
                ], [
                    "serie"=>$caja["serie_ingresos"],
                    "idtipo_comprobante"=>"IN"
                ]);

                echo "\nCommited, registros INGRESOS: $numero_correlativo";

                $sql  = "SELECT cim.id_caja_instancia_movimiento as id
                            FROM caja_instancia_movimiento cim
                            INNER JOIN caja_instancia ci ON ci.id_caja_instancia = cim.id_caja_instancia
                            INNER JOIN tipo_movimiento tm ON tm.id_tipo_movimiento = cim.id_tipo_movimiento
                            WHERE ci.id_caja = :0 AND tm.tipo = 'E'
                            ORDER BY cim.fecha_hora_registrado";

                $registros = $this->consultarFilas($sql, [$caja["id_caja"]]);

                $numero_correlativo = 0;
                foreach ($registros as $i => $registro) {
                    $numero_correlativo = $i + 1;
                    $this->update("caja_instancia_movimiento", 
                            [
                                "correlativo_egreso"=>$numero_correlativo
                            ],
                            [
                                "id_caja_instancia_movimiento"=>$registro["id"]
                            ]);
                }

                $this->update("serie_documento", [
                    "numero"=>$numero_correlativo + 1
                ], [
                    "serie"=>$caja["serie_egresos"],
                    "idtipo_comprobante"=>"EG"
                ]);

                echo "\nCommited, registros EGRESOS: $numero_correlativo";
            }

            $this->commit();

            return true;

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    

}