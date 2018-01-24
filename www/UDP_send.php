<?php
session_start();
include '/var/www/db_params.php';
if(isset($_POST["q"],$_POST["status"],$_POST["last_wats"])){
	if(!empty($_POST["q"] && !empty($_POST["status"]) && !empty($_POST["last_wats"]))){
		$in_id=$_POST["q"];
		$status=$_POST["status"];
		$last_wats=$_POST["last_wats"];
	}
}
elseif(isset($_POST["in_type"]) && !empty($_POST["in_type"])){
	$in_type=$_POST["in_type"];
	if($in_type=="mouse"){
		$datax=$_POST["datax"];
		$datay=$_POST["datay"];
		$command=$datax."&".$datay;
	}
	else if($in_type=="key"){
		$command=$_POST["command"];
	}
	else if($in_type=="mouse_click"){
		$command=$_POST["command"];
	}
	   $variable = array( 'type' => "$in_type", 
									'command' => "$command" );
	$msg=json_encode($variable);
}
if(isset($_SESSION["logged"])){
	$conn = new mysqli($host, $username, $password, $dbname);
	if(isset($_POST["q"])){
		if($stmt = $conn->prepare("SELECT ip,port,id_second,time_on,time_start,label FROM devices WHERE id=(?)")){ //vziamm informaciq ot sct1-kontakt purvi
			$stmt->bind_param("i", $in_id);
			$stmt->execute();
			$stmt->bind_result($col1,$col2,$col3,$col4,$col5,$col6);
			$stmt->fetch();
			$stmt->close();
		}
		if(($col1==0)&&($col3==0)){ //SCT nomer 2
			$stmt = $conn->prepare("SELECT ip,port,time_on_second,time_start_second,label1 FROM devices WHERE id_second=(?)"); //izbiram vsichko ot sct nomer 2
			$stmt->bind_param("i", $in_id);
			$stmt->execute();
			$stmt->bind_result($col1,$col2,$col3,$col4,$col5);
			$stmt->fetch();
			$stmt->close();
			$msg=2;
			if($status!="true"){ //status false
				$time_cmp = date("H:i"); //segashno vreme
				$currentTime = strtotime($time_cmp);
				$formatTime2= date("H:i", $currentTime); //tekusht chas
				$time_total= (strtotime($formatTime2) - strtotime($col4))/60; //razlika vuvu vremeto
				//$time_h=floor($time_h);
				//$time_m=$time_h%60;
				//$time_h=($time_h-$time_m)/60;
				//$time_total=$time_h.":".$time_m;
				//$time_ac=$formatTime2-$col4; //izminato vreme
				if($col5!="")
					$time_total=$time_total+$col3;
				else
					$time_total="";
				$formatTime="";
			}
			else{ //status true
				$time_cmp = date("H:i");
				$currentTime = strtotime($time_cmp);
				$formatTime= date("H:i", $currentTime);
				$time_total=$col3;
			}
			$stmt = $conn->prepare("UPDATE devices SET time_start_second=?, time_on_second=?, last_wats_second=? WHERE id_second=?"); //updeitvam vremeto i vatovete
			$stmt->bind_param("sisi", $formatTime,$time_total,$last_wats,$in_id);
			$stmt->execute();
			$stmt->close();
		}//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
		else{
			$msg = 1;
			if($status!="true"){
				$time_cmp = date("H:i");
				$currentTime = strtotime($time_cmp);
				$formatTime2= date("H:i", $currentTime); //tekusht chas
				$time_total= (strtotime($formatTime2) - strtotime($col5))/60;
				if($col6!="")
					$time_total=$time_total+$col4;
				else
					$time_total="";
				$formatTime="";
			}
			else{
				$time_cmp = date("H:i");
				$currentTime = strtotime($time_cmp);
				$formatTime= date("H:i", $currentTime);
				$time_total=$col4;
			}
			if($col3!=0){
				$stmt = $conn->prepare("UPDATE devices SET time_start=?, time_on=?, last_wats=? WHERE id=?");
				$stmt->bind_param("sisi", $formatTime,$time_total,$last_wats,$in_id);
				$stmt->execute();
				$stmt->close();
			}
		}
		$conn->close();
}
	else{
		if($stmt = $conn->prepare("SELECT pc_ip,pc_port FROM settings")){ //vziamm informaciq ot sct1-kontakt purvi
			$stmt->execute();
			$stmt->bind_result($col1,$col2);
			$stmt->fetch();
			$stmt->close();
		}
	}
	$len = strlen($msg);
	$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
	socket_sendto($socket, $msg, $len, 0, $col1, $col2);
}
?>