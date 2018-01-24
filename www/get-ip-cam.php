<?php
session_start();
include '/var/www/db_params.php';
if(isset($_SESSION["logged"])){
$ip_cam = array();
$cam_label = array();
$n=0;
$conn = new mysqli($host, $username, $password, $dbname);
$stmt = $conn->prepare("SELECT ip,label FROM cameras"); //vziamm infoto ot db za kamerite
$stmt->execute();
$stmt->bind_result($col,$col1);
while ($stmt->fetch()) {
	$array=array(
				'cam_ip' => $col, //slagam v masiv
				'label' => $col1,
			);
$return[]=$array;
}
$stmt->close();
echo json_encode($return); //encodvam v json i prashtam
}
?>