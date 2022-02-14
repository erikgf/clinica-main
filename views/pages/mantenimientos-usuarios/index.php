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

if (!$objTemplate->validarPermisoRoles($objUsuario, [$objTemplate->ID_ROL_ADMINISTRADOR])){
	$objTemplate->mostrarAccesoNoValido();
	exit;
}

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.colaboradores.php';
include_once 'prt.modal.cambiarclave.php';
include_once 'prt.modal.roles.php';

?>

<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="ClsColaborador.js" defer></script>
<script type="text/javascript" src="ClsRol.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>
