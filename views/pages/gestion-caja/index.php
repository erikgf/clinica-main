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

include_once 'prt.modal.abrir.caja.php';
include_once 'prt.modal.cerrar.caja.php';
include_once 'ptr.modal.registrar.ingreso.php';
include_once 'ptr.modal.registrar.egreso.php';


?>

<script type="text/javascript" src="cls.recibo.js" defer></script>

<script type="text/javascript" src="index.registrar.ingreso.js" defer></script>
<script type="text/javascript" src="index.registrar.egreso.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>


<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
