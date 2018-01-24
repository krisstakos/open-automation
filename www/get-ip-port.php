<?php
session_start();
include '/var/www/db_params.php';
if(isset($_SESSION["logged"])){
$command="/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
$localIP = exec ($command); //vziamm localnoto ip 
$port=5113;
$conn = new mysqli($host, $username, $password, $dbname);
if($stmt = $conn->prepare("SELECT day_price FROM electricity_price")){ //vzimam cenata ot db
	$stmt->execute();
	$stmt->bind_result($col);
	$stmt->fetch();
	$stmt->close();
}
$array=array(
					'host_ip' => $localIP, //slagam gi v masivi
					'host_port' => $port,
					'el_price'=> $col
				);
				$return[]=$array;
echo json_encode($return); //encodvam v json i prashtam
}
?>