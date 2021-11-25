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
include_once "prt.modal.empresa.convenio.php";
include_once "prt.modal.facturacion.convenio.php";

?>

<script type="text/javascript" src="../../componentes/PrecioVenta.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/ConsultarDocumento.componente.js" defer></script>
<!--
<script type="text/javascript" src="ClsServicioGeneral.js" defer></script>
<script type="text/javascript" src="ClsServicioExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPerfilExamen.js" defer></script>
-->
<script type="text/javascript" src="index.empresas.convenio.js" defer></script>
<script type="text/javascript" src="index.atenciones.convenio.js" defer></script>
<script type="text/javascript" src="index.facturacion.convenio.js" defer></script>

