<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(0);
if(!isset($_SESSION["logged"]))
	header('Location: index.php');
//include_once "items_boot.php";
	echo <<<EOT
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<link rel="stylesheet" href="css/foundation.css" />
	<link rel="stylesheet" href="css/ui_colors.css" />
	<link rel="stylesheet" href="icon_pack/foundation-icons.css" />
</head>
<body>
<div class="off-canvas-wrap" data-offcanvas>
	<div class="inner-wrap">
	<div id="navigation" class="icon-bar five-up fixed" role="navigation"> 
		<a class="item left-off-canvas-toggle" id="settings">
			<i id="settings" class="fi-list"></i>
		</a>
		<a class="item" id="cams">
			<i id="cams" class="fi-video"></i>
		</a>
		<a class="item" id="home">
			<i  id="home"class="fi-home" ></i>
		</a>
		<a class="item" id="lights">
			<i  id="lights"class="fi-lightbulb"></i> 
		</a>
		<a class="item" id="remotes">
			<i id="remotes"class="fi-mobile-signal"></i>
		</a>
	</div>
	<aside class="left-off-canvas-menu" id="off-canvas-prevent">
		<ul class="off-canvas-list">
			<li><label>Настройки</label></li>
			<li><a href="#" data-reveal-id="dvModal"  onclick="Defaults()">Смарт устройства</a></li>
			<li><a href="#" data-reveal-id="camModal"  id="cam_info">Охранителни камери</a></li>
			<li><a href="#" data-reveal-id="ifModal" disabled="true" >IFTTT</a></li>
			<li><a href="#" data-reveal-id="stModal" id="settings_info">Настройки</a></li>
			<li><a href="logout.php">Излез</a></li>
		</ul>
	</aside>
<div id="main-container">	
</div>
	<a class="exit-off-canvas"></a>
	<div id="lx"></div>
	<div id="gyro"></div>
	<button onclick="geoFindMe()">Show my location</button>
<div id="out"></div>
	</div>		  
</div>
<div id="ifModal" class="reveal-modal full" data-reveal aria-labelledby="ifModal" aria-hidden="true" role="dialog">
  <h3 id="ifModal">IFTTT</h3>
    <div class="panel">
  <div class="row">
  <div class="small-12 collumns">
  <table class="ifttt_tb">
  <tr>
    <th>Ако час</th>
    <th>
	<select>
  <option value=">">></option>
  <option value="<"><</option>
  <option value="==">==</option>
  <option value=">=">>=</option>
  <option value="<="><=</option>
</select>
	</th>
    <th ><input type="text" class="input_ifttt" placeholder="час:минути"></th>
	</tr>
	<tr>
	<th >тогава</th>
	<th>
	<select>
  <option value="volvo">Лампа1</option>
  <option value="saab">Телевизор</option>
</select>
	</th>
	<th >
	<input type="radio" >ON
  <input type="radio" >OFF
	</th>
  </tr>
  </table>
  </div>
  </div>
  </div>
  <button class="radius tiny button" onclick="ClearEl_Pr()" style="margin-left:1.875rem;">Задай</button>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
  </div>
<div id="tbModal" class="reveal-modal full"  style="padding: 2rem 0rem;"data-reveal aria-hidden="true" role="dialog">
<table style="padding: 0.5625rem 0.625rem;" id="statistic">
  <caption>Статистика за:<span id="title-tb"></span></caption>
  <thead>
	  <tr>
	  <th></th>
		<th>Консумация</th>
		<th>Време</th>
		<th >Цена-кВ/ч</th>
	  </tr>
  </thead>
  <tbody>
	  <tr>
		<td>Текущо</td>
		<td>-</td>
		<td>-</td>
		<td ><input type="text" id="price_field" class="wet_asphalt btn_stl"  maxlength="10" placeholder="-"/></td>
	  </tr>
	  <tr>
		<td>Общо</td>
		<td>-</td>
		<td>-</td>
		<td >-</td>
	  </tr>
	   <tr class="tb_footer">
		<td>Общо</td>
		<td>-</td>
		<td>-</td>
		<td >-</td>
	  </tr>
  <tbody>
</table>
<button class="radius tiny button " id="clear_el_pr" style="margin-left:1.875rem;" >Изчисти</button>
  <a class="close-reveal-modal" aria-label="Close" onclick="resp_hand(0)">&#215;</a>
</div>
<div id="camModal" class="reveal-modal full" data-reveal aria-labelledby="modalTitlecm" aria-hidden="true" role="dialog">
	<h4 id="modalTitlecm">Добавяне на охранителна камера</h4>
	<input id="ip-cam-name" type="text" maxlength='50' placeholder="Име на камерата" />
	<input id="ip-cam" type="text" maxlength='20' placeholder="Адрес на камерата" />
	<input id="ip-cam-port" type="text" maxlength='5' placeholder="Порт на камерата" />
	<button class="radius tiny button" id="cam_add" >Добави</button>
	<button class="radius tiny button"  id="cam_remove">Премахни</button>
	<div id="table_cams"></div>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="stModal" class="reveal-modal full" data-reveal aria-labelledby="modalTitlest" aria-hidden="true" role="dialog">
	<h4 id="modalTitlest">Настройки</h4>
	<div>Управление на компютър</div>
	<input id="pc_ip" type="text" maxlength='20' placeholder="Адрес " />
	<input id="pc_port" type="text" maxlength='5' placeholder="Порт" />
	<h5>Достъп<h5>
	<div class="row">
		<div class="small-8 columns" style="padding-left:0;">
			<div style=" text-align: left;">Местоположение</div>
		</div>
		<div class="small-4 columns">
			<div class="switch round medium" >
				<input id="gps_st" type='checkbox' onchange='gps_send(this)'>
				<label style='padding: auto; display: block;' for="gps_st"></label>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="small-8 columns" style="padding-left:0;">
			<div style=" text-align: left;">Акселерометър</div>
		</div>
		<div class="small-4 columns">
			<div class="switch round medium" >
				<input id="gps_st" type='checkbox' onchange='gps_send(this)'>
				<label style='margin: auto; display: block;' for="gps_st"></label>
			</div>
		</div>
	</div>
	<button class="radius tiny button" id="add_pc_data">Запиши</button>
  <a class="close-reveal-modal" aria-label="Close">&#215;</a>
</div>
<div id="dvModal" class="reveal-modal full" data-reveal aria-labelledby="dvModalTitle" aria-hidden="true" role="dialog" >
	<h4 id="dvModalTitle">Добавяне на смарт устройство</h4>
	<input id="ssid-holder" type="text" placeholder="Име на мрежата" />
	<input id="pass-holder" type="password" placeholder="Парола" />
	<button class="radius tiny button " id="add_new_dv">Добави</button>
	<div id="close" >
		<a class="close-reveal-modal" aria-label="Close"  resp_hand(0,0) >&#215;</a>
	</div>
</div>
</div>
  <script src="js/vendor/jquery.js"></script>
  <script src="js/foundation.min.js"></script>
  <script src="js/functionality.js"></script>
  <script>
    $(document).foundation();
  </script>
</body>
</html>
EOT;
?>