<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

$id = $_POST['idservicio'];
$fetchData = mysqli_query($conn,"SELECT * FROM servicio WHERE id_servicio ='$id'");
 

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_servicio'], "nombre"=>$row['descripcion'], "precio"=>$row['precio_unitario'], "precio_sin_IGV"=>$row['precio_venta_sin_igv']);
}
echo json_encode($data);