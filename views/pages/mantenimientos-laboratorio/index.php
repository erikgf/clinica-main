<?php

include_once "../../../datos/configuracion.vista.php";
include_once "../../../negocio/Sesion.clase.php";
include_once "../Template.demo.php";

$objTemplate = new Template();

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");

$_HOY = date("Y-m-d");

include_once "../../template.php";

include_once 'prt.modal.servicio.examen.php';
include_once 'prt.modal.servicio.perfil.examen.php';
include_once 'prt.modal.servicio.paquete.php';
include_once 'prt.modal.seccion.php';
include_once 'prt.modal.unidad.php';
include_once 'prt.modal.abreviatura.php';
include_once 'prt.modal.muestra.php';
include_once 'prt.modal.metodo.php';

?>

<script type="text/javascript" src="../../componentes/PrecioVenta.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/ExportadorCSV.componente.js" defer></script>

<script type="text/javascript" src="ClsServicioExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPerfilExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPaquete.js" defer></script>
<script type="text/javascript" src="index.servicios.1.js" defer></script>

<script type="text/javascript" src="ClsLabAbreviatura.js" defer></script>
<script type="text/javascript" src="ClsLabUnidad.js" defer></script>
<script type="text/javascript" src="ClsLabMuestra.js" defer></script>
<script type="text/javascript" src="ClsLabSeccion.js" defer></script>
<script type="text/javascript" src="ClsLabMetodo.js" defer></script>
<script type="text/javascript" src="index.mantenimientos.js" defer></script>

