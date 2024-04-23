<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Medico.clase.php';

$op = $_GET["op"];
$obj = new Medico();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "leer":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.", 1);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";

            if ($id_medico == ""){
                throw new Exception("Médico ingresado no válido.", 1);
            }
            $obj->id_medico = $id_medico;
            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;
            $obj->apellidos_nombres = isset($_POST["p_apellidos_nombres"]) ? $_POST["p_apellidos_nombres"] : NULL;
            $obj->colegiatura = isset($_POST["p_colegiatura"]) ? $_POST["p_colegiatura"] : NULL;
            $obj->rne = isset($_POST["p_rne"]) ? $_POST["p_rne"] : NULL;
            $obj->telefono_uno = isset($_POST["p_telefono_uno"]) ? $_POST["p_telefono_uno"] : NULL;
            $obj->telefono_dos = isset($_POST["p_telefono_dos"]) ? $_POST["p_telefono_dos"] : NULL;
            $obj->correo = isset($_POST["p_correo"]) ? $_POST["p_correo"] : NULL;

            $obj->id_especialidad = isset($_POST["p_id_especialidad"]) ? $_POST["p_id_especialidad"] : NULL;
            $obj->domicilio = isset($_POST["p_domicilio"]) ? $_POST["p_domicilio"] : NULL;
            $obj->id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : NULL;
            $obj->observaciones = isset($_POST["p_observaciones"]) ? $_POST["p_observaciones"] : NULL;

            $obj->es_informante = isset($_POST["p_es_informante"]) ? $_POST["p_es_informante"] : "0";
            $obj->tipo_personal_medico = isset($_POST["p_tipo_personal_medico"]) ? $_POST["p_tipo_personal_medico"] : "0";
            $obj->es_realizante = isset($_POST["p_es_realizante"]) ? $_POST["p_es_realizante"] : "0";
            $obj->id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : NULL;

            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : NULL;
            $obj->id_medico = $id_medico;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "buscar":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscar($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "listar_validos_promotoras":
            $data = $obj->listarMedicosValidosParaPromotoras();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_promotoras":
            $data = $obj->listarPromotoras();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_medicos_x_promotora":
            $id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";

            if ($id_promotora == ""){
                throw new Exception("Promotora ingresada no válida.", 1);
            }
            $obj->id_promotora = $id_promotora;
            $data = $obj->listarMedicosXPromotora();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_para_liquidaciones":
            $hoy = date("Y-m-d");

            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;
            $totales_mayores_a = isset($_POST["p_totales_mayores"]) ? $_POST["p_totales_mayores"] : "100";
            $id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : "";

            $data = $obj->listarMedicosParaLiquidaciones($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atenciones_comision_liquidacion_medico":
            $hoy = date("Y-m-d");

            $obj->id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";
            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;
            $totales_mayores_a = isset($_POST["p_totales_mayores"]) ? $_POST["p_totales_mayores"] : "0.00";
            $id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : "";

            $data = $obj->listarAtencionesComisionParaLiquidacionXMedico($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atenciones_comision_liquidacion_medico_imprimir":
            $hoy = date("Y-m-d");

            $obj->id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";
            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;
            $totales_mayores_a = isset($_POST["p_totales_mayores"]) ? $_POST["p_totales_mayores"] : "100";

            $data = $obj->listarAtencionesComisionParaLiquidacionXMedicoImprimir($fecha_inicio, $fecha_fin, $totales_mayores_a);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "listar_liquidacion_medicos":
            $hoy = date("Y-m-d");

            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;
            $totales_mayores_a = isset($_POST["p_totales_mayores"]) ? $_POST["p_totales_mayores"] : "0";
            $id_sede = isset($_POST["p_id_sede"]) ? $_POST["p_id_sede"] : "";

            $data = $obj->listarLiquidacionesMedicos($fecha_inicio, $fecha_fin, $totales_mayores_a, $id_sede);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_medicos_liquidacion_x_promotra_imprimir":
            $hoy = date("Y-m-d");

            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : $hoy;
            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : $hoy;
            $obj->id_promotora = isset($_POST["p_id_promotora"]) ? $_POST["p_id_promotora"] : "";

            $data = $obj->listarMedicosLiquidacionXPromotoraImprimir($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_medicos_informantes":
            $buscar = isset($_POST["p_cadenabuscar"]) ? $_POST["p_cadenabuscar"] : NULL;

            $data = $obj->obtenerMedicosInformantes($buscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_medicos_realizantes":
            $buscar = isset($_POST["p_cadenabuscar"]) ? $_POST["p_cadenabuscar"] : NULL;

            $data = $obj->obtenerMedicosRealizantes($buscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "listar_liquidaciones_seguimiento_medico":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $id_area = $_POST["p_id_area"];
            $id_promotora = $_POST["p_id_promotora"];
            $id_sede = $_POST["p_sede"];
            $totales_mayores_a = Funciones::sanitizar($_POST["p_monto"]);

            $data = $obj->listarLiquidacionesSeguimientoMedico($fecha_inicio, $fecha_fin, $id_sede, $id_promotora, $id_area, $totales_mayores_a);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}