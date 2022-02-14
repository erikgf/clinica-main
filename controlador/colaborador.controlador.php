<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Colaborador.clase.php';

$op = $_GET["op"];
$obj = new Colaborador();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
    exit;
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "leer":
            $obj->id_colaborador = isset($_POST["p_id_colaborador"]) ? $_POST["p_id_colaborador"] : NULL;
            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "guardar":
            $obj->id_tipo_documento = isset($_POST["p_id_tipo_documento"]) ? $_POST["p_id_tipo_documento"] : NULL;
            $obj->numero_documento = isset($_POST["p_numero_documento"]) ? $_POST["p_numero_documento"] : NULL;

            if ($obj->numero_documento == "" || $obj->numero_documento == NULL){
                throw new Exception("Se debe ingresar un número de documento de identificación válido.", 1);
            }

            $obj->nombres = isset($_POST["p_nombres"]) ? $_POST["p_nombres"] : NULL;
            $obj->apellido_paterno = isset($_POST["p_apellido_paterno"]) ? $_POST["p_apellido_paterno"] : NULL;
            $obj->apellido_materno = isset($_POST["p_apellido_materno"]) ? $_POST["p_apellido_materno"] : NULL;
            
            $obj->telefono = isset($_POST["p_telefono"]) ? $_POST["p_telefono"] : NULL;
            $obj->correo = isset($_POST["p_correo"]) ? $_POST["p_correo"] : NULL;
            $obj->id_rol = isset($_POST["p_id_rol"]) ? $_POST["p_id_rol"] : NULL;
            $obj->estado_acceso = isset($_POST["p_estado_acceso"]) ? $_POST["p_estado_acceso"] : "0";

            $id_colaborador = isset($_POST["p_id_colaborador"]) ? $_POST["p_id_colaborador"] : NULL;
            $obj->id_colaborador = $id_colaborador;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "cambiar_clave":
            $id_colaborador = isset($_POST["p_id_colaborador"]) ? $_POST["p_id_colaborador"] : NULL;
            $obj->id_colaborador = $id_colaborador;

            $obj->clave = isset($_POST["p_clave"]) ? $_POST["p_clave"] : NULL;
            if ($obj->clave == "" || $obj->clave == NULL){
                throw new Exception("Se debe ingresar una clave válida.", 1);
            }

            $data = $obj->cambiarClave();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        
        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "anular":
            $id_colaborador = isset($_POST["p_id_colaborador"]) ? $_POST["p_id_colaborador"] : "";
            if ($id_colaborador == ""){
                throw new Exception("Colaborador consultado no válido.", 1);
            }
            $obj->id_colaborador = $id_colaborador;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}