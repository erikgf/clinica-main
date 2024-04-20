<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.colaboradores.php';
include_once 'prt.modal.cambiarclave.php';
include_once 'prt.modal.roles.php';
include_once 'prt.modal.promotoras.php';
include_once 'prt.modal.cambiarclave.promotora.php'

?>

<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="ClsColaborador.js" defer></script>
<script type="text/javascript" src="ClsRol.js" defer></script>
<script type="text/javascript" src="ClsPromotora.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>
