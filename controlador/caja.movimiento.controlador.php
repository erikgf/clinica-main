<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/CajaMovimiento.clase.php';

$op = $_GET["op"];
$obj = new CajaMovimiento();


require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "anular_movimiento":
            $obj->id_caja_instancia_movimiento = Funciones::sanitizar($_POST["p_id_movimiento"]);
            $motivo_anulacion = Funciones::sanitizar($_POST["p_motivo_anulacion"]);

            $data = $obj->anularMovimiento($motivo_anulacion);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "registrar_ingreso":
            $obj->id_registro_atencion_relacionada = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->id_tipo_comprobante = Funciones::sanitizar($_POST["p_id_tipo_comprobante"]);
            $obj->id_tipo_movimiento = Funciones::sanitizar($_POST["p_id_tipo_movimiento"]);
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $obj->monto_efectivo = Funciones::sanitizar($_POST["p_monto_efectivo"]);
            $obj->monto_deposito = Funciones::sanitizar($_POST["p_monto_deposito"]);
            $obj->id_banco = Funciones::sanitizar($_POST["p_id_banco"]) == "" ? NULL : $_POST["p_id_banco"];
            $obj->numero_operacion = Funciones::sanitizar($_POST["p_numero_operacion"]) == "" ? NULL : $_POST["p_numero_operacion"];
            $obj->fecha_deposito = Funciones::sanitizar($_POST["p_fecha_deposito"]);
            $obj->monto_tarjeta = Funciones::sanitizar($_POST["p_monto_tarjeta"]);
            $obj->numero_tarjeta = Funciones::sanitizar($_POST["p_numero_tarjeta"]) == "" ? NULL : $_POST["p_numero_tarjeta"];
            $obj->numero_voucher = Funciones::sanitizar($_POST["p_numero_voucher"]) == "" ? NULL : $_POST["p_numero_voucher"];
            $obj->fecha_transaccion = Funciones::sanitizar($_POST["p_fecha_transaccion"]);

            $obj->monto_descuento =  0.00;
            $obj->monto_credito = 0.00;

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

            $obj->descripcion_movimiento = Funciones::sanitizar(isset($_POST["p_observaciones"]) ? $_POST["p_observaciones"] : "");

            $data = $obj->registrarIngreso();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "registrar_egreso":
            $obj->id_registro_atencion_relacionada = Funciones::sanitizar($_POST["p_id_atencion_medica"]);
            $obj->id_paciente = Funciones::sanitizar($_POST["p_id_paciente"]);
            $obj->id_tipo_movimiento = Funciones::sanitizar($_POST["p_id_tipo_movimiento"]);
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $obj->monto_efectivo = Funciones::sanitizar($_POST["p_monto_efectivo"]);

            $obj->monto_descuento =  0.00;
            $obj->monto_deposito =  0.00;
            $obj->monto_tarjeta = 0.00;
            $obj->monto_credito = 0.00;

            $obj->descripcion_movimiento = Funciones::sanitizar(isset($_POST["p_observaciones"]) ? $_POST["p_observaciones"] : "");

            $data = $obj->registrarEgreso();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}