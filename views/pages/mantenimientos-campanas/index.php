<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.campañas.php';

?>

<script type="text/javascript" src="ClsCampaña.2.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>
