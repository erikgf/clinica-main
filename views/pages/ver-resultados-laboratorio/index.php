<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

include_once "../../template.php";
include_once "ptr.modal.resultadosdetalle.php";

?>

<script type="text/javascript" src="index.js" defer></script>
