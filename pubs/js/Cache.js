var Ajax_cache = new Object();

Ajax_cache.Request = function(url, callbackMethod)
{
	Ajax_cache.request = Ajax_cache.createRequestObject();
	Ajax_cache.request.onreadystatechange = callbackMethod;
	Ajax_cache.request.open("POST", url, true);
	Ajax_cache.request.send(url);
}

Ajax_cache.SendJSON = function(url, callbackMethod, JSONString)
{
	// Custom properties for debugging
//	Ajax_cache.callbackMethod = callbackMethod;
	Ajax_cache.jsonString = JSONString;
	Ajax_cache.url = 'pubs/'+url;
	Ajax_cache.callee = arguments.callee;			// The source code of this function.
	Ajax_cache.caller = arguments.callee.caller; 	// Same : The source code of the calling function.
		
	Ajax_cache.request = Ajax_cache.createRequestObject();
	Ajax_cache.request.onreadystatechange = callbackMethod;
	Ajax_cache.request.open("POST", Ajax_cache.url, true);
	Ajax_cache.request.send(JSONString);
}

Ajax_cache.createRequestObject = function()
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

Ajax_cache.CheckReadyState = function(obj)
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
			var args_arr = false;
			var options_arr = new Array(true,true);	 	// method_only, alert_only

			return true;
		}
		else
		{
			alert("HTTP " + obj.status);
		}
	}
}

Page.cache_all_request = function(proxies_or_owner)
{
	var citation_id_page = 0;
	if ((Page._current_citation.citation_id) && (Page._current_citation.citation_id != ''))
	{
		citation_id_page = Page._current_citation.citation_id;
	}
	
	var proxies_or_owner_array = new Array();
	if (proxies_or_owner == 'proxies')
	{
		for (var i=0; i<Page.proxies.length; i++)
		{
			proxies_or_owner_array.push(Page.proxies[i].username);
		}
	}
	else
	{
		proxies_or_owner_array.push(Page.owner);
	}
		
	var jsonStr = '{"request": {"type": "cache_all",  "page": "' + Page.current_page + '", "citation_id_page": "' + citation_id_page + '", "citations_per_page": "' + Page.citations_per_page + '", "sort_order": "' + Page.sort_order + '", "citations": {"submitter": "' + Page.submitter + '",  "owner": ' + YAHOO.lang.JSON.stringify(proxies_or_owner_array) + ', "entryTime": ""}}}';
	Ajax_cache.SendJSON('services/cache.php', null, jsonStr);
}

//Page.junkresponse = function();
