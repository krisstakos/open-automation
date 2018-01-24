<?php
session_start();
include '/var/www/db_params.php';
$id=$_GET["id"];
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$devices = array(
0 => "SELECT time_start,time_on,last_wats FROM devices WHERE id=?", 
1 => "SELECT time_start_second,time_on_second,last_wats_second FROM devices WHERE id_second=?");
$history = array(
0 => "SELECT time_on FROM months_consumption WHERE id_hs=?",
1 => "SELECT time_on_second FROM months_consumption WHERE id_second_hs=?");
for($i=0;$i<=1;$i++){
	if($stmt = $conn->prepare($devices[$i])){
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($col_dv_time_start,$col1_dv_time_on,$col2_dv_last_wats);
		$stmt->fetch();
		$stmt->close();
	}
}
for($i=0;$i<=1;$i++){
	if($stmt = $conn->prepare($history[$i])){
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($mn_time_on);
		$stmt->fetch();
		$stmt->close();
	}
}
$array=array(
					'time_start' => $col_dv_time_start, //slagam gi v masiv
					'time_on' => $col1_dv_time_on,
					'last_wats' => $col2_dv_last_wats,
					'history' => $mn_time_on
				);
				$return[]=$array;
echo json_encode($return); //encodvam i prashtam
$conn->close();
?>