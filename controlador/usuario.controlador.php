<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Usuario.clase.php';
require_once '../negocio/Sesion.clase.php';

$op = $_GET["op"];
$obj = new Usuario();

try {
    switch($op){
        case "iniciar_sesion":
            $obj->nombre_usuario = $_POST["p_nombre_usuario"];
            $obj->clave = $_POST["p_clave"];

            $data = $obj->iniciarSesion();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "cerrar_sesion":
            Sesion::destruirSesion();
            header ("Location: ../views");
        break;

        case "obtener_autorizadores_descuentos":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->obtenerAutorizadoresDescuentos($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "validar_descuento":
            $obj->id_usuario = $_POST["p_idusuario"];
            $obj->clave = $_POST["p_clave"];
            $data = $obj->validarDescuento();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "cambiar_clave":

            $objUsuario = Sesion::obtenerSesion();
            if ($objUsuario == null){
                Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
            }

            $obj->id_usuario = Sesion::obtenerSesionId();
            $antigua_clave = $_POST["p_antigua_clave"];
            $nueva_clave = $_POST["p_nueva_clave"];
            $data = $obj->cambiarclave($antigua_clave, $nueva_clave);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}