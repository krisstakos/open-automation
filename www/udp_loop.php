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
	socket_recvfrom($socket, $buf, 150, 0, $from, $port);
	if($buf!=null){ //proverqm za informaciq
		$match=0;	
		$obj = json_decode($buf);
		if(isset($obj->{'head'})){
			if($obj->{'head'}!=$head) {
				$dv_type = $obj->{'type'};
				$data = $obj->{'data'};
				$k1= $obj->{'k1'};
				if($dv_type==$dv_type1)
					$k2= $obj->{'k2'};
				else
					$k2="";
				$conn = new mysqli($host, $username, $password, $dbname);
				$stmt = $conn->prepare("INSERT INTO incoming_data (ip, device,data,k1,k2) VALUES (?, ?, ?,?,?) ON DUPLICATE KEY UPDATE data=?,k1=?,k2=?"); //zapisvam 	jivite danniti koito ustroistvoto e izpratilo
				$stmt->bind_param("sssiisii", $from, $dv_type,$data,$k1,$k2,$data,$k1,$k2);
				$stmt->execute();
				$stmt->close();
				$conn->close();
			}
		}
		else{
			if(isset($obj->{'ip'})){
				$dv_ip= $obj->{'ip'};
			
			if(isset($obj->{'port'})){
				$dv_port = $obj->{'port'};
			
			$dv_type = $obj->{'type'};
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
			}
			}
				$conn->close();
			}
			
		}
	}
}
?>
