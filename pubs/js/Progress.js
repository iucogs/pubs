var Ajax_progress = new Object();
var intervalID = false;	// interval ID

Ajax_progress.send_request = function()
{
	// Custom properties for debugging
	var callbackMethod = Ajax_progress.CheckReadyState;
	var jsonString = null;
	var url = 'pubs/services/progress.php';
	
	Ajax_progress.request = Ajax_progress.createRequestObject();
	Ajax_progress.request.onreadystatechange = callbackMethod;
	Ajax_progress.request.open("POST", url, true);
	Ajax_progress.request.send(jsonString);
}

Ajax_progress.createRequestObject = function()
{
	var obj;
	if(window.XMLHttpRequest)
	{
		obj = new XMLHttpRequest();
	}
	else if(window.ActiveXObject)
	{
		obj = new ActiveXObject("MSXML2.XMLHTTP");
	}
	return obj;
}

Ajax_progress.CheckReadyState = function()
{
	var obj = Ajax_progress.request;
	//if(Page.panel4) Page.panel4.show(); // Show loading panel
	if(obj.readyState == 0) { /*document.getElementById('loading').innerHTML = "Sending Request...";*/ }
	if(obj.readyState == 1) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 2) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 3) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 4)
	{
		//if(Page.panel4)	Page.panel4.hide(); // Hide loading panel
		if(obj.status == 200)
		{
			//var responseObj = eval("(" + Ajax_progress.request.responseText + ")");
			var stop_button = '<input type="button" onclick="polling_stop();" value="stop" />';
			document.getElementById('progress').innerHTML = '<center><div style="width:100%;margin-left:auto;margin-right:auto;text-align:left">' + Ajax_progress.request.responseText + '<br />' + stop_button + '</div></center>';
			return true;
		}
		else
		{
			document.getElementById('progress').innerHTML = 'Error: [' + Ajax_progress.request.status + '] ' + Ajax_progress.request.statusText;
			alert("HTTP " + obj.status);
		}
	}
}

// button actions (start / stop)
function polling_start() {
	if (!intervalID) {
		intervalID = window.setInterval('Ajax_progress.send_request()', 1000);
	}
}

function polling_stop() {
	window.clearInterval(intervalID);
	intervalID = false;
	document.getElementById('progress').innerHTML = '';
}