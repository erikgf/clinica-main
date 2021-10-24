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

if (!$objTemplate->validarPermisoRoles($objUsuario, [$objTemplate->ID_ROL_COORDINADOR_LABORATORIO, $objTemplate->ID_ROL_ADMINISTRADOR])){
	$objTemplate->mostrarAccesoNoValido();
	exit;
}

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
