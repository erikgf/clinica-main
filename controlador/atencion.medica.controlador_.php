<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/AtencionMedica.clase.php';

$op = $_GET["op"];
$obj = new AtencionMedica();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "registrar":
            $obj->id_paciente = Funciones::sanitizar($_POST["p_id_paciente"]);
            $obj->id_medico_realizante = Funciones::sanitizar($_POST["p_id_medico_realizante"]);
            $obj->id_medico_ordenante = Funciones::sanitizar($_POST["p_id_medico_ordenante"]);
            $obj->fecha_atencion = Funciones::sanitizar($_POST["p_fecha_atencion"]);
            $obj->hora_atencion = Funciones::sanitizar($_POST["p_hora_atencion"]);
            $obj->observaciones = Funciones::sanitizar($_POST["p_observaciones"]);
            $obj->id_tipo_comprobante = Funciones::sanitizar($_POST["p_id_tipo_comprobante"]);
            $obj->serie = Funciones::sanitizar($_POST["p_serie"]);
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $obj->pago_efectivo = Funciones::sanitizar($_POST["p_pago_efectivo"]);
            $obj->pago_deposito = Funciones::sanitizar($_POST["p_pago_deposito"]);
            $obj->id_banco = Funciones::sanitizar($_POST["p_id_banco"]);
            $obj->numero_operacion = Funciones::sanitizar($_POST["p_numero_operacion"]);
            $obj->pago_tarjeta = Funciones::sanitizar($_POST["p_pago_tarjeta"]);
            $obj->numero_tarjeta = Funciones::sanitizar($_POST["p_numero_tarjeta"]);
            $obj->numero_voucher = Funciones::sanitizar($_POST["p_numero_voucher"]);
            $obj->servicios = $_POST["p_servicios"];
            $obj->total = Funciones::sanitizar($_POST["p_total"]);

            $obj->monto_descuento = Funciones::sanitizar($_POST["p_monto_descuento"]);
            $obj->id_usuario_validador = Funciones::sanitizar($_POST["p_id_usuario_validador"]);
            $obj->motivo_descuento = Funciones::sanitizar($_POST["p_motivo_descuento"]);
            $obj->es_gratuito_descuento = Funciones::sanitizar($_POST["p_es_gratuito_descuento"]);

            $obj->id_usuario_validador_descuento_sin_efectivo = Funciones::sanitizar($_POST["p_id_usuario_validador_descuento_sin_efectivo"]);
            $obj->clave_descuento_sin_efectivo = Funciones::sanitizar($_POST["p_clave_descuento_sin_efectivo"]);

            $obj->factura_ruc = Funciones::sanitizar($_POST["p_factura_ruc"]);
            $obj->factura_razon_social = Funciones::sanitizar($_POST["p_factura_razon_social"]);
            $obj->factura_direccion = Funciones::sanitizar($_POST["p_factura_direccion"]);

            $obj->boleta_tipo_documento = Funciones::sanitizar(isset($_POST["p_boleta_tipo_documento"]) ? $_POST["p_boleta_tipo_documento"] : "");
            $obj->boleta_numero_documento = Funciones::sanitizar(isset($_POST["p_boleta_numero_documento"]) ? $_POST["p_boleta_numero_documento"] : "");
            $obj->boleta_nombres = Funciones::sanitizar(isset($_POST["p_boleta_nombres"]) ? $_POST["p_boleta_nombres"] : "");
            $obj->boleta_apellido_paterno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_paterno"]) ? $_POST["p_boleta_apellido_paterno"] : "");
            $obj->boleta_apellido_materno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_materno"]) ? $_POST["p_boleta_apellido_materno"] : "");
            $obj->boleta_sexo = Funciones::sanitizar(isset($_POST["p_boleta_sexo"]) ? $_POST["p_boleta_sexo"] : "");
            $obj->boleta_fecha_nacimiento = Funciones::sanitizar(isset($_POST["p_boleta_fecha_nacimiento"]) ? $_POST["p_boleta_fecha_nacimiento"] : "");

            $obj->id_convenio_empresa = (isset($_POST["p_id_convenio_empresa"]) && $_POST["p_id_convenio_empresa"] != "") ? $_POST["p_id_convenio_empresa"] : NULL;
            $obj->convenio_porcentaje = Funciones::sanitizar(isset($_POST["p_convenio_porcentaje"]) ? $_POST["p_convenio_porcentaje"] : NULL);

            $data = $obj->registrar();

            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "anular":
            $obj->id_atencion_medica = isset($_POST["p_id_atencion_medica"]) ? $_POST["p_id_atencion_medica"] : "";

            if ($obj->id_atencion_medica == ""){
                throw new Exception("ID Atención no válido.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->anularAtencion());
        break;

        case "ver_atencion":
            $obj->id_atencion_medica = isset($_POST["p_id_atencion_medica"]) ? $_POST["p_id_atencion_medica"] : "";

            if ($obj->id_atencion_medica == ""){
                throw new Exception("ID Atención no válido.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->verAtencion());
        break;

        case "cambiar_medico":
            $obj->id_atencion_medica = isset($_POST["p_id_atencion_medica"]) ? $_POST["p_id_atencion_medica"] : "";
            $obj->id_medico_ordenante = isset($_POST["p_id_medico_ordenante"]) ? $_POST["p_id_medico_ordenante"] : "1";
            $obj->id_medico_realizante = isset($_POST["p_id_medico_realizante"]) ? $_POST["p_id_medico_realizante"] : "1";

            if ($obj->id_atencion_medica == ""){
                throw new Exception("ID Atención no válido.", 1);
            }

            Funciones::imprimeJSON("200", "OK", $obj->cambiarMedicoAtencion());
        break;

        case "listar_atenciones_general":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);

            $data = $obj->listarAtencionesGeneral($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular_atencion":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $motivo_anulacion = Funciones::sanitizar($_POST["p_motivo_anulacion"]);

            $data = $obj->anularComprobanteAtencion($motivo_anulacion, true);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular_solo_comprobante_atencion":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $motivo_anulacion = Funciones::sanitizar($_POST["p_motivo_anulacion"]);

            $data = $obj->anularComprobanteAtencion($motivo_anulacion, false);
            Funciones::imprimeJSON("200", "OK", $data);
        break;


        case "obtener_atencion_medica_para_saldos":
            $obj->numero_acto_medico = Funciones::sanitizar($_POST["p_numero_acto_medico"]);

            $data = $obj->obtenerAtencionMedicaParaSaldos();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_atencion_medica_para_egreso":
            //devuelve siempre un registro (este o no anulado)
            $obj->numero_acto_medico = Funciones::sanitizar($_POST["p_numero_acto_medico"]);

            $data = $obj->obtenerAtencionMedicaParaEgreso();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        
        case "pagar_saldo_atencion":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->id_tipo_comprobante = Funciones::sanitizar($_POST["p_id_tipo_comprobante"]);
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $obj->pago_efectivo = Funciones::sanitizar($_POST["p_pago_efectivo"]);
            $obj->pago_deposito = Funciones::sanitizar($_POST["p_pago_deposito"]);
            $obj->id_banco = Funciones::sanitizar($_POST["p_id_banco"]);
            $obj->numero_operacion = Funciones::sanitizar($_POST["p_numero_operacion"]);
            $obj->pago_tarjeta = Funciones::sanitizar($_POST["p_pago_tarjeta"]);
            $obj->numero_tarjeta = Funciones::sanitizar($_POST["p_numero_tarjeta"]);
            $obj->numero_voucher = Funciones::sanitizar($_POST["p_numero_voucher"]);

            $obj->monto_descuento =  0.00;

            $obj->factura_ruc = Funciones::sanitizar($_POST["p_factura_ruc"]);
            $obj->factura_razon_social = Funciones::sanitizar($_POST["p_factura_razon_social"]);
            $obj->factura_direccion = Funciones::sanitizar($_POST["p_factura_direccion"]);

            $obj->boleta_tipo_documento = Funciones::sanitizar(isset($_POST["p_boleta_tipo_documento"]) ? $_POST["p_boleta_tipo_documento"] : "");
            $obj->boleta_numero_documento = Funciones::sanitizar(isset($_POST["p_boleta_numero_documento"]) ? $_POST["p_boleta_numero_documento"] : "");
            $obj->boleta_nombres = Funciones::sanitizar(isset($_POST["p_boleta_nombres"]) ? $_POST["p_boleta_nombres"] : "");
            $obj->boleta_apellido_paterno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_paterno"]) ? $_POST["p_boleta_apellido_paterno"] : "");
            $obj->boleta_apellido_materno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_materno"]) ? $_POST["p_boleta_apellido_materno"] : "");
            $obj->boleta_sexo = Funciones::sanitizar(isset($_POST["p_boleta_sexo"]) ? $_POST["p_boleta_sexo"] : "");
            $obj->boleta_fecha_nacimiento = Funciones::sanitizar(isset($_POST["p_boleta_fecha_nacimiento"]) ? $_POST["p_boleta_fecha_nacimiento"] : "");

            $data = $obj->pagarSaldoAtencion();

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
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->id_medico_atendido = Funciones::sanitizar($_POST["p_id_medico_informante"]);
            $obj->observaciones_atendido = Funciones::sanitizar($_POST["p_observaciones"]);
            $obj->fue_atendido = Funciones::sanitizar($_POST["p_estado"]);
            $data = $obj->guardarRevision();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "mostrar_pagos_de_atencion":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);

            $data = $obj->mostrarPagosDeAtencion();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atenciones_laboratorio":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $tipo_filtro = Funciones::sanitizar($_POST["p_tipo_filtro"]);

            $data = $obj->listarAtencionesLaboratorio($fecha_inicio, $fecha_fin, $tipo_filtro);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atencion_detalle_laboratorio":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);

            $data = $obj->listarAtencionDetalleLaboratorio();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_recepcion_laboratorio_resultados":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);

            $data = $obj->listarRecepcionLaboratorioResultados($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_recepcion_laboratorio_resultados_detalle":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);

            $data = $obj->listarRecepcionLaboratorioResultadosDetalle();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_datos_atencion_comprobante":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);

            $data = $obj->obtenerDatosAtencionComprobante();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "canjear_comprobante":
            $obj->id_atencion_medica = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->id_tipo_comprobante = isset($_POST["p_id_tipo_comprobante"]) ? $_POST["p_id_tipo_comprobante"] : NULL;

            $obj->fecha_atencion = Funciones::sanitizar($_POST["p_fecha_emision"]);

            if ($obj->id_tipo_comprobante == NULL){
                throw new Exception("Tipo Comprobante a canjear no válido", 1);
            }

            if ($obj->id_tipo_comprobante == "01"){
                $obj->factura_ruc = Funciones::sanitizar($_POST["p_numero_documento"]);
                $obj->factura_razon_social = Funciones::sanitizar($_POST["p_factura_razon_social"]);
                $obj->factura_direccion = Funciones::sanitizar($_POST["p_factura_direccion"]);    
            } else {
                $obj->boleta_tipo_documento = Funciones::sanitizar(isset($_POST["p_id_tipo_documento"]) ? $_POST["p_id_tipo_documento"] : "");
                $obj->boleta_numero_documento = Funciones::sanitizar(isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : "");
                $obj->boleta_nombres = Funciones::sanitizar(isset($_POST["p_boleta_nombres"]) ? $_POST["p_boleta_nombres"] : "");
                $obj->boleta_apellido_paterno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_paterno"]) ? $_POST["p_boleta_apellido_paterno"] : "");
                $obj->boleta_apellido_materno = Funciones::sanitizar(isset($_POST["p_boleta_apellido_materno"]) ? $_POST["p_boleta_apellido_materno"] : "");
                $obj->boleta_sexo = Funciones::sanitizar(isset($_POST["p_boleta_sexo"]) ? $_POST["p_boleta_sexo"] : "");
                $obj->boleta_fecha_nacimiento = Funciones::sanitizar(isset($_POST["p_boleta_fecha_nacimiento"]) ? $_POST["p_boleta_fecha_nacimiento"] : "");
            }
            
            $data = $obj->canjearComprobante();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atenciones_con_saldo":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $tipo_filtro = Funciones::sanitizar($_POST["p_tipo_filtro"]);

            $data = $obj->listarAtencionesConSaldo($fecha_inicio, $fecha_fin, $tipo_filtro);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_atenciones_convenio":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);

            $data = $obj->listarAtencionesConvenio($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}