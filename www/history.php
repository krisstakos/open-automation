<?php
include '/var/www/db_params.php';
$dv_id = array();
$dv_id_second=array();
$dv_time_on=array();
$dv_time_on_second=array();
$empty="";
$done=0;
//while(1){
	if(date("d")==1 && $done==0){
		//if(date("G")==17 && date("i")==26 && date("s")==1){
			$done=1;
			$conn = new mysqli($host, $username, $password, $dbname); //vruzka kum db
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			if($stmt = $conn->prepare("DELETE FROM months_consumption"));
				$stmt->execute();
			if($stmt = $conn->prepare("SELECT id,id_second,time_on,time_on_second FROM devices WHERE id_second<>?")){//izbiram vsichko ot devices za da zapisha v druga tablica
				$stmt->bind_param("i",$empty);
				$stmt->execute();
				$stmt->bind_result($col_id,$col_id_second,$col_time_on,$col_time_second);
				$n=0;
				while ($stmt->fetch()) {//poluchavane na otgovor i podrejdane v masivi
					$dv_id[$n]=$col_id;
					$dv_id_second[$n]=$col_id_second;
					$dv_time_on[$n]=$col_time_on;
					$dv_time_on_second[$n]=$col_time_second;
					$n++;
				}
				//$stmt->close();
				//$conn->close();
			}
			if(!empty($dv_id)){
				for($r=0;$r<=count($dv_id)-1;$r++){
					if($stmt = $conn->prepare("INSERT INTO months_consumption (id_hs, id_second_hs,time_on,time_on_second) VALUES (?, ?, ?,?)")){ //zapisvam jivite danniti koito ustroistvoto e izpratilo
						$stmt->bind_param("iiii", $dv_id[$r], $dv_id_second[$r],$dv_time_on[$r],$dv_time_on_second[$r]);
						$stmt->execute();
						$stmt->close();
					}
				}
				if($stmt = $conn->prepare("UPDATE devices SET time_on=?,time_on_second=?")){//updeitvam vremeto i vatovete
					$stmt->bind_param("ii", $empty,$empty);
					$stmt->execute();
				}
				$conn->close();
			}
			
		//}
	}
	else if(date("d")!=1)
		$done=0;
//}
?>