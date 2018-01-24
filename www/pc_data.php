<?php
session_start();
include '/var/www/db_params.php';
if(isset($_POST['pc_ip'],$_POST['pc_port'],$_POST['update'])){
	if($_POST['update']==0){
	$pc_ip=$_POST['pc_ip'];
	$pc_port=$_POST['pc_port'];
		if(isset($_SESSION["logged"])){
			$conn = new mysqli($host, $username, $password, $dbname);
			$stmt = $conn->prepare("DELETE FROM settings");
			$stmt->execute();
			$stmt = $conn->prepare("INSERT INTO settings (pc_ip,pc_port) VALUES (?,?)"); //zpisvam q v db
			$stmt->bind_param("si", $pc_ip,$pc_port);
			$stmt->execute();
			$stmt->close();
			$conn->close();
			get_info($host, $username, $password, $dbname);
		}
	}
	else if($_POST['update']==1)
		get_info($host, $username, $password, $dbname);
}
function get_info($host, $username, $password, $dbname){
	$conn = new mysqli($host, $username, $password, $dbname);
	if($stmt = $conn->prepare("SELECT pc_ip,pc_port FROM settings")){ //vzimam vsicko kakvoto e zapisano v db
	$stmt->execute();
	$stmt->bind_result($col,$col1);
	$stmt->fetch();
	$array=array( 
				'ip' => $col,
				'port' => $col1
			);
			$return[]=$array;
	$stmt->close();
	if(!empty($return))
		echo json_encode($return); //encodvam i prashtam
	}
}
?>
