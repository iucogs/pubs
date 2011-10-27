Page.print_user_back_button = function()
{
	var admin_back_html = '<input type="button" value="Back to admin" onclick="Page.adminPage();" /><br><br>';
	var user_back_html = '<input type="button" value="Back to user" onclick="Page.myAccount();" /><br><br>';
	
	if(Page.user_back_button_state == 'admin')	return admin_back_html;
	else return user_back_html;
}

// proxy_table	- Generate current proxy table for a particular user
//				- Called by onResponse() (under createproxy, viewproxy and deleteproxy XML tag)
Page.proxy_table = function(proxies, selected_username)
{
	var html = "";

	var count = proxies.length;
	html += '<p><center>Current Proxies:</center></p><br />';
	html += '<table style="border: 2px solid #7D110C; width:25%; text-align:left"><th>Count</th><th>id</th>';
	html += '<th>Firstname</th><th>Lastname</th><th>Username</th><th>Edit</th><th>Delete</th>';
	var toggle = true; 
	var highlight1 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#DDDDDD\'" bgcolor="#DDDDDD"';
	var highlight2 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#FFFFFF\'" bgcolor="#FFFFFF"';
	
	for(var i = 0; i < count; i++){
		var current_id = proxies[i].id; 
		if(toggle){
			html += '<tr id="proxy_'+current_id+'" '+highlight1+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		else{
			html += '<tr id="proxy_'+current_id+'" '+highlight2+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		html += '<td>'+proxies[i].id+'</td>';
		html += '<td>'+proxies[i].firstname+'</td>';
		html += '<td>'+proxies[i].lastname+'</td>';
		html += '<td>'+proxies[i].username+'</td>';
		html += '<td><a href="#" onclick="Page.editinputs(this.parentNode, \''+ current_id +'\');return false;" />Edit</a></td>';
		html += '<td><a href="#" onclick="Page.deleteproxy_request(\''+current_id+'\',\'' + selected_username+'\');return false;">Delete</a></td></tr>';
	}
	
	html += '</table>';
	return html;
}

//	user_table	- Called by onResponse()
//				- Original requested by viewuser()
Page.user_table = function(users, selected_username)
{
	var html = "";
	var count = users.length;
	html += '<p><center>Current Users:</center></p><br />';
	html += '<table style="border: 2px solid #7D110C; width:25%; text-align:left"><th>Count</th><th>id</th><th>Firstname</th><th>Lastname</th><th>Username</th><th>Edit</th><th>Delete</th><th>Proxy</th>';
	var toggle = true;
	var highlight1 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#DDDDDD\'" bgcolor="#DDDDDD"';
	var highlight2 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#FFFFFF\'" bgcolor="#FFFFFF"';
	for(var i = 0; i < count; i++){
		var current_id = users[i].id;
		if(toggle){
			html += '<tr id="proxy_'+current_id+'" '+highlight1+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		else{
			html += '<tr id="proxy_'+current_id+'" '+highlight2+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		html += '<td>'+users[i].id+'</td>';
		html += '<td>'+users[i].firstname+'</td>';
		html += '<td>'+users[i].lastname+'</td>';
		html += '<td>'+users[i].username+'</td>';
		html += '<td><a href="#" onclick="Page.editinputs(this.parentNode, \''+ current_id +'\');" />Edit</a></td>';
		html += '<td><a href="#" onclick="Page.deleteuser_request(\''+current_id+'\',\'' + selected_username+'\');">Delete</a></td>';
		html += '<td><a href="#" onclick="Page.manageproxy_request(\''+users[i].username+'\',\'' + selected_username+'\');">Manage</a></td></tr>';
	}
	
	html += '</table>';
	return html;
}

// proxytable_request	- Called by proxy_table() and user_table()
//						- Do an Ajax request to user.php
//						- proxytable_update() is the callback method
//						- Handle the Save and Cancel functionality of proxy_table() and user_table()
Page.proxytable_request = function(type, id)
{
	var submitter = Page.submitter;
	var callbackmethod = Page.proxytable_update;
	
	if(type == "update"){	
		var firstname = document.getElementById('proxy_'+id).childNodes[2].firstChild.value;
		var lastname = document.getElementById('proxy_'+id).childNodes[3].firstChild.value;
		var username = document.getElementById('proxy_'+id).childNodes[4].firstChild.value;
		
		var jsonStr = '{"request": {"type": "update",  "id": "' + id + '",  "username": "' + username + '", "lastname": "' + lastname + '", "firstname": "' + firstname + '", "submitter": "' + submitter + '"}}';
	}
	else{
		var jsonStr = '{"request": {"type": "cancel",  "id": "' + id + '"}}';
	}
	Ajax.SendJSON('services/user.php', callbackmethod, jsonStr);
}

// proxytable_update	- Called by Ajax upon readystate
//						- Set proxy_table() and user_table() appropriately (Save or Cancel)
Page.proxytable_update = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");

		// No element or search failed.
		if(responseObj.getuser != undefined)
		{
			var user = responseObj.getuser[0];
			var id = user.id;
			document.getElementById('proxy_'+id).childNodes[2].innerHTML = user.firstname;
			document.getElementById('proxy_'+id).childNodes[3].innerHTML = user.lastname;
			document.getElementById('proxy_'+id).childNodes[4].innerHTML = user.username;
			document.getElementById('proxy_'+id).childNodes[5].innerHTML = '<a href="#" onclick="Page.editinputs(this.parentNode, \''+ id +'\');return false;" />Edit</a>';
		}
		else if(responseObj.updateuser != undefined)
		{
			var user = responseObj.updateuser[0];
			var id = user.id;
			document.getElementById('proxy_'+id).childNodes[2].innerHTML = user.firstname;
			document.getElementById('proxy_'+id).childNodes[3].innerHTML = user.lastname;
			document.getElementById('proxy_'+id).childNodes[4].innerHTML = user.username;
			document.getElementById('proxy_'+id).childNodes[5].innerHTML = '<a href="#" onclick="Page.editinputs(this.parentNode, \''+ id +'\');return false;" />Edit</a>';
		}
		else{}	
	}
}

// editinputs	- Used by proxytable_update() and proxy_table()
//				- Replace <td>Edit</td> element with <td>Save [x]</td>
//				- Get other <td></td> elements and replace with textbox to make cell editable
Page.editinputs = function(obj, id)
{
	obj.innerHTML = '<strong><a title="Save" href="#" onclick="Page.proxytable_request(\'update\',\''+id+'\');return false;" />Save</a>&nbsp;' 
						+ '<a style="color:#000000" title="Cancel" href="#" onclick="Page.cancelinputs(\''+id+'\');return false;">[x]</a></strong>';
	var fn = obj.parentNode.childNodes[2].innerHTML;
	var ln = obj.parentNode.childNodes[3].innerHTML;
	var usr = obj.parentNode.childNodes[4].innerHTML;
	
	document.getElementById('proxy_'+id).childNodes[2].innerHTML = Page.printTextBox('proxy_'+id, fn, '');
	document.getElementById('proxy_'+id).childNodes[3].innerHTML = Page.printTextBox('proxy_'+id, ln, '');
	document.getElementById('proxy_'+id).childNodes[4].innerHTML = Page.printTextBox('proxy_'+id, usr, '');
}

// cancelinputs	- Used by user_table() or proxy_table() (after editinputs make the changes)
// 				- Simply calls proxytable_request() the get the original values
Page.cancelinputs = function(id)
{
	Page.proxytable_request('cancel', id);
}

// clear_createproxyfrm	- Clear createproxy form in Create Proxy page
Page.clear_createproxyfrm = function()
{
	document.getElementById('proxy_firstname').value = "";
	document.getElementById('proxy_lastname').value = "";
	document.getElementById('proxy_username').value = "";
}


// createuser	- Send Ajax request to create a proxy
//				- Called by inputMethod(), create proxy page
Page.createuser_request = function(account, username, firstname, lastname, submitter)
{
	var option_admin = Page.getCheckedValue(document.getElementsByName('option_admin'));
	var option_cogs = Page.getCheckedValue(document.getElementsByName('option_cogs'));
	var option_self_proxy = Page.getCheckedValue(document.getElementsByName('option_self_proxy'));
	
	if(username == "" && firstname == "" && lastname == "")
	{
		var html = '<center>Please enter a Username or a Last Name.</center>';
		document.getElementById('search_result').innerHTML = html;
	}
	else
	{	
		var jsonStr = '{"request": {"type": "create",  "account": "' + account + '", "username": "' + username + '", "lastname": "' + lastname + '", "firstname": "' + firstname + '", "option_admin": "' + option_admin + '", "option_cogs": "' + option_cogs + '", "option_self_proxy": "' + option_self_proxy + '", "submitter": "' + submitter + '"}}';
		//alert(jsonStr);
		Ajax.SendJSON('services/user.php', Page.createuser_response, jsonStr);
	}
}

Page.createuser_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		if(responseObj.createuser != undefined)
		{
			var html = Page.print_user_back_button();
			
			if(responseObj.result == "true") { 
				html += "<center>User Created Successfully!</center><br />";
				html += '<table style="border: 2px solid #7D110C; width:15%; text-align:center">';
				html += '<tr><td bgcolor="#DDDDDD">Username: </td><td>' + responseObj.username + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Firstname: </td><td>' + responseObj.firstname + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Lastname: </td><td>' + responseObj.lastname + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Submitter: </td><td>' + responseObj.submitter + '</td></tr>';
				html += '</table>';
				html += Page.user_table(responseObj.createuser, responseObj.submitter);
				
				document.getElementById('secondary').innerHTML = html;
			}
			else {
				var warning = "";
				if(responseObj.createuser == "no_ads") { warning = "No such ADS username exists."; }
				else if(responseObj.createuser == "user_exist") { warning = "User exists."; }
				else if(responseObj.createuser == "multiple_users") { warning = "Multiple Users Exists!"; }
				else if(responseObj.createuser == "query_error") { warning = "Error in the query!"; }
				else { warning = "Undefined Error!"; }
				html += '<center>'+warning+'</center>';
				document.getElementById('secondary').innerHTML = html;
			}
		}
	}
}

// createproxy	- Send Ajax request to create a proxy
//				- Called by inputMethod(), create proxy page
Page.createproxy_request = function(account, username, firstname, lastname, submitter)
{
	if(username == "" && firstname == "" && lastname == "")
	{
		var html = '<center>Please enter a Username or a Last Name.</center>';
		document.getElementById('search_result').innerHTML = html;
	}
	else
	{	
		var jsonStr = '{"request": {"type": "create",  "account": "' + account + '",  "username": "' + username + '", "lastname": "' + lastname + '", "firstname": "' + firstname + '", "submitter": "' + submitter + '"}}';
		Ajax.SendJSON('services/proxy.php', Page.createproxy_response, jsonStr);
	}
}

Page.createproxy_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		if(responseObj.createproxy != undefined)
		{
			var html = Page.print_user_back_button();
			
			if(responseObj.result == "true") { 
				html += "<center>Proxy Created Successfully!</center><br />";
				html += '<table style="border: 2px solid #7D110C; width:15%; text-align:center">';
				html += '<tr><td bgcolor="#DDDDDD">Username: </td><td>' + responseObj.username + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Firstname: </td><td>' + responseObj.firstname + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Lastname: </td><td>' + responseObj.lastname + '</td></tr>';
				html += '<tr><td bgcolor="#DDDDDD">Submitter: </td><td>' + responseObj.submitter + '</td></tr>';
				html += '</table>';
				html += Page.proxy_table(responseObj.createproxy, responseObj.submitter);
				document.getElementById('secondary').innerHTML = html;
			}
			else {
				var warning = "";
				if(responseObj.createproxy == "no_ads") { warning = "No such ADS username exist."; }
				else if(responseObj.createproxy == "proxy_exist") { warning = "Proxy exist."; }
				else if(responseObj.createproxy == "proxy_equal_submitter") { warning = "Cannot create yourself as a proxy."; }
				else if(responseObj.createproxy == "multiple_users") { warning = "Multiple Users Exists!"; }
				else if(responseObj.createproxy == "query_error") { warning = "Error in the query!"; }
				else { warning = "Undefined Error!"; }
				html += '<center>'+warning+'</center>';
				document.getElementById('secondary').innerHTML = html;
			}
		}
	}
}

// deleteproxy 	- Send Ajax request to delte a proxy based on proxy id
// 				- Called by proxy_table
Page.deleteproxy_request = function(id, submitter)
{
	var jsonStr = '{"request": {"type": "delete", "id": "' + id + '", "submitter": "' + submitter + '"}}';
	Ajax.SendJSON('services/proxy.php', Page.deleteproxy_response, jsonStr);
}

// deleteproxy 	- Callback method for deleteproxy_request
// 				- proxy.php - Return deleted proxy / show current proxies
Page.deleteproxy_response = function()
{
	if(Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
		if(responseObj.deleteproxy != undefined) 
		{
			var html = '';
			html += Page.print_user_back_button();
			html += "<center>Proxy Deleted Successfully!</center><br />";
			html += Page.proxy_table(responseObj.deleteproxy, responseObj.submitter);
			document.getElementById('secondary').innerHTML = html;
		}
	}
}

// viewproxy_request 	- Send Ajax request to get all proxies for current user
// 						- Called by proxy_table
Page.viewproxy_request = function(submitter)
{
	var jsonStr = '{"request": {"type": "view", "submitter": "' + submitter + '"}}';
	Ajax.SendJSON('services/proxy.php', Page.viewproxy_response, jsonStr);
}

// viewproxy_response	- Callback method for viewproxy_request
// 						- proxy.php - Return deleted proxy / show current proxies
Page.viewproxy_response = function()
{
	if(Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
		if(responseObj.viewproxy != undefined) 
		{
			var html = '';
			html += Page.print_user_back_button();
			html += Page.proxy_table(responseObj.viewproxy, responseObj.submitter);
			document.getElementById('secondary').innerHTML = html;
		}
	}
}

// viewuser 	- Send Ajax request to get all users
//				- Called by inputMethod(), view all users page
Page.viewuser_request = function(submitter)
{
	var jsonStr = '{"request": {"type": "view",  "submitter": "' + submitter + '"}}';
	Ajax.SendJSON('services/user.php', Page.viewuser_response, jsonStr);
}

Page.viewuser_response = function()
{
	if(Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		if(responseObj.viewuser != undefined)
		{
			var html = '';
			html += Page.print_user_back_button();
			html += Page.user_table(responseObj.viewuser, responseObj.submitter);
			document.getElementById('secondary').innerHTML = html;
		}
	}
}

// deleteuser	- Send Ajax request
// 				- Called by inputMethod(), create proxy page
Page.deleteuser_request = function(id, submitter)
{
	var jsonStr = '{"request": {"type": "delete", "id": "' + id + '", "submitter": "' + submitter + '"}}';
	Ajax.SendJSON('services/user.php', Page.deleteuser_response, jsonStr);
}

Page.deleteuser_response = function()
{
	if(Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
		if(responseObj.deleteuser != undefined)
		{
			var html = '';
			html += Page.print_user_back_button();
			
			if(responseObj.deleteuser == false) html += '<center>Error Deleting User.</center>';
			else html += '<center>User Deleted.</center>';
			
			html += Page.user_table(responseObj.deleteuser, responseObj.submitter);
			document.getElementById('secondary').innerHTML = html;
		}
	}
}

// checkproxy_update	- Callback method for checkproxy_request()
//						- Retrieve search result of users and prints table
Page.checkproxy_update = function()
{
	if(Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		
		// No element or search failed.
		if(responseObj.users == undefined)
		{
			var html = '<center>Search Failed.</center>';
			document.getElementById('search_result').innerHTML = html;	
		}
		else
		{
			// Save in global
			Page.ldap_dom = responseObj;
			
			// Create table
			Page.search_result(responseObj);
		}	
	}
}

// search_result	- Prints table of users
//					- Called by checkproxy_update()
Page.search_result = function(response)
{
	var toggle = true;
	var count = response.users.length;
	var html = '<center>Please click a user.</center><p></p>';
	
	if(response.account == "guest") html += '<table style="border: 2px solid #7D110C;"><th>First Name</th><th>Last Name</th><th>Username/Email</th><th>ID</th><th>Department</th>'; 
	else html += '<table style="border: 2px solid #7D110C;"><th>First Name</th><th>Last Name</th><th>Username</th><th>Email</th><th>Department</th>';
	
	for(i = 0; i < count; i++)
	{
		var firstname = response.users[i].firstname;
		var lastname = response.users[i].lastname;
		if(response.account == "guest") {
			var mail = response.users[i].username;
			var username = response.users[i].mail;
		}
		else {
			var username = response.users[i].username;
			var mail = response.users[i].mail;
		}
		var department = response.users[i].department;
		
		var highlight1 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#DDDDDD\'" bgcolor="#DDDDDD"';
		var highlight2 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#FFFFFF\'" bgcolor="#FFFFFF"';
		
		html += '<tr id="'+i+'" onclick="Page.search_update(this.id,\''+response.account+'\');" '
		if(toggle) {
			html += highlight1; 
		}
		else {
			html += highlight2; 
		}
		html += '><td>' + firstname + '</td><td>' + lastname + '</td><td>' + username + '</td><td>' + mail + '</td><td>' + department + '</td></tr>';
		toggle = !toggle;
	}	
	
	html += '</table>';
	
	document.getElementById('search_result').innerHTML = html;
}

// search_update	- Called by search_result() table
//					- Fill the selected user info into the search form
Page.search_update = function(id, account)
{
	document.getElementById('proxy_firstname').value = Page.ldap_dom.users[id].firstname;
	document.getElementById('proxy_lastname').value = Page.ldap_dom.users[id].lastname;
	if(account == "guest") {
		document.getElementById('proxy_username').value = Page.ldap_dom.users[id].mail;
	}
	else {  // "ads"
		document.getElementById('proxy_username').value = Page.ldap_dom.users[id].username;
	}
}

// checkproxy_request	- Called by inputMethod(), create proxy page 
//						- Check if the search is based on username or name
Page.checkproxy_request = function()
{
	document.getElementById('search_result').innerHTML = "";	
	var value_username = document.getElementById('proxy_username').value;
	var value_firstname = document.getElementById('proxy_firstname').value;
	var value_lastname = document.getElementById('proxy_lastname').value;
	var value_account = Page.getCheckedValue(document.getElementsByName('account_type'));
	var callbackmethod = Page.checkproxy_update;
	
	if(value_username != "") 
	{
		var type = "username";		
		var jsonStr = '{"request": {"type": "'+ type +'",  "value": "' + value_username + '" ,  "account": "' + value_account + '"}}';
		Ajax.SendJSON('services/checkproxy.php', callbackmethod, jsonStr);	
	}
	else if(value_lastname != "")
	{
		var type = "name";
		var jsonStr = '{"request": {"type": "'+ type +'",  "lastname": "' + value_lastname + '",  "firstname": "' + value_firstname + '",  "account": "' + value_account + '"}}';
		Ajax.SendJSON('services/checkproxy.php', callbackmethod, jsonStr);	
	}
	else
	{
		var html = '<center>Please enter a Username or a Last Name.</center>';
		document.getElementById('search_result').innerHTML = html;	
	}	
}

Page.viewOwners_request = function()
{
	var jsonStr = '{"request": {"type": "viewOwners", "submitter": "' + Page.submitter + '"}}';
	Ajax.SendJSON('services/proxy.php', Page.viewOwners_response, jsonStr);
}

Page.viewOwners_response = function()
{
	var html = '';
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		// No element or search failed.
		if ((responseObj.owners != undefined) && (responseObj.owners != false))
		{
			var owners = responseObj.owners;
			Page.proxies = new Array();
			for (var i=0; i<owners.length; i++) {
				Page.proxies.push(owners[i]);
			}
			
			if (Page.owner == "") {
				Page.owner = owners[0].username;		
			}
			Page.setHasProxy();
		//	Page.printWorkAsMenu();
		}
		Page.current_page=1; 
		Page.current_viewable_pages=new Array();
	//	if (Page.loggedIn)
	/*	{
			alert('before');
			Page.getCollectionNamesAndIds('display_collections');
			alert('middle: ' + Page.special_collections);
			Page.homePage_loggedIn();

		}
		else
		{*/
			Page.get_faculty_request();
	//	}
		
	}
	return '';
}

Page.printWorkAsMenu = function()
{
	var html = '';
	html += 'Show collections belonging to:&nbsp;&nbsp;<select id="setOwner" onchange="Page.changeSetOwnerSelectMenu(this);">';
		
	if(!Page.hasProxy) {
		html += '<option value=""></option>';
	}
	
	for (var i=0; i<Page.proxies.length; i++) {
		if (Page.proxies[i].username == 'sep') {
			html += '<option onmouseover="this.parentNode.title=\'Stanford Encyclopedia of Philosophy\'" value="' + Page.proxies[i].username + '"';
			if (Page.owner == Page.proxies[i].username)	{
				html += ' selected="selected"';
			}
			html += '>Stanford Encyclopedia ...</option>';
		}
		else {
			var user_name = Page.proxies[i].lastname + ', ' + Page.proxies[i].firstname;
			html += '<option onmouseover="this.parentNode.title=\''+user_name+'\'" value="' + Page.proxies[i].username + '"';
			if (Page.owner == Page.proxies[i].username)	{
				html += ' selected="selected"';
			}
			// Check if the full name is more than 20 characters and cut it.
			if(user_name.length > 20) user_name = user_name.substr(0,20) + ' ...';
			html += '>' + user_name + '</option>';
		}	
	}
	html += '</select>';
//	document.getElementById('owner_div').innerHTML = html;				//set	
	Page.cache_all_request('proxies');
	return html;
}

Page.changeSetOwnerSelectMenu = function(myselect)
{
	// Cancel onchange event
	if(myselect.options[myselect.selectedIndex].value == '') {
	   return false;	
	}
	
	Page.owner=myselect.options[myselect.selectedIndex].value;
	Page.currentCollection = 'all';
	Page.setHasProxy();
	Page.current_page=1;
	Page.current_viewable_pages=new Array();
	Page.getCitationsGivenCollectionID();
}

Page.get_faculty_request = function()
{
	var jsonStr = '{"request": {"type": "get_faculty"}}';
	Ajax.SendJSON('services/user.php', Page.get_faculty_response, jsonStr);
}

Page.get_faculty_response = function()
{
	var html = '';
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	 
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.homePage(responseObj.get_faculty);
	}
}

Page.setHasProxy = function()
{
	var found = false;
	for(var i = 0; i < Page.proxies.length; i++) {
		if(Page.owner == Page.proxies[i].username)
			found = true;
	}
	Page.hasProxy = found;
}

Page.setOwnerDiv = function()
{
	Page.viewOwners_request();
}

// homePage		- Called by index.php
//				- Called by onClick event of "Home" button
Page.homePage = function(faculty)
{	
	// Reset owner then rewrite proxy select menu
	Page.owner = '';
	if (Page.loggedIn) { 
		Page.printWorkAsMenu();
	}
	
	var html = 'Welcome to Publications, sponsored by the Indiana University Cognitive Science Program.<br><br>';
	
	html += '<table align="center" style="height:100%;width:100%;text-align:left">';
	for (var i=0; i<faculty.length; i++)
	{
		html += '<tr><td class="pointerhand" onclick="Page.owner=\'' + faculty[i].username + '\';Page.owner_fullname=\'' + faculty[i].firstname + ' ' +faculty[i].lastname + '\';Page.setHasProxy();Page.currentCollection=\'' + faculty[i].collection_id + '\'; Page.getFacultyRepresentativePublications(\'' + faculty[i].username + '\');">' + faculty[i].lastname + ', ' + faculty[i].firstname + '</td></tr>';
	}
	html += '</table>';
	document.getElementById('secondary').innerHTML = html;
	Page.hideCitations();
}

Page.homePage_loggedIn = function()
{
	var html = Page.printCollectionNamesMenuInMainWindow();
	document.getElementById('secondary').innerHTML = html;
	Page.hideCitations();
}

Page.getFacultyRepresentativePublications = function(faculty_username)  // essentially same as getCitationsGivenCollectionID, but don't set owner
{
	var citation_id = 0;
	
	// Rewrite proxy select menu
	/*if(Page.loggedIn) {
		Page.rewriteSelectMenu();
	}*/
	Page.current_get_type = 'getCollection';
	
	Page.getCitations(Page.current_page, Page.current_get_type);

}

Page.hideCitations = function()
{
	Page.right_column_display('none');
	document.getElementById('citations').style.display = 'none';
	document.getElementById('secondary').style.display = '';
	if (document.getElementById('collectionNamesMenuForViewing'))
	{
		document.getElementById('collectionNamesMenuForViewing').selectedIndex = 0;
	}
}

Page.register = function()
{
	document.getElementById('secondary').innerHTML = 'Here is how to register.';
	Page.hideCitations();
}

Page.myAccount = function()
{
	Page.user_back_button_state = 'user';
	
	var html = '';
	html += '<table align="center" style="border: 2px solid #7D110C;">';
	html += '<tr><td width="10">&nbsp;</td><td><b>Proxy management</b></td></tr>';
    html += '<tr><td></td><td>';
 	html += '<table border="0">';
    //html += '<tr><td><input type="radio" onclick="Page.inputMethod(16)" value="16" name="input_method"/></td><td align="left">Create User</td></tr>';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(6)" value="6" name="input_method"/></td><td align="left">Create Proxy</td></tr>';
    //html += '<tr><td><input type="radio" onclick="Page.inputMethod(8)" value="8" name="input_method"/></td><td align="left">View / Manage All Users</td></tr>';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(7)" value="7" name="input_method"/></td><td align="left">View / Manage Current Proxies</td>';
	html += '</table>';
	html += '</td></tr>';
	html += '<tr><td></td><td>';
	html += '</td></tr>';
	html += '</table>';
	
	document.getElementById('secondary').innerHTML = html; //'Page that will allow individual users to view and set their own proxies when appropriate.';
	Page.hideCitations();
}



Page.createproxy_html = function(selected_username, print_back_button)
{
	var html = '';
	if(print_back_button) html += Page.print_user_back_button(); 
	html += '<center>Create Proxy</center>';
	html += '<br />';
	html += '<form name="createproxy" onkeyup="Page.checkproxy_request();">';
	html += '<table style="border: 2px solid #7D110C;"><th></th><th></th><th></th>';
	html += '<tr><td></td><td>';
	html += '<input type="radio" id="account_type1" name="account_type" value="ads" onclick="Page.checkproxy_request();" checked="checked">';
	html += '<label for="account_type1">ADS</label>';
	html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<input type="radio" id="account_type2" name="account_type" value="guest" onclick="Page.checkproxy_request();">';
	html += '<label for="account_type2">GUEST</label>';
	html += '</td><td></td><tr>';
	html += '<tr><td>Last Name</td><td>' + Page.printTextBox('proxy_lastname', '', '30') + '</td><td>(starts with)</td><tr>';
	html += '<tr><td>First Name</td><td>' + Page.printTextBox('proxy_firstname', '', '30') + '</td><td></td><tr>';
	html += '<tr><td></td><td><center>OR</center></td><td></td></tr>';
	html += '<tr><td>Username</td><td>' + Page.printTextBox('proxy_username', '', '20')	+ '</td><td></td><tr>';															  
	html += '<tr><td></td><td><p><input type="button" onclick="Page.checkproxy_request();" value="Search">&nbsp;&nbsp;';
	html += '<input type="button" onclick="Page.clear_createproxyfrm();" value="Clear">&nbsp;&nbsp;';
	html += '<input type="button" onclick="Page.createproxy_request(';
	html += 'Page.getCheckedValue(document.getElementsByName(\'account_type\')),';
	html += 'document.getElementById(\'proxy_username\').value,';
	html += 'document.getElementById(\'proxy_firstname\').value,';
	html += 'document.getElementById(\'proxy_lastname\').value, \'' + selected_username + '\');" value="Create"></p>';
	html += '</td></tr>';
	html += '</table>';
	html += '</form>';
	html += '<p></p><div id="search_result"></div>';
	return html;
}

Page.createuser_html = function(selected_username)
{
	var html = '';
	html += Page.print_user_back_button(); 
	html += '<center>Create User</center>';
	html += '<br />';
	html += '<form name="createproxy" onkeyup="Page.checkproxy_request();">';
	html += '<table style="border: 2px solid #7D110C;"><th></th><th></th><th></th>';
	html += '<tr><td></td><td>';
	html += '<input type="radio" id="account_type1" name="account_type" value="ads" onclick="Page.checkproxy_request();" checked="checked">';
	html += '<label for="account_type1">ADS</label>';
	html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<input type="radio" id="account_type2" name="account_type" value="guest" onclick="Page.checkproxy_request();">';
	html += '<label for="account_type2">GUEST</label>';
	html += '</td><td></td><tr>';
	html += '<tr><td>Last Name</td><td>' + Page.printTextBox('proxy_lastname', '', '30') + '</td><td>(starts with)</td><tr>';
	html += '<tr><td>First Name</td><td>' + Page.printTextBox('proxy_firstname', '', '30') + '</td><td></td><tr>';
	html += '<tr><td></td><td><center>OR</center></td><td></td></tr>';
	html += '<tr><td>Username</td><td>' + Page.printTextBox('proxy_username', '', '20')	+ '</td><td></td><tr>';	
	
	html += '<tr><td>Options</td><td>';
	html += '<input type="checkbox" id="option_admin" name="option_admin" value="admin"><label for="option_admin">Admin</label>&nbsp;&nbsp;';
	html += '<input type="checkbox" id="option_cogs" name="option_cogs" value="cogs"><label for="option_cogs">Cogs</label>&nbsp;&nbsp;';
	html += '<input type="checkbox" id="option_self_proxy" name="option_self_proxy" value="self_proxy" checked disabled><label for="option_self_proxy">Proxy to self</label>&nbsp;&nbsp;';
	html += '</td><td></td><tr>';
	
	html += '<tr><td></td><td><p><input type="button" onclick="Page.checkproxy_request();" value="Search">&nbsp;&nbsp;';
	html += '<input type="button" onclick="Page.clear_createproxyfrm();" value="Clear">&nbsp;&nbsp;';
	html += '<input type="button" onclick="Page.createuser_request(';
	html += 'Page.getCheckedValue(document.getElementsByName(\'account_type\')),';
	html += 'document.getElementById(\'proxy_username\').value,';
	html += 'document.getElementById(\'proxy_firstname\').value,';
	html += 'document.getElementById(\'proxy_lastname\').value, \'' + selected_username + '\');" value="Create"></p>';
	html += '</td></tr>';
	html += '</table>';
	html += '</form>';
	html += '<p></p><div id="search_result"></div>';
	return html;
}

Page.manageproxy_html = function(selected_username, proxies) // Submitter is currently the user being viewed.
{
	var html = '';

	html += '<p><center>Managing user: <b>' + selected_username + '</b></center></p><br />';

	var count = proxies.length;
	html += '<p><center>Current Proxies:</center></p><br />';
	html += '<table style="border: 2px solid #7D110C; width:25%; text-align:left"><th>Count</th><th>id</th>';
	html += '<th>Firstname</th><th>Lastname</th><th>Username</th><th>Edit</th><th>Delete</th>';
	var toggle = true;
	var highlight1 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#DDDDDD\'" bgcolor="#DDDDDD"';
	var highlight2 = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#FFFFFF\'" bgcolor="#FFFFFF"';
	
	for(var i = 0; i < count; i++){
		var current_id = proxies[i].id;
		if(toggle){
			html += '<tr id="proxy_'+current_id+'" '+highlight1+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		else{
			html += '<tr id="proxy_'+current_id+'" '+highlight2+'><td>' + (i + 1) + '</td>';
			toggle = !toggle;
		}
		html += '<td>'+proxies[i].id+'</td>';
		html += '<td>'+proxies[i].firstname+'</td>';
		html += '<td>'+proxies[i].lastname+'</td>';
		html += '<td>'+proxies[i].username+'</td>';
		html += '<td><a href="#" onclick="Page.editinputs(this.parentNode, \''+ current_id +'\');" />Edit</a></td>';
		html += '<td><a href="#" onclick="Page.deleteproxy_request(\''+current_id+'\',\'' + selected_username+'\');">Delete</a></td></tr>';
	}
	
	html += '</table>';
	
	html += '<br />';
	
	html += Page.createproxy_html(selected_username, false);
	
	return html;
}

Page.manageproxy_request = function(selected_username, submitter)
{
	var jsonStr = '{"request": {"type": "manage", "submitter": "' + selected_username + '"}}';
	Ajax.SendJSON('services/proxy.php', Page.manageproxy_response, jsonStr);
}

Page.manageproxy_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		if(responseObj.manageproxy != undefined)
		{
			var html = '';
			html += '<input type="button" value="Back to manage users" onclick="Page.viewuser_request(Page.submitter);" /><br><br>'; //Page.inputMethod(8);
			html += Page.manageproxy_html(responseObj.submitter, responseObj.manageproxy);
			document.getElementById('secondary').innerHTML = html;
		}
		
	}
}

Page.adminPage = function()
{
	Page.hideCitations();
	
	Page.user_back_button_state = 'admin';
	
	var html = '';
	html += '<div id="user_div"></div>';
	
	// Collections Table Management
	html += '<br><br><table align="center" style="border: 2px solid #7D110C;">';
	html += '<tr><td align="center"><b>Collections Table Management</b></td></tr>';
    html += '<tr><td>';
	html += '<div id="populate_div">'
	html += '&nbsp;&nbsp;';
	html += '<b>1.</b>&nbsp;<input type="button" onclick="Page.truncateCollectionsTable_request();" value="Truncate" name="truncate_button"/>&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<b>2.</b>&nbsp;<input type="button" onclick="Page.populateCollectionsTable_request();" value="Populate" name="populate_button"/>&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<b>3.</b>&nbsp;<input type="button" onclick="Page.createMiscCollectionsTable_request();" value="Create Misc" name="create_misc_button"/>&nbsp;&nbsp;';
	html += '</div>';
	html += '</td></tr>';
	html += '</table>';
	
	// Similar Citations Management
	html += '<br><br><table align="center" style="border: 2px solid #7D110C;">';
	html += '<tr><td align="center"><b>Similar Citations Management</b></td></tr>';
    html += '<tr><td>';
	html += '<div id="populate_div">'
	html += '&nbsp;&nbsp;';
	html += '<b>1.</b>&nbsp;<input type="button" onclick="Page.truncateSimilarTo_request();" value="Truncate" name="truncate_similar_button"/>&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<b>2.</b>&nbsp;<input type="button" onclick="Page.populateSimilarTo_request();" value="Populate" name="populate_similar_button" disabled/>&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<b>3.</b>&nbsp;<input type="button" onclick="Page.updateSimilarTo_byID_request();" value="Update By ID" name="update_similar_by_id_button"/>&nbsp;&nbsp;';
	html += '<b>:</b>&nbsp;<input type="text" id="update_similar_by_id_text" name="update_similar_by_id_text"/>&nbsp;&nbsp;';
	html += '</div>';
	html += '</td></tr>';
	html += '</table>';
	
	html += '<br><br><table align="center" style="border: 2px solid #7D110C;">';
	html += '<tr><td align="center"><b>Feedback</b></td></tr>';
    html += '<tr><td>';
	html += '<div id="feedback_div"></div>';
	html += '</td></tr>';
	html += '</table>';
	document.getElementById('secondary').innerHTML = html;
	
	html = '';
	html += '<table align="center" style="border: 2px solid #7D110C;">';
	html += '<tr><td width="10">&nbsp;</td><td><b>User management</b></td></tr>';
    html += '<tr><td></td><td>';
 	html += '<table border="0">';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(16)" value="16" name="input_method"/></td><td align="left">Create User</td></tr>';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(6)" value="6" name="input_method"/></td><td align="left">Create Proxy</td></tr>';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(8)" value="8" name="input_method"/></td><td align="left">View / Manage All Users</td></tr>';
    html += '<tr><td><input type="radio" onclick="Page.inputMethod(7)" value="7" name="input_method"/></td><td align="left">View / Manage Current Proxies</td>';
	html += '</table>';
	html += '</td></tr>';
	html += '<tr><td></td><td>';
	html += '</td></tr>';
	html += '</table>';
	document.getElementById('user_div').innerHTML = html;
	Page.get_feedback_request();
}