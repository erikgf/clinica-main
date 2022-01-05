<?php

require_once '../datos/Conexion.clase.php';
require_once '../datos/variables.php';

class AtencionMedicaServicio extends Conexion {
    public $id_atencion_medica;
    public $id_atencion_medica_servicio;
    public $id_medico_atendido;
    public $id_medico_realizante;
    public $observaciones_atendido;
    
    public $resultados_examenes_laboratorio;
    public $fue_atendido;
    public $id_usuario_registrado;

    
    public $ID_TIPO_ACCION_MUESTRA = 1;
    public $ID_TIPO_ACCION_ENTREGA = 2;
    public $ID_TIPO_ACCION_RESULTADO = 3;
    public $ID_TIPO_ACCION_VALIDADO = 4;
    public $ID_TIPO_ACCION_CANCELAR_VALIDADO = 5;
    
    
    public function listarExamenesAdministrador($fecha_inicio, $fecha_fin, $id_area = "*"){
        try {
            $sqlEstado = " ";
            switch ($this->fue_atendido){
                case "*":
                $sqlEstado = " ";
                break;
                case "P":
                $sqlEstado = " AND fue_atendido = '0'";
                break;
                case "R":
                $sqlEstado = " AND fue_atendido = '1'";
                break;
                case "C":
                $sqlEstado = " AND fue_atendido = '2'";
                break;
            }

            $sqlMedicoAtendido = " ";
            if (!($this->id_medico_atendido == NULL || $this->id_medico_atendido == "")){
                $sqlMedicoAtendido = " AND id_medico_atendido = ".$this->id_medico_atendido;
            }

            $sqlMedicoRealizante = " ";
            if (!($this->id_medico_realizante == NULL || $this->id_medico_realizante == "")){
                $sqlMedicoRealizante = " AND id_medico_realizado = ".$this->id_medico_realizante;
            }

            $sqlArea = "";
            if ($id_area != "*" && strlen($id_area) > 0){
                $sqlArea = " AND s.id_categoria_servicio = ".$id_area;
            }
            

            $sql  = "SELECT 
                    amd.id_atencion_medica,
                    amd.id_atencion_medica_servicio,
                    am.numero_acto_medico as recibo, 
                    am.nombre_paciente, 
                    DATE_FORMAT(am.fecha_atencion,'%d-%m-%Y') as fecha_atencion, 
                    amd.nombre_servicio as examen, 
                    cs.descripcion as area, 
                    amd.sub_total as monto_examen,
                    am.importe_total as monto, 
                    am.monto_descuento as monto_descuento,
                    (am.pago_credito - (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica)) as monto_deuda,  
                    (SELECT COUNT(*) FROM  caja_instancia_movimiento cim
                        WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb 
                        AND cim.id_registro_atencion_relacionada = am.id_atencion_medica) as veces_amortizacion,
                    CONCAT(IF(am.pago_efectivo > 0, 'EF ',''),
                    IF(am.pago_tarjeta > 0, 'TJ ',''), IF(am.pago_deposito > 0, 'DP ',''), 
                    IF(am.pago_credito > 0, 'CR ','')) as metodo_pago, 
                    amd.fue_atendido, 
                    (CASE amd.fue_atendido WHEN '0' THEN 'white' WHEN '1' THEN 'gradient-success' ELSE 'gradient-danger' END) as rotulo_color_atendido,
                    (CASE amd.fue_atendido WHEN '0' THEN 'PENDIENTE' WHEN '1' THEN 'REALIZADO' ELSE 'CANCELADO' END) as rotulo_atendido,
                    DATE_FORMAT(amd.fecha_hora_atendido, '%d-%m-%Y') as fecha_atendido, 
                    DATE_FORMAT(amd.fecha_hora_atendido, '%H:%i:%s') as hora_atendido, 
                    COALESCE(amd.observaciones_atendido,'-') as observaciones_atendido,
                    COALESCE(m.nombres_apellidos,'-') as medico_atendido,
                    COALESCE(mr.nombres_apellidos,'-') as medico_realizado,
                    CONCAT(col.apellido_paterno,' ',col.apellido_materno,' ',col.nombres) as usuario_registro
                    FROM atencion_medica_servicio amd 
                    INNER JOIN atencion_medica am ON am.id_atencion_medica = amd.id_atencion_medica
                    INNER JOIN servicio s ON s.id_servicio = amd.id_servicio
                    INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = s.id_categoria_servicio
                    LEFT JOIN medico m ON m.id_medico = amd.id_medico_atendido
                    LEFT JOIN medico mr ON mr.id_medico = amd.id_medico_realizado
                    LEFT JOIN usuario u ON u.id_usuario = amd.id_usuario_atendido
                    LEFT JOIN colaborador col ON col.id_colaborador = u.id_colaborador
                    WHERE am.estado_mrcb AND amd.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) 
                            AND cs.es_mostrado_asistentes = 1
                            $sqlEstado
                            $sqlMedicoAtendido
                            $sqlMedicoRealizante
                            $sqlArea  ORDER BY am.numero_acto_medico DESC";

            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarExamenesAsistentes($fecha_inicio, $fecha_fin, $id_area){
        try {
            $sql  = "SELECT 
                    amd.id_atencion_medica_servicio, 
                    am.numero_acto_medico as recibo,
                    am.nombre_paciente, 
                    DATE_FORMAT(am.fecha_atencion,'%d-%m-%Y') as fecha_atencion, 
                    amd.nombre_servicio as examen, 
                    cs.descripcion as area, 
                    (am.pago_credito - (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica)) as monto_deuda,
                    amd.fue_atendido, 
                    (CASE amd.fue_atendido WHEN '0' THEN 'white' WHEN '1' THEN 'gradient-success' ELSE 'gradient-danger' END) as rotulo_color_atendido,
                    amd.fecha_hora_atendido, 
                    amd.observaciones_atendido,
                    amd.id_medico_atendido,
                    amd.id_medico_realizado
                    FROM atencion_medica_servicio amd 
                    INNER JOIN atencion_medica am ON am.id_atencion_medica = amd.id_atencion_medica
                    INNER JOIN servicio s ON s.id_servicio = amd.id_servicio
                    INNER JOIN categoria_servicio cs ON cs.id_categoria_servicio = s.id_categoria_servicio
                    WHERE am.estado_mrcb AND amd.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND cs.id_categoria_servicio = :2
                    ORDER BY am.numero_acto_medico DESC";
            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $id_area]);

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    public function guardarRevision(){
        try {
            $this->beginTransaction();

            $sql = "SELECT ams.id_atencion_medica_servicio, 
                        (am.pago_credito - (SELECT COALESCE(SUM(cim.monto_efectivo + cim.monto_tarjeta + cim.monto_deposito),'0.00')
                                    FROM caja_instancia_movimiento cim 
                                    WHERE cim.id_tipo_movimiento IN (4) AND cim.estado_mrcb AND cim.id_registro_atencion_relacionada = am.id_atencion_medica)) as monto_deuda
                        FROM atencion_medica_servicio ams
                        INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                        WHERE ams.id_atencion_medica_servicio = :0 AND ams.estado_mrcb";
            $objAtencionMedicaServicio = $this->consultarFila($sql, [$this->id_atencion_medica_servicio]);

            if ($objAtencionMedicaServicio == false){
                throw new Exception("Atención médica no válida", 1);
            }

            if ($objAtencionMedicaServicio["monto_deuda"] > 0 && $this->fue_atendido != "2"){
                throw new Exception("El examen pertence a un RECIBO que aún figura con una DEUDA PENDIENTE.", 1);
            }

            $fecha_hora_atendido = date("Y-m-d H:i:s");

            $this->observaciones_atendido = $this->observaciones_atendido  == "" ? NULL :mb_strtoupper($this->observaciones_atendido,'UTF-8');
            $this->fue_atendido = $this->fue_atendido == "" ? "0" : $this->fue_atendido;

            if ($this->fue_atendido != "1"){
                $this->id_medico_atendido = NULL;
                $this->id_medico_realizante = NULL;
                if ($this->fue_atendido == "0"){
                    $this->observaciones_atendido = "";
                }
            }

            $campos_valores = [
                "id_medico_atendido"=>$this->id_medico_atendido == "" ? NULL : $this->id_medico_atendido,
                "id_medico_realizado"=>$this->id_medico_realizante == "" ? NULL : $this->id_medico_realizante,
                "observaciones_atendido"=>$this->observaciones_atendido,
                "fue_atendido"=>$this->fue_atendido,
                "id_usuario_atendido"=>$this->id_usuario_registrado,
                "fecha_hora_atendido"=>$fecha_hora_atendido
            ];

            $campos_valores_where = [
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio
            ];

            $this->update("atencion_medica_servicio", $campos_valores, $campos_valores_where);

            $campos_valores = [
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                "id_medico_informante"=>$this->id_medico_atendido == "" ? NULL : $this->id_medico_atendido,
                "id_medico_realizante"=>$this->id_medico_realizante == "" ? NULL : $this->id_medico_realizante,
                "observaciones"=>$this->observaciones_atendido,
                "estado_atendido"=>$this->fue_atendido,
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$fecha_hora_atendido
            ];

            $this->insert("bitacora_atencion_medica_servicio_revision", $campos_valores);

            $this->commit();
            return ["msj"=>"Atención actualizada.", "fecha_hora_atendido"=>$fecha_hora_atendido];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function listarServicioLaboratorioExamen(){
        try {
            $sql  = "SELECT ams.nombre_servicio as servicio_atencion,
                        IF(ams.fecha_hora_validado IS NOT NULL,'1','0')  as fue_validado,
                        IF(ams.fecha_hora_resultado IS NOT NULL,'1','0')  as tiene_resultados,
                        COALESCE(le.arreglo_perfil,'') as arreglo_perfil
                        FROM atencion_medica_servicio ams
                        LEFT JOIN lab_examen le ON le.id_servicio = ams.id_servicio
                        WHERE ams.id_atencion_medica_servicio = :0";
            $datos = $this->consultarFila($sql, [$this->id_atencion_medica_servicio]);

            if ($datos["tiene_resultados"] == "1"){
                $sql  = "SELECT 
                        COALESCE(id_lab_examen, '') as id_lab_examen,
                        nivel,
                        descripcion,
                        resultado,
                        COALESCE(unidad, '') as unidad,
                        valor_minimo,
                        valor_maximo,
                        valores_referencia as valor_referencial,
                        COALESCE(metodo, '') as metodo
                        FROM atencion_medica_servicio_laboratorio_resultados
                        WHERE id_atencion_medica_servicio = :0 AND estado_mrcb
                        ORDER BY orden_ubicacion";

                $examenes_registros = $this->consultarFilas($sql, [$this->id_atencion_medica_servicio]);
                $datos["detalle"] = $examenes_registros;

                return $datos;
            }

            $examenes_registros = $this->obtenerServiciosLaboratorioExamenesDescripciones($datos["arreglo_perfil"]);
            $datos["detalle"] = $examenes_registros;
            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function guardarServicioLaboratorioExamen(){

        try {
            $this->beginTransaction();

            $this->arreglo_examenes = json_decode($this->arreglo_examenes, true);

            if ($this->id_atencion_medica == "" || $this->id_atencion_medica == null){
                throw new Exception("Se debe seleccionar un ID de atención válido.", 1);
            }

            $fecha_hora_registrado = date("Y-m-d H:i:s");
            $total_examenes =count($this->arreglo_examenes);
            if ($total_examenes > 0){
                foreach ($this->arreglo_examenes as $key => $value) {
                    $se_registro_entrega = $value["fecha_hora_entrega"] != "";

                    $this->update("atencion_medica_servicio", 
                                    [
                                        "fue_muestreado"=>$value["fue_muestreado"],
                                        "fecha_hora_muestra"=>$value["fecha_hora_muestra"] == "" ? $fecha_hora_registrado : $value["fecha_hora_muestra"],
                                        "id_usuario_muestra"=>$value["fecha_hora_muestra"] == "" ? NULL : $this->id_usuario_registrado,
                                        "fecha_hora_entrega"=>!$se_registro_entrega ? NULL : $value["fecha_hora_entrega"],
                                        "id_usuario_entrega"=>!$se_registro_entrega ? NULL : $this->id_usuario_registrado
                                    ],
                                    ["id_atencion_medica_servicio"=>$value["id_atencion_medica_servicio"]]);
                                    
                    $campos_valores_insert = [
                        "id_usuario_registrado"=>$this->id_usuario_registrado,
                        "fecha_hora_registrado"=>$fecha_hora_registrado,
                        "id_atencion_medica_servicio"=>$value["id_atencion_medica_servicio"],
                        "tipo_accion"=>$this->ID_TIPO_ACCION_MUESTRA
                    ];
        
                    $this->insert("bitacora_atencion_medica_servicio_laboratorio", $campos_valores_insert);

                    if ($se_registro_entrega){
                        $campos_valores_insert = [
                            "id_usuario_registrado"=>$this->id_usuario_registrado,
                            "fecha_hora_registrado"=>$fecha_hora_registrado,
                            "id_atencion_medica_servicio"=>$value["id_atencion_medica_servicio"],
                            "tipo_accion"=>$this->ID_TIPO_ACCION_ENTREGA
                        ];
            
                        $this->insert("bitacora_atencion_medica_servicio_laboratorio", $campos_valores_insert);
                    }
                }

                $sql = "SELECT id_atencion_medica, SUM(IF(fue_muestreado = 0, 1, 0)) as cantidad_servicios_muestra_incompleta
                        FROM atencion_medica_servicio
                        WHERE id_atencion_medica IN (SELECT ams.id_atencion_medica
                            FROM atencion_medica_servicio ams
                            WHERE ams.id_atencion_medica_servicio = :0 AND ams.estado_mrcb) AND estado_mrcb";
                $fila = $this->consultarFila($sql, [$value["id_atencion_medica_servicio"]]);

                $this->id_atencion_medica = $fila["id_atencion_medica"];
                if ($fila["cantidad_servicios_muestra_incompleta"] <= 0){
                    $campos_valores_where = [
                        "id_atencion_medica"=>$this->id_atencion_medica
                    ];

                    $this->update("atencion_medica", ["fecha_hora_muestra"=>$fecha_hora_registrado, "id_usuario_muestra"=>$this->id_usuario_registrado], ["id_atencion_medica"=>$this->id_atencion_medica]);
                }

            }
            $this->commit();

            return ["msj"=>"Registro realizado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function guardarServicioLaboratorioExamenResultados(){
        try {
            $this->beginTransaction();


            $this->resultados_examenes_laboratorio = json_decode($this->resultados_examenes_laboratorio, true);
            $fecha_hora_registrado = date("Y-m-d H:i:s");

            $this->update("atencion_medica_servicio_laboratorio_resultados", 
                                ["estado_mrcb"=>"0"], 
                                [   "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio]);

            $this->update("bitacora_atencion_medica_servicio_laboratorio", 
                                ["estado_mrcb"=>"0"], 
                                ["id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                                "tipo_accion"=>$this->ID_TIPO_ACCION_RESULTADO]);

            $numero_resultados = $this->consultarValor("SELECT COALESCE(MAX(distinct numero_resultados) + 1,1) 
                                    FROM atencion_medica_servicio_laboratorio_resultados
                                    WHERE id_atencion_medica_servicio = :0", $this->id_atencion_medica_servicio);

            foreach ($this->resultados_examenes_laboratorio as $key => $examen) {
                $campos_valores = [
                    "id_lab_examen"=>$examen["id_lab_examen"] == "" ? NULL : $examen["id_lab_examen"],
                    "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                    "nivel"=>$examen["nivel"],
                    "orden_ubicacion"=>$key,
                    "descripcion"=>$examen["descripcion"],
                    "resultado"=>$examen["resultado"],
                    "unidad"=>$examen["unidad"],
                    //"valor_minimo"=>$examen["valor_minimo"],
                    //"valor_maximo"=>$examen["valor_maximo"],
                    "numero_resultados"=>$numero_resultados,
                    "valores_referencia"=>$examen["valores_referencia"],
                    "metodo"=>$examen["metodo"],
                    "fecha_hora_registrado"=>$fecha_hora_registrado,
                    "id_usuario_registrado"=>$this->id_usuario_registrado
                ];

                $this->insert("atencion_medica_servicio_laboratorio_resultados", $campos_valores);
            }
            
            $campos_valores = [
                "id_usuario_resultado"=>$this->id_usuario_registrado,
                "fecha_hora_resultado"=>$fecha_hora_registrado
            ];

            $campos_valores_where = [
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio
            ];

            $this->update("atencion_medica_servicio", $campos_valores, $campos_valores_where);


            $campos_valores_insert = [
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$fecha_hora_registrado,
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                "numero_resultados"=>$numero_resultados,
                "tipo_accion"=>$this->ID_TIPO_ACCION_RESULTADO
            ];

            $this->insert("bitacora_atencion_medica_servicio_laboratorio", $campos_valores_insert);

            $sql = "SELECT id_atencion_medica, SUM(IF(fecha_hora_resultado IS NULL, 1, 0)) as cantidad_servicios_resultados_incompletos
                    FROM atencion_medica_servicio
                    WHERE id_atencion_medica IN (SELECT ams.id_atencion_medica
                        FROM atencion_medica_servicio ams
                        WHERE ams.id_atencion_medica_servicio = :0 AND ams.estado_mrcb) AND estado_mrcb";
            $fila = $this->consultarFila($sql, [$this->id_atencion_medica_servicio]);
            $this->id_atencion_medica = $fila["id_atencion_medica"];


            if ($fila["cantidad_servicios_resultados_incompletos"] <= 0){
                $this->id_atencion_medica = $fila["id_atencion_medica"];
                $campos_valores_where = [
                    "id_atencion_medica"=>$this->id_atencion_medica
                ];

                $this->update("atencion_medica", $campos_valores, $campos_valores_where);
            }

            $this->commit();
            return ["msj"=>"Registros guardados correctamente.","id_atencion_medica"=>$this->id_atencion_medica];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function validarServicioLaboratorioExamenResultados(){
        try {
            $this->beginTransaction();

            $ID_MEDICO_INFORMANTE_LABORATORISTA = 2189;

            $sql = "SELECT id_medico 
                    FROM medico 
                    WHERE estado_mrcb AND es_realizante IN ('1') AND numero_documento IN (SELECT c.numero_documento 
                                                    FROM usuario u
                                                    INNER JOIN colaborador c ON c.id_colaborador = u.id_colaborador
                                                    WHERE u.id_usuario = :0 AND u.estado_acceso = 'A' AND u.estado_mrcb)";
            $objTecnologoRealizante = $this->consultarFila($sql, [$this->id_usuario_registrado]);

            if ($objTecnologoRealizante == false){
                $id_tecnologo_realizante = NULL;
            } else {
                $id_tecnologo_realizante = $objTecnologoRealizante["id_medico"];
            }

            /*
                verificar si existe cada fila y si existe, actualiza ese dato.
                tras esto consultar el id_atencion_medica_servicio y id_atencion_medica y actualizar en ambos fecha_hora_resultado / iid_usuario_resultado
            */
            $fecha_hora_registrado = date("Y-m-d H:i:s");
            
            $campos_valores = [
                "id_usuario_validado"=>$this->id_usuario_registrado,
                "fecha_hora_validado"=>$fecha_hora_registrado,
                "id_medico_atendido"=>$ID_MEDICO_INFORMANTE_LABORATORISTA,
                "id_medico_realizado"=>$id_tecnologo_realizante,
                "id_usuario_atendido"=>$this->id_usuario_registrado,
                "fecha_hora_atendido"=>$fecha_hora_registrado,
                "fue_atendido"=>"1"
            ];

            $campos_valores_where = [
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio
            ];

            $this->update("atencion_medica_servicio", $campos_valores, $campos_valores_where);

            $campos_valores_insert = [
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$fecha_hora_registrado,
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                "tipo_accion"=>$this->ID_TIPO_ACCION_VALIDADO
            ];

            $this->insert("bitacora_atencion_medica_servicio_laboratorio", $campos_valores_insert);

            $sql = "SELECT id_atencion_medica, SUM(IF(fecha_hora_validado IS NULL, 1, 0)) as cantidad_servicios_validados_incompletos
                    FROM atencion_medica_servicio
                    WHERE id_atencion_medica IN (SELECT ams.id_atencion_medica
                        FROM atencion_medica_servicio ams
                        WHERE ams.id_atencion_medica_servicio = :0 AND ams.estado_mrcb) AND estado_mrcb";
            $fila = $this->consultarFila($sql, [$this->id_atencion_medica_servicio]);

            $this->id_atencion_medica = $fila["id_atencion_medica"];
            if ($fila["cantidad_servicios_validados_incompletos"] <= 0){
                $campos_valores = [
                    "id_usuario_validado"=>$this->id_usuario_registrado,
                    "fecha_hora_validado"=>$fecha_hora_registrado
                ];

                $campos_valores_where = [
                    "id_atencion_medica"=>$this->id_atencion_medica
                ];

                $this->update("atencion_medica", $campos_valores, $campos_valores_where);
            }

            $this->commit();
            return ["msj"=>"Examen/Grupo Examen validado correctamente.", "id_atencion_medica"=>$this->id_atencion_medica];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function cancelarValidarServicioLaboratorioExamenResultados(){
        try {
            $this->beginTransaction();

            /*
                verificar si existe cada fila y si existe, actualiza ese dato.
                tras esto consultar el id_atencion_medica_servicio y id_atencion_medica y actualizar en ambos fecha_hora_resultado / iid_usuario_resultado
            */
            $fecha_hora_registrado = date("Y-m-d H:i:s");
            
            $campos_valores = [
                "id_usuario_validado"=>NULL,
                "fecha_hora_validado"=>NULL,
                "id_medico_atendido"=>NULL,
                "id_medico_realizado"=>NULL,
                "id_usuario_atendido"=>NULL,
                "fecha_hora_atendido"=>NULL,
                "fue_atendido"=>"0"
            ];

            $campos_valores_where = [
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio
            ];

            $this->update("atencion_medica_servicio", $campos_valores, $campos_valores_where);

            $campos_valores_insert = [
                "id_usuario_registrado"=>$this->id_usuario_registrado,
                "fecha_hora_registrado"=>$fecha_hora_registrado,
                "id_atencion_medica_servicio"=>$this->id_atencion_medica_servicio,
                "tipo_accion"=>$this->ID_TIPO_ACCION_CANCELAR_VALIDADO
            ];

            $this->insert("bitacora_atencion_medica_servicio_laboratorio", $campos_valores_insert);

            $sql = "SELECT id_atencion_medica
                    FROM atencion_medica_servicio
                    WHERE id_atencion_medica IN (SELECT ams.id_atencion_medica
                        FROM atencion_medica_servicio ams
                        WHERE ams.id_atencion_medica_servicio = :0 AND ams.estado_mrcb) AND estado_mrcb";
            $fila = $this->consultarFila($sql, [$this->id_atencion_medica_servicio]);
            $this->id_atencion_medica = $fila["id_atencion_medica"];

            $campos_valores = [
                "id_usuario_validado"=>NULL,
                "fecha_hora_validado"=>NULL
            ];

            $campos_valores_where = [
                "id_atencion_medica"=>$this->id_atencion_medica
            ];

            $this->update("atencion_medica", $campos_valores, $campos_valores_where);

            $this->commit();
            return ["msj"=>"Examen/Grupo Examen validacion cancelada correctamente.", "id_atencion_medica"=>$this->id_atencion_medica];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }
    
    public function obtenerReporteExamenesRealizado($fecha_inicio, $fecha_fin){
        try {

            $sql = "SELECT  
                        ls.id_lab_seccion,
                        ls.descripcion as seccion
                        FROM atencion_medica_servicio ams
                        INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                        INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                        INNER JOIN lab_examen le ON ams.id_servicio = le.id_servicio
                        INNER JOIN lab_seccion ls ON ls.id_lab_seccion = le.id_lab_seccion
                        WHERE s.id_categoria_servicio IN (14) AND am.estado_mrcb AND ams.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1)
                        GROUP BY ls.id_lab_seccion, ls.descripcion";

            $datos = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin]);

            $sql  = "SELECT 
                    ams.nombre_servicio,
                    ams.precio_unitario,
                    SUM(ams.cantidad) as cantidad
                    FROM atencion_medica_servicio ams
                    INNER JOIN atencion_medica am ON am.id_atencion_medica = ams.id_atencion_medica
                    INNER JOIN servicio s ON s.id_servicio = ams.id_servicio
                    INNER JOIN lab_examen le ON ams.id_servicio = le.id_servicio
                    WHERE s.id_categoria_servicio IN (14) AND am.estado_mrcb AND ams.estado_mrcb AND (am.fecha_atencion BETWEEN :0 AND :1) AND le.id_lab_seccion = :2
                    GROUP BY ams.nombre_servicio, ams.precio_unitario
                    ORDER BY am.numero_acto_medico DESC";

            foreach ($datos as $key => $value) {
                $datos[$key]["examenes"] = $this->consultarFilas($sql, [$fecha_inicio, $fecha_fin, $value["id_lab_seccion"]]);
            }

            return $datos;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    public function actualizarServicioLaboratorioExamenResultadosImpresion($id_examenes_laboratorio){
        try {
            $this->beginTransaction();
            $arregloIdExamenLaboratorio = json_decode($id_examenes_laboratorio);
            $cadenaIdExamenLaboratorio = implode(",",$arregloIdExamenLaboratorio);

            $sql = "UPDATE atencion_medica_servicio 
                    SET numero_impresiones_laboratorio = numero_impresiones_laboratorio + 1 
                    WHERE id_atencion_medica_servicio IN ($cadenaIdExamenLaboratorio)";

            $this->consultaRaw($sql);

            $this->commit();
            return ["msj"=>"Número de impresiones actualizadas."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), 1);
        }
    }

    private function obtenerServiciosLaboratorioExamenesDescripciones($es_examen_perfil = ''){
        $sql = []; $examenes_registros = [];

        if ($es_examen_perfil == ''){
            $sql[0]  = "SELECT
                le.id_lab_examen,
                le.nivel,
                le.descripcion,
                '' as id_lab_examen_niveluno,
                '' as id_lab_examen_niveldos,
                '' as orden_niveluno,
                '' as orden_niveldos,
                '' as resultado,
                le.valor_maximo,
                le.valor_minimo,
                COALESCE(le.unidad, '') as unidad,
                COALESCE(le.metodo, '') as metodo,
                le.valor_referencial
                FROM atencion_medica_servicio ams
                LEFT JOIN lab_examen le ON le.id_servicio = ams.id_servicio 
                WHERE ams.estado_mrcb AND ams.id_atencion_medica_servicio = :0 AND le.estado_mrcb
                ORDER BY le.orden_niveluno, le.nivel";

            $examen_principales = $this->consultarFilas($sql[0], [$this->id_atencion_medica_servicio]);
        } else {
            $cadena_arreglo_perfil = $es_examen_perfil;

            $sql[0]  = "SELECT
                le.id_lab_examen,
                le.nivel,
                le.descripcion,
                '' as id_lab_examen_niveluno,
                '' as id_lab_examen_niveldos,
                '' as orden_niveluno,
                '' as orden_niveldos,
                '' as resultado,
                le.valor_maximo,
                le.valor_minimo,
                COALESCE(le.unidad, '') as unidad,
                le.valor_referencial,
                COALESCE(le.metodo,'') as metodo
                FROM lab_examen le 
                WHERE le.id_lab_examen IN ($cadena_arreglo_perfil) AND le.estado_mrcb
                ORDER BY le.id_lab_examen, le.nivel";

            $examen_principales = $this->consultarFilas($sql[0]);
        }

        if (count($examen_principales)<= 0){
            throw new Exception("No se ha encontrado el examen en la base de datos.", 1);
        }

        foreach ($examen_principales as $key => $examen_principal) {            
            array_push($examenes_registros, $examen_principal);
            $sql[1]  = "SELECT
                    COALESCE(le.id_lab_examen,'') as id_lab_examen,
                    le.nivel,
                    le.descripcion,
                    le.id_lab_examen_niveluno,
                    le.id_lab_examen_niveldos,
                    le.orden_niveluno,
                    le.orden_niveldos,
                    '' as resultado,
                    le.valor_maximo,
                    le.valor_minimo,
                    COALESCE(le.unidad, '') as unidad,
                    le.valor_referencial,
                    COALESCE(le.metodo, '') as metodo
                    FROM lab_examen le 
                    WHERE le.estado_mrcb AND le.id_lab_examen_niveluno = :0
                    ORDER BY le.orden_niveluno, le.orden_niveldos";

            $sql[2]  = "SELECT
                        id_lab_examen,
                        le.nivel,
                        le.descripcion,
                        le.id_lab_examen_niveluno,
                        le.id_lab_examen_niveldos,
                        le.orden_niveluno,
                        le.orden_niveldos,
                        '' as resultado,
                        le.valor_maximo,
                        le.valor_minimo,
                        COALESCE(le.unidad, '') as unidad,
                        le.valor_referencial,
                        COALESCE(le.metodo, '') as metodo
                        FROM lab_examen le 
                        LEFT JOIN lab_unidad lu ON lu.id_lab_unidad = le.id_lab_unidad
                        WHERE le.estado_mrcb AND le.id_lab_examen_niveldos = :0
                        ORDER BY le.orden_niveluno, le.orden_niveldos";

            $sql[3] = "SELECT
                        '' as id_lab_examen,
                        '99' as nivel,
                        '' as descripcion,
                        '' as id_lab_examen_niveluno,
                        '' as id_lab_examen_niveldos,
                        '' as orden_niveluno,
                        '' as orden_niveldos,
                        '' as resultado,
                        '' as valor_maximo,
                        '' as valor_minimo,
                        '' as unidad,
                        '' as metodo,
                        le.descripcion as valor_referencial
                        FROM lab_examendescripcion le 
                        WHERE le.estado_mrcb AND le.id_lab_examen = :0
                        ORDER BY le.numero_orden"; 

            $examen_descripciones = $this->consultarFilas($sql[3], $examen_principal["id_lab_examen"]);
            if (count($examen_descripciones) > 0){
                $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
            }

            $examenes_secundarios = $this->consultarFilas($sql[1], $examen_principal["id_lab_examen"]);

            if (count($examenes_secundarios) > 0 ){
                foreach ($examenes_secundarios as $k0 => $examen_secundario) {                
                    $examenes_terciarios = $this->consultarFilas($sql[2], $examen_secundario["id_lab_examen"]);
                    array_push($examenes_registros, $examen_secundario);

                    $examen_descripciones = $this->consultarFilas($sql[3], $examen_secundario["id_lab_examen"]);
                    if (count($examen_descripciones) > 0){
                        $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
                    }

                    if (count($examenes_terciarios) > 0){
                        foreach ($examenes_terciarios as $k1 => $examen_terciaria) {
                            array_push($examenes_registros, $examen_terciaria);
                            $examen_descripciones = $this->consultarFilas($sql[3], $examen_terciaria["id_lab_examen"]);
                            if (count($examen_descripciones) > 0){
                                $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
                            }
                        }
                    }

                }
            }
        }
        

        return $examenes_registros;
    }

    public function reestructurarExamenes($id_lab_examen){
        $sql = []; $examenes_registros = [];

        $sql[0]  = "SELECT
            le.id_lab_examen,
            le.nivel,
            le.descripcion,
            '' as id_lab_examen_niveluno,
            '' as id_lab_examen_niveldos,
            '' as orden_niveluno,
            '' as orden_niveldos,
            '' as resultado,
            le.valor_maximo,
            le.valor_minimo,
            COALESCE(le.unidad, '') as unidad,
            COALESCE(le.metodo, '') as metodo,
            COALESCE(le.abreviatura, '') as abreviatura,
            COALESCE(le.id_lab_seccion, '') as id_lab_seccion,
            COALESCE(le.id_lab_muestra, '') as id_lab_muestra,
            le.id_servicio,
            le.valor_referencial
            FROM lab_examen le
            WHERE le.estado_mrcb AND  le.id_servicio IS NOT NULL AND arreglo_perfil IS NULL AND id_lab_examen <> 309
            ORDER BY le.nivel";

        $examen_principales = $this->consultarFilas($sql[0]);


        if (count($examen_principales)<= 0){
            throw new Exception("No se ha encontrado el examen en la base de datos.", 1);
        }

        $examenes_por_principal = [];
        foreach ($examen_principales as $key => $examen_principal) {    
            $temp_por_principal = [];        
            array_push($examenes_registros, $examen_principal);
            array_push($temp_por_principal, $examen_principal);
            $sql[1]  = "SELECT
                    COALESCE(le.id_lab_examen,'') as id_lab_examen,
                    le.nivel,
                    le.descripcion,
                    le.id_lab_examen_niveluno,
                    le.id_lab_examen_niveldos,
                    le.orden_niveluno,
                    le.orden_niveldos,
                    '' as resultado,
                    le.valor_maximo,
                    le.valor_minimo,
                    COALESCE(le.unidad, '') as unidad,
                    le.valor_referencial,
                    COALESCE(le.metodo, '') as metodo,
                    COALESCE(le.abreviatura, '') as abreviatura,
                    COALESCE(le.id_lab_seccion, '') as id_lab_seccion,
                    COALESCE(le.id_lab_muestra, '') as id_lab_muestra,
                    '' as id_servicio
                    FROM lab_examen le 
                    WHERE le.estado_mrcb AND le.id_lab_examen_niveluno = :0
                    ORDER BY le.orden_niveluno, le.orden_niveldos";

            $sql[2]  = "SELECT
                        id_lab_examen,
                        le.nivel,
                        le.descripcion,
                        le.id_lab_examen_niveluno,
                        le.id_lab_examen_niveldos,
                        le.orden_niveluno,
                        le.orden_niveldos,
                        '' as resultado,
                        le.valor_maximo,
                        le.valor_minimo,
                        COALESCE(le.unidad, '') as unidad,
                        le.valor_referencial,
                        COALESCE(le.metodo, '') as metodo,
                        COALESCE(le.abreviatura, '') as abreviatura,
                        COALESCE(le.id_lab_seccion, '') as id_lab_seccion,
                        COALESCE(le.id_lab_muestra, '') as id_lab_muestra,
                        '' as id_servicio
                        FROM lab_examen le 
                        LEFT JOIN lab_unidad lu ON lu.id_lab_unidad = le.id_lab_unidad
                        WHERE le.estado_mrcb AND le.id_lab_examen_niveldos = :0
                        ORDER BY le.orden_niveluno, le.orden_niveldos";

            $sql[3] = "SELECT
                        '' as id_lab_examen,
                        '99' as nivel,
                        '' as descripcion,
                        '' as id_lab_examen_niveluno,
                        '' as id_lab_examen_niveldos,
                        '' as orden_niveluno,
                        '' as orden_niveldos,
                        '' as resultado,
                        '' as valor_maximo,
                        '' as valor_minimo,
                        '' as unidad,
                        '' as metodo,
                        le.descripcion as valor_referencial,
                        '' as id_lab_seccion,
                        '' as id_lab_muestra,
                        '' as id_servicio,
                        '' as abreviatura
                        FROM lab_examendescripcion le 
                        WHERE le.estado_mrcb AND le.id_lab_examen = :0
                        ORDER BY le.numero_orden"; 

            $examen_descripciones = $this->consultarFilas($sql[3], $examen_principal["id_lab_examen"]);
            if (count($examen_descripciones) > 0){
                $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
                $temp_por_principal = array_merge($temp_por_principal, $examen_descripciones);
            }

            $examenes_secundarios = $this->consultarFilas($sql[1], $examen_principal["id_lab_examen"]);

            if (count($examenes_secundarios) > 0 ){
                foreach ($examenes_secundarios as $k0 => $examen_secundario) {                
                    $examenes_terciarios = $this->consultarFilas($sql[2], $examen_secundario["id_lab_examen"]);
                    array_push($examenes_registros, $examen_secundario);
                    array_push($temp_por_principal, $examen_secundario);

                    $examen_descripciones = $this->consultarFilas($sql[3], $examen_secundario["id_lab_examen"]);
                    if (count($examen_descripciones) > 0){
                        $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
                        $temp_por_principal = array_merge($temp_por_principal, $examen_descripciones);
                    }

                    if (count($examenes_terciarios) > 0){
                        foreach ($examenes_terciarios as $k1 => $examen_terciaria) {
                            array_push($examenes_registros, $examen_terciaria);
                            array_push($temp_por_principal, $examen_terciaria);
                            
                            $examen_descripciones = $this->consultarFilas($sql[3], $examen_terciaria["id_lab_examen"]);
                            if (count($examen_descripciones) > 0){
                                $examenes_registros = array_merge($examenes_registros, $examen_descripciones);
                                $temp_por_principal = array_merge($temp_por_principal, $examen_descripciones);
                            }
                        }
                    }

                }
            }

            array_push($examenes_por_principal, $temp_por_principal);
        }


        $this->beginTransaction();
        
        foreach($examenes_por_principal as $key => $examenes){
            $id_lab_examen_last = NULL;
            $id_servicio_last = NULL;
            $id_lab_seccion = "";
            $id_lab_muestra = "";
            $numero_orden = 1;
            $numero_orden_total = 1;

            foreach($examenes as $key => $examen){
                    $nivel = $examen["nivel"];
                    if ($nivel != "0"){
                        if ($nivel == "99"){
                            $campos_valores_desc = [
                                "id_lab_examen"=>$id_lab_examen_last,
                                "numero_orden"=>$numero_orden,
                                "descripcion"=>$examen["valor_referencial"]
                            ];
            
                            $this->insert("lab_examendescripcion", $campos_valores_desc);
                            $numero_orden++;    
                        } else {
                            $campos_valores = [
                                "descripcion"=>$examen["descripcion"],
                                "abreviatura"=>$examen["abreviatura"] == "" ? NULL : $examen["abreviatura"],
                                "unidad"=>$examen["unidad"] == "" ? NULL : $examen["unidad"],
                                "id_lab_seccion"=>$id_lab_seccion,
                                "id_servicio"=>$id_servicio_last == "" ? NULL : $id_servicio_last,
                                "id_lab_muestra"=>$id_lab_muestra,
                                "valor_referencial"=>$examen["valor_referencial"],
                                "metodo"=>$examen["metodo"] == "" ? NULL : $examen["metodo"],
                                "nivel"=>$examen["nivel"] == "" ? NULL : $examen["nivel"],
                                "orden_niveluno"=>$numero_orden_total++
                            ];
            
                            $this->insert("lab_examen", $campos_valores);
                            $id_lab_examen_last = $this->getLastID();
                            $numero_orden = 1;
                        }
                        
                    } else {
                        $id_servicio_last = $examen["id_servicio"];
                        $id_lab_seccion = $examen["id_lab_seccion"];
                        $id_lab_muestra = $examen["id_lab_muestra"];

                        $numero_orden_total = 1;
                    }
            }
        }
        
        $sql = "update lab_examen SET estado_mrcb = 0 WHERE id_servicio IS NULL";
        $this->ejecutarSimple($sql);

        $this->commit();

        return ["res"=>$examenes_por_principal,"c"=>count($examenes_por_principal)];
    }
    
}