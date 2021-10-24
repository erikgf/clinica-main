<?php

require_once '../datos/Conexion.clase.php';

class Servicio extends Conexion {

    public $id_servicio;
    public $descripcion;
    public $descripcion_detallada;
    public $precio_unitario;
    public $precio_unitario_sin_igv;
    public $id_categoria_servicio;
    public $comision;
    public $idtipo_afectacion;
    public $arreglo_perfil;

    public $ID_CATEGORIA_LABORATORIO = "14";

    public function __construct($objDB = null){
        if ($objDB != null){
            parent::__construct($objDB);
        } else {
            parent::__construct();
        }
    }

    public function registrar(){
        try {
            $this->beginTransaction();

            $fecha_ahora = date("Y-m-d H:i:s");
            $estoyEditando = $this->id_servicio != null;

            $numero_repetido = 0;

            $params = $estoyEditando ? [$this->descripcion, $this->id_servicio] : [$this->descripcion];
            $sql = "SELECT COUNT(*) FROM servicio WHERE estado_mrcb AND descripcion = :0 ".($estoyEditando ? " AND id_servicio <> :1 " : "");
            $numero_repetido = $this->consultarValor($sql, $params);

            if ($numero_repetido > 0){
                throw new Exception("El nombre del servicio ya existe en el sistema.", 1);
            }

            $this->descripcion = mb_strtoupper($this->descripcion,'UTF-8');
            $this->descripcion_detallada = mb_strtoupper($this->descripcion_detallada,'UTF-8');

            $campos_valores = [
                "id_servicio"=>$this->id_servicio,
                "descripcion"=>$this->descripcion == "" ? NULL : $this->descripcion,
                "descripcion_detallada"=>$this->descripcion_detallada == "" ? NULL : $this->descripcion_detallada,
                "precio_unitario"=>$this->precio_unitario,
                "precio_venta_sin_igv"=>$this->precio_unitario_sin_igv,
                "id_categoria_servicio"=>$this->id_categoria_servicio,
                "comision"=>$this->comision,
                "cantidad_examenes"=>$this->cantidad_examenes,
                "idtipo_afectacion"=>$this->idtipo_afectacion,
                "arreglo_perfil"=>$this->arreglo_perfil
            ];

            if ($estoyEditando){
                $campos_valores["id_usuario_editado"] = $this->id_usuario_registrado;
                $campos_valores["fecha_hora_editado"] = $fecha_ahora;

                $campos_valores_where = ["id_servicio"=>$this->id_servicio];

                $sql  = "INSERT INTO bitacora_servicio( 
                        id_servicio,
                        descripcion, 
                        descripcion_detallada,
                        precio_unitario,
                        precio_venta_sin_igv,
                        id_categoria_servicio,
                        comision,
                        cantidad_examenes,
                        arreglo_perfil,
                        id_usuario_registrado,
                        fecha_hora_registrado)
                        SELECT  id_servicio,
                                descripcion, 
                                descripcion_detallada,
                                precio_unitario,
                                precio_venta_sin_igv,
                                id_categoria_servicio,
                                comision,
                                cantidad_examenes,
                                arreglo_perfil,
                                :0,
                                CURRENT_TIMESTAMP
                                FROM servicio 
                                WHERE id_servicio = :1 AND estado_mrcb";

                $this->ejecutarSimple($sql, [$this->id_usuario_registrado, $this->id_servicio]);

                $this->update("servicio", $campos_valores, $campos_valores_where);
            } else{                

                $campos_valores["id_usuario_registrado"] = $this->id_usuario_registrado;
                $campos_valores["fecha_hora_registrado"] = $fecha_ahora;

                $this->insert("servicio", $campos_valores);

                $this->id_servicio = $this->getLastID();
            }

            $this->commit();

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as descripcion, 
                    cat.descripcion as area_categoria,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    COALESCE(comision * 100, '0.00') as porcentaje_comision,
                    IF (arreglo_perfil IS NOT NULL AND se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', 'PERFIL LAB.', IF(se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', 'EXAMEN LAB.', 'SERVICIO')) as tipo_servicio,
                    IF (arreglo_perfil IS NOT NULL AND se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', '3', IF(se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', '2', '1')) as id_tipo_servicio
                    FROM servicio se 
                    INNER JOIN categoria_servicio cat ON se.id_categoria_servicio =  cat.id_categoria_servicio
                    WHERE se.estado_mrcb  AND se.id_servicio = :0";
            $registro = $this->consultarFila($sql, [$this->id_servicio]);

            return ["msj"=>"Registro realizado correctamente.", 
                    "registro"=>$registro];  

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listar($filtro = NULL){
        try {
            $sqlFiltroCategoria = " true ";

            if ($filtro != NULL){
                switch($filtro){
                    case "1":
                    $sqlFiltroCategoria = " se.id_categoria_servicio <> '".$this->ID_CATEGORIA_LABORATORIO."' ";
                    break;
                    case "2":
                    $sqlFiltroCategoria = " se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."' ";
                    break;
                    case "3":
                    $sqlFiltroCategoria = " arreglo_perfil IS NOT NULL AND se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."' ";
                    break;
                }
            }

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as descripcion, 
                    cat.descripcion as area_categoria,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    COALESCE(comision * 100, '0.00') as porcentaje_comision,
                    IF (arreglo_perfil IS NOT NULL AND se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', 'PERFIL LAB.', IF(se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', 'EXAMEN LAB.', 'SERVICIO')) as tipo_servicio,
                    IF (arreglo_perfil IS NOT NULL AND se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', '3', IF(se.id_categoria_servicio = '".$this->ID_CATEGORIA_LABORATORIO."', '2', '1')) as id_tipo_servicio
                    FROM servicio se 
                    INNER JOIN categoria_servicio cat ON se.id_categoria_servicio =  cat.id_categoria_servicio
                    WHERE se.estado_mrcb  AND $sqlFiltroCategoria
                    ORDER BY se.descripcion";
                    
            $servicios =  $this->consultarFilas($sql);
            return $servicios;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    } 

    public function buscar($cadenaBuscar){
        try {
            $params = [];
            $sqlCategoriaServicio  = "true";
            if ($this->id_categoria_servicio != NULL && $this->id_categoria_servicio != ""){
                $params = [$this->id_categoria_servicio];
                $sqlCategoriaServicio  = "id_categoria_servicio = :0";
            }

            $sql = "SELECT 
                    id_servicio as id,
                    CONCAT(descripcion,' - S/', precio_unitario) as text
                    FROM servicio
                    WHERE estado_mrcb AND precio_unitario > 0.00 AND descripcion LIKE '%".$cadenaBuscar."%' AND ".$sqlCategoriaServicio."
                    LIMIT 10";
            $data =  $this->consultarFilas($sql, $params);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerActivos(){
        try {

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as servicio, 
                    se.id_categoria_servicio as categoria,
                    precio_unitario as precioUnitario,
                    precio_venta_sin_igv
                    FROM servicio se 
                    WHERE se.estado_mrcb ";
                    
            $servicios =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$servicios);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtener(){
        try {
            $sql = "SELECT  se.id_servicio, 
                    se.descripcion as nombre_servicio, 
                    COALESCE(TRIM(REPLACE(se.descripcion_detallada,'\n',' ')),'') as descripcion,
                    precio_unitario as precio_unitario,
                    idtipo_afectacion,
                    idunidad_medida,
                    precio_venta_sin_igv as precio_sin_IGV,
                    COALESCE(arreglo_perfil,'') as arreglo_perfil
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 ";
                    
            $servicio =  $this->consultarFila($sql, [$this->id_servicio]);

            if ($servicio["arreglo_perfil"] != ""){
                $sql = "SELECT  se.id_servicio, 
                    se.descripcion as nombre_servicio, 
                    COALESCE(TRIM(REPLACE(se.descripcion_detallada,'\n',' ')),'') as descripcion,
                    precio_unitario as precio_unitario,
                    idtipo_afectacion,
                    idunidad_medida,
                    precio_venta_sin_igv as precio_sin_IGV,
                    COALESCE(arreglo_perfil,'') as arreglo_perfil
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio IN (".$servicio["arreglo_perfil"].")";

                $servicio["servicios_perfil"] = $this->consultarFilas($sql);
            }

            return array("rpt"=>true,"datos"=>$servicio);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerTipoAfectacion(){
        try {
            $sql = "SELECT idtipo_afectacion as id, descripcion
                    FROM tipo_afectacion    
                    WHERE estado_mrcb";
                    
            $registros =  $this->consultarFilas($sql);
            return $registros;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function leerServicioGeneral(){
        try {
            $sql = "SELECT  se.id_servicio, 
                    se.descripcion, 
                    se.descripcion_detallada,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    id_categoria_servicio,
                    idtipo_afectacion,
                    comision,
                    cantidad_examenes
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 ";
                    
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);
            return $registro;

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function leerExamenLaboratorio(){
        try {
            $sql = "SELECT  
                    se.id_servicio, 
                    se.descripcion, 
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    idtipo_afectacion,
                    comision
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 ";
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);

            $sql = "SELECT descripcion, id_abreviatura, id_lab_seccion, id_lab_muestra,
                        valor_referencial, nivel
                        FROM lab_examen le
                        WHERE le.estado_mrcb AND le.id_servicio = :0
                        ORDER BY orden_niveluno, orden_niveldos";
            $lab_examenes = $this->consultarFilas($sql, [$this->id_servicio]);

            return $registro;

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function anular(){
        try {
            
            $fecha_ahora = date("Y-m-d H:i:s");

            $campos_valores = [
                "estado_mrcb"=>"0",
                "fecha_hora_anulado"=>$fecha_ahora,
                "id_usuario_anulado"=>$this->id_usuario_registrado
            ];

            $campos_valores_where = [
                "id_servicio"=>$this->id_servicio
            ];

            $this->update("servicio", $campos_valores, $campos_valores_where);

            return ["msj"=>"Anulado correctamente"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
}