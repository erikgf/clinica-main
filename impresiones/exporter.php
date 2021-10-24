 
<?php

date_default_timezone_set('America/Lima');
require '../datos/datos.empresa.php';
require "../negocio/AtencionMedica.clase.php";

$objAtencionMedica = new AtencionMedica();

$fi = isset($_GET["p_fi"]) ? $_GET["p_fi"] : "";
$ff = isset($_GET["p_ff"]) ? $_GET["p_ff"] : "";

if ($fi == ""){
    echo "No se ha ingresado fecha de inicio";
    exit;
} 

if ($ff == ""){
    echo "Nose ha ingresado fecha de fin";
    exit;
}

$mesaniofi = date("mY", strtotime($fi));
$mesanioff = date("mY", strtotime($ff));


if ($mesanioff != $mesaniofi){
	echo "Solo se permite data del mismo período, mismo mes y año.";
	exit;
}

$ventas = $objAtencionMedica->obtenerVentasExportacionContab($fi, $ff);

$nombre_archivo = $mesanioff.F_RUC.".txt";

// En nuestro ejemplo estamos abriendo $nombre_archivo en modo de adición.
// El puntero al archivo está al final del archivo
// donde irá $contenido cuando usemos fwrite() sobre él.
if (!$gestor = fopen($nombre_archivo, 'a')) {
     echo "No se puede abrir el archivo ($nombre_archivo)";
     exit;
}

$contenido = "";

var_dump($ventas); exit;

foreach ($ventas as $key => $registro) {
	foreach ($registro as $_ => $__) {
		if ($_ > 0){
			$contenido .= "|";	
		}
		$contenido .= $__;	
	}

	$contenido .= "\n";
}


// Escribir $contenido a nuestro archivo abierto.
if (fwrite($gestor, $contenido) === FALSE) {
    echo "No se puede escribir en el archivo ($nombre_archivo)";
    exit;
}

echo "Éxito, se escribió ($contenido) en el archivo ($nombre_archivo)";

fclose($gestor);


