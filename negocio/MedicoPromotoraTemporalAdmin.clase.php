<?php

require_once '../datos/Conexion.clase.php';
require_once 'Medico.clase.php';

class MedicoPromotoraTemporalAdmin extends Conexion {
    public int $id_medico;
    public int $id_promotora;
    public string $apellidos_nombres;
    public string $numero_documento;
    public string $colegiatura;
    public string $fecha_nacimiento;
    public int $id_especialidad;
    public ?int $id_medico_modificado;
    public ?string $observacion_rechazo;
    public ?string $estado_activo;

    public int $id_usuario_registrado;

    public function listarParaAprobar(){
        try {
            $sql = "SELECT 
                        m.id_medico,
                        m.nombres_apellidos,
                        m.cmp,
                        esp.descripcion as especialidad,
                        DATE_FORMAT(m.fecha_nacimiento, '%d/%m') as fecha_nacimiento,
                        pr.descripcion as promotora,
                        m.estado_activo,
                        m.direccion,
                        m.celular,
                        s.nombre as sede,
                        (CASE m.estado_activo WHEN 'P' THEN 'PENDIENTE' WHEN 'A' THEN 'APROBADO' ELSE 'RECHAZADO' END) as estado_descripcion,
                        (CASE m.estado_activo WHEN 'P' THEN 'warning' WHEN 'A' THEN 'success' ELSE 'danger' END) as estado_color,
                        m.observacion_rechazo
                    FROM medico_promotora_temporal m 
                    INNER JOIN promotora pr ON pr.id_promotora = m.id_promotora
                    INNER JOIN sede s ON s.id_sede = m.id_sede
                    LEFT JOIN especialidad_medico esp ON esp.id_especialidad_medico = m.id_especialidad
                    WHERE m.estado_mrcb AND estado_activo = :0
                    ORDER BY m.fecha_hora_registro";
            return  $this->consultarFilas($sql, [$this->estado_activo ?? "P"]);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function obtenerCantidadMedicosParaAprobar(){
        try {
            $sql = "SELECT COUNT(id_medico) as cantidad
                    FROM medico_promotora_temporal m 
                    WHERE m.estado_mrcb AND m.estado_activo = 'P'";
            return $this->consultarFila($sql);
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function aprobarMedico(){
        try {

            $this->beginTransaction();

            $sql = "SELECT cmp, nombres_apellidos, fecha_nacimiento, id_especialidad, 
                    direccion, celular, id_sede,
                    id_promotora, id_medico_modificado
                    FROM medico_promotora_temporal
                    WHERE id_medico = :0 AND estado_mrcb AND estado_activo = 'P'";

            $medicoTemporal = $this->consultarFila($sql, [$this->id_medico]);

            if (!$medicoTemporal){
                throw new Exception("No existe el registro del médico a aprobar", Globals::$HTTP_NO_ENCONTRADO);
            }

            $objMedico = new Medico();
            $objMedico->id_medico = $medicoTemporal["id_medico_modificado"] ;
            //$objMedico->numero_documento = $medicoTemporal["numero_documento"];
            $objMedico->apellidos_nombres = $medicoTemporal["nombres_apellidos"];
            $objMedico->colegiatura = $medicoTemporal["cmp"];
            $objMedico->id_promotora = $medicoTemporal["id_promotora"];
            $objMedico->id_especialidad = $medicoTemporal["id_especialidad"];
            $objMedico->fecha_nacimiento = $medicoTemporal["fecha_nacimiento"];
            $objMedico->domicilio = $medicoTemporal["direccion"];
            $objMedico->telefono_uno = $medicoTemporal["celular"];
            $objMedico->tipo_personal_medico = 0;
            $objMedico->es_informante = 0;
            $objMedico->es_realizante = 0;
            $objMedico->id_sede = $medicoTemporal["id_sede"];
            $objMedico->rne = NULL;
            $objMedico->telefono_dos = NULL;
            $objMedico->correo = NULL;

            $objRpt = $objMedico->guardar();

            if (!$objRpt["rpt"]){
                throw new Exception("Médico registrado incorrectamente.");
            }

            $registro = $objRpt["registro"];

            $campos_valores = [
                "estado_activo"=>"A",
                "id_usuario_aprobacion"=>$this->id_usuario_registrado
            ];

            $campos_valores_where = [
                "id_medico"=>$this->id_medico,
            ];

            $this->update("medico_promotora_temporal", $campos_valores, $campos_valores_where);

            $this->commit();

            return ["rpt"=>"Registrado correctamente", "registro"=>$registro];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }

    public function rechazarMedico(){
        try {

            $campos_valores = [
                "estado_activo"=>"R",
                "observacion_rechazo"=>$this->observacion_rechazo,
                "id_usuario_aprobacion"=>$this->id_usuario_registrado
            ];

            $campos_valores_where = [
                "id_medico"=>$this->id_medico
            ];

            $this->update("medico_promotora_temporal", $campos_valores, $campos_valores_where);

            return ["rpt"=>"Registrado correctamente"];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage(), $exc->getCode());
        }
    }
    
}