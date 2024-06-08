<?php 
/*hacer que solo imprimir si usuario de la sesion == idusuario*/
date_default_timezone_set('America/Lima');
require_once '../datos/datos.empresa.php';
require_once "../negocio/Sesion.clase.php";
include_once "../negocio/util/Funciones.php";

if (!Sesion::obtenerSesion()){
  echo "No tiene permisos suficientes para ver esto.";
  exit;
}
$login = Sesion::obtenerSesion()["nombre_usuario"];

$id_informe = isset($_GET["id"]) ? $_GET["id"] : NULL;

if ($id_informe == NULL){
    echo "No se ha ingresado el ID del informe..";
    exit;
}

require "../negocio/Informe.clase.php";

$titulo_xls  = "";
try {
  $obj = new Informe();

  $data = $obj->obtenerContenidoParaWord($id_informe);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }

  $contenido = $data["contenido"];
  $nombre_archivo = $data["nombre_archivo"];

} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}

try {
    header("Content-Type: application/application/vnd.ms-word");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Disposition: attachment; filename="'.$nombre_archivo.'"');
    echo $contenido;

} catch (Exception $exc) {
    print_r(["state"=>500,"msj"=>$exc->getMessage()]);
}   

