<?php
session_start();//startiram sesiq
include '/var/www/db_params.php';
if(isset($_GET['ip'],$_GET["name"],$_GET["port"],$_GET["stat"])){ //proverqvam za zadadeni stoinosti
	$name=$_GET["name"];
	$ip=$_GET["ip"];
	$port=$_GET["port"];
	$stat=$_GET["stat"];
	$ip_cam= array();
	$port_cam= array();
	$label_cam= array();
	if(isset($_SESSION["logged"])){ //proverqm dali si lognat
		$conn = new mysqli($host, $username, $password, $dbname);
		if($stat==0){
			$stmt = $conn->prepare("INSERT INTO cameras (ip,label,port) VALUES (?,?,?) ON DUPLICATE KEY UPDATE label=?"); //zapisvam  informaciqta
			$stmt->bind_param("ssis", $ip,$name,$port,$name);
			$stmt->execute();
		//	echo "Камерата е добавена!";
		}
		else if($stat==1){
			$stmt = $conn->prepare("DELETE FROM cameras WHERE ip=? AND port=?"); //triq informaciq
			$stmt->bind_param("si", $ip,$port);
			$stmt->execute();	
			//echo "Камерата е премахната!";
		}
		$stmt = $conn->prepare("SELECT ip,port,label FROM cameras");
		$stmt->execute();	
		$stmt->bind_result($col,$col1,$col2);
		$n=0;
		while ($stmt->fetch()) {
			$ip_cam[$n]=$col;
			$port_cam[$n]=$col1;
			$label_cam[$n]=$col2;
			$n++;
		}
		if(array_filter($ip_cam)){
echo <<<EOT
	<table style=width:100%>
		<tr>
			<td>Адрес</td>
			<td>Порт</td>		
			<td>Име</td>	
	</tr>
EOT;
for($u=0;$u<=$n-1;$u++){
	echo <<<EOT
	<tr>
		<td>$ip_cam[$u]</td>
		<td>$port_cam[$u]</td>	
		<td>$label_cam[$u]</td>		
	</tr>
EOT;
}
echo "</table>";
		}
		else
			echo "Няма добавени камери!";
$stmt->close();
$conn->close();
}
}
?>