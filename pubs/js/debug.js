Page.oneTimeToggle = true;

Page.alertOnce = function(obj)
{
	if(Page.oneTimeToggle == true)
	{
		Page.oneTimeToggle = false;
		alert(obj);
	}
}

//document.onkeydown = KeyCheck;  // For backspace (key 8) use onekeydown.
//document.onkeyup = KeyCheck;
function KeyCheck(e) {
	var KeyID = (window.event) ? event.keyCode : e.keyCode;

	alert("Key " + KeyID + " pressed.");
	
	switch(KeyID)
	{
/*		case 16:
		document.Form1.KeyName.value = "Shift";
		break; 
		
		case 17:
		document.Form1.KeyName.value = "Ctrl";
		break;
		
		case 18:
		document.Form1.KeyName.value = "Alt";
		break;
		
		case 19:
		document.Form1.KeyName.value = "Pause";
		break;
		
		case 37:
		document.Form1.KeyName.value = "Arrow Left";
		break;
		
		case 38:
		document.Form1.KeyName.value = "Arrow Up";
		break;
		
		case 39:
		document.Form1.KeyName.value = "Arrow Right";
		break;
		
		case 40:
		document.Form1.KeyName.value = "Arrow Down";
		break;*/
	}
}