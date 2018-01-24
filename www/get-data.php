<?php
session_start();
if(isset($_SESSION["logged"])){ //proverka koi e lognat
include '/var/www/db_params.php';
$conn = new mysqli($host, $username, $password, $dbname); //vruzka kum db
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
//header("Access-Control-Allow-Origin:*");
if($stmt = $conn->prepare("SELECT ip,data,k1,k2 FROM incoming_data")){ //vzimam vsicko kakvoto e zapisano v db
	$stmt->execute();
	$stmt->bind_result($col,$col1,$col2,$col3);
	while ($stmt->fetch()) {
		$array=array( //slagom go v masiv
					'ip' => $col,
					'data' => $col1,
					'k1' => $col2,
					'k2' => $col3,
				);
				$return[]=$array;
	}
	$stmt->close();
}
$conn->close();
if(!empty($return))
echo json_encode($return); //encodvam i prashtam
}
?>
