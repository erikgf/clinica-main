<?php

require_once '../datos/Conexion.clase.php';

class Medico extends Conexion {
    public $id_medico;
    public $id_promotora;
    public $numero_documento;
    public $apellidos_nombres;
    public $colegiatura;
    public $rne;
    public $telefono_uno;
    public $telefono_dos;
    public $correo;
    public $domicilio;
    public $id_especialidad;
    public $id_usuario_registrado;
    public $observaciones;
    public $es_informante;
    public $tipo_personal_medico;
    public $es_realizante;

    public function buscar($cadenaBuscar){
        try {
            $sql = "SELECT 
                    id_medico as id,
                    TRIM(COALESCE(nombres_apellidos,'')) as text
                    FROM medico
                    WHERE estado_mrcb AND
                        COALESCE(nombres_apellidos,'') LIKE '%".$cadenaBuscar."%' 
                    LIMIT 5";
                    
            $data =  $this->consultarFilas($sql);
            return array("rpt"=>true,"datos"=>$data);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    public function guardar(){
        try {
            $fecha_ahora = date("Y-m-d H:i:s");

            $this->apellidos_nombres = mb_strtoupper($this->apellidos_nombres,'UTF-8');

            $campos_valores = [
                "numero_documento"=>$this->numero_documento,
                "nombres_apellidos"=>$this->apellidos_nombres,
                "colegiatura"=>$this->colegiatura,
                "rne"=>$this->rne,
                "telefono_uno"=>$this->telefono_uno,
                "telefono_dos"=>$this->telefono_dos,
                "correo"=>$this->correo,
                "domicilio"=>$this->domicilio,
                "id_promotora"=>$this->id_promotora == "" ? NULL : $this->id_promotora,
                "id_especialidad_medico"=>$this->id_especialidad,
                "observaciones"=>$this->observaciones == "" ? NULL : $this->observaciones,
                "es_informante"=>$this->es_informante,
                "tipo_personal_medico"=>$this->tipo_personal_medico,
                "es_realizante"=>$this->es_realizante
            ];

            if ($this->id_medico == NULL){
                $campos_valores["fecha_hora_registro"] = $fecha_ahora;
                $this->insert("medico", $campos_valores);
                $this->id_medico = $this->getLastID();
            } else {
                $campos_valores["fecha_hora_edicion"] = $fecha_ahora;

                $campos_valores_where = [
                    "id_medico"=>$this->id_medico
                ];

                $sql  = "INSERT INTO bitacora_medico(
                    id_medico, 
                    numero_documento,
                    nombres_apellidos,
                    colegiatura,
                    rne,
                    telefono_uno,
                    telefono_dos,
                    correo,
                    domicilio,
                    id_especialidad_medico,
                    id_promotora,
                    observaciones,
                    tipo_personal_medico,
                    es_informante,
                    es_realizante,
                    id_usuario_registrado,
                    fecha_hora_registrado)
                    SELECT  id_medico, 
                            numero_documento,
                            nombres_apellidos,
                            colegiatura,
                            rne,
                            telefono_uno,
                            telefono_dos,
                            correo,
                            domicilio,
                            id_especialidad_medico,
                            id_promotora,
                            observaciones,
                            tipo_personal_medico,
                            es_informante,
                            es_realizante,
                            :0,
                            CURRENT_TIMESTAMP
                            FROM medico WHERE id_medico = :1 AND estado_mrcb";

                $this->ejecutarSimple($sql, [$this->id_usuario_registrado, $this->id_medico]);
                $this->update("medico", $campos_valores, $campos_valores_where);
            }

            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos as medico,
                        m.colegiatura,
                        m.rne, 
                        CONCAT(telefono_uno,' ',telefono_dos) as telefonos,
                        m.correo,
                        m.domicilio,
                        pr.descripcion as promotora,
                        esp.descripcion as especialidad
                FROM medico m 
                LEFT JOIN promotora pr ON pr.id_promotora = m.id_promotora
                LEFT JOIN especialidad_medico esp ON esp.id_especialidad_medico = m.id_especialidad_medico
                WHERE m.estado_mrcb AND m.id_medico = :0";
            $registro = $this->consultarFila($sql, [$this->id_medico]);
            
            return array("rpt"=>true, "msj"=>"Registro realizado correctamente.",
                            "registro"=>$registro);

        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function anular(){
        try {
            $fecha_ahora = date("Y-m-d H:i:s");

            $campos_valores = [
                "fecha_hora_anulado"=>$fecha_ahora,
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_medico"=>$this->id_medico
            ];

            $this->update("medico", $campos_valores, $campos_valores_where);
            
            return array("rpt"=>true, "msj"=>"Registro anulado correctamente.");
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function listar(){
        try {
            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos as medico,
                        m.colegiatura,
                        m.rne, 
                        CONCAT(telefono_uno,' ',telefono_dos) as telefonos,
                        m.correo,
                        m.domicilio,
                        pr.descripcion as promotora,
                        esp.descripcion as especialidad
                    FROM medico m 
                    LEFT JOIN promotora pr ON pr.id_promotora = m.id_promotora
                    LEFT JOIN especialidad_medico esp ON esp.id_especialidad_medico = m.id_especialidad_medico
                    WHERE m.estado_mrcb AND m.id_medico NOT IN (1,2)";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leer(){
        try {
            $sql = "SELECT 
                        id_medico,
                        numero_documento,
                        nombres_apellidos as apellidos_nombres,
                        colegiatura,
                        rne, 
                        telefono_uno,
                        telefono_dos,
                        correo,
                        domicilio,
                        COALESCE(id_promotora,'') as id_promotora,
                        id_especialidad_medico as id_especialidad,
                        observaciones,
                        es_informante,
                        tipo_personal_medico,
                        es_realizante
                    FROM medico
                    WHERE estado_mrcb AND id_medico = :0";
                    
            $data =  $this->consultarFila($sql, $this->id_medico);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarMedicosValidosParaPromotoras(){
        try {
            $sql = "SELECT 
                    id_medico as id,
                    TRIM(COALESCE(nombres_apellidos,'')) as text,
                    CONCAT(telefono_uno,' ',telefono_dos) as telefonos
                    FROM medico
                    WHERE estado_mrcb AND id_medico NOT IN (1,2)";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarPromotoras(){
        try {
            $sql = "SELECT 
                    id_promotora as id,
                    TRIM(COALESCE(descripcion,'')) as text
                    FROM promotora
                    WHERE estado_mrcb";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarMedicosXPromotora(){
        try {
            $sql = "SELECT 
                    id_medico as id,
                    TRIM(COALESCE(nombres_apellidos,'')) as text
                    FROM medico
                    WHERE estado_mrcb AND id_medico NOT IN (1,2) AND id_promotora = :0";
                    
            $data =  $this->consultarFilas($sql, [$this->id_promotora]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function registrarMedicosEnPromotores($arregloIdMedicos){
        try {
            $cantidadMedicos = count($arregloIdMedicos);
            $cadenaIdMedicos = implode(",", $arregloIdMedicos);
            if ($cadenaIdMedicos == ""){
                return ["msj"=>"No hay medicos que agregar."];
            }

            $sql = "UPDATE medico SET id_promotora = :0 WHERE id_medico IN ('".$cadenaIdMedicos."')";
            $this->ejecutarSimple($sql, [$this->id_promotora]);

            return ["msj"=>"Operación correcta. ".$cantidadMedicos." médicos asignados."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarMedicosParaLiquidaciones($fecha_inicio, $fecha_fin, $totales_mayores_a){
        try {

            //coger todos los servicios realizados y hacer un distinct con el médico en cuestión, tomar en cujenta lols medicos 
            //realizantes.
            $sql = "SELECT 
                    am.id_medico_ordenante as id_medico, me.nombres_apellidos as descripcion, 
                        SUM(ams.monto_comision_categoria_sin_igv) as sin_igv
                    FROM atencion_medica am 
                    INNER JOIN medico me ON me.id_medico = am.id_medico_ordenante
                    INNER JOIN atencion_medica_servicio ams  ON am.id_atencion_medica = ams.id_atencion_medica
                    WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1)
                    GROUP BY am.id_medico_ordenante, me.nombres_apellidos
                    HAVING sin_igv >= :2
                    ORDER BY me.nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $totales_mayores_a]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarAtencionesComisionParaLiquidacionXMedico($fecha_inicio, $fecha_fin){
        try {

            $params = [$fecha_inicio, $fecha_fin];
            $sqlMedico = " true ";

            if ($this->id_medico != ""){
                $sqlMedico = " am.id_medico_ordenante = :2 ";
                array_push($params, $this->id_medico);
            }

            $sql = "SELECT 
                        CONCAT(ams.id_servicio) as codigo, ams.nombre_servicio, 
                            COUNT(ams.id_servicio) as cantidad_servicios,
                            ROUND((SUM(sub_total) / 1.18),2)  as subtotal_sin_igv,     
                            ROUND(SUM(ams.monto_comision_categoria),2) as con_igv, 
                            ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as sin_igv
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND $sqlMedico
                        GROUP BY ams.id_servicio, ams.nombre_servicio
                        HAVING  sin_igv > 0.00
                        ORDER BY ams.nombre_servicio";
                    
            $data =  $this->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarAtencionesComisionParaLiquidacionXMedicoImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a){
        try {

            $params = [$fecha_inicio, $fecha_fin];
            $sqlMedico = " true ";

            if ($this->id_medico != ""){
                $sqlMedico = " am.id_medico_ordenante = :2 ";
                array_push($params, $this->id_medico);
            }

            $sql = "SELECT 
                    distinct am.id_medico_ordenante as id_medico, me.nombres_apellidos, 
                        SUM(ams.monto_comision_categoria_sin_igv) as sin_igv,
                        COUNT(distinct(am.id_paciente)) as total_pacientes
                    FROM atencion_medica am 
                    INNER JOIN medico me ON me.id_medico = am.id_medico_ordenante
                    INNER JOIN atencion_medica_servicio ams  ON am.id_atencion_medica = ams.id_atencion_medica
                    WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND $sqlMedico
                    GROUP BY am.id_medico_ordenante, me.nombres_apellidos
                    HAVING sin_igv >= ".$totales_mayores_a."
                    ORDER BY me.nombres_apellidos";
            $data = $this->consultarFilas($sql, $params);

            $sql = "SELECT cs.descripcion as categoria, cs.id_categoria_servicio
                    FROM atencion_medica am 
                    INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                    INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio
                    INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = ser.id_categoria_servicio
                    WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND am.id_medico_ordenante = :2
                    GROUP BY cs.descripcion, cs.id_categoria_servicio
                    ORDER BY cs.descripcion DESC";

            foreach ($data as $key => $medico) {
                $categorias = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $medico["id_medico"]]);
                
                $sql_ = "SELECT 
                            am.fecha_atencion, am.nombre_paciente, ams.nombre_servicio,  
                                    SUM(ams.sub_total), (SUM(sub_total) / 1.18)  as subtotal_sin_igv, 
                                    SUM(ams.monto_comision_categoria) as con_igv, 
                                    SUM(ams.monto_comision_categoria_sin_igv) as sin_igv,
                                    porcentaje_comision_categoria
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        INNER JOIN servicio ser ON ser.id_servicio = ams.id_servicio
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND am.id_medico_ordenante = :2 AND ser.id_categoria_servicio = :3
                        GROUP BY am.fecha_atencion, am.nombre_paciente, ams.id_servicio, ams.nombre_servicio
                        ORDER BY ams.nombre_servicio DESC";
            
                foreach ($categorias as $_key => $value) {
                    $categorias[$_key]["atenciones"] =  $this->consultarFilas($sql_, [$fecha_inicio, $fecha_fin, $medico["id_medico"], $value["id_categoria_servicio"]]);
                }
                
                $data[$key]["categorias"] = $categorias;
            }
                    
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarLiquidacionesMedicos($fecha_inicio, $fecha_fin, $totales_mayores_a){
        try {

            $params = [$fecha_inicio, $fecha_fin];

            $sql = "SELECT 
                        m.id_medico as codigo,
                        m.nombres_apellidos as medicos,
                        ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as comision_sin_igv
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        INNER JOIN medico m ON m.id_medico = am.id_medico_ordenante
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND id_medico_ordenante NOT IN (1,2)
                        GROUP BY m.id_medico, m.nombres_apellidos
                        HAVING  comision_sin_igv > ".$totales_mayores_a."
                        ORDER BY m.nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarLiquidacionesMedicosImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a){
        try {

            $params = [$fecha_inicio, $fecha_fin];

            $sql = "SELECT 
                        m.id_medico as codigo,
                        m.nombres_apellidos as medicos,
                        ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as comision_sin_igv
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        INNER JOIN medico m ON m.id_medico = am.id_medico_ordenante
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND id_medico_ordenante NOT IN (1,2)
                        GROUP BY m.id_medico, m.nombres_apellidos
                        HAVING  comision_sin_igv > ".$totales_mayores_a."
                        ORDER BY m.nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql, $params);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarMedicosLiquidacionXPromotoraImprimir($fecha_inicio, $fecha_fin){
        try {

            $sql = "SELECT descripcion as nombre_promotora, 
                        (SELECT porcentaje_comision 
                            FROM promotora_porcentaje_comision
                            WHERE estado_validez = 'A' AND estado_mrcb AND fecha_fin IS NULL AND id_promotora = pr.id_promotora) as porcentaje_comision
                    FROM promotora pr
                    WHERE pr.id_promotora = :0 AND estado_mrcb";
            $cabecera = $this->consultarFila($sql, [$this->id_promotora]);

            if ($cabecera == false){
                throw new Exception("ID Promotora no encontrado.", 1);
            }

            $sql = "SELECT 
                        m.id_medico as codigo,
                        m.nombres_apellidos as medicos,
                        COUNT(ams.id_servicio) as cantidad_servicios,
                        ROUND((SUM(sub_total) / 1.18),2)  as subtotal_sin_igv,
                        ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as comision_sin_igv,
                        ROUND(SUM(ams.monto_comision_categoria),2) as monto_comision_con_igv
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        INNER JOIN medico m ON m.id_medico = am.id_medico_ordenante
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND id_medico_ordenante NOT IN (1,2) AND id_promotora_ordenante = :2
                        GROUP BY m.id_medico, m.nombres_apellidos
                        HAVING  comision_sin_igv > 0.00
                        ORDER BY m.nombres_apellidos";
                    
            $cabecera["registros"] =  $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin ,$this->id_promotora]);

            return $cabecera;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function obtenerMedicosInformantes($buscar = null){
        try {
            $sqlBuscar = "";
            if ($buscar != null){
                $sqlBuscar = " AND m.nombres_apellidos LIKE '%".$buscar."%'" ;
            }

            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos as medico,
                        m.tipo_personal_medico,
                        m.id_medico as id,
                        m.nombres_apellidos as text
                    FROM medico m 
                    WHERE m.estado_mrcb AND m.es_informante IN (1,2) $sqlBuscar
                    ORDER BY nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerMedicosRealizantes($buscar = null){
        try {
            $sqlBuscar = "";
            if ($buscar != null){
                $sqlBuscar = " AND m.nombres_apellidos LIKE '%".$buscar."%'" ;
            }

            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos as medico,
                        m.tipo_personal_medico,
                        m.id_medico as id,
                        m.nombres_apellidos as text
                    FROM medico m 
                    WHERE m.estado_mrcb AND m.es_realizante IN (1) $sqlBuscar
                    ORDER BY nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    

    public function getMedicosOrdenantesRandom($cantidad, $fecha_inicio, $fecha_fin){
        try {


            $sql = "SELECT 
                        distinct am.id_medico_ordenante as id_medico
                    FROM atencion_medica am 
                    WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1)
                    ORDER BY RAND()
                    LIMIT ".$cantidad;
                    
            $data =  $this->consultarFilas($sql, [$fecha_inicio,$fecha_fin]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarLiquidacionesSeguimientoMedico($fecha_inicio, $fecha_fin, $totales_mayores_a){
        try {

            $params = [$fecha_inicio, $fecha_fin];

            $sql = "SELECT MONTH('$fecha_inicio') as mes_inicio, YEAR('$fecha_inicio') as anio_inicio,  MONTH('$fecha_fin') as mes_fin, YEAR('$fecha_fin') as anio_fin";
            $dataMesAnioFechas = $this->consultarFila($sql);

            $fechas = [];

            $mesIterado = $dataMesAnioFechas["mes_inicio"];
            $anioIterado = $dataMesAnioFechas["anio_inicio"];

            $seguirLoop = false;
            do {
                array_push($fechas, $mesIterado.'-'.$anioIterado);
                $detenerLoop = $mesIterado == $dataMesAnioFechas["mes_fin"] && $anioIterado == $dataMesAnioFechas["anio_fin"];

                //detenerLoop = false
                if ($mesIterado >= 12){
                    $mesIterado = 1;
                    $anioIterado++;
                } else{
                    $mesIterado++;
                }

            } while (!$detenerLoop);
            
            $sql = "SELECT 
                        m.nombres_apellidos as medico,
                        COALESCE(pr.descripcion, 'NO TIENE') as promotora,
                        CONCAT(MONTH(am.fecha_atencion),'-',YEAR(am.fecha_atencion)) as mes_anio_atencion,
                        ROUND(SUM(ams.monto_comision_categoria_sin_igv),2) as comision_sin_igv
                        FROM atencion_medica am 
                        INNER JOIN atencion_medica_servicio ams ON am.id_atencion_medica = ams.id_atencion_medica AND ams.estado_mrcb
                        INNER JOIN medico m ON m.id_medico = am.id_medico_ordenante
                        LEFT JOIN promotora pr ON pr.id_promotora = am.id_promotora_ordenante
                        WHERE am.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND  :1) AND id_medico_ordenante NOT IN (1,2)
                        GROUP BY m.id_medico, m.nombres_apellidos, CONCAT(MONTH(am.fecha_atencion),'-',YEAR(am.fecha_atencion))
                        HAVING  comision_sin_igv >  ".$totales_mayores_a."
                        ORDER BY m.nombres_apellidos";
                    
            $data =  $this->consultarFilas($sql, $params);
            return ["data"=>$data, "fechas"=>$fechas];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    

}