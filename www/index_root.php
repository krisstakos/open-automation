<?php
session_start();
if(!isset($_SESSION["logged_root"])){
	header('Location: index1.php');
	die();
}
else
echo <<<EOT
<html>
<head>
	<title>TEst</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/foundation.css" />
	<style>
	#login{
		text-align:center;
		width:80%;
		margin:0 auto;
	}
	html { 
	overflow-y: hidden; 
	}
	</style>
</head>
<body>
<div class="row">
<div class="small-12 columns">
<div id="login">
		<h2>Регистриране на потребител:</h2>
		<input type="text" id="name" placeholder="Име" />
		<input type="password" id="pass" placeholder="Парола"/>
		<div class="row">
			<button id ="sign_up" class="radius tiny button">Регистрирай</button>
			<button id="change" class="radius tiny button">Промени</button>
			<button id="sign_out" class="radius tiny button" >Излез</button>
		</div>
		<div id="status"></div>
</div>
</div>
</div>
  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/reg.js"></script>
  <script>
    $(document).foundation();
  </script>
</body>
</html>
EOT;
?>