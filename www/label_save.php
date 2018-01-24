<?php
session_start();
include '/var/www/db_params.php';
if(isset($_GET['dvs_id'])){
	$label=$_GET["label"];
	$second_label=$_GET["label1"];
	$id=$_GET["dvs_id"];
}
if(isset($_SESSION["logged"])){
$conn = new mysqli($host, $username, $password, $dbname);
if($label==""){
	$stmt = $conn->prepare("UPDATE devices SET label1=(?) WHERE id=(?)"); //zapisvam label ot sct1 ili LC
	$stmt->bind_param("si", $second_label,$id);
	$stmt->execute();
}
else{
	$stmt = $conn->prepare("UPDATE devices SET label=(?) WHERE id=(?)");//zapisvam label ot SCT2 
	$stmt->bind_param("si", $label,$id);
	$stmt->execute();
}
$stmt->close();
$conn->close();
}
?>