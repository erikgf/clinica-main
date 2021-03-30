<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

if(!isset($_POST['searchTerm'])){ 
  $fetchData = mysqli_query($conn,"SELECT * FROM categoria_servicio LIMIT 5");
}else{ 
  $search = $_POST['searchTerm'];   
  $fetchData = mysqli_query($conn,"SELECT * FROM categoria_servicio WHERE descripcion LIKE '%".$search."%' LIMIT 5");
} 

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_categoria_servicio'], "text"=>$row['descripcion']);
}
echo json_encode($data);