<?php

require_once '../datos/variables.php';
require_once '../datos/local_config_web.php';
require_once '../negocio/Rol.clase.php';

$op = $_GET["op"];
$obj = new Rol();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "obtener_combo":
            $data = $obj->obtenerCombo();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $obj->id_rol = isset($_POST["p_id_rol"]) ? $_POST["p_id_rol"] : NULL;
            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->id_rol = isset($_POST["p_id_rol"]) ? $_POST["p_id_rol"] : NULL;
            $obj->descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : NULL;

            $interfaces = isset($_POST["p_id_interfaz"]) ? $_POST["p_id_interfaz"] : '';
            $obj->interfaces = json_decode($interfaces);

            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}