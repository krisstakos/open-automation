<?php
//$command="/sbin/ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'";
//$localIP = exec ($command); //vziamm localnoto ip ot linux mashinata
$from = '';
$port = 0;
$socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP); //suzdavam UDO soket
socket_bind($socket,'192.168.1.166', 5150); //i go bind-vam na localnoto ip i port
	socket_recvfrom($socket, $buf, 150, 0, $from, $port);
	//if($buf!=null){ //proverqm za informaciq
	//	$match=0;	
	echo $buf;
	$obj = json_decode($buf);
	echo $obj->{'head'}; // 12345
?>