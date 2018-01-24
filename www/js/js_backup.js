var c = 0;
var t;
var tr = 0;
var drop_stat = 1;
var timer_is_on = 0;
var lock_stat = 1;
var i = 0; //loops
var buffer = 0;
var ip = 0;
var ssid = 0;
var pass = 0;
var jsonData = [];
var jsonIpPort = [];
var jsonModals = [];
var jsonData3 = [];
var ids = [];
var label_ids = [];
var labels = [];
var labels_second = [];
var id_sd = 0;
var second_label;
var raw;
var stats;
var stats_n;
var inx;
var tg=0;
var b;
var data_js=[];
var frst=[];
var scnd=[];
var dt_scnd=[];
var dt_frst=[];
var wts_1=[];
var wts_2=[];
var each_info=[[],[]];
var inf_arrs=0;
var ip_ch = new RegExp(/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/);
var table = document.getElementsByTagName("TABLE")[0];
var rows=table.getElementsByTagName("TR");
var cell=table.getElementsByTagName("TD");
//===>Intervals
setInterval(outReq, 1000);
//===>Functions
function get_crn_d(id_tb){
	tg=1;
	var numb = id_tb.id.match(/\d/g);
	numb = numb.join("");
	for(var t=0;t<=jsonData.length-1;t++){
		if(jsonData[t].id==numb)
			document.getElementById("title-tb").innerHTML =jsonData[t].label;
		else if(jsonData[t].second_id==numb)
			document.getElementById("title-tb").innerHTML =jsonData[t].second_label;
	}
}
function resp_hand(a) {
	if(a!=undefined)
		b=a
	for (var j = 0; j <= jsonData3.length - 1; j++) {
		for (var q = 0; q <= jsonData3.length - 1; q++) {
			if (jsonData3[j].ip == jsonData[q].ip) {
				if (jsonData[q].type == "LC") {
					inx = jsonData3[j].data.indexOf(":TMP:");
					stats_n = jsonData3[j].data.substring(inx - 1, inx)
					if (stats_n == 0)
						stats = false;
					else
						stats = true;
					document.getElementById(jsonData[q].id).checked = stats;
					//alert(stats);
				} else {
					data_js[inf_arrs]=jsonData3[j].data;
					frst[inf_arrs]=data_js[inf_arrs].indexOf("N");
					scnd[inf_arrs]=data_js[inf_arrs].indexOf("k1");
					dt_frst[inf_arrs]=data_js[inf_arrs].substring(frst[inf_arrs]+1, scnd[inf_arrs]);
					dt_scnd[inf_arrs]=data_js[inf_arrs].substring(0, frst[inf_arrs]);
					wts_1[inf_arrs]=dt_frst[inf_arrs]*228;
					wts_2[inf_arrs]=dt_scnd[inf_arrs]*228;
					//for (var l = 0; l <= 1; l++) {		
					//	if (l == 0) {
					for(var t=0;t<=1;t++)
						each_info[inf_arrs][t]=;
					}
							inx = data_js.lastIndexOf(":"); 
							stats_n = data_js.substring(inx + 1, inx + 2)
							var id_inf="info-"+jsonData[q].id;
							if(b==1)
								rows[1].getElementsByTagName("TD")[1].innerHTML = wts_1.toFixed(2)+"W";
							if (stats_n == 0){
								stats = false;
								wts_1=0;
							}
							else
								stats = true;
							document.getElementById(id_inf).innerHTML=wts_1.toFixed(2)+"W";
							document.getElementById(jsonData[q].id).checked = stats;
					//	} else {
							inx = data_js.indexOf(":");
							stats_n = data_js.substring(inx + 1, inx + 2)
							id_inf="info-"+jsonData[q].second_id;
							if(b==2)
								rows[1].getElementsByTagName("TD")[1].innerHTML = wts_2.toFixed(2)+"W";
							if (stats_n == 0){
								stats = false;
								wts_2=0;
							}
							else
								stats = true;
							document.getElementById(id_inf).innerHTML=wts_2.toFixed(2)+"W";
							document.getElementById(jsonData[q].second_id).checked = stats;
					//	}
					//}
					inf_arrs=inf_arrs+1;
				}
			}
		}
	}
	inf_arrs=0;
}

function outReq() {
	var oReq = new XMLHttpRequest();
	oReq.onreadystatechange = function() {
		if (oReq.readyState == 4 && oReq.status == 200) {
			if (oReq.responseText.length == 0) {
				for (var t = 0; t <= jsonData.length - 1; t++) {
					document.getElementById(jsonData[t].id).disabled = true;
					if (jsonData[t].second_id != 0)
						document.getElementById(jsonData[t].second_id).disabled = true;
				}
			} else {
				for (var t = 0; t <= jsonData.length - 1; t++) {
					document.getElementById(jsonData[t].id).disabled = false;
					if (jsonData[t].second_id != 0)
						document.getElementById(jsonData[t].second_id).disabled = false;
				}
				jsonData3 = JSON.parse(oReq.responseText);
				resp_hand();
			}
		}
	};
	oReq.open("get", "get-data.php", true);
	oReq.send();
}
var cpReq = new XMLHttpRequest();
cpReq.onreadystatechange = function() {
	if (cpReq.readyState == 4 && cpReq.status == 200) {
		jsonIpPort = JSON.parse(cpReq.responseText);
	}
};
cpReq.open("GET", "get-ip-port.php", true);
cpReq.send();

function label_save(val) {
	var plc_holder = document.getElementById(val.id).value;
	var p = val.id.search("sub-");
	if (p >= 0) {
		var numb = val.id.match(/\d/g);
		numb = numb.join("");
		for (var n = 0; n <= jsonData.length - 1; n++) {
			if (numb == jsonData[n].label_id) {
				id_sd = jsonData[n].id;
				second_label = plc_holder;
				plc_holder = "";
			}
		}
	} else {
		for (var n = 0; n <= jsonData.length - 1; n++) {
			if (val.id == jsonData[n].label_id)
				id_sd = jsonData[n].id;
			second_label = "";
		}
	}
	var lblReq = new XMLHttpRequest();
	lblReq.open("GET", "label_save.php?label=" + plc_holder + "&label1=" + second_label + "&dvs_id=" + id_sd, true);
	lblReq.send();
}
var coReq = new XMLHttpRequest();
coReq.onreadystatechange = function() {
	if (coReq.readyState == 4 && coReq.status == 200) {
		document.getElementById("main-container").innerHTML = coReq.responseText;
		get_el();
	}
};
coReq.open("GET", "items_boot.php", true);
coReq.send();

function get_el() {
	raw = document.getElementById("id-info").innerHTML;
	jsonData = JSON.parse(raw);
	for (var k = 0; k <= jsonData.length - 1; k++) {
		ids[k] = jsonData[k].id;
		label_ids[k] = jsonData[k].label_id;
		labels[k] = jsonData[k].label;
		labels_second[k] = jsonData[k].second_label;
		if (labels_second[k] != "") {
			var id_mrg = "sub-" + label_ids[k];
			document.getElementById(id_mrg).value = labels_second[k];
		}
		if (labels[k] != "")
			document.getElementById(label_ids[k]).value = labels[k];
	}
	//alert(jsonData[0].id);
}
document.getElementById("wifi-data").style.display = "none";

function dvSearch() {
	ip = document.getElementById("ip-holder").value;
	if (ip_ch.test(ip)) {
		document.getElementById("ip-data").style.display = "none";
		document.getElementById("wifi-data").style.display = "block";
	} else
		document.getElementById("ip-holder").style.border = "solid #ff0000";
}

function dvSave() {
	ssid = document.getElementById("ssid-holder").value;
	pass = document.getElementById("pass-holder").value;
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", "http://" + ip, true);
	xmlhttp.send("ssid=" + ssid + "&" + "pass=" + pass + "&target=" +jsonIpPort[0].host_ip + "&port=" + jsonIpPort[0].host_port + "end");
}

function Defaults() {
	ip = "";
	document.getElementById("ip-holder").style.border = "";
	document.getElementById("ip-holder").value = "";
	document.getElementById("ssid-holder").value = "";
	document.getElementById("pass-holder").value = "";
	document.getElementById("wifi-data").style.display = "none";
	document.getElementById("ip-data").style.display = "block";
}

function UDP_send(elt) {
	var sReq = new XMLHttpRequest();
	sReq.open("get", "UDP_send.php?q=" + elt.id, true);
	sReq.send();
}

function doTimer() {
	if (!timer_is_on) {
		timer_is_on = 1;
		timedCount();
	}
}

function stopCount() {
	clearTimeout(t);
	timer_is_on = 0;
}

function timedCount() {
	if (lock_stat == 1) {
		document.getElementById("slider").onchange = function() {
			var value = document.getElementById('slider').getAttribute('data-slider');
			document.getElementById('qj').innerHTML = value;
			if (value > 80)
				document.getElementById('qj').style.backgroundColor = 'red';
			else
				document.getElementById('qj').style.backgroundColor = 'green';
			if (c > 0) {
				stopCount()
				c = 0;
			}
		};
		c = c + 10;
		if (c == 500) {
			if (tr == 0) {
				vibrate();
				document.getElementById("switch").style.background = "silver";
				tr = 1;
			} else {
				vibrate();
				document.getElementById("switch").style.background = "#16A085";
				tr = 0;
			}
		} else {
			document.addEventListener("touchend", function() {
				stopCount()
				c = 0;
			});
		}
		t = setTimeout(function() {
			timedCount()
		}, 10);
	} else
		document.addEventListener("touchend", function() {
			stopCount()
			c = 0;
		});
}

function vibrate() {
	window.navigator.vibrate(500);
}

function iconLock() {
	var check = document.getElementById('icon').className;
	if (check == 'fi-unlock') {
		if (drop_stat == 0)
			menuUp()
		document.getElementById('icon').className = "fi-lock";
		document.getElementById('slider').className += " disabled";
		document.getElementById("switch").style.background = 'red';
		lock_stat = 0;
	} else {
		document.getElementById('icon').className = "fi-unlock";
		document.getElementById("slider").className =
			document.getElementById("slider").className.replace(/(?:^|\s)disabled(?!\S)/g, '')
		document.getElementById("switch").style.background = '#16A085';
		lock_stat = 1;
	}
}
// document.getElementById("panel").addEventListener("touchmove", function(p){
// p.preventDefault()
// window.navigator.vibrate(15);
// });
// document.getElementById("left").addEventListener("touchstart", function(){
// document.getElementById("qj").innerHTML="levo";
// document.getElementById("left").style.background = "grey";
// window.navigator.vibrate(150);
// });
// document.getElementById("left").addEventListener("touchend", function(){
// document.getElementById("left").style.background = "#DDDDDD";
// });
// document.getElementById("right").addEventListener("touchstart", function(){
// document.getElementById("right").style.background = "grey";
// window.navigator.vibrate(150);
// document.getElementById("qj").innerHTML="desno";

// });
// document.getElementById("right").addEventListener("touchend", function(){
// document.getElementById("right").style.background = "#DDDDDD";
// });
document.getElementById("settings").addEventListener("touchend", function() {
	document.body.scrollTop = 0;
});
document.getElementById("off-canvas-prevent").addEventListener("touchmove", function(e) {
	e.preventDefault()
});