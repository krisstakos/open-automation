<?php 
session_start();
if(isset($_SESSION["logged"])){ //proverka dali si lognat
	if(isset($_GET["load"])){ //proverka i vzima na stoinosti ot zaqvkata
		$load_n=$_GET["load"];
	}
	else
		$load_n="";
include 'items.php';
include 'items_types.php';
include '/var/www/db_params.php';
$ip_dvs = array(); //deklarirane na mnogo masivi 
$id_dvs = array();
$id_dvs_second = array();
$id_dvs_second_sub = array();
$dv_status=array();
$tp_dvs = array();
$label_id=array();
$label_id_lc=array();
$label_id_sct=array();
$label_id_sct_second=array();
$label=array();
$id_dvs_lc=array();
$cam_ip=array();
$cam_label=array();
$cam_id=array();
$cam_port=array();
$lc_cnt=0;
$sct_cnt=0;
$match=0;
$conn = new mysqli($host, $username, $password, $dbname); //vruzka kum db
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$ip_cam = array();
$cam_label = array();
$n=0;
$conn = new mysqli($host, $username, $password, $dbname);
$stmt = $conn->prepare("SELECT ip,label,id,port FROM cameras"); //izprashtane na zaqvka
$stmt->execute();
$stmt->bind_result($col_ip,$col_label,$col_ids,$col_ports); 
$z=0;
while ($stmt->fetch()) { //poluchavane na otgovor
	$cam_ip[$z]=$col_ip;
	$cam_label[$z]=$col_label;
	$cam_id[$z]=$col_ids;
	$cam_port[$z]=$col_ports;
	$z++;
}
$stmt->close();
$z=$z-1;
if(count($cam_ip)>0){ //proverqvam kolko sa kamerite
		for($q=0;$q<=$z;$q++){		//slagam gi v masivi
				$array=array(
					'cam_ip'=> $cam_ip[$q],
					'cam_port' => $cam_port[$q],
					'cam_id'=>$cam_id[$q],
					'cam_label'=>$cam_label[$q],
				);
				$return_cams[]=$array;
			}
	}
//echo json_encode($return);
if($stmt = $conn->prepare("SELECT type,id,ip,label,id_second,label1 FROM devices")){//izprashtane na zaqvka
	$stmt->execute();
	$stmt->bind_result($col1,$col2,$col3,$col4,$col5,$col6);
	$n=0;
	$r=0;
	while ($stmt->fetch()) {//poluchavane na otgovor
		$tp_dvs[$n]=$col1;
		$id_dvs[$n]=$col2;
		$ip_dvs[$n]=$col3;
		$label[$n]=$col4;
		$id_dvs_second[$n]=$col5;
		$label_id_sct_second[$n]=$col6;
		if($id_dvs_second[$n]>0){
			$id_dvs_second_sub[$r]=$id_dvs_second[$n];
			$r++;
		}
		$label_id[$n]=0;
		$n++;
	}
	$n=$n-1;
	$stmt->close();
	$conn->close();
	if(count($ip_dvs)>0){ //proverqvam kolko sa ustroistvata
		for($j=0;$j<=$n;$j++){		//slagam gi v masivi
				$array=array(
					'ip'=> $ip_dvs[$j],
					'id' => $id_dvs[$j],
					'second_id'=>$id_dvs_second[$j],
					'type'=>$tp_dvs[$j],
					'label' => $label[$j],
					'second_label' => $label_id_sct_second[$j],
				);
				$return[]=$array;
			}
		//}
		for($k=0;$k<=$n;$k++){
			if($tp_dvs[$k]==$dv_type0){
				$id_dvs_lc[$lc_cnt]=$id_dvs[$k]; //pravq malko magii za vseki ot elementite
				$lc_cnt++;
			}
			if($tp_dvs[$k]==$dv_type1){
					$id_dvs_sct[$sct_cnt]=$id_dvs[$k]; //sushtoto
					$sct_cnt++;
			}
		}
	}
}
if(!empty($return)){
		echo "<div id='id-info' style='display:none;'>";
		echo json_encode($return); //encodvam gi v json
		echo "</div>";
	}
switch ($load_n) { //proverka na zaqvenite elementi
    case 1: //lampi i razklonitel
        $fn_type1($id_dvs_sct,$id_dvs_second_sub,$sct_cnt);
		$fn_type0($id_dvs_lc,$lc_cnt);
		
        break;
    case 2:
        $fn_type2($cam_ip,$cam_label,$cam_port,$cam_id,$z+1); //kameri
		if(!empty($return)){
			echo "<div id='id-info-cams' style='display:none;'>";
			echo json_encode($return_cams); //encodvam gi v json i gi izkarvam
			echo "</div>";
		}
        break;
    case 3:
        $fn_type0($id_dvs_lc,$lc_cnt); //samo lampi
        break;
	case 4:
        //echo "REMOTES"; //budeshta funkciq
		$fn_type4();
        break;
	default:
		$fn_type1($id_dvs_sct,$id_dvs_second_sub,$sct_cnt); //defaultnite kakto pri 1
		$fn_type0($id_dvs_lc,$lc_cnt);
		//$fn_type2($cam_ip,$cam_label,$z);
}
}
?>