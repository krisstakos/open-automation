<?php
session_start();  
if(isset($_POST['name']) && isset($_POST['pass'])){
	$name=$_POST['name'];
	$pass=$_POST['pass'];
	include '/var/www/db_params.php';
	login_check($name,$pass,$host,$username,$password,$dbname);
}
else
	header('Location: index.php');
function login_check($name,$pass,$host,$username,$password,$dbname){
	$time=0;
	$atp=0;
	$msg1="";
	$msg2="";
	$url="";
	function update_attempts($conn,$atp,$name,$time){
	if($stmt = $conn->prepare("UPDATE users SET attempts=(?),time_exp=(?) WHERE name=(?)")){ //updatvam informaciq za potrebitelq
		$stmt->bind_param("iss", $atp,$time,$name);
		$stmt->execute();
		$stmt->close();
		$conn->close();
	}
	}
	$conn = new mysqli($host, $username, $password, $dbname);
	if($stmt = $conn->prepare("SELECT name,hash,attempts,time_exp FROM users WHERE name=(?)")){//poluchavam informaciq za opitite mu i ostavashto vreme
		$stmt->bind_param("s", $name);
		$stmt->execute();
		$stmt->bind_result($col1,$col2,$col3,$col4);
		$stmt->fetch();
		$stmt->close();
		$time_cmp = date("H:i:s");//teskushto vreme
		$currentTime = strtotime($time_cmp);
		$expTime = strtotime($col4);//malko magii s vremeto
		$formatTime= date("H:i:s", $currentTime);
		$formatTime2= date("i:s", $currentTime);
		$formatTime3= date("i:s", $expTime) ;
		if($col4>0 && $col4<=$formatTime)
			$col3=0;
		if (password_verify($pass, $col2) && $col3<3 && $col1==$name) { //proverqvam dali parolata i imeto sa verni
			$atp=0;
			$time=0;
			if($name=="root"){
				$stat=0;
				$_SESSION['logged_root']=true;
				$url="index_root.php";
			}
			else{
				$_SESSION['logged']=true;
				$stat=0;
				$url="index1.php"; //prenasochvam go kum interfeisa
			}
			update_attempts($conn,$atp,$name,$time);
		} 
		else if($col3<3 && $col1==$name){
			$atp=$col3+1;	
			if($atp<3){
				update_attempts($conn,$atp,$name,$time); 
				$stat=1;
				$msg1= 'Паролата или името са неправилни!'; //problem s vlizaneto
				$msg2='Остават: '.(3-$atp).' опит/а';
			}
			if($atp==3){
				$time = date("H:i:s");
				$currentTime = strtotime($time);
				$futureTime = $currentTime+(60*5); //zabrana za vlizane ot 5 minuti
				$formatTime= date("H:i:s", $futureTime);
				update_attempts($conn,$atp,$name,$formatTime);
				$stat=1;
				$msg1='Превишени са опитите за влизане!';
				$msg2='Моля, изчакайте: 5 минути';
			}	
		}
		else if($col1!=$name){
			$stat=1;
			$msg1='Паролата или името са неправилни!';
		}
		else{
			$time_left=$formatTime3-$formatTime2;
			if($time_left==0)
				$time_left="< 0 минути";
			else if($time_left==1)
				$time_left=$time_left." минута";
			else
				$time_left=$time_left." минути"; //proverki i izkravane za tova kolko vreme mu ostava do iztichane na zabranata
			$stat=1;
			$msg1= 'Превишени са опитите за влизане!';
			$msg2= 'Моля, изчакайте: '.$time_left;
		}
		$array=array( //slagom go v masiv
					'url' => $url,
					'msg1' => $msg1,
					'msg2' => $msg2,
					'stat' => $stat
				);
				$return[]=$array;
		echo json_encode($return);
	}
}
?>