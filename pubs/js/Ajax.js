var Ajax = new Object();

// Debug purposes 
//var time_object = new timestamp_class(0, 0, 0, 0);	//create new time object and initialize it

Ajax.Request = function(url, callbackMethod)
{
	Ajax.request = Ajax.createRequestObject();
	Ajax.request.onreadystatechange = callbackMethod;
	Ajax.request.open("POST", url, true);
	Ajax.request.send(url);
}

Ajax.SendJSON = function(url, callbackMethod, JSONString)
{
	// Custom properties for debugging
	Ajax.callbackMethod = callbackMethod;
	Ajax.jsonString = JSONString;
	Ajax.url = 'pubs/'+url;
	//alert(Ajax.url);
	Ajax.callee = arguments.callee;			// The source code of this function.
	Ajax.caller = arguments.callee.caller; 	// Same : The source code of the calling function.
	//Ajax.caller2 = Function.caller;		// Same : The source code of the calling function.
	
	//time_object.StartTiming();
		
	Ajax.request = Ajax.createRequestObject();
	Ajax.request.onreadystatechange = callbackMethod;
	Ajax.request.open("POST", Ajax.url, true);
	Ajax.request.send(JSONString);
}

Ajax.SendXML = function(url, callbackMethod, xmlString)
{
	Ajax.request = Ajax.createRequestObject();
	Ajax.request.onreadystatechange = callbackMethod;
	Ajax.request.open("POST", url, true);
	Ajax.request.setRequestHeader("Content-Type", "text/xml");
	Ajax.request.send(xmlString);
}

Ajax.createRequestObject = function()
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

Ajax.CheckReadyState = function(obj)
{
	if(Page.panel4) Page.panel4.show(); // Show loading panel
	if(obj.readyState == 0) { /*document.getElementById('loading').innerHTML = "Sending Request...";*/ }
	if(obj.readyState == 1) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 2) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 3) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 4)
	{
		if(Page.panel4)	Page.panel4.hide(); // Hide loading panel
		if(obj.status == 200)
		{
			
			//time_object.EndTiming();
			
			// Note:
			// - obj == Ajax.request.
			// - The use of "Ajax" or "this" are the same in this context.
			
			// Debugging: 
			//alert('[Ajax.js] Response callbackMethod: ' + Ajax.callbackMethod);
			//alert('[Ajax.js] Request JSON String: ' + Ajax.jsonString);
			//alert('[Ajax.js] Response Text: ' + obj.responseText);
			//alert('[Ajax.js] Request URL: ' + Ajax.url)
			//alert('[Ajax.js] Request callee: ' + this.callee);
			//alert('[Ajax.js] Request caller: ' + this.caller);
			//alert('[Ajax.js] Request caller2: ' + this.caller2);  // Not working at the moment.
			//alertObjectProperties(obj);
			//alert('[Ajax.js] Request caller: ' + objectCaller(this.caller, Page));
			//alert('[Ajax.js] Response callbackMethod: ' + objectCaller(this.callbackMethod, Page));
			
			var args_arr = false;
			var options_arr = new Array(true,true);	 	// method_only, alert_only
			//var options_arr = new Array(false,true);  	// method_only, alert_only
			//var args_arr = new Array("SendText","onResponseBatchSubmission"); 
			//var args_arr = new Array("checkInputAndSave","onResponseCheckAuthors");
			//var args_arr = new Array("getCitations","onResponse"); 
			
			//alertAjaxCalls(this, args_arr, options_arr);

			return true;
		}
		else
		{
			alert("HTTP " + obj.status);
		}
	}
}

// Used by tooltips in listCitations - no loading panel4 
Ajax.CheckReadyStateNoLoading = function(obj)
{
	if(obj.readyState == 0) { /*document.getElementById('loading').innerHTML = "Sending Request...";*/ }
	if(obj.readyState == 1) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 2) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 3) { /*document.getElementById('loading').innerHTML = "Loading...";*/ }
	if(obj.readyState == 4)
	{
		if(obj.status == 200){return true;}
		else{alert("HTTP " + obj.status);}
	}
}

// Debug
function alertAjaxCalls(ajax_obj, func_list, options_arr)
{
	var msg = '[Ajax.js]\n';
	
	var method_only = true;
	var alert_only = true;
	
	if(options_arr.length != 0)
	{
		method_only = options_arr[0];
		alert_only = options_arr[1];
	}
	
	if(!func_list)
	{
		var caller = '';
		if(caller = objectCaller(ajax_obj.caller, Page))
		{
			msg += 'Request caller: ' + caller + ' => ' + ajax_obj.url + '\n';
			if(!method_only) msg += 'Request jsonString: ' + ajax_obj.jsonString + '\n';
			msg += '\n';
			
		}
		if(caller = objectCaller(ajax_obj.callbackMethod, Page))
		{
			msg += 'Response callbackMethod: ' + caller + '\n';
			if(!method_only) msg += 'Response responseText: ' + ajax_obj.request.responseText + '\n';
			msg += '\n';
		}
		else {}
	}
	else {
		for(var i in func_list)
		{
			var caller = '';
			if((caller = objectCaller(ajax_obj.caller, Page)) == func_list[i])
			{
				msg += 'Request caller: ' + caller + ' => ' + ajax_obj.url + '\n';
				if(!method_only) msg += 'Request jsonString: ' + ajax_obj.jsonString + '\n'; 
				msg += '\n';
			}
			if((caller = objectCaller(ajax_obj.callbackMethod, Page)) == func_list[i])
			{
				msg += 'Response callbackMethod: ' + caller + '\n';
				if(!method_only) msg += 'Response responseText: ' + ajax_obj.request.responseText + '\n';
				msg += '\n';
			}
		}
	}
	
	if(msg != '[Ajax.js]\n') 
	{
		alert(msg); 
		if(!alert_only) PopUp("<pre>" + msg + "</pre>");  
	}
	
	return false;
}

function alertObjectProperties(obj)
{
	/**** Display Object Properties ****/
	var i = 0;
	var props = "";
	for(var prop in obj)
	{		
		props += prop + " | ";								
		i++;
	}
	alert('[Ajax.js] Total elements: ' + i + '\n'+ props);
	/************************************/
}

function objectCaller(caller, obj)
{
	for(var prop in obj)
	{
		var func = obj[prop]; // To call the function: obj[prop](); or func();
		if(func == caller) 
		{
			return prop;
			//return obj.toString() + '.' + prop;
		}	
	}
	return "not found!";
}

function PopUp(SayWhat) {
	WinPop = this.WinPop; 																// Set WinPop to global.
	if(this.window.onunload != "") this.window.onunload = function() {WinPop.close()};  // Debug window on refresh.
	
	var width = 900;
	var height = 400;
	var ScreenWidth=window.screen.width;
	var ScreenHeight=window.screen.height;
	var movefromedge=0;
	placementx=(ScreenWidth/2)-((width)/2);
	placementy=(ScreenHeight/2)-((height+50)/2);
	if(!WinPop) {
		WinPop=window.open("About:Blank","","width="+width+",height="+height+",toolbar=0,location=0,directories=0,status=0,scrollbars=1,menubar=0,resizable=1,left="+placementx+",top="+placementy+",scre enX="+placementx+",screenY="+placementy+",");
	}
	var pre_table = '<table border="1" style="width:'+width+'px;word-wrap:break-word;"><tr><td>';
	WinPop.document.write('<html>\n<head>\n</head>\n<title>[Ajax.js] Pubs Debug\n</title>\n<body>'+pre_table+SayWhat+'</td></tr></table></body></html>');
	WinPop.focus();
}

// To calculate AJAX call time difference.
function timestamp_class(this_current_time, this_start_time, this_end_time, this_time_difference) { 
		this.this_current_time = this_current_time;
		this.this_start_time = this_start_time;
		this.this_end_time = this_end_time;
		this.this_time_difference = this_time_difference;
		this.GetCurrentTime = GetCurrentTime;
		this.StartTiming = StartTiming;
		this.EndTiming = EndTiming;
	}

//Get current time from date timestamp
function GetCurrentTime() {
var my_current_timestamp;
	my_current_timestamp = new Date();		//stamp current date & time
	return my_current_timestamp.getTime();
	}

//Stamp current time as start time and reset display textbox
function StartTiming() {
	this.this_start_time = GetCurrentTime();	//stamp current time
	//document.TimeDisplayForm.TimeDisplayBox.value = 0;	//init textbox display to zero
	}

//Stamp current time as stop time, compute elapsed time difference and display in textbox
function EndTiming() {
	this.this_end_time = GetCurrentTime();		//stamp current time
	this.this_time_difference = (this.this_end_time - this.this_start_time) / 1000;	//compute elapsed time
	//document.TimeDisplayForm.TimeDisplayBox.value = this.this_time_difference;	//set elapsed time in display box
	alert('[Ajax.js] Ajax Time Elapsed: ' + this.this_time_difference + ' seconds.');
	}