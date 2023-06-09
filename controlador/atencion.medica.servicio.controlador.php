<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/AtencionMedicaServicio.clase.php';

$op = $_GET["op"];
$obj = new AtencionMedicaServicio();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){

        case "listar_examenes_administrador":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $id_area = Funciones::sanitizar($_POST["p_id_area"]);
            
            $obj->id_medico_atendido = Funciones::sanitizar($_POST["p_id_medico_atendido"]);
            $obj->id_medico_realizante = Funciones::sanitizar($_POST["p_id_medico_realizante"]);
            $obj->fue_atendido = Funciones::sanitizar($_POST["p_estado"]);

            $data = $obj->listarExamenesAdministrador($fecha_inicio, $fecha_fin, $id_area);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "listar_examenes_asistentes":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $area = Funciones::sanitizar($_POST["p_area"]);

            $data = $obj->listarExamenesAsistentes($fecha_inicio, $fecha_fin, $area);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar_revision":
            $obj->id_atencion_medica_servicio = Funciones::sanitizar($_POST["p_id_atencion_medica_servicio"]);
            $obj->id_medico_atendido = Funciones::sanitizar($_POST["p_id_medico_atendido"]);
            $obj->id_medico_realizante = Funciones::sanitizar($_POST["p_id_medico_realizante"]);
            $obj->observaciones_atendido = Funciones::sanitizar($_POST["p_observaciones"]);
            $obj->fue_atendido = Funciones::sanitizar($_POST["p_estado"]);
            $data = $obj->guardarRevision();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_servicio_laboratorio_examen":
            $obj->id_atencion_medica_servicio = Funciones::sanitizar($_POST["p_id_atencion_medica_servicio"]);

            $data = $obj->listarServicioLaboratorioExamen();
            Funciones::imprimeJSON("200", "OK", $data);
        break;


        case "guardar_servicio_laboratorio_examen":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->arreglo_examenes = isset($_POST["p_arreglo_examenes"]) ? $_POST["p_arreglo_examenes"] : $_POST["p_arreglo_examenes"];

            $data = $obj->guardarServicioLaboratorioExamen();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar_servicio_laboratorio_examen_resultados":
            $obj->id_atencion_medica_servicio = Funciones::sanitizar($_POST["p_id_atencion_medica_servicio"]);
            $obj->resultados_examenes_laboratorio = isset($_POST["p_resultados_examenes_laboratorio"]) ? $_POST["p_resultados_examenes_laboratorio"] : $_POST["p_resultados_examenes"];

            $data = $obj->guardarServicioLaboratorioExamenResultados();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "validar_servicio_laboratorio_examen_resultados":
            $obj->id_atencion_medica_servicio = Funciones::sanitizar($_POST["p_id_atencion_medica_servicio"]);

            $data = $obj->validarServicioLaboratorioExamenResultados();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "cancelar_validar_servicio_laboratorio_examen_resultados":
            $obj->id_atencion_medica_servicio = Funciones::sanitizar($_POST["p_id_atencion_medica_servicio"]);

            $data = $obj->cancelarValidarServicioLaboratorioExamenResultados();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "actualizar_servicio_laboratorio_examen_resultados_impresion":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $id_examenes_laboratorio = isset($_POST["p_id_examenes_laboratorio"]) ? $_POST["p_id_examenes_laboratorio"] : $_POST["p_id_examenes_laboratorio"];

            $data = $obj->actualizarServicioLaboratorioExamenResultadosImpresion($id_examenes_laboratorio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "reestructurar":
            $id_lab_examen = Funciones::sanitizar($_POST["p_id_lab_examen"]);
            $data = $obj->reestructurarExamenes($id_lab_examen);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_examenes_atenciones_por_sede":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $id_area = $_POST["p_id_area"];
            //$id_area = Funciones::sanitizar($_POST["p_id_area"]);
            $estado = Funciones::sanitizar($_POST["p_estado"]);
            $sede = Funciones::sanitizar($_POST["p_sede"]);
            
            $data = $obj->listarExamenesAtencionesPorSede($fecha_inicio, $fecha_fin, $estado, $id_area, $sede);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}