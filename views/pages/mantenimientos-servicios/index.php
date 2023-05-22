<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.servicio.general.php';
include_once 'prt.modal.servicio.examen.php';
include_once 'prt.modal.servicio.perfil.examen.php';
/*
include_once 'prt.modal.seccion.php';
include_once 'prt.modal.caracteristica.php';
include_once 'prt.modal.unidad.php';
include_once 'prt.modal.abreviatura.php';
include_once 'prt.modal.muestra.php';
include_once 'prt.modal.metodo.php';
*/


?>


<script type="text/javascript" src="../../componentes/PrecioVenta.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="ClsServicioGeneral.js" defer></script>
<script type="text/javascript" src="ClsServicioExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPerfilExamen.js" defer></script>
<script type="text/javascript" src="index.servicios.js" defer></script>

<!--
<script type="text/javascript" src="ClsMedico.js" defer></script>
<script type="text/javascript" src="ClsArea.js" defer></script>
<script type="text/javascript" src="ClsPromotora.js" defer></script>
<script type="text/javascript" src="index.liquidaciones.individual.medicos.js" defer></script>
<script type="text/javascript" src="index.liquidaciones.medicos.js" defer></script>
<script type="text/javascript" src="index.medicos.asignar.promotora.js" defer></script>
<script type="text/javascript" src="index.promotoras.medicos.js" defer></script>
-->
<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
