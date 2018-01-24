var valid_name=0;
var valid_pass=0;
var update=0;
var border_name;
var border_pass;
var status;
var patt = new RegExp(/^[a-z0-9]+$/i);
var name_obj=document.getElementById("name");
var pass_obj=document.getElementById("pass");
var status_obj=document.getElementById("status");
function check_name(){
	var name = name_obj.value;
	   if(name.length<3){
			   border_name= "solid #ff0000";
			   status="Името е твърде късо!";		   
	   }
	   else if (name.length>20){
			   border_name = "solid #ff0000";
			   status="Името е твърде дълго!";
	   }
	    else if (!patt.test(name)){
			   border_name = "solid #ff0000";
			   status="Името съдържа забранени знаци!";
	   }
		else{
		   border_name = "solid #00ff00";
		   status="";	
		   valid_name=1;
		}
		name_obj.style.border=border_name;
		status_obj.innerHTML=status;
}
function check_pass(){
	var pass = pass_obj.value;
	   if (pass.length<8){
			border_pass = "solid #ff0000";
			status="Паролата е твърде къса!";
	   }
		else if (!patt.test(pass)){
			border_pass = "solid #ff0000";
			status="Паролата съдържа забранени знаци!";
	   }
	   else{
		   border_pass= "solid #00ff00";
		   status="";	
		   valid_pass=1;
	   }
		pass_obj.style.border=border_pass;
		status_obj.innerHTML=status;
}
var register=document.getElementById('sign_up');
var change=document.getElementById('change');
var sign_out=document.getElementById('sign_out');
register.addEventListener('touchend', function() {
		send(0);
	}, false)
change.addEventListener('touchend', function() {
		send(1)
	}, false)
sign_out.addEventListener('touchend', function() {
		window.location="logout.php";
	}, false)
name_obj.addEventListener('keyup', function() {
		check_name();
	}, false)
pass_obj.addEventListener('keyup', function() {
		check_pass();
	}, false)
function send(update){	
if(valid_pass==1 && valid_name==1){
    var oreg = new XMLHttpRequest();	
	oreg.onreadystatechange = function() {
		if(this.responseText!=0)
			status_obj.innerHTML=this.responseText;
	};
	oreg.open("post", "users_save.php", true);
	oreg.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	oreg.send("name="+name_obj.value+"&pass="+pass_obj.value+"&update="+update);	
}	
}