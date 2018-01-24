<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION["logged"])){ //proverqvam dali sum lognat
function touch_pad(){ //touch pad za upravlenie na pc
	echo <<<EOT
	<div class="row">
	
		<div class="small-12 columns">
			<div class="panel">
				<div id="kb-buttons" class="pc-buttons buttons-container">
					<div id="esc">ESC</div>
					<div id="left" class="fi-arrow-left"></div>
					<div id="up" class="fi-arrow-up"></div>
					<div id="down" class="fi-arrow-down"></div>
					<div id="right" class="fi-arrow-right"></div>
					<div id="enter" >ENTER</div>
					<div id="vdown" class="fi-volume-none"></div>
					<div id="mute" class="fi-volume-strike"></div>
					<div id="vup" class="fi-volume"></div>
					<div id="space" >SPACE</div>
					<div id="f" class="fi-arrows-out"></div>
					<div id="exit" class="fi-x"></div>
				</div>
				<div class="row">
					<div class="small-12 columns">
						<div id="panel" class="touch_area"></div>
					</div>
				</div>
				<div  class="row">
					<div id="mouse_buttons">
						<div class="small-6 columns" style="padding-right: 0px;">
							<div id="left_mouse" class="left_button" ></div>
						</div>
						<div class="small-6 columns" style="padding-left: 0px;">
							<div id="right_mouse" class="right_button"></div> 
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id=testing></div>
	</div>
EOT;
}
function sct_btn(&$sct_id ,&$sct_id_second,$br_sct){ //zarejdane na kontaktite s id-ta i imena
	for($q=0;$q<=$br_sct-1;$q++){
		echo <<<EOT
		<div class="row">
			<div class="small-6 columns">
				<div class="panel">
					<input type="text" class="wet_asphalt btn_stl" id="lb-$sct_id[$q]"  maxlength="10"  placeholder="Бутон" onfocusout="label_save(this)" />
					<div class="switch round large" >
						<input id="$sct_id[$q]" type='checkbox' onchange='UDP_send(this)'>
						<label for="$sct_id[$q]" style='margin: 0 auto; display: block;'></label>
					</div>
					<hr style="padding:0 0 0.5rem; margin:0;">
					<div class="row">
						<div class="small-6 columns">
							<div class="inf-w" id="info-$sct_id[$q]"></div>
						</div>
						<div class="small-6 columns">
							<i data-reveal-id="tbModal"  class="fi-info info-tb" id="info-tb-$sct_id[$q]" onclick="resp_hand(1,this)"></i>
						</div>
					</div>
				</div>
			</div>
			<div class="small-6 columns">
				<div class="panel">
					<input type="text" class="wet_asphalt btn_stl" id="sub-lb-$sct_id[$q]" maxlength="10" placeholder="Бутон" onfocusout="label_save(this)"/>
					<div class="switch round large">
						<input id="$sct_id_second[$q]" type="checkbox" onchange="UDP_send(this)">
						<label for="$sct_id_second[$q]" style="margin: 0 auto; display: block;" ></label>
					</div>
					<hr style="padding:0 0 0.5rem; margin:0;">
					<div class="row">
						<div class="small-6 columns">
							<div class="inf-w" id="info-$sct_id_second[$q]" ></div>
						</div>
						<div class="small-6 columns">
							<i data-reveal-id="tbModal" class="fi-info info-tb" id="info-tb-$sct_id_second[$q]" onclick="resp_hand(1,this)"></i>
						</div>
					</div>
				</div>
			</div>
		</div>
EOT;
	}
}
function lc_btn(&$lc_id,$br){ //zarejdane na kliuchovete za lampi s id-ta i nomera
	$tr=0;
	$br_lc=intval($br/2); 
	$br_second_lc=$br%2;
	if($br_lc>0){
		$br_lo=$br_lc-1;
		$br_li=1;
		$columns=6;
	}
	else if($br_second_lc==1){
		$br_lo=0;
		$br_li=0;
		$columns=12;
	}
	else{
		$br_lo=-2;
		$br_li=-2;
	}
	for($q=0;$q<=$br_lo;$q++){
		echo "<div class='row'>";
		for($i=0;$i<=$br_li;$i++){
			if($q>0)
			$v=$i+2*$q;
		else
			if($tr==1)
				$v=$br-1;
			else
				$v=$i;
		echo "<div class='small-$columns columns'>";
			echo <<<EOT
					<div class="panel">
						<input type='text' class="midnight_blue btn_stl" id="lb-$lc_id[$v]" maxlength='10' placeholder='Бутон' onfocusout="label_save(this)"/>
						<div class="switch round large" >
						<input id=$lc_id[$v] type = 'checkbox' onchange='UDP_send(this)'>
						<label for=$lc_id[$v] style='margin: 0 auto; display: block;'></label>
						</div>
					</div>
				</div>
EOT;
		}
		echo "</div>";
		if(($br_second_lc==1)&&($q==$br_lo)){
			if($br_lc==0)
			$br_lo=-1;
		else
			$br_lo=0;
			$br_li=0;
			$columns=12;
			$br_second_lc=0;
			$tr=1;
			$v=$v+1;
			$q=-1;
		}
	}
}
function video_panel(&$cam_ip,&$cam_label,&$cam_port,&$cam_id,$cam_br){ //video panel za kamerite
// echo <<<EOT
// <div class="row">
		 // <div class="small-12 columns">
// <ul class="example-orbit" data-orbit>
  // <li>
    // <img src="../assets/img/examples/satelite-orbit.jpg" alt="slide 1" />
    // <div class="orbit-caption">
      // Caption One.
    // </div>
  // </li>
  // <li class="active">
    // <img src="../assets/img/examples/andromeda-orbit.jpg" alt="slide 2" />
    // <div class="orbit-caption">
      // Caption Two.
    // </div>
  // </li>
  // <li>
    // <img src="../assets/img/examples/launch-orbit.jpg" alt="slide 3" />
    // <div class="orbit-caption">
      // Caption Three.
    // </div>
  // </li>
// </ul>
	// </div>
	// </div>
// EOT;
// <div class="video-panel">
				// <ul class="example-orbit" data-orbit>
				// <li>
					// <div class="orbit-caption">дз</div>
// </li>
echo <<<EOT
	
			
EOT;
for($t=0;$t<=$cam_br-1;$t++){
	$slide=$t+1;
	$stream="http://".$cam_ip[$t].":".$cam_port[$t]."/video";
echo <<<EOT
<div class="medium-12 columns">
	<div class="row">
		<div class="panel">	
			<img id="cam_$cam_id[$t]" src="$stream"/>
			<div id="cam_title">$cam_label[$t]</div>
		</div>
	</div>
</div>
EOT;
}
if($cam_br==0)
	echo "<h3 class='midnight_blue'>Няма охранителни камери</h3>";
}
function led_panel(){ //budeshta funkciq
	echo <<<EOT
	<div class="row">
		<div class="small-12  columns">
			<div class="led-panel"> 
				<div class="row">
					<div class="small-8 columns">
						<label class="midnight_blue">ЛЕД осветление</label>
					</div>
					<div class="small-2 small-offset-2 columns">
						<span id="sliderOutput3" ></span><span class="midnight_blue">%</span>
					</div>
				</div>
				<div class="row">
					<div class="small-12 medium-12 columns">
					  <div id="slider" class="range-slider round" data-slider data-options="display_selector: #sliderOutput3; step:2;">
						  <span id="switch" class="range-slider-handle" ontouchstart="doTimer()"></span>
						  <span class="range-slider-active-segment"></span>
						  <input type="hidden">
						</div>
					</div>
				</div>
				<div class="row">
					  <div class="silver medium">
						   <div class="small-2 medium-2 columns " ontouchend="iconLock()">
								<div class="right">
									<i id="icon" class="fi-unlock"></i>
								</div>
						   </div>
					  </div>
				</div>
			</div>
		</div>
	</div>
EOT;
}
}
?>