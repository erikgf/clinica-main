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

    public $arreglo_detalle;
    public $id_lab_seccion;
    public $id_lab_muestra;
    public $se_modifico_detalle_lab_examen;

    public $ID_CATEGORIA_LABORATORIO = "14";
    public $id_usuario_registro;

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

            //$this->descripcion = mb_strtoupper($this->descripcion,'UTF-8');
            $this->descripcion_detallada = mb_strtoupper($this->descripcion_detallada,'UTF-8');

            $campos_valores = [
                "id_servicio"=>$this->id_servicio,
                "descripcion"=>$this->descripcion == "" ? NULL : $this->descripcion,
                "descripcion_detallada"=>$this->descripcion_detallada == "" ? NULL : $this->descripcion_detallada,
                "precio_unitario"=>$this->precio_unitario,
                "precio_venta_sin_igv"=>$this->precio_unitario_sin_igv,
                "id_categoria_servicio"=>$this->id_categoria_servicio,
                "comision"=>$this->comision / 100,
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
                    LEFT JOIN categoria_servicio cat ON se.id_categoria_servicio =  cat.id_categoria_servicio
                    WHERE se.estado_mrcb  AND $sqlFiltroCategoria
                    ORDER BY se.descripcion";
                    
            $servicios =  $this->consultarFilas($sql);
            return $servicios;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    } 

    public function buscar($cadenaBuscar, $mostrarPrecio = true){
        try {
            $params = [];
            $sqlCategoriaServicio  = "true";
            if ($this->id_categoria_servicio != NULL && $this->id_categoria_servicio != ""){
                $params = [$this->id_categoria_servicio];
                $sqlCategoriaServicio  = "id_categoria_servicio = :0";
            }

            if ($mostrarPrecio == "true"){
                $sqlText = "CONCAT(descripcion,' - S/',precio_unitario)";
            } else {
                $sqlText = "descripcion";
            }

            $sql = "SELECT 
                    id_servicio as id,
                    $sqlText as text
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
                    COALESCE(arreglo_perfil,'') as arreglo_perfil,
                    id_categoria_servicio
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
                    id_categoria_servicio,
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
                    COALESCE(comision * 100, '0.00') as comision,
                    cantidad_examenes
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 ";
                    
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);
            return $registro;

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function leerServicioExamen(){
        try {
            $sql = "SELECT  
                    se.id_servicio, 
                    se.descripcion, 
                    se.descripcion_detallada,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    idtipo_afectacion,
                    COALESCE(comision * 100, '0.00') as comision,
                    (SELECT distinct id_lab_seccion FROM lab_examen WHERE id_servicio = se.id_servicio) as id_lab_seccion,
                    (SELECT distinct id_lab_muestra FROM lab_examen WHERE id_servicio = se.id_servicio) as id_lab_muestra
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 ";
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);

            if ($registro != false){
                $niveles = [1,2];
                $detalle = [];
                $sql = "SELECT 
                            le.id_lab_examen,
                            le.descripcion, 
                            COALESCE(abreviatura, '') as abreviatura,
                            COALESCE(unidad,'') as unidad,
                            COALESCE(metodo,'') as metodo,
                            valor_referencial, 
                            nivel,
                            '1' as eliminar
                            FROM lab_examen le
                            WHERE le.estado_mrcb AND le.id_servicio = :0
                            ORDER BY orden_niveluno, orden_niveldos";

                $lab_examenes = $this->consultarFilas($sql, [$this->id_servicio]); 

                foreach ($lab_examenes as $key => $lab_examen) {
                   $sqlDesc = "SELECT 
                            descripcion as valor_referencial,
                            '99' as nivel,
                            '1' as eliminar
                            FROM lab_examendescripcion le
                            WHERE le.id_lab_examen = :0 AND le.estado_mrcb
                            ORDER BY numero_orden";
                   $lab_examendescripciones = $this->consultarFilas($sqlDesc, [$lab_examen["id_lab_examen"]]);

                   array_push($detalle, $lab_examen);
                   if (count($lab_examendescripciones) > 0){
                        $detalle = array_merge($detalle, $lab_examendescripciones); 
                   }
                }

                $registro["detalle"] = $detalle;
            }

            return $registro;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function leerServicioPerfilExamen(){
        try {
            $sql = "SELECT  
                    se.id_servicio, 
                    se.descripcion, 
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    arreglo_perfil
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0 AND arreglo_perfil IS NOT NULL";
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);

            if ($registro != false){
                $cadena_id_servicios = $registro["arreglo_perfil"];
                $sql = "SELECT 
                            id_servicio,
                            descripcion as nombre_servicio,
                            precio_unitario as precio_venta,
                            precio_venta_sin_igv as valor_venta
                            FROM servicio se
                            WHERE se.estado_mrcb AND se.id_servicio IN ($cadena_id_servicios)";
                $registro["detalle"] =$this->consultarFilas($sql); 
            }

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

    public function registrarExamen(){
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

            $campos_valores = [
                "id_servicio"=>$this->id_servicio,
                "descripcion"=>$this->descripcion == "" ? NULL : $this->descripcion,
                "descripcion_detallada"=>$this->descripcion_detallada == "" ? NULL : $this->descripcion_detallada,
                "precio_unitario"=>$this->precio_unitario,
                "precio_venta_sin_igv"=>$this->precio_unitario_sin_igv,
                "id_categoria_servicio"=>$this->ID_CATEGORIA_LABORATORIO,
                "comision"=>$this->comision / 100,
                "cantidad_examenes"=>"1",
                "idtipo_afectacion"=>$this->idtipo_afectacion,
                "arreglo_perfil"=>NULL
            ];

            $this->arreglo_detalle = json_decode($this->arreglo_detalle, true);
            $procedeRegistraExamenLabs = false;

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

                //El arreglo detalle debe venir con al menos 1 FILA, esa FILA tendra un id_lab_examane a lq ue sea hará update, todos los demás s
                //Los demas serán deleteados y se procederá a registrar como si fuesen nuevos.
                //Debe llegar una variable que admita los registros (o sea que asegure que se ha cambiado el detalle exceptuan al primer registro)
                $main_lab_examen = array_shift($this->arreglo_detalle);
                $abreviatura = !isset( $main_lab_examen["abreviatura"]) || $main_lab_examen["abreviatura"] == "" ? NULL : $main_lab_examen["abreviatura"];
                $unidad = !isset($main_lab_examen["unidad"]) || $main_lab_examen["unidad"] == "" ? NULL : $main_lab_examen["unidad"];
                $metodo = !isset($main_lab_examen["metodo"]) || $main_lab_examen["metodo"] == "" ? NULL : $main_lab_examen["metodo"];
                $nivel  = $main_lab_examen["nivel"];

                $campos_valores_detalle = [
                    "descripcion" => $main_lab_examen["descripcion"],
                    "unidad"=>$unidad,
                    "abreviatura"=>$abreviatura,
                    "id_lab_seccion"=>$this->id_lab_seccion,
                    "id_lab_muestra"=>$this->id_lab_muestra,
                    "id_lab_examen_padre"=>NULL,
                    "id_servicio"=>$this->id_servicio,
                    "valor_referencial"=>$main_lab_examen["valores_referenciales"],
                    "metodo"=>$metodo,
                    "nivel"=>$nivel
                ];

                $this->update("lab_examen", $campos_valores_detalle, ["id_lab_examen"=>$main_lab_examen["id_lab_examen"]]);

                if ($this->se_modifico_detalle_lab_examen){
                    $this->ejecutarSimple("DELETE FROM lab_examen WHERE id_servicio = :0 AND id_lab_examen <> :1", [$this->id_servicio, $main_lab_examen["id_lab_examen"]]);
                    $this->ejecutarSimple("UPDATE lab_examendescripcion SET estado_mrcb = 0 WHERE id_lab_examen = :0", [$main_lab_examen["id_lab_examen"]]);
                    $procedeRegistraExamenLabs = true;
                }

            } else{                
                $campos_valores["id_usuario_registrado"] = $this->id_usuario_registrado;
                $campos_valores["fecha_hora_registrado"] = $fecha_ahora;

                $this->insert("servicio", $campos_valores);

                $this->id_servicio = $this->getLastID();

                $procedeRegistraExamenLabs = true;
            }

            if ($procedeRegistraExamenLabs){
                $numero_orden_0 = 0;
                $numero_orden_1 = 0;
                $numero_orden_2 = 0;
                $numero_orden_99 = 0;

                $last_padre_0 = NULL;
                $last_padre_1 = NULL;
                $last_padre = $main_lab_examen["id_lab_examen"];

                foreach ($this->arreglo_detalle as $key => $fila) {
                    $abreviatura = !isset( $fila["abreviatura"]) || $fila["abreviatura"] == "" ? NULL : $fila["abreviatura"];
                    $unidad = !isset($fila["unidad"]) || $fila["unidad"] == "" ? NULL : $fila["unidad"];
                    $metodo = !isset($fila["metodo"]) || $fila["metodo"] == "" ? NULL : $fila["metodo"];
                    $nivel  = (int) $fila["nivel"];

                    if ($nivel <= 3){
                        switch ($nivel){
                            case 0:
                                $campos_valores_detalle = [
                                    "descripcion" => $fila["descripcion"],
                                    "unidad"=>$unidad,
                                    "abreviatura"=>$abreviatura,
                                    "id_lab_seccion"=>$this->id_lab_seccion,
                                    "id_lab_muestra"=>$this->id_lab_muestra,
                                    "id_lab_examen_padre"=>NULL,
                                    "id_servicio"=>$this->id_servicio,
                                    "valor_referencial"=>$fila["valores_referenciales"],
                                    "metodo"=>$metodo,
                                    "nivel"=>$nivel,
                                    "orden_niveluno"=>$numero_orden_0
                                ];

                                $this->insert("lab_examen", $campos_valores_detalle);
                                $last_padre = $this->getLastID();
                                $last_padre_0 = $last_padre;
                                $numero_orden_0++;

                            break;
                            case 1:
                                $campos_valores_detalle = [
                                    "descripcion" => $fila["descripcion"],
                                    "unidad"=>$unidad,
                                    "abreviatura"=>$abreviatura,
                                    "id_lab_seccion"=>$this->id_lab_seccion,
                                    "id_lab_muestra"=>$this->id_lab_muestra,
                                    "id_lab_examen_padre"=>$last_padre_0,
                                    "id_servicio"=>$this->id_servicio,
                                    "valor_referencial"=>$fila["valores_referenciales"],
                                    "metodo"=>$metodo,
                                    "nivel"=>$nivel,
                                    "orden_niveluno"=>$numero_orden_0
                                ];

                                $this->insert("lab_examen", $campos_valores_detalle);
                                $last_padre = $this->getLastID();
                                $last_padre_1 = $last_padre;
                                $numero_orden_1++;
                            break;
                            case 2:
                                $campos_valores_detalle = [
                                    "descripcion" => $fila["descripcion"],
                                    "unidad"=>$unidad,
                                    "abreviatura"=>$abreviatura,
                                    "id_lab_seccion"=>$this->id_lab_seccion,
                                    "id_lab_muestra"=>$this->id_lab_muestra,
                                    "id_lab_examen_padre"=>$last_padre_1,
                                    "id_servicio"=>$this->id_servicio,
                                    "valor_referencial"=>$fila["valores_referenciales"],
                                    "metodo"=>$metodo,
                                    "nivel"=>$nivel,
                                    "orden_niveldos"=>$numero_orden_2
                                ];

                                $this->insert("lab_examen", $campos_valores_detalle);
                                $last_padre = $this->getLastID();
                                $numero_orden_2++;
                            break;
                        }
                    } else {

                        $campos_valores_detalle = [
                            "descripcion" =>$fila["valores_referenciales"],
                            "numero_orden"=>$numero_orden_99,
                            "id_lab_examen"=>$last_padre
                        ];

                        
                        $this->insert("lab_examendescripcion", $campos_valores_detalle);
                        $numero_orden_99++;

                    }
                }
            }

            $this->commit();

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as descripcion, 
                    cat.descripcion as area_categoria,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    COALESCE(comision * 100, '0.00') as porcentaje_comision,
                    'EXAMEN LAB.' as tipo_servicio,
                    '2' as id_tipo_servicio
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

    public function obtenerPreciosXId(){
        try {
            $sql = "SELECT  
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta
                    FROM servicio se 
                    WHERE se.estado_mrcb AND se.id_servicio = :0";
            $registro =  $this->consultarFila($sql, [$this->id_servicio]);
            return $registro;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
                
    public function buscarLaboratorioCombo($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_servicio as id,
                    descripcion as text
                    FROM servicio
                    WHERE estado_mrcb AND descripcion LIKE '%".$cadenaBuscar."%' AND id_categoria_servicio = :0 AND arreglo_perfil IS NULL
                    LIMIT 10";
            $data =  $this->consultarFilas($sql, [$this->ID_CATEGORIA_LABORATORIO]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function registrarPerfilExamen(){
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
            $this->arreglo_detalle = json_decode($this->arreglo_detalle, true);

            $arreglo_perfil = implode(',', $this->arreglo_detalle);

            $campos_valores = [
                "id_servicio"=>$this->id_servicio,
                "descripcion"=>$this->descripcion == "" ? NULL : $this->descripcion,
                "descripcion_detallada"=>NULL,
                "precio_unitario"=>$this->precio_unitario,
                "precio_venta_sin_igv"=>$this->precio_unitario_sin_igv,
                "id_categoria_servicio"=>$this->ID_CATEGORIA_LABORATORIO,
                "comision"=>$this->comision / 100,
                "cantidad_examenes"=>"1",
                "idtipo_afectacion"=>$this->idtipo_afectacion,
                "arreglo_perfil"=>$arreglo_perfil
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

                $procedeRegistraExamenLabs = true;
            }

            $this->commit();

            $sql = "SELECT  id_servicio as id, 
                    se.descripcion as descripcion, 
                    cat.descripcion as area_categoria,
                    precio_unitario as precio_venta,
                    precio_venta_sin_igv as valor_venta,
                    COALESCE(comision * 100, '0.00') as porcentaje_comision,
                    'PERFIL LAB.' as tipo_servicio,
                    '3' as id_tipo_servicio
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
}