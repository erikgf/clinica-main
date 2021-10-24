<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Caja.clase.php';

$op = $_GET["op"];
$obj = new Caja();


require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "obtener_caja_abiertas_validas":
            $data = $obj->obtenerCajasAbiertasValidas();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_caja_abiertas_validas_registrar_atencion":
            $obj->id_caja = Funciones::sanitizar($_POST["p_id_caja"]);
            
            $data = $obj->obtenerCajasAbiertasValidasRegistrarAtencion();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_cajas":
            $data = $obj->obtenerCajas();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "es_valida_instancia_caja":
            $id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $fecha_atencion = Funciones::sanitizar($_POST["p_fecha_atencion"]);

            $data = $obj->esValidaInstanciaCaja($id_caja_instancia, $fecha_atencion);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_instancias":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $obj->id_caja = Funciones::sanitizar($_POST["p_id_caja"]);

            $data = $obj->obtenerInstancias($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "seleccionar_movimientos_caja_instancia":
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);

            $data = $obj->seleccionarMovimientosCajaInstancia();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "abrir_caja":
            $obj->id_caja = Funciones::sanitizar($_POST["p_id_caja"]);
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);
            $obj->fecha_apertura = Funciones::sanitizar($_POST["p_fecha_apertura"]);
            $obj->monto_apertura = Funciones::sanitizar($_POST["p_monto_apertura"]);

            $es_fecha_anterior = Funciones::sanitizar($_POST["p_es_fecha_anterior"]);
            $es_fecha_repetida = Funciones::sanitizar($_POST["p_es_fecha_repetida"]);
            $clave_admin = Funciones::sanitizar($_POST["p_clave_admin"]);

            $data = $obj->abrirCaja(
                                $es_fecha_anterior == NULL ? "0" : $es_fecha_anterior, 
                                $es_fecha_repetida == NULL ? "0" : $es_fecha_repetida, 
                                $clave_admin == NULL ? "" : $clave_admin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "cerrar_caja":
            $obj->id_caja_instancia = Funciones::sanitizar($_POST["p_id_caja_instancia"]);

            $data = $obj->cerrarCaja();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_movimientos_general":
            $fecha_inicio = Funciones::sanitizar($_POST["p_fecha_inicio"]);
            $fecha_fin = Funciones::sanitizar($_POST["p_fecha_fin"]);
            $obj->id_caja = Funciones::sanitizar($_POST["p_id_caja"]);

            $data = $obj->listarMovimientosGeneral($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}