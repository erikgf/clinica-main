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
include_once 'prt.modal.servicio.paquete.php';
include_once 'ptr.modal.categoriaproduccion.php';

?>

<link rel="stylesheet" href="../../componentes/InputSearch/InputSearch.componente.css" />
<script type="text/javascript" src="../../componentes/InputSearch/InputSearch.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/PrecioVenta.componente.js" defer></script>
<script type="text/javascript" src="../../componentes/Select.componente.js" defer></script>
<script type="text/javascript" src="ClsServicioGeneral.js" defer></script>
<script type="text/javascript" src="ClsServicioExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPerfilExamen.js" defer></script>
<script type="text/javascript" src="ClsServicioPaquete.js" defer></script>
<script type="text/javascript" src="index.servicios.2.js" defer></script>

<script type="text/javascript" src="ClsCategoriaProduccion.js" defer></script>
<script type="text/javascript" src="ClsMatrizProduccionMedico.js" defer></script>
