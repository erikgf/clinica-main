<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }


$search = $_POST['searchTerm'];
$fetchData = mysqli_query($conn,"select * from paciente where numero_documento = '$search'");

// OR numero_documento LIKE '%".$search."%'

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_paciente'], "text"=>$row['numero_documento'],"nombres"=>$row['nombres'],"apellidoP"=>$row['apellidos_paterno'],"apellidoM"=>$row['apellidos_materno'], "historial"=>$row['numero_historia'], "domicilio"=>$row['domilicio'], "distrito"=>$row['codigo_ubigeo_distrito'], "provincia"=>$row['codigo_ubigeo_provincia'], "region"=>$row['codigo_ubigeo_region'], "tipoDocumento"=>$row['id_tipo_documento'], "DNI"=>$row['numero_documento'],'DoB'=>$row['fecha_nacimiento'],'deuda'=>$row['saldo_deuda'],'ocupacion'=>$row['ocupacion'],'tipo'=>$row['id_tipo_paciente'],'telFijo'=>$row['telefono_fijo'],'celUno'=>$row['celular_uno'],'celDos'=>$row['celular_dos'],'correo'=>$row['correo'],'distrito'=>$row['codigo_ubigeo_distrito'],'provincia'=>$row['codigo_ubigeo_provincia'],'region'=>$row['codigo_ubigeo_region'],'sexo'=>$row['sexo'],'estadoCivil'=>$row['estado_civil']);
  // 
}
echo json_encode($data);