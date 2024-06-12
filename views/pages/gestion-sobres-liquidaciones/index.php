<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objUsuario = Sesion::obtenerSesion();

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

include_once "../../template.php";

?>

<script type="text/javascript" src="../../componentes/Paginador.componente.js" defer></script>
<script type="text/javascript" src="EntregasSobres.js" defer></script>
<script type="text/javascript" src="LiquidacionesSinSobre.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>