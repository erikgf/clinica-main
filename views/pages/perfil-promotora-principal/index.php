<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";


$objUsuario = Sesion::obtenerSesion();
$objTemplate = new Template();
$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

include_once "../../template.php";
include_once "./ptr.modal.registro.php";
include_once "./ptr.modal.viejo.registro.php";

?>


<script type="text/javascript" src="./componentes/GenerarReportePromotora.js" defer></script>
<script type="text/javascript" src="./componentes/MantenimientoMedicosActivar.js" defer></script>
<script type="text/javascript" src="./componentes/MantenimientoMedicos.js" defer></script>
<script type="text/javascript" src="index.1.js" defer></script>
