<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";


$objTemplate = new Template();

$esRolSupervisor = $objTemplate->esIdRolSupervisor();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

$hoy = date("Y-m-d");
$hace_siete_dias = date('Y-m-d', strtotime('-7 day', strtotime($hoy)));

include_once "../../template.php";
include_once "prt.modal.canjearcomprobante.php";

?>

<script>
    var _ES_ID_ROL_SUPERVISOR = <?php echo $esRolSupervisor;?>;
</script>

<script type="text/javascript" src="canjear.comprobante.1.js" defer></script>
<script type="text/javascript" src="index.1.js" defer></script>
<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
