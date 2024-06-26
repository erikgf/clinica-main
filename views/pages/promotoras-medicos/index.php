<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objUsuario = Sesion::obtenerSesion();

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);

if ($objUsuario["id_rol"] == $objTemplate->ID_ROL_ASISTENTE_ADMINISTRADOR){
	$objTemplate->loadContent("prt.main.asistente.administrador.php");
} else {
	$objTemplate->loadContent("prt.main.php");	
}

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.medicos.php';
include_once 'prt.modal.promotoras.php';
include_once 'prt.modal.areas.php';

?>

<script type="text/javascript" src="ClsMedico.1.js" defer></script>
<script type="text/javascript" src="ClsArea.js" defer></script>
<script type="text/javascript" src="ClsPromotora.js" defer></script>
<script type="text/javascript" src="index.liquidaciones.individual.medicos.1.js" defer></script>
<script type="text/javascript" src="index.liquidaciones.medicos.1.js" defer></script>
<script type="text/javascript" src="index.medicos.asignar.promotora.1.js" defer></script>
<script type="text/javascript" src="index.promotoras.medicos.1.js" defer></script>
<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
