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

$objTemplate->setTitle(NOMBRE_SISTEMA);
$objTemplate->loadContent("prt.main.php");
//$objTemplate->loadNavbar();

$hoy = date("Y-m-d");
$hace_siete_dias = date('Y-m-d', strtotime('-7 day', strtotime($hoy)));

include_once "../../template.php";
include_once "prt.modal.canjearcomprobante.php";

?>

<script>
    var _ES_ID_ROL_SUPERVISOR = <?php echo $objUsuario["id_rol"] == $objTemplate->ID_ROL_RECEPCION_SUPERVISOR ? "1" : "0"; ?>;
</script>

<script type="text/javascript" src="canjear.comprobante.js" defer></script>
<script type="text/javascript" src="index.js" defer></script>
<!--
<script type="text/javascript" src="index.paciente.js" defer></script>
<script type="text/javascript" src="index.continuar.pago.js" defer></script>
-->
