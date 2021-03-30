<?php
$conn=mysqli_connect('localhost','root','','bd_dpi');
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
  }

if(!isset($_POST['searchTerm'])){ 
  $fetchData = mysqli_query($conn,"select * from medico limit 5");
}else{ 
  $search = $_POST['searchTerm'];   
  $fetchData = mysqli_query($conn,"select * from medico where CONCAT(nombres, ' ', COALESCE(apellidos_paterno,''), ' ', COALESCE(apellidos_materno,'')) like '%".$search."%' limit 5");
} 

$data = array();
while ($row = mysqli_fetch_array($fetchData)) {    
  $data[] = array("id"=>$row['id_medico'], "text"=>$row['nombres'].' '.$row['apellidos_paterno'].' '.$row['apellidos_materno']);
}
echo json_encode($data);