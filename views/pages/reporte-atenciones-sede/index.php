<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");


include_once "../../template.php";

?>
<!--
<script type="text/javascript" src="index.liquidaciones.individual.medicos.js" defer></script>
<script type="text/javascript" src="index.liquidaciones.medicos.js" defer></script>
<script type="text/javascript" src="index.medicos.asignar.promotora.js" defer></script>
-->
<script type="text/javascript" src="index.js" defer></script>
<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
