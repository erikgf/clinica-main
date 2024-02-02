<?php
require_once '../datos/local_config_web.php';
require_once '../negocio/CategoriaServicio.clase.php';

$op = $_GET["op"];
$obj = new CategoriaServicio();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
    exit;
}

$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "buscar":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscar($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar":
            $data = $obj->listar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "listar_solo__asistentes":
            $data = $obj->listarSoloAsistentes();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener":
            $sin_laboratorio = isset($_POST["p_sin_laboratorio"]) ? $_POST["p_sin_laboratorio"] : "0";
            $data = $obj->obtener($sin_laboratorio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "guardar":
            $obj->descripcion = isset($_POST["p_descripcion"]) ? $_POST["p_descripcion"] : NULL;

            if ($obj->descripcion == NULL || $obj->descripcion == ""){
                throw new Exception("No se ha enviado el nombre/descripción de Categoría", 1);
            }

            $obj->comisiones_sedes = json_decode(isset($_POST["p_comisiones"]) ? $_POST["p_comisiones"] : "[]");

            $id_categoria_servicio = isset($_POST["p_id_categoria_servicio"]) ? $_POST["p_id_categoria_servicio"] : NULL;
            $obj->id_categoria_servicio = $id_categoria_servicio;
            $data = $obj->guardar();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer":
            $id_categoria_servicio = isset($_POST["p_id_categoria_servicio"]) ? $_POST["p_id_categoria_servicio"] : "";
            if ($id_categoria_servicio == ""){
                throw new Exception("Categoría consultada no válida.", 1);
            }
            $obj->id_categoria_servicio = $id_categoria_servicio;

            $data = $obj->leer();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
            $id_categoria_servicio = isset($_POST["p_id_categoria_servicio"]) ? $_POST["p_id_categoria_servicio"] : "";
            if ($id_categoria_servicio == ""){
                throw new Exception("Categoría consultada no válida.", 1);
            }
            $obj->id_categoria_servicio = $id_categoria_servicio;

            $data = $obj->anular();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "buscar_imagenes":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscarParaAsistentes($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}