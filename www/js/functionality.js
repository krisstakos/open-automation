//==> ALL variables
//var c = 0;
//var t;
//var tr = 0;
//var drop_stat = 1;
//var timer_is_on = 0;
//var lock_stat = 1;
//var i = 0; //loops
//var buffer = 0;
//var ip = 0;
var ssid = 0;
var pass = 0;
var json_data = [];
var json_static = [];
var json_time = [];
var json_real_time = [];
var json_pc_data = [];
var ids = [];
var labels = [];
var labels_second = [];
var id_sd = 0;
var second_label;
var raw;
var stats;
var stats_n;
var parsed_data;
var frst;
var scnd;
var dt_scnd;
var dt_frst;
var wts_1;
var wts_2;
var obj = 0;
var command;
var name;
var ip_ch = new RegExp(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/); //proverka na ip-ta
var table = document.getElementById("statistic"); //vzimam vsichko ot tablicata s id statistic
var rows = table.getElementsByTagName("TR");
var cell = table.getElementsByTagName("TD");
//===>Intervals
setInterval(outReq, 1000); //interval za opresnqvane
//===>Functions
function ClearEl_Pr() { //nulirane
	for(var y=2;y<=3;y++){
		rows[y].getElementsByTagName("TD")[1].innerHTML = "0.00 КвЧ"; 
		rows[y].getElementsByTagName("TD")[2].innerHTML = "0.00 ч";
		rows[y].getElementsByTagName("TD")[3].innerHTML = "0.0000 Лв"; 		
	}
	var id_dv = "";
	var pos;
	var st_label = document.getElementById("title-tb").innerHTML;
	for (var t = 0; t <= json_data.length - 1; t++) {
		if (json_data[t].label == st_label) {
			id_dv = json_data[t].id;
			pos = 1;
		} else if (json_data[t].second_label == st_label) {
			id_dv = json_data[t].second_id;
			pos = 2;
		}
	}
	var price_request = new XMLHttpRequest();
	price_request.open("GET", "wts_clear.php?id=" + id_dv + "&pos=" + pos, true);
	price_request.send();
}

function price_save() { //zapisvane cenata na toka v db
	var val_pr = document.getElementById("price_field").value;
	var price_request = new XMLHttpRequest();
	price_request.open("GET", "price_save.php?day_price=" + val_pr, true);
	price_request.send();
}

function resp_hand(a, inf_id) { //funkciq za obrabotka na danni
	if (a != undefined && inf_id != undefined) {
		var date_obj = new Date();
		var last_day = new Date(date_obj.getFullYear(), date_obj.getMonth() + 1, 0);
		last_day = last_day.getDate();
		var month = date_obj.getMonth() + 1; //months from 1-12
		if (month == 1)
			prev_month = 12
		else
			prev_month = month - 1;
		if (prev_month <= 9)
			prev_month = "0" + prev_month;
		var day = date_obj.getDate();
		if (day <= 9)
			day = "0" + day;
		if (month <= 9)
			month = "0" + month;
		rows[2].getElementsByTagName("TD")[0].innerHTML = "Общо\n <font size=1>(01." + month + "-" + day + "." + month + ")</font>";
		rows[3].getElementsByTagName("TD")[0].innerHTML = "Общо\n <font size=1>(01." + prev_month + "-" + last_day + "." + prev_month + ")</font>";
		var ex = 0;
		var numb2 = inf_id.id.match(/\d/g);
		numb2 = numb2.join("");
		if (a == 1) {
			var time_request = new XMLHttpRequest();
			time_request.onreadystatechange = function () {
				if (time_request.readyState == 4 && time_request.status == 200) {
					json_time = JSON.parse(time_request.responseText); //poluchavan json otgovor za vremeto na puskane i akumulirano vreme v minuti
					var el_pr = document.getElementById("price_field").value; //vzimam cenata
					var cps = el_pr / 60;
					var today = new Date();
					var d =today.getDate();//segashno vreme
					var h = today.getHours(); 
					var m = today.getMinutes();
					var s = today.getSeconds();
					//+++++++++++++++++
					var hs_minutes = json_time[0].history;
					var minutes = json_time[0].time_on; //akumulirano vreme
					
					var hours = Math.floor(minutes / 60);//chasove ot json
					var minutes = Math.floor(minutes % 60);//minuti
					
					var parts = json_time[0].time_start.split(":"); //splitvat chasove i minute za po-lesni smetki
					var past_h = parseInt(parts[0]);
					var past_m = parseInt(parts[1]);
					
					var hs_hours = Math.floor(hs_minutes / 60);//minal mesec
					var hs_minutes = Math.floor(hs_minutes % 60);
					
					if (hs_minutes <= 9)
						hs_minutes = "0" + hs_minutes;
					
					if (m - past_m < 0) {
						var exp_m = 60 - past_m + m;
						var exp_h = h - past_h - 1
					} else {
						var exp_m = m - past_m;
						var exp_h = h - past_h;
					}
					if (exp_m <= 9)
						exp_m_rl = "0" + exp_m;
					else {
						exp_m_rl = exp_m; //minali minuti za sesiq
					}
					
					exp_m = exp_m + minutes; //minali minuti obshto
					//if(exp_m=>60){
						exp_m=exp_m%60;
						var testh=Math.floor(exp_m/60);
						console.log(exp_m,exp_h,testh);
						exp_h=exp_h+Math.floor(exp_m/60);
					//}
					if (exp_m <= 9)
						exp_m = "0" + exp_m;
					var exp_h_rl = exp_h;
					exp_h = exp_h + hours;
					var vl = document.getElementById("info-" + numb2); //vzimam vatove 
					if (vl.innerHTML == "0.00W") {
						var numb3 = json_time[0].last_wats.match(/\d/g); //posledno zapisani vatove sled izkliuchvane
						vl = json_time[0].last_wats;
						var exp_h_rl = 0;
						var exp_m_rl = "00";
						var exp_m = minutes;
						var exp_h = hours;
					} else {
						var numb3 = vl.innerHTML.match(/\d/g);
						vl = vl.innerHTML;
					}
					numb3 = numb3.join("");
					var wtst = ~~(numb3 / 100); //presmqtane na cenata za akumulirano vreme
					wtst = wtst / 1000;
					var time_on = exp_h + exp_m / 60;
					var total = wtst * el_pr * time_on;
					var exp_time_hs = hs_hours + hs_minutes / 60;
					var total_hs = wtst * exp_time_hs * el_pr;
					total = total.toFixed(4);
					total_hs = total_hs.toFixed(4);
					var avg_kwh = total / el_pr;
					var avg_kwh_hs = total_hs / el_pr;
					avg_kwh_hs = avg_kwh_hs.toFixed(2);
					avg_kwh = avg_kwh.toFixed(2);
					for (var p = 0; p <= json_data.length - 1; p++) {
						if (numb2 == json_data[p].second_id) {
							numb2 = json_data[p].id
							ex = 1;
						}
					}
					if (ex == 1)
						name = document.getElementById("sub-lb-" + numb2).value;
					else
						name = document.getElementById("lb-" + numb2).value;
					document.getElementById("title-tb").innerHTML = name;
					if (vl !== null) { //<==izobrazqvane v tablica veche presmetnatite stoinosti
						rows[1].getElementsByTagName("TD")[1].innerHTML = vl;
						rows[1].getElementsByTagName("TD")[2].innerHTML = exp_h_rl + "." + exp_m_rl + " ч"; //izminalo vreme za sesiq
						rows[2].getElementsByTagName("TD")[2].innerHTML = exp_h + "." + exp_m + " ч"; //izminalo vreme obshto
						rows[2].getElementsByTagName("TD")[3].innerHTML = total + " Лв"; //cena za izrazshoden tok
						rows[2].getElementsByTagName("TD")[1].innerHTML = avg_kwh + " КвЧ"; //kwatove za segashniq mesec
						rows[3].getElementsByTagName("TD")[1].innerHTML = avg_kwh_hs + " КвЧ"; //Kwatove za minal mesec
						rows[3].getElementsByTagName("TD")[2].innerHTML = hs_hours + "." + hs_minutes+" ч"; //vreme za minal mesec
						rows[3].getElementsByTagName("TD")[3].innerHTML = total_hs + " Лв"; //cena za izrazshoden tok za minal mesec
					}
				}
			};
			time_request.open("GET", "get-time.php?id=" + numb2, true);
			time_request.send();
		}
	}


	for (var j = 0; j <= json_real_time.length - 1; j++) { //updeitvane na statuse na butonite sprqmo ustroistvata
		for (var q = 0; q <= json_data.length - 1; q++) {
			if (json_data[q].ip == json_real_time[j].ip) {
				if (json_data[q].type == "LC") { //tursq ustroistva s LC ili LampContact
					stats_n = json_real_time[j].k1; //realen status na ustroistvoto
					if (stats_n == 0)
						stats = false;
					else
						stats = true;
					var ts4 = document.getElementById(json_data[q].id);
					if (ts4 !== null)
						document.getElementById(json_data[q].id).checked = stats;
				} else {
					parsed_data = json_real_time[j].data;
					frst = parsed_data.indexOf("N"); //tursq N za indexirane
					dt_scnd = parsed_data.substring(0, frst); //status na purvi kontakt
					dt_frst = parsed_data.substring(frst + 1, scnd); // i na vtori
					wts_1 = dt_frst * 220; //presmqtane na toka v realno vreme
					wts_2 = dt_scnd * 220;
					for (var l = 0; l <= 1; l++) {
						if (l == 0) {
							stats_n = json_real_time[j].k2;
							var id_inf = "info-" + json_data[q].id;
							if (stats_n == 0) {
								stats = false;
								wts_1 = 0;
							} else
								stats = true;
							var ts2 = document.getElementById(id_inf)
							if (ts2 !== null) {
								document.getElementById(id_inf).innerHTML = wts_1.toFixed(2) + "W"; //izkarvam veche presmetnatite vatove
								document.getElementById(json_data[q].id).checked = stats;
							}
						} else {
							stats_n = json_real_time[j].k1;
							id_inf = "info-" + json_data[q].second_id;
							if (stats_n == 0) {
								stats = false;
								wts_2 = 0;
							} else
								stats = true;
							if (ts2 !== null) {
								document.getElementById(id_inf).innerHTML = wts_2.toFixed(2) + "W"; //izkarvam veche presmetnatite vatove
								document.getElementById(json_data[q].second_id).checked = stats;
							}
						}
					}
				}
			}
		}
	}
}

function outReq() { //funkciq zadeistavana ot intervala za poluchavane na real-time data
	var data_request = new XMLHttpRequest();
	data_request.onreadystatechange = function () {
		if (data_request.readyState == 4 && data_request.status == 200) { //validen response
			if (data_request.responseText.length == 0) {
				document.getElementById('main-container').innerHTML="<img src='msg.png' />";;
				// for (var t = 0; t <= json_data.length - 1; t++) {
				// document.getElementById(json_data[t].id).disabled = true;
				// if (json_data[t].second_id != 0)
				// document.getElementById(json_data[t].second_id).disabled = true;
				// }
			} else {
				// for (var t = 0; t <= json_data.length - 1; t++) {
				// var ts = document.getElementById(json_data[t].id);
				// if (ts !== null) {
				// document.getElementById(json_data[t].id).disabled = false;
				// if (json_data[t].second_id != 0)
				// document.getElementById(json_data[t].second_id).disabled = false;
				// }
				// }
				json_real_time = JSON.parse(data_request.responseText);
				if (json_real_time.length != obj) {
					obj = json_real_time.length;
					boot();
				}
				resp_hand();
			}
		}
	};
	data_request.open("get", "get-data.php", true); //izprashtam request
	data_request.send();
}
var time_request = new XMLHttpRequest(); //izprashtam request za sa polucha ip port i druga statichna informaciq
time_request.onreadystatechange = function () {
	if (time_request.readyState == 4 && time_request.status == 200) {
		json_static = JSON.parse(time_request.responseText);
		document.getElementById("price_field").value = json_static[0].el_price;
	}
};
time_request.open("GET", "get-ip-port.php", true);
time_request.send();
function label_save(val) { //funkciq za zapisvane vuvedenite imena na ustroistva
	var label = document.getElementById(val.id).value;
	var p = val.id.search("sub-lb-");
	var numb = val.id.match(/\d/g); //vzimam samo cifrite
	numb = numb.join("");
	if (p >= 0) {
		for (var n = 0; n <= json_data.length - 1; n++) {
			if (numb == ids[n]) {
				id_sd = ids[n];
				second_label = label;
				label = "";
			}
		}
	} else {
		for (var n = 0; n <= json_data.length - 1; n++) {
			if (numb == ids[n])
				id_sd = ids[n];
			second_label = "";
		}
	}
	var lblReq = new XMLHttpRequest();
	lblReq.open("GET", "label_save.php?label=" + label + "&label1=" + second_label + "&dvs_id=" + id_sd, true); //izprashtam request za da se zapishat v db
	lblReq.send();
}

function boot(a) { //funkciq za bootvane na interfeisa za ustroistvata
	var coReq = new XMLHttpRequest();
	coReq.onreadystatechange = function () {
		if (coReq.readyState == 4 && coReq.status == 200) {
			document.getElementById("main-container").innerHTML = coReq.responseText;
			if (a == 4)
				set_pad_listener();
			if (coReq.responseText != "")
				get_el();
		}
	};
	coReq.open("GET", "items_boot.php?load=" + a, true); //izprashtam request kum servera
	coReq.send();
}
boot();
function get_el() { //funkciq za chetene na zadadenite imena/labeli na ustroistvata
	raw = document.getElementById("id-info").innerHTML;
	json_data = JSON.parse(raw);
	for (var k = 0; k <= json_data.length - 1; k++) {
		ids[k] = json_data[k].id;
		labels[k] = json_data[k].label;
		labels_second[k] = json_data[k].second_label;
		if (labels_second[k] != "") {
			var id_mrg = "sub-lb-" + ids[k];
			var ts1 = document.getElementById(id_mrg);
			if (ts1 !== null)
				document.getElementById(id_mrg).value = labels_second[k];
		}
		if (labels[k] != "") {
			var id2_mrg = "lb-" + ids[k];
			var ts3 = document.getElementById(id2_mrg);
			if (ts3 !== null)
				document.getElementById(id2_mrg).value = labels[k];
		}
	}
}
var cam_ip_st=false;
var cam_port_st=false;
function check_cam_ip(){
	var ip = document.getElementById("ip-cam");
	if (!ip_ch.test(ip.value)) {
			ip.style.border = "solid #ff4d4d"; //ako ima greshka v ip
			cam_ip_st=false;
		} else{
			ip.style.border="solid #4dff4d";
			cam_ip_st=true;
		}
}
function check_cam_port(){
	var port = document.getElementById("ip-cam-port");
	if (!/^[0-9]+$/.test(port.value)) {
			port.style.border = "solid #ff4d4d"; //ako ima greshka v port
			cam_port_st=false;
		} 
		else{
			 var port_int=parseInt(port.value);
			 if(port_int>0 && port_int<=65535){
				 port.style.border="solid #4dff4d";
				 cam_port_st=true;
			 }
			 else{
				 cam_port_st=false;
				 port.style.border = "solid #ff4d4d";
			 }
		}
}
function Add_cam(t){
	var ip=document.getElementById("ip-cam");
	var port=document.getElementById("ip-cam-port");
	var name=document.getElementById("ip-cam-name").value;
	if(t!=2){
		if(cam_ip_st==false)          //cam_ip_st && cam_port_st && 
			ip.style.border="solid #ff4d4d";
		if(cam_port_st==false) 
			port.style.border="solid #ff4d4d";
		else if(cam_ip_st && cam_port_st)
			send_cams();
	}
	else if(t==2)
		send_cams();
	function send_cams(){
		xmlhttp = new XMLHttpRequest();
		xmlhttp.onreadystatechange = function () {
		if (xmlhttp.readyState == 4 && xmlhttp.status == 200) //poluchavam otgovor za tova kakvo deistvie e izvursheno
			document.getElementById("table_cams").innerHTML = xmlhttp.responseText;
			};
		xmlhttp.open("GET", "cams.php?ip=" + ip.value + "&name=" + name + "&port=" + port.value + "&stat=" + t, true); //izprashtam request s danni za kamera i deistviq triene ili dobavqne
		xmlhttp.send();
	}
}

function dvSave() { //izprashtane na request kum ustroistvoto
	var ssid_stat=false;
	var pass_stat=false;
	ssid = document.getElementById("ssid-holder");
	pass = document.getElementById("pass-holder");
	if(ssid.value.length==0){
		ssid.style.border="solid #ff4d4d";
		ssid_stat=false;
	}
	else{
		ssid.style.border="";
		ssid_stat=true;
	}
	if(pass.value.length<8){
		pass.style.border="solid #ff4d4d";
		pass_stat=false;
	}
	else
	{
		pass.style.border="";
		pass_stat=true;
	}
	if(pass_stat && ssid_stat){
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "http://192.168.4.1", true);
	xmlhttp.send("ssid=" + ssid.value + "&" + "pass=" + pass.value + "&target=" + json_static[0].host_ip + "&port=" + json_static[0].host_port + "end");
	$('#dvModal').foundation('reveal', 'close');
	}
}

function Defaults() { //izchistvane stoinostite za dvSave
	document.getElementById("pass-holder").value = "";
	document.getElementById("pass-holder").style.border = "";
}
function add_pc_data(y){
	console.log("testing");
	var pc_ip=document.getElementById('pc_ip');
	var pc_port=document.getElementById('pc_port');
	if(y==0){
		if(!ip_ch.test(pc_ip.value)){
			pc_ip.style.border="solid #ff0000";
		}
		else{
			pc_ip.style.border="solid #00ff00";
			var pc_port_num= pc_port.value.match(/\d/g);
			if(pc_port_num==null){
				pc_port.style.border="solid #ff0000";
			}
			else{
				pc_port_num= pc_port_num.join("");
				if(pc_port_num>0 && pc_port_num <=65535){
					pc_port.style.border="solid #00ff00";
					pc_ip=pc_ip.value;
					send_pc_data();
				}
			}
				
		}
	}
	else if(y==1)
		send_pc_data();
	function send_pc_data(){
		pcReq = new XMLHttpRequest();
		pcReq.onreadystatechange = function () {
		if (pcReq.readyState == 4 && pcReq.status == 200) { //validen response
				json_pc_data = JSON.parse(pcReq.responseText);
				pc_ip.value=json_pc_data[0].ip;
				pc_port.value=json_pc_data[0].port;
			}
		};
		pcReq.open("post", "pc_data.php", true);
		pcReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		pcReq.send("pc_ip=" + pc_ip + "&pc_port=" + pc_port_num+"&update="+y);
	}
}
//<<===UDP senders
var send_request;
function UDP_header() {
	send_request = new XMLHttpRequest();
	send_request.open("post", "UDP_send.php", true);
	send_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
}

function UDP_send(elt) { //funkciq za izprashtane na komandi kum ustroistvata
	var cr_stat = document.getElementById(elt.id).checked;
	var numb2 = elt.id.match(/\d/g);
	numb2 = numb2.join("");
	var vl = document.getElementById("info-" + numb2);
	if (vl != null)
		vl = vl.innerHTML;
	UDP_header();
	send_request.send("q=" + elt.id + "&status=" + cr_stat + "&last_wats=" + vl);
}

function UDP_pc_pad(distX, distY) {
	UDP_header();
	send_request.send("in_type=mouse&datax=" + distX + "&datay=" + distY);
}

function UDP_pc_click(command) {
	UDP_header();
	send_request.send("in_type=mouse_click&command=" + command);
}

function UDP_pc_keyboard(command) {
	UDP_header();
	send_request.send("in_type=key&command=" + command);
}
//END
//<<============listeners

var off_canvas=document.getElementById("off-canvas-prevent");
off_canvas.addEventListener('touchend', function (e) {
	switch(e.target.id) {
		case 'settings_info':
			add_pc_data(1);
			break;
		case 'add_pc_data':
			//add_pc_data(0);
			console.log("are u a live");
			break;
		default:
	} 
}, false)


var add_pc_data_bt = document.getElementById('add_pc_data'); //zapis na cena
add_pc_data_bt.addEventListener('touchend', function () {
	add_pc_data(0);
}, false)




var price_field = document.getElementById('price_field'); //zapis na cena
price_field.addEventListener('focusout', function () {
	price_save();
}, false)

var clear_el_pr = document.getElementById('clear_el_pr'); //chistene na cena
clear_el_pr.addEventListener('touchend', function () {
	ClearEl_Pr();
}, false)
document.getElementById('ip-cam').addEventListener('keyup', check_cam_ip);
document.getElementById('ip-cam-port').addEventListener('keyup', check_cam_port);
var cam_add = document.getElementById('cam_add'); //cameri
var cam_remove = document.getElementById('cam_remove');
var cam_info = document.getElementById('cam_info');
cam_add.addEventListener('touchend', function () {
	Add_cam(0);
}, false)
cam_remove.addEventListener('touchend', function () {
	Add_cam(1);
}, false)
cam_info.addEventListener('touchend', function () {
	Add_cam(2);
}, false)
document.getElementById('add_new_dv').addEventListener('touchend',dvSave);
var navigation =document.getElementById('navigation'); //navigaciq
navigation.addEventListener('touchend', function (t) {
	switch(t.target.id) {
    case 'cams':
        boot(2);
        break;
    case 'home':
        boot(1);
        break;
	case 'lights':
        boot(3);
        break;
	case 'remotes':
        boot(4);
        break;
	case 'settings':
		document.body.scrollTop = 0;
		break;
    default:
	} 
}, false)
function set_pad_listener() { //setvam listener za pad-a
	var dv_type_pc;
	var startx = 0
	var startY = 0
	var distX = 0
	var distY = 0
	var box1 = document.getElementById('panel');
	var statusdiv = document.getElementById('testing');
	var kb_buttons = document.getElementById('kb-buttons');
	kb_buttons.addEventListener('touchstart', function (e) {
		var btn_id = e.target.id;
		window.navigator.vibrate(70);
		UDP_pc_keyboard(e.target.id)
		statusdiv.innerHTML = e.target.id;
	}, false)
	
	var mouse_buttons=document.getElementById('mouse_buttons');
	mouse_buttons.addEventListener('touchstart', function (e) {
		document.getElementById(e.target.id).style.background =  "grey";
		window.navigator.vibrate(100);
	}, false)
	mouse_buttons.addEventListener('touchend', function (e) {
		document.getElementById(e.target.id).style.background = "#DDDDDD";
		switch(e.target.id) {
			case 'left_mouse':
				UDP_pc_click('left')
				break;
			case 'right_mouse':
				UDP_pc_click('right')
				break;
			default:
		} 
		window.navigator.vibrate(100);
	}, false)
	box1.addEventListener('touchstart', function (e) {
		var touchobj = e.changedTouches[0] //purvo dokosvane
		startx = parseInt(touchobj.clientX) 
		starty = parseInt(touchobj.clientY)
		e.preventDefault()
	}, false)
	box1.addEventListener('touchmove', function (z) {
		var touchobj = z.changedTouches[0] // reference first touch point for this event
		var distX = parseInt(touchobj.clientX) - startx
		var distY = parseInt(touchobj.clientY) - starty
		window.navigator.vibrate(50);
		statusdiv.innerHTML = 'X: ' + distX + '<br>Y: ' + distY;
		z.preventDefault()
		UDP_pc_pad(distX, distY)
	}, false)
}
off_canvas.addEventListener("touchmove", function (e) {
	e.preventDefault()
});
//=====phone acelerometer
if (window.DeviceMotionEvent == undefined) {
    }
    else {
        window.addEventListener("devicemotion", accelerometerUpdate, true);
    }
var cnt_g=0;
function accelerometerUpdate(e) {
	var rand=document.getElementById(1);
   var aX = event.accelerationIncludingGravity.x*1;
   var aY = event.accelerationIncludingGravity.y*1;
   var aZ = event.accelerationIncludingGravity.z*1;
   // if(aZ.toFixed(2)<=-8){
	   // if(cnt_g==0){
	   // window.navigator.vibrate(100);
	   // UDP_send(rand);
	   // cnt_g=1;
	   // }
   // }
   // if(aZ.toFixed(2)>=8){
	   //window.navigator.vibrate(100);
	   // if(cnt_g==1){
		   // window.navigator.vibrate(100);
		   // UDP_send(rand);
		   // cnt_g=0;
	   // }
   // }
  // xPosition = Math.atan2(aY, aZ);
   //yPosition = Math.atan2(aX, aZ);
 //  lx.innerHTML="x:"+xPosition.toFixed(2)+"y:"+yPosition.toFixed(2)+"az"+aZ.toFixed(2);
 var lx=document.getElementById("lx");
 lx.innerHTML=aZ.toFixed(2)+"cnt"+cnt_g;
}
//==phone gyroscope
var gyro =document.getElementById("gyro");
window.addEventListener("deviceorientation", handleOrientation, true);
function handleOrientation(event) {
  var absolute = event.absolute;
  var alpha    = event.alpha;
  var beta     = event.beta;
  var gamma    = event.gamma;
gyro.innerHTML="abs: "+absolute+"alpha: "+alpha+"beta: "+beta+"gamma: "+gamma;
  // Do stuff with the new orientation data
}
//==GPS Data
var output = document.getElementById("out");

function geoFindMe() {
  

  if (!navigator.geolocation){
    output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
    return;
  }

  function success(position) {
	var home_lat=43.1343657; //y
	var home_long=24.7017677; //x
    var latitude  = position.coords.latitude;
    var longitude = position.coords.longitude;
    var accu = position.coords.accuracy;
	var side_1=latitude - home_lat;
	var side_2=longitude - home_long;
	var dist=Math.pow(side_1,2)+Math.pow(side_2,2);
	var R = 6371; // Radius of the earth in km
  var dLat = (latitude - home_lat) * Math.PI / 180;  // deg2rad below
  var dLon = (longitude - home_long) * Math.PI / 180;
  var a = 
     0.5 - Math.cos(dLat)/2 + 
     Math.cos(home_lat * Math.PI / 180) * Math.cos(latitude * Math.PI / 180) * 
     (1 - Math.cos(dLon))/2;

  var d=R * 2 * Math.asin(Math.sqrt(a));
	output.innerHTML = '<p>Latitude is ' + latitude + '° <br>Longitude is ' + longitude + '°'+'accu'+accu+'distance '+d+'</p>';
    //var img = new Image();
  //  img.src = "https://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=18&size=300x300\&markers=color:red%7Clabel:S%7C"+latitude+","+longitude;

   // output.appendChild(img);
  };

  function error() {
    output.innerHTML = "Unable to retrieve your location";
  };
  output.innerHTML = "<p>Locating…</p>";

  navigator.geolocation.getCurrentPosition(success, error);
}



//==================================== budeshti funkciii
// function doTimer() { //startirane na taimer
	// if (!timer_is_on) {
		// timer_is_on = 1;
		// timedCount();
	// }
// }

// function stopCount() { //spirane na taimer
	// clearTimeout(t);
	// timer_is_on = 0;
// }

// function timedCount() { //broqch za taimera
	// if (lock_stat == 1) {
		// document.getElementById("slider").onchange = function () {
			// var value = document.getElementById('slider').getAttribute('data-slider');
			// document.getElementById('qj').innerHTML = value;
			// if (value > 80)
				// document.getElementById('qj').style.backgroundColor = 'red';
			// else
				// document.getElementById('qj').style.backgroundColor = 'green';
			// if (c > 0) {
				// stopCount()
				// c = 0;
			// }
		// };
		// c = c + 10;
		// if (c == 500) {
			// if (tr == 0) {
				// vibrate();
				// document.getElementById("switch").style.background = "silver";
				// tr = 1;
			// } else {
				// vibrate();
				// document.getElementById("switch").style.background = "#16A085";
				// tr = 0;
			// }
		// } else {
			// document.addEventListener("touchend", function () {
				// stopCount()
				// c = 0;
			// });
		// }
		// t = setTimeout(function () {
			// timedCount()
		// }, 10);
	// } else
		// document.addEventListener("touchend", function () {
			// stopCount()
			// c = 0;
		// });
// }

// function vibrate() { //vibraciq
	// window.navigator.vibrate(500);
// }

// function iconLock() {
// var check = document.getElementById('icon').className;
// if (check == 'fi-unlock') {
// if (drop_stat == 0)
// menuUp()
// document.getElementById('icon').className = "fi-lock";
// document.getElementById('slider').className += " disabled";
// document.getElementById("switch").style.background = 'red';
// lock_stat = 0;
// } else {
// document.getElementById('icon').className = "fi-unlock";
// document.getElementById("slider").className =
// document.getElementById("slider").className.replace(/(?:^|\s)disabled(?!\S)/g, '')
// document.getElementById("switch").style.background = '#16A085';
// lock_stat = 1;
// }
// }