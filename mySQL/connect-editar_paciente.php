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

if(isset($_POST['id'])){
  
  $sql = "UPDATE paciente
  SET nombres = '$nombres',
  apellidos_paterno = '$apellidoP',
  apellidos_materno = '$apellidoM',
  numero_historia = '$historial',
  id_tipo_documento = '$tipoDocumento',
  numero_documento = '$dni',
  telefono_fijo = '$telFijo',
  celular_uno = '$celUno',
  celular_dos = '$celDos',
  correo = '$correo',
  domilicio = '$domicilio',
  codigo_ubigeo_distrito = '$distrito',
  codigo_ubigeo_provincia = '$provincia',
  codigo_ubigeo_region = '$region',
  ocupacion = '$ocupacion',
  id_tipo_paciente = '$tipo',
  estado_civil = '$estadoCivil',
  sexo = '$sexo',
  fecha_nacimiento = '$nacimiento',
  saldo_deuda = '$deuda'
  WHERE id_paciente = '$id'";
  
  if ($conn->query($sql) === TRUE) {
    echo json_encode("EdicionOK");
  } else {
    echo json_encode("Error: " . $sql . "<br>" . $conn->error);
  }
}else{
  $sql = "UPDATE paciente
  SET nombres = '$nombres',
  apellidos_paterno = '$apellidoP',
  apellidos_materno = '$apellidoM',
  numero_historia = '$historial',
  id_tipo_documento = '$tipoDocumento',
  numero_documento = '$dni',
  telefono_fijo = '$telFijo',
  celular_uno = '$celUno',
  celular_dos = '$celDos',
  correo = '$correo',
  domilicio = '$domicilio',
  codigo_ubigeo_distrito = '$distrito',
  codigo_ubigeo_provincia = '$provincia',
  codigo_ubigeo_region = '$region',
  ocupacion = '$ocupacion',
  id_tipo_paciente = '$tipo',
  estado_civil = '$estadoCivil',
  sexo = '$sexo',
  fecha_nacimiento = '$nacimiento',
  saldo_deuda = '$deuda'
  WHERE id_paciente = '$id'";
  
  if ($conn->query($sql) === TRUE) {
    echo json_encode("RegistroOK");
  } else {
    echo json_encode("Error: " . $sql . "<br>" . $conn->error);
  }
}

$conn->close();