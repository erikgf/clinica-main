<?php

require_once '../datos/Conexion.clase.php';

class CategoriaServicio extends Conexion {

    public $id_categoria_servicio;
    public $descripcion;
    public $porcentaje_comision;
    public $id_usuario_registrado;
    public $comisiones_sedes;

    private $ID_CATEGORIA_LABORATORIO = "14";

    public function listar(){
        try {
            $sql = "SELECT 
                            cs.id_categoria_servicio as id,
                            cs.descripcion,
                            GROUP_CONCAT(
                                        CONCAT(SUBSTR(s.nombre, 5),' ',  ROUND(porcentaje_comision  * 100, 2),'%')
                                    ) as comisiones_sedes
                            FROM categoria_servicio cs
                            LEFT JOIN categoria_porcentaje_comision cpc ON cpc.id_categoria_servicio = cs.id_categoria_servicio
                                        AND cpc.estado_validez = 'A' 
                                        AND cpc.estado_mrcb 
                                        AND cpc.fecha_fin IS NULL
                            LEFT JOIN sede s ON s.id_sede = cpc.id_sede 
                            WHERE cs.estado_mrcb
                            GROUP BY cs.id_categoria_servicio, cs.descripcion
                            ORDER BY cs.descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarSoloAsistentes(){
        try {
            $sql = "SELECT 
                        cs.id_categoria_servicio as id,
                        cs.descripcion,
                        COALESCE((SELECT ROUND(porcentaje_comision * 100, 2)
                            FROM categoria_porcentaje_comision
                            WHERE estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL 
                            AND id_categoria_servicio = cs.id_categoria_servicio
                            ORDER BY porcentaje_comision DESC LIMIT 1),'0.00') as porcentaje_comision
                    FROM categoria_servicio cs
                    WHERE estado_mrcb AND (cs.es_mostrado_asistentes = 1 OR cs.id_categoria_servicio iN (10))
                    ORDER BY cs.descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    

    public function guardar(){
        try {

            $this->beginTransaction();

            $hoy = date("Y-m-d");
            $fecha_ahora = date("Y-m-d H:i:s");

            $campos_valores = [
                "descripcion"=>$this->descripcion
            ];

            if ($this->id_categoria_servicio == NULL){
                $this->insert("categoria_servicio", $campos_valores);
                $this->id_categoria_servicio = $this->getLastID();

                foreach($this->comisiones_sedes as $key => $value){
                    $campos_valores = [
                        "estado_validez"=>'A',
                        "id_categoria_servicio"=>$this->id_categoria_servicio,
                        "fecha_inicio"=>$hoy,
                        "porcentaje_comision"=>$value->comision / 100.00,
                        "id_sede"=>$value->id_sede,
                        "id_usuario_registrado"=>$this->id_usuario_registrado,
                        "fecha_hora_registrado"=>$fecha_ahora
                    ];
    
                    $this->insert("categoria_porcentaje_comision", $campos_valores);
                }
            } else {
                $campos_valores_where = [
                    "id_categoria_servicio"=>$this->id_categoria_servicio
                ];

                $this->update("categoria_servicio", $campos_valores, $campos_valores_where);

                foreach($this->comisiones_sedes as $key => $value){
                    $porcentaje_comision = $value->comision;

                    $sql = "SELECT porcentaje_comision 
                            FROM categoria_porcentaje_comision 
                            WHERE id_categoria_servicio = :0 AND id_sede = :1 AND estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL
                            LIMIT 1";

                    $objPorcComisionActual = $this->consultarFila($sql, [$this->id_categoria_servicio, $value->id_sede]);

                    if ($objPorcComisionActual === false){
                        $porcentaje_comision_actual = -1;
                    } else {
                        $porcentaje_comision_actual = (float) $objPorcComisionActual["porcentaje_comision"];
                    }

                    if ((float) $porcentaje_comision != $porcentaje_comision_actual){
                        if ($porcentaje_comision_actual >= 0){
                            //es un registro nuevo
                            $campos_valores = [
                                "estado_validez"=>'I',
                                "fecha_fin"=>$hoy
                            ];

                            $campos_valores_where = [
                                "id_categoria_servicio"=>$this->id_categoria_servicio,
                                "id_sede"=>$value->id_sede
                            ];

                            $this->update("categoria_porcentaje_comision", $campos_valores, $campos_valores_where);
                        }
                        
                        $campos_valores = [
                            "estado_validez"=>'A',
                            "id_categoria_servicio"=>$this->id_categoria_servicio,
                            "fecha_inicio"=>$hoy,
                            "id_sede"=>$value->id_sede,
                            "porcentaje_comision"=>$porcentaje_comision / 100.00,
                            "id_usuario_registrado"=>$this->id_usuario_registrado,
                            "fecha_hora_registrado"=>$fecha_ahora
                        ];

                        $this->insert("categoria_porcentaje_comision", $campos_valores);
                    }
                }

            }
            
            $this->commit();
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.");

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function anular(){
        try {
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_categoria_servicio"=>$this->id_categoria_servicio
            ];

            $this->update("categoria_servicio", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function leer(){
        try {
            $sql = "SELECT 
                        cs.id_categoria_servicio,
                        cs.descripcion
                    FROM categoria_servicio cs
                    WHERE estado_mrcb AND id_categoria_servicio = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_categoria_servicio);


            $sql = "SELECT 
                        s.id_sede as id,
                        s.nombre as descripcion,
                        COALESCE(t.comision, '0.00') as comision
                        FROM sede s
                        LEFT JOIN 
                        (SELECT id_sede, ROUND(porcentaje_comision  * 100, 2) as comision
                            FROM categoria_porcentaje_comision cpc
                            WHERE id_categoria_servicio = :0
                            AND cpc.estado_validez = 'A' 
                            AND cpc.estado_mrcb 
                            AND cpc.fecha_fin IS NULL) t 
                        ON s.id_sede = t.id_sede";

            $comisiones_sedes = $this->consultarFilas($sql, [$this->id_categoria_servicio]);

            $data["comisiones_sedes"] = $comisiones_sedes;

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerActivos(){
        try {

            $sql = "SELECT  id_categoria_servicio as id, 
                    cat.descripcion as categoria
                    FROM categoria_servicio cat
                    WHERE cat.estado_mrcb ";
                    
            $servicios =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$servicios);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function buscar($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_categoria_servicio as id,
                    descripcion as text
                    FROM categoria_servicio
                    WHERE estado_mrcb AND descripcion LIKE '%".$cadenaBuscar."%' 
                    LIMIT 5";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }


    public function buscarParaAsistentes($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_categoria_servicio as id,
                    descripcion as text
                    FROM categoria_servicio
                    WHERE estado_mrcb AND descripcion LIKE '%".$cadenaBuscar."%' AND es_mostrado_asistentes = 1
                    LIMIT 5";
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }


     public function obtener($sin_laboratorio = "0"){
        try {
            $sql = "SELECT 
                        cs.id_categoria_servicio as id,
                        cs.descripcion
                    FROM categoria_servicio cs
                    WHERE estado_mrcb AND ".($sin_laboratorio == "1" ? " cs.id_categoria_servicio <> ". $this->ID_CATEGORIA_LABORATORIO : " true ")."
                    ORDER BY cs.descripcion";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}