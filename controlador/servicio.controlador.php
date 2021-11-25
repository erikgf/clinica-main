<?php

require_once '../datos/variables.php';
require_once '../datos/local_config_web.php';
require_once '../negocio/Servicio.clase.php';

$op = $_GET["op"];
$obj = new Servicio();

require_once '../negocio/Sesion.clase.php';
$objUsuario = Sesion::obtenerSesion();
if ($objUsuario == null){
    Funciones::imprimeJSON("401", "ERROR", utf8_decode("No hay credenciales válidas."));
}
$obj->id_usuario_registrado = Sesion::obtenerSesionId();

try {
    switch($op){
        case "registrar":

            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? Funciones::sanitizar($_POST["p_id_servicio"]): NULL;
            $obj->descripcion = isset($_POST["p_descripcion"]) ? Funciones::sanitizar($_POST["p_descripcion"]): "";

            if ($obj->descripcion  == NULL || $obj->descripcion == ""){
                throw new Exception("No se puede regisrar un servicio sin nombre.", 1);
            }

            $obj->descripcion_detallada = isset($_POST["p_descripcion_detallada"]) ? Funciones::sanitizar($_POST["p_descripcion_detallada"]): NULL;
            $obj->precio_unitario = isset($_POST["p_precio_venta"]) ? Funciones::sanitizar($_POST["p_precio_venta"]): "0.00";

            if ($obj->precio_unitario < 0.00){
                throw new Exception("No se puede registrar un servicio con precio negativo.", 1);
            }
            $obj->idtipo_afectacion = isset($_POST["p_id_tipo_afectacion"]) ? Funciones::sanitizar($_POST["p_id_tipo_afectacion"]): "10";

            if ($obj->idtipo_afectacion == "10"){
                $obj->precio_unitario_sin_igv = round(($obj->precio_unitario / (1.00 + IGV)), 4);
            } else {
                $obj->precio_unitario_sin_igv = $obj->precio_unitario;
            }
            
            $obj->id_categoria_servicio = isset($_POST["p_id_categoria_servicio"]) ? Funciones::sanitizar($_POST["p_id_categoria_servicio"]): NULL;
            $obj->comision =isset($_POST["p_comision"]) ? Funciones::sanitizar($_POST["p_comision"]): "0.00";
            $obj->cantidad_examenes = isset($_POST["p_cantidad_examenes"]) ? Funciones::sanitizar($_POST["p_cantidad_examenes"]): "1";

            $obj->arreglo_perfil = isset($_POST["p_arreglo_perfil"]) ? Funciones::sanitizar($_POST["p_arreglo_perfil"]): NULL;
            $data = $obj->registrar();

            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "listar":
            $filtro = isset($_POST["p_filtro"]) ? Funciones::sanitizar($_POST["p_filtro"]): NULL;

            $data = $obj->listar($filtro);
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;
        case "leer":
            $obj->id_servicio = isset($_POST["id_servicio"]) ? $_POST["id_servicio"] : NULL;
            $data = $obj->leer();
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer_servicio_general":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? $_POST["p_id_servicio"] : NULL;
            $data = $obj->leerServicioGeneral();
            
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "anular":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? $_POST["p_id_servicio"] : "";
            $data = $obj->anular();
            
            Funciones::imprimeJSON("200", "OK", $data);
            break;
        case "buscar":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $mostrarPrecio = isset($_POST["p_mostrar_precio"]) ? $_POST["p_mostrar_precio"] : true;
            $obj->id_categoria_servicio = isset($_POST["p_idcategoria"]) ? $_POST["p_idcategoria"] : NULL;

            $data = $obj->buscar($cadenaBuscar, $mostrarPrecio);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_servicio":
            $obj->id_servicio = isset($_POST["p_idservicio"]) ? $_POST["p_idservicio"] : NULL;

            $data = $obj->obtener();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_tipo_afectacion":
            $data = $obj->obtenerTipoAfectacion();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "registrar_examen":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? Funciones::sanitizar($_POST["p_id_servicio"]): NULL;
            $obj->descripcion = isset($_POST["p_descripcion"]) ? Funciones::sanitizar($_POST["p_descripcion"]): "";

            if ($obj->descripcion  == NULL || $obj->descripcion == ""){
                throw new Exception("No se puede regisrar un servicio sin nombre.", 1);
            }

            $obj->precio_unitario = isset($_POST["p_precio_venta"]) ? Funciones::sanitizar($_POST["p_precio_venta"]): "0.00";

            if ($obj->precio_unitario < 0.00){
                throw new Exception("No se puede registrar un servicio con precio negativo.", 1);
            }
            $obj->idtipo_afectacion = isset($_POST["p_id_tipo_afectacion"]) ? Funciones::sanitizar($_POST["p_id_tipo_afectacion"]): "10";

            if ($obj->idtipo_afectacion == "10"){
                $obj->precio_unitario_sin_igv = round(($obj->precio_unitario / (1.00 + IGV)), 4);
            } else {
                $obj->precio_unitario_sin_igv = $obj->precio_unitario;
            }
            
            $obj->comision =isset($_POST["p_comision"]) ? Funciones::sanitizar($_POST["p_comision"]): "0.00";
            $obj->id_lab_muestra =isset($_POST["p_id_muestra"]) ? Funciones::sanitizar($_POST["p_id_muestra"]): "1";
            $obj->id_lab_seccion =isset($_POST["p_id_seccion"]) ? Funciones::sanitizar($_POST["p_id_seccion"]): "1";
            
            $obj->arreglo_detalle = isset($_POST["p_detalle"]) ? $_POST["p_detalle"] : NULL;

            $obj->se_modifico_detalle_lab_examen = isset($_POST["p_se_modifico"]) ? $_POST["p_se_modifico"] : 0;

            $data = $obj->registrarExamen();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer_servicio_examen":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? $_POST["p_id_servicio"] : NULL;
            $data = $obj->leerServicioExamen();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "buscar_laboratorio_combo":
            $cadenaBuscar = $_POST["p_cadenabuscar"];
            $data = $obj->buscarLaboratorioCombo($cadenaBuscar);
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "leer_servicio_perfil_examen":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? $_POST["p_id_servicio"] : NULL;
            $data = $obj->leerServicioPerfilExamen();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "registrar_perfil_examen":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? Funciones::sanitizar($_POST["p_id_servicio"]): NULL;
            $obj->descripcion = isset($_POST["p_descripcion"]) ? Funciones::sanitizar($_POST["p_descripcion"]): "";

            if ($obj->descripcion  == NULL || $obj->descripcion == ""){
                throw new Exception("No se puede regisrar un servicio sin nombre.", 1);
            }

            $obj->precio_unitario = isset($_POST["p_precio_venta"]) ? Funciones::sanitizar($_POST["p_precio_venta"]): "0.00";

            if ($obj->precio_unitario < 0.00){
                throw new Exception("No se puede registrar un servicio con precio negativo.", 1);
            }
            $obj->idtipo_afectacion = "10";
            $obj->precio_unitario_sin_igv = round(($obj->precio_unitario / (1.00 + IGV)), 4);
            
            $obj->comision = "0.00";
            $obj->arreglo_detalle = isset($_POST["p_detalle"]) ? $_POST["p_detalle"] : NULL;

            $data = $obj->registrarPerfilExamen();

            Funciones::imprimeJSON("200", "OK", $data);
        break;

        case "obtener_precios_x_id":
            $obj->id_servicio = isset($_POST["p_id_servicio"]) ? $_POST["p_id_servicio"] : NULL;
            $data = $obj->obtenerPreciosXId();
            Funciones::imprimeJSON("200", "OK", $data);
        break;

        default:
            Funciones::imprimeJSON("500","ERROR","No existe la función consultada en el API.");
        break;
    }

} catch (\Throwable $th) {
    Funciones::imprimeJSON("500", "ERROR",mb_convert_encoding($th->getMessage(),'HTML-ENTITIES','UTF-8'));
}