<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.php";

$objUsuario = Sesion::obtenerSesion();

if ($objUsuario == null){
    echo '<script> alert("Permisos de sesión no validados"); </script>';
    header("Location: ../login");
}

$objTemplate = new Template();

if (!$objTemplate->validarPermisoRoles($objUsuario, [$objTemplate->ID_ROL_ADMINISTRADOR, $objTemplate->ID_ROL_LOGISTICA, $objTemplate->ID_ROL_ASISTENTE_ADMINISTRADOR])){
	$objTemplate->mostrarAccesoNoValido();
	exit;
}

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

$hoy = date("Y-m-d");
$hace_siete_dias = date('Y-m-d', strtotime('-7 day', strtotime($hoy)));

include_once "../../template.php";
include_once "ptr.modal.veratencion.php";
include_once "../gestion-atenciones/prt.modal.canjearcomprobante.php";

?>

<script type="text/javascript" src="../gestion-atenciones/canjear.comprobante.js" defer></script>
<script type="text/javascript" src="index.veratencion.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>

<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
