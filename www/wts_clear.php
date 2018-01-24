<?php
session_start();  
include '/var/www/db_params.php'; //proverka i vzima na stoinosti ot zaqvkata
$pos=$_GET["pos"];
$id=$_GET["id"];
if(isset($_SESSION["logged"])){ 
$value="";
$conn = new mysqli($host, $username, $password, $dbname);
if($pos==1){
if($stmt = $conn->prepare("UPDATE devices SET time_on=(?) WHERE id=(?)")){
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
}
if($stmt = $conn->prepare("UPDATE months_consumption SET time_on=(?) WHERE id_hs=(?)")){
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
}
$conn->close();
}
else if($pos==2){
if($stmt = $conn->prepare("UPDATE devices SET time_on_second=(?) WHERE id_second=(?)")){
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
}
if($stmt = $conn->prepare("UPDATE months_consumption SET time_on_second=(?) WHERE id_second_hs=(?)")){
	$stmt->bind_param("ii",$value,$id);
	$stmt->execute();
}
$conn->close();
}
}
?>