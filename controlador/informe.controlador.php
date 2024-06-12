<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/Sesion.clase.php';
require_once '../negocio/Globals.clase.php';

$objUsuario = Sesion::obtenerSesion();

if ($objUsuario == null){
    Funciones::imprimeJSON(Globals::$HTTP_NO_CREDENCIALES, "ERROR", "No hay credenciales válidas.");
    exit;
}

if (!in_array($objUsuario["id_rol"], [Globals::$ID_ROL_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_ADMINISTRADOR, Globals::$ID_ROL_ASISTENTE_REVISION, Globals::$ID_ROL_MEDICO])){
    Funciones::imprimeJSON(Globals::$HTTP_NO_PERMISOS, "ERROR", "No tiene permisos para ver esto.");
    exit;
}

require_once '../negocio/Informe.clase.php';
$obj = new Informe();
$obj->id_usuario_registrado = $objUsuario["id_usuario_registrado"];
$op = $_GET["op"];

try {
    switch($op){
        case "listar":
            $fecha_inicio = isset($_POST["p_fecha_inicio"]) ? $_POST["p_fecha_inicio"] : "";
            if ($fecha_inicio == ""){
                throw new Exception("Fecha inicio no válido.", 1);
            }

            $fecha_fin = isset($_POST["p_fecha_fin"]) ? $_POST["p_fecha_fin"] : "";
            if ($fecha_fin == ""){
                throw new Exception("Fecha fin no válido.", 1);
            }


            if ($objUsuario["id_rol"] == Globals::$ID_ROL_MEDICO){
                $data = $obj->listar($fecha_inicio, $fecha_fin, true);
                Funciones::imprimeJSON("200", "OK", $data);
                exit;
            }


            $data = $obj->listar($fecha_inicio, $fecha_fin);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "leer":
            $id_informe = isset($_POST["p_id_informe"]) ? $_POST["p_id_informe"] : "";
            if ($id_informe == ""){
                throw new Exception("Informe ID no válido.", 1);
            }

            if ($objUsuario["id_rol"] == Globals::$ID_ROL_MEDICO){
                $data = $obj->leer($id_informe, true);
                Funciones::imprimeJSON("200", "OK", $data);
                exit;
            }

            $data = $obj->leer($id_informe);
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "cambiar_orden":
            $id_medico = isset($_POST["p_id_medico"]) ? $_POST["p_id_medico"] : "";
            if ($id_medico == ""){
                throw new Exception("Médico no válido.", 1);
            }

            $arregloOrdenado = isset($_POST["p_arreglo"]) ? $_POST["p_arreglo"] : "[]";
            if ($arregloOrdenado == "[]"){
                throw new Exception("Arreglo de ID no válido.", 1);
            }

            $data = $obj->cambiarOrden($id_medico, json_decode($arregloOrdenado));
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "modificar_contenido":
            $id_informe = isset($_POST["p_id_informe"]) ? $_POST["p_id_informe"] : "";
            if ($id_informe == ""){
                throw new Exception("Informe ID no válido.", 1);
            }

            $contenido = isset($_POST["p_contenido"]) ? $_POST["p_contenido"] : "";

            $data = $obj->modificarContenido($id_informe, $contenido);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            throw new Exception( "No existe la función consultada en el API.", 1);
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}