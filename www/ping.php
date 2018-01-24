<?php
session_start();
include '/var/www/db_params.php';
//if(isset($_SESSION["logged"])){
while(1){
	$all_ip= array();
	$death_ip=array();
	$conn = new mysqli($host, $username, $password, $dbname);
	if($stmt = $conn->prepare("SELECT ip FROM incoming_data")){ //vzimam cenata ot db
		$stmt->execute();
		$stmt->bind_result($col);
		$n=0;
		while ($stmt->fetch()) {
			$all_ip[$n]=$col;
			$n++;
			}
		$stmt->close();
	}
	$allIp = implode(' ', $all_ip);
	exec("fping  -u -i 100 $allIp" , $death_ip);
	//echo print_r($death_ip);
	 /* for($i=0;$i<=$n-1;$i++){
		if($stmt = $conn->prepare("DELETE FROM incoming_data WHERE ip=?")){ //vzimam cenata ot db
			$stmt->bind_param("s",$death_ip[$i]);
			$stmt->execute();
			$stmt->close();
		}
		if($stmt = $conn->prepare("DELETE FROM devices WHERE ip=?")){ //vzimam cenata ot db
			$stmt->bind_param("s",$death_ip[$i]);
			$stmt->execute();
			$stmt->close();
		}
	}  */
	 $conn->close();
	 sleep(1);
}
//}
?>