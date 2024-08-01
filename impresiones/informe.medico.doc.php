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
$logo = isset($_GET["l"]) ? $_GET["l"] : "0";

if ($id_informe == NULL){
    echo "No se ha ingresado el ID del informe..";
    exit;
}

require '../vendor/autoload.php';
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Image;

$phpWord = new PhpWord;

$section = $phpWord->addSection();
if ($logo === "1"){
  $header = $section->addHeader();
  $header->addWatermark('../icon/fondo_impresionword.jpeg', 
    array(
      'width' => 596, 
      'marginTop' => -36,
      'marginLeft' => -75,
      'posHorizontal' => 'absolute',
      'posVertical' => 'absolute',
      'wrappingStyle' => 'behind'
    )
  );
}

require "../negocio/Informe.clase.php";

try {
  $obj = new Informe();
  $data = $obj->obtenerContenidoParaWord($id_informe);

  if (count($data) <= 0){
    echo "Sin datos encontrados.";
    exit;
  }
$contenido = $data["contenido"];
$contenido = str_replace("<br>", '<br/>', $contenido);
$nombre_archivo = str_replace("/","",$data["nombre_archivo"]);

Html::addHtml($section, $contenido);

$section->addImage('./medicos-firmas/7.jpg', array(
  'width' => 140,
  'height' => 70,
  'wrappingStyle' => 'infront',
  'positioning' => 'absolute'
));

$fileName = "{$nombre_archivo}.docx";

$objWriter = IOFactory::createWriter($phpWord, 'Word2007');
$objWriter->save($fileName);

header("Content-Disposition: attachment; filename=$fileName");
$objWriter->save("php://output");
flush();
unlink($fileName);


} catch (\Throwable $th) {
  echo $th->getMessage();
  exit;
}