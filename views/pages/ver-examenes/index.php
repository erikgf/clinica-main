<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

$hoy = date("Y-m-d");
$hace_siete_dias = date('Y-m-d', strtotime('-7 day', strtotime($hoy)));

include_once "../../template.php";

?>

<link rel="stylesheet" href="./components/ResumenCantidadExamens.css">
<script type="text/javascript" src="./components/ResumenCantidadExamenes.js" defer></script>
<script type="text/javascript" src="index.1.js" defer></script>
