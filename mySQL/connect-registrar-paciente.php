<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }
$id = $_POST['id'];
$tipoDocumento =  $_POST['tipoDocumento'];
$dni = $_POST['dni'];
$historial = $_POST['historial'];
$nombres = $_POST['nombres'];
$apellidoP = $_POST['apellidoP'];
$apellidoM = $_POST['apellidoM'];
$sexo = $_POST['sexo'];
$nacimiento = $_POST['nacimiento'];
$deuda = $_POST['deuda'];
$ocupacion = $_POST['ocupacion'];
$tipo = $_POST['tipo'];
$estadoCivil = $_POST['estadoCivil'];
$telFijo = $_POST['telFijo'];
$celUno = $_POST['celUno'];
$celDos = $_POST['celDos'];
$correo = $_POST['correo'];
$domicilio = $_POST['domicilio'];
$distrito = $_POST['distrito'];
$provincia = $_POST['provincia'];
$region = $_POST['region'];
$answer;
if(isset($_POST['id'])){
  $answer = mysqli_query($conn,"INSERT INTO paciente WHERE numero_documento = $id VALUES ($nombres,$apellidoP,$apellidoM,$historial,$tipoDocumento,$dni,$telFijo,$celUno,$celDos,,,,$correo,$domicilio,$distrito,$provincia,$region,$ocupacion,$tipo,$estadoCivil,$sexo,$nacimiento,$deuda,,,)");
}else{
  $answer = mysqli_query($conn,"INSERT INTO paciente VALUES ($nombres,$apellidoP,$apellidoM,$historial,$tipoDocumento,$dni,$telFijo,$celUno,$celDos,,,,$correo,$domicilio,$distrito,$provincia,$region,$ocupacion,$tipo,$estadoCivil,$sexo,$nacimiento,$deuda,,,");
}

// OR numero_documento LIKE '%".$search."%'

// $data = array();
// while ($row = mysqli_fetch_array($fetchData)) {    
//   $data[] = array("id"=>$row['id_paciente'], "text"=>$row['numero_documento'],"nombres"=>$row['nombres'],"apellidoP"=>$row['apellidos_paterno'],"apellidoM"=>$row['apellidos_materno'], "historial"=>$row['numero_historia'], "domicilio"=>$row['domilicio'], "distrito"=>$row['codigo_ubigeo_distrito'], "provincia"=>$row['codigo_ubigeo_provincia'], "region"=>$row['codigo_ubigeo_region'], "tipoDocumento"=>$row['id_tipo_documento'], "DNI"=>$row['numero_documento'],'DoB'=>$row['fecha_nacimiento'],'deuda'=>$row['saldo_deuda'],'ocupacion'=>$row['ocupacion'],'tipo'=>$row['id_tipo_paciente'],'telFijo'=>$row['telefono_fijo'],'celUno'=>$row['celular_uno'],'celDos'=>$row['celular_dos'],'correo'=>$row['correo'],'distrito'=>$row['codigo_ubigeo_distrito'],'provincia'=>$row['codigo_ubigeo_provincia'],'region'=>$row['codigo_ubigeo_region'],'sexo'=>$row['sexo'],'estadoCivil'=>$row['estado_civil']);
//   // 
// }
echo json_encode($answer);