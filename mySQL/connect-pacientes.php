<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

if(!isset($_POST['searchTerm'])){ 
  $fetchData = mysqli_query($conn,"select * from paciente limit 5");
}else{ 
  $search = $_POST['searchTerm'];   
  $fetchData = mysqli_query($conn,"select * from paciente where CONCAT(numero_documento, ' - ', nombres, ' ', apellidos_paterno, ' ', apellidos_materno) like '%".$search."%' limit 5");
} 
// OR numero_documento LIKE '%".$search."%'

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_paciente'],
   "text"=>$row['numero_documento'].' - '.$row['nombres'].' '.$row['apellidos_paterno'].' '.$row['apellidos_materno'], 
   "historial"=>$row['numero_historia'], "domicilio"=>$row['domilicio'], 
   "distrito"=>$row['codigo_ubigeo_distrito'], 
   "provincia"=>$row['codigo_ubigeo_provincia'], 
   "region"=>$row['codigo_ubigeo_region'],
    "tipoDocumento"=>$row['id_tipo_documento'],
    "DNI"=>$row['numero_documento']);
  // 
}
echo json_encode($data);