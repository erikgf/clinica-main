<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

if(!isset($_POST['searchTerm'])){ 
  $fetchData = mysqli_query($conn,"SELECT * FROM servicio LIMIT 5");
}else{ 
  $search = $_POST['searchTerm'];
  $catID = $_POST['id'];
  $fetchData = mysqli_query($conn,"SELECT * FROM servicio WHERE id_categoria_servicio = $catID AND descripcion LIKE '%".$search."%' LIMIT 10");
} 

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_servicio'], "text"=>$row['descripcion'].' - S/ '.$row["precio_unitario"]);
}
echo json_encode($data);