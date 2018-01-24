<?php
$command="/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
$localIP = exec ($command); //vziamm localnoto ip ot linux mashinata
include '/var/www/db_params.php'; //zarejdam danni za db
include 'items_types.php';
$head   = 'DV-INFORMATION'; //tursq tozi fragment
$cl   = ':';
$match=0;
$from = '';
$port = 0;
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); //suzdavam UDO soket
socket_bind($socket,$localIP, 5113); //i go bind-vam na localnoto ip i port
while(1){	
	socket_recvfrom($socket, $buf, 50, 0, $from, $port);
	if($buf!=null){ //proverqm za informaciq
		$match=0;
		$pos_h = strpos($buf, $head); //sravnqvam po gorniq fragment
		if ($pos_h=== false) {
			$pos_tp = strpos($buf, $dv_type1); //tursq tipa na ustroistvoto
			if($pos_tp===false){
				$int=2;
				$pos_tp = strpos($buf, $dv_type0);
			}
			else{
				$int=3;
				
			}
			$dv_type = substr($buf,$pos_tp,$int);
			$data = substr($buf,$int,50);
			$conn = new mysqli($host, $username, $password, $dbname);
			$stmt = $conn->prepare("INSERT INTO incoming_data (ip, device,data) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE data=?"); //zapisvam jivite danniti koito ustroistvoto e izpratilo
			$stmt->bind_param("ssss", $from, $dv_type,$data,$data);
			$stmt->execute();
			$stmt->close();
			$conn->close();
		}
		else{
			$pos_tp = strpos($buf, $dv_type1);
			if($pos_tp==false){
				$int=2;
				$pos_tp = strpos($buf, $dv_type0);
			}
			else
				$int=3;
            $pos_port = strripos($buf, $cl);
			$dv_ip= substr($buf, $pos_tp+$int, $pos_port-($pos_tp+$int));
			$dv_port = substr($buf,$pos_port+1,4);
			$dv_type = substr($buf,$pos_tp,$int);
			if (!filter_var($dv_ip, FILTER_VALIDATE_IP) === false) {
				$conn = new mysqli($host, $username, $password, $dbname);
				 if($stmt = $conn->prepare("SELECT ip,id,id_second FROM devices")){
					$stmt->execute();
					$stmt->bind_result($col,$col1,$col2);
					while ($stmt->fetch()) {
						if($col==$dv_ip){
							$match=1;
						}
						$lst_id_second=$col2;
						$lst_id=$col1;
					}
						$stmt->close();
				 }
				if($match==0){
					if($lst_id_second>$lst_id)
						$dv_id=$lst_id_second+1;
					else
						$dv_id=$lst_id+1;
					if($dv_type==$dv_type1)
						$dv_id_second=$dv_id+1;
					else
						$dv_id_second=0;
					if($stmt = $conn->prepare("INSERT INTO devices (ip,port,type,id,id_second) VALUES (?,?,?,?,?)")){ //zapisvam v db dannite za ustroistvoto
						$stmt->bind_param("sisii", $dv_ip, $dv_port,$dv_type,$dv_id,$dv_id_second);
						$stmt->execute();
						$stmt->close();
					}
				}
				$conn->close();
			}
			
		}
	}
}
?>
