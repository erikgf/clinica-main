<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle("DMI");
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

include_once "../../template.php";

include_once 'prt.modal.gestionar.paciente.php';
include_once 'ptr.modal.continuar.pago.php';
include_once 'ptr.modal.descuentos.php';
include_once 'ptr.modal.validacion.descuento.sin.efectivo.php';

?>


<script type="text/javascript" src="../../componentes/Select.Componente.js" defer></script>
<link rel="stylesheet" href="./componentes/HistorialPaciente.css">
<script type="text/javascript" src="./componentes/HistorialPaciente.js" defer></script>
<script type="text/javascript" src="index.paciente.4.js" defer></script>
<script type="text/javascript" src="index.8.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.8.js" defer></script>


<script>
    var ID_REGISTRO = <?php echo isset($_GET["p_id"]) ? '$_GET["id"]': 'null'?>;
</script>