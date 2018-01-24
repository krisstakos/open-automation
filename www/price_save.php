<?php
session_start();
include '/var/www/db_params.php';
if(isset($_GET['day_price']))
	$day_price=$_GET["day_price"]; //vzimam informaciq za centa
if(isset($_SESSION["logged"])){
$conn = new mysqli($host, $username, $password, $dbname);
	$stmt = $conn->prepare("INSERT INTO electricity_price (day_price) VALUES (?) ON DUPLICATE KEY UPDATE day_price=?"); //zpisvam q v db
	$stmt->bind_param("ss", $day_price,$day_price);
	$stmt->execute();
$stmt->close();
$conn->close();
}
?>
