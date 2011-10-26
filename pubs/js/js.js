function getNextHighestZindex(obj){  
	var highestIndex = 0;   
	var currentIndex = 0;   
	var elArray = Array();   
	if(obj){ elArray = obj.getElementsByTagName('*'); }else{ elArray = document.getElementsByTagName('*'); }  
	for(var i=0; i < elArray.length; i++){  
	if (elArray[i].currentStyle){  
		currentIndex = parseFloat(elArray[i].currentStyle['zIndex']);  
	}else if(window.getComputedStyle){  
		currentIndex = parseFloat(document.defaultView.getComputedStyle(elArray[i],null).getPropertyValue('z-index'));  
	}  
	if(!isNaN(currentIndex) && currentIndex > highestIndex){ highestIndex = currentIndex; }  
	}  
	return(highestIndex+1);  
}

function inViewPort(el)
{ 
	var Dom = YAHOO.util.Dom;
	var y = Dom.getY(el);
	var top = (document.documentElement.scrollTop ?	document.documentElement.scrollTop : document.body.scrollTop);
	var vpH = Dom.getViewportHeight();
	var coverage = parseInt(vpH + top);
	if ( coverage >= y ) {
		return true; // in view
	}
	else {
		return false; // not in view
	}
}

function moveWindow(anchor_id) 
{	
	if(inViewPort(document.getElementById(anchor_id)) == true) {
		// Do not move window.
	}
	else {
		document.getElementById(anchor_id).scrollIntoView(true);
	}
}

function scrollToTop()
{
	window.scrollTo(0,0);
}

function clearAllRadios(radioName) {
	var radiosList = document.getElementsByName(radioName);
	for (var i = 0; i < radiosList.length; i++) {
		if(radiosList[i].checked) radiosList[i].checked = false;
	}
}

// Loading Using Panel
function startUploadPanel()
{
	if(Page.panel4) Page.panel4.show(); // Show loading panel
	if(Page.panel1) Page.panel1.hide(); // Hide panel1
    return true;
}

function startUpload2()
{
	var cur_loading_div = document.getElementById('loading_div'+Page.upload_citation_suffix);
	var cur_label_div = document.getElementById('label_div'+Page.upload_citation_suffix);
	//var cur_status_div = document.getElementById('status_div'+Page.upload_citation_suffix);
	if(cur_label_div) cur_label_div.style.display = 'none';
	if(cur_loading_div) cur_loading_div.style.height = '35px';
	if(cur_loading_div) cur_loading_div.style.visibility = 'visible';
	return true;
}

function stopUpload(success, filename)
{
	if(Page.panel4) Page.panel4.hide(); // Hide loading panel
	var html = '';
	if (success == 1) {
		// parser.php will call appropriate functions.
	}
	else if (success == 2) {
		html = '<div class="e_panel_msg">Invalid file type!<br/><br/>';
		html += '<input type="button" value="Ok" onclick="Page.panel1.hide();" /></div>'; // Ok button
	}
	else if (success == 3) {
		html = '<div class="e_panel_msg">File is too big!<br/><br/>';
		html += '<input class="e_panel_msg" type="button" value="Ok" onclick="Page.panel1.hide();" /></div>'; // Ok button
	}
	else{
		html = '<div class="e_panel_msg">There was an error during file upload!<br/><br/>';
		html += '<input class="e_panel_msg" type="button" value="Ok" onclick="Page.panel1.hide();" /></div>'; // Ok button
	}
	
	if(html != '') {		// Show panel1 for error message.
		Page.panel1.setBody(html);
		Page.panel1.show();
	}
	
	var f1_upload_form = 'Please upload a text (*.txt) file only!<br/><br/>';
	f1_upload_form += '<label>File:  ';
	f1_upload_form += '<input name="myfile" type="file" size="30" onchange="document.uploadForm.submitBtn.disabled=false"/>';
	f1_upload_form += '</label>';
	f1_upload_form += '<label>';
	f1_upload_form += '<input type="submit" name="submitBtn" class="sbtn" value="Upload" disabled="true" />';
	f1_upload_form += '</label><br />';
	f1_upload_form += '<label>(File size limit: 10 MB.)</label>';
	
	document.getElementById('f1_upload_form').innerHTML = f1_upload_form;
	return true;   
}

function stopUpload2(success, filename, elements)
{
	var responseObj = eval("(" + elements + ")");
	var result = '';
	
	if (success == 1){
		//var currentDirectory = unescape(document.URL.substring(0,(document.URL.lastIndexOf("/")) + 1));
		//html += '<b>Attached file:</b> <a href="' + currentDirectory + 'pdfs/temp/' + attached_filename_obj.value + '" target="_blank">' + attached_filename_obj.value + '</a>' + clear_attached_file_html + '<br>';
		result = 'The file <b>' + filename + '</b> was attached successfully.<br/>To save the file, click on "Save".';
		
		document.getElementById('attached_filename'+responseObj['citation_suffix']).value = filename;
		document.getElementById('attached_filename').value = filename;
		
		//alert('stopUpload2 [' + 'attached_filename'+responseObj['citation_suffix'] + ']: ' + document.getElementById('attached_filename'+responseObj['citation_suffix']).value);
	}
	else if (success == 2){
		result = 'Invalid file type!';
	}
	else if (success == 3){
		result = 'File is too big!';
	}
	else{
		result = 'There was an error during file upload!';
	}
	
	var upload_html = Page.printFileUploadDiv(responseObj['citation_id'], responseObj['citation_suffix'], result);
	document.getElementById(responseObj['upload_div']).innerHTML = upload_html;
  
	return true;   
}

function initializeAutocompleteFields(citation_suffix) {
	initializeAutocomplete('title'+citation_suffix);  
	initializeAutocomplete('journal'+citation_suffix);
	initializeAutocomplete('publisher'+citation_suffix);
	
	
	var tempPubtype = document.getElementById('pubtype'+citation_suffix).value;
	if (tempPubtype == 'inbook')
	{
		initializeAutocomplete('editor'+citation_suffix);
	}

	initializeAutocompleteItemSelect('author0ln'+citation_suffix);
	initializeAutocompleteItemSelect('author1ln'+citation_suffix);
	initializeAutocompleteItemSelect('author2ln'+citation_suffix);
	initializeAutocompleteItemSelect('author3ln'+citation_suffix);
	initializeAutocompleteItemSelect('author4ln'+citation_suffix);
	initializeAutocompleteItemSelect('author5ln'+citation_suffix);
	
	initializeAutocomplete('author0fn'+citation_suffix);
	initializeAutocomplete('author1fn'+citation_suffix);
	initializeAutocomplete('author2fn'+citation_suffix);
	initializeAutocomplete('author3fn'+citation_suffix);
	initializeAutocomplete('author4fn'+citation_suffix);
	initializeAutocomplete('author5fn'+citation_suffix);
}

// AutoComplete
function initializeAutocomplete(name) {
	//might not need whole element list
	var ele_list = ["pubtype", "title", "journal", "year", "booktitle", "chapter", "crossref", "publisher", "howpublished", "location", "institution", "organization", "school", "editor", "volume", "number", "pages", "month", "note", "series", "address", "edition", "url", "author0fn", "author0ln", "author1fn", "author1ln", "author2fn", "author2ln", "author3fn", "author3ln", "author4fn", "author4ln", "author5fn", "author5ln", "citation_id"];
	var DS = new YAHOO.widget.DS_XHR(Page.document_root+"services/autocomplete.php", ["citation", "theMenuItem"].concat(ele_list)); 
	DS.responseType = YAHOO.widget.DS_XHR.TYPE_XML;   
	var auto = new YAHOO.widget.AutoComplete(name, "autocomplete_" + name, DS);  

	var tempPubtype = "";
	var tempAuthorLN = "";
	var tempBooktitle = "";
	var tempYear = "";
	
	var citation_suffix = get_citation_suffix(name);
	if (citation_suffix.indexOf("_b") >= 0)
	{
		name = name.substring(0, name.length-3);
	}
	else if (citation_suffix == "_a")
	{
	//	citation_suffix = name.substr(name.length-2, 2);
		name = name.substring(0, name.length-2);
	}

	tempPubtype = document.getElementById('pubtype'+citation_suffix).value;

	
	auto.generateRequest = function(sQuery) {  
		if (name == 'author0fn') {tempAuthorLN = document.getElementById('author0ln'+citation_suffix).value;}
		else if (name == 'author1fn') {tempAuthorLN = document.getElementById('author1ln'+citation_suffix).value;}
		else if (name == 'author2fn') {tempAuthorLN = document.getElementById('author2ln'+citation_suffix).value;}
		else if (name == 'author3fn') {tempAuthorLN = document.getElementById('author3ln'+citation_suffix).value;}
		else if (name == 'author4fn') {tempAuthorLN = document.getElementById('author4ln'+citation_suffix).value;}
		else if (name == 'author5fn') {tempAuthorLN = document.getElementById('author5ln'+citation_suffix).value;}
		else if (name == 'editor') 
			{
				tempBooktitle = document.getElementById('booktitle'+citation_suffix).value;
				tempYear = document.getElementById('year'+citation_suffix).value;  
			} 
		
		return "?field=" + name + "&query=" + sQuery + "&pubtype=" + tempPubtype + "&authorLNVal=" + tempAuthorLN + "&booktitle=" + tempBooktitle + "&year=" + tempYear;  
	};   
	
	/*if(name == 'editor') 
	{
		YAHOO.util.Event.onContentReady("autocomplete_" + name, function () {	
			auto.textboxFocusEvent.subscribe(
				function(oSelf, oAutoComplete){
					alert('focus');
			//		oAutoComplete[0].sendQuery(oAutoComplete[0]['_elTextbox'].value);
					oAutoComplete[0].sendQuery('x'); // sending 'x' to trigger autocomplete send query
				}
			);
			auto.textboxChangeEvent.subscribe(
				function(oSelf, oAutoComplete){
					alert('change value and lose focus');
				}
			);
		}, auto);
		
		
	}*/
	
}

function initializeAutocompleteCollectionNames(name) 
{
	var DS = new YAHOO.widget.DS_XHR(Page.document_root+"services/autocomplete.php", ["citation", "theMenuItem"]); 
	DS.responseType = YAHOO.widget.DS_XHR.TYPE_XML;   
	var auto = new YAHOO.widget.AutoComplete(name, "autocomplete_" + name, DS);  
	
	auto.generateRequest = function(sQuery) {  
		return "?field=collection_name&query=" + sQuery + "&owner=" + Page.owner;  
	};  
}

function initializeAutocompleteItemSelect(name) {
YAHOO.example.ItemSelectHandler = function() {
    // Use a LocalDataSource
    //var oDS = new YAHOO.util.LocalDataSource(YAHOO.example.Data.accounts);
    //oDS.responseSchema = {fields : ["name", "id"]};
	var ele_list = ["pubtype", "title", "journal", "year", "booktitle", "chapter", "crossref", "publisher", "howpublished", "location", "institution", "organization", "school", "editor", "volume", "number", "pages", "month", "note", "series", "address", "edition", "url", "author0id", "author0fn","author0ln", "author1id", "author1fn", "author1ln", "author2id", "author2fn", "author2ln", "author3id", "author3fn", "author3ln", "author4id", "author4fn", "author4ln", "author5id", "author5fn", "author5ln", "citation_id", "raw", "format"];

	var DS = new YAHOO.widget.DS_XHR(Page.document_root+"services/autocomplete.php", ["citation", "theMenuItem"].concat(ele_list));  

	DS.responseType = YAHOO.widget.DS_XHR.TYPE_XML;   

 	var auto = new YAHOO.widget.AutoComplete(name, "autocomplete_" + name, DS);  
	auto.maxResultsDisplayed = 200;
//	auto.forceSelection = true;
	auto.doBeforeExpandContainer = function(oTextbox, oContainer, sQuery, aResults) { 
			YAHOO.util.Dom.setStyle(oContainer, "width", "50em");
	        return true; 
	    }; 
		
	var tempPubtype = "";
	
	var citation_suffix = get_citation_suffix(name);

	if (citation_suffix.indexOf("_b") >= 0)
	{
	//	citation_suffix = name.substr(name.length-3, 3);
		name = name.substring(0, name.length-3);
	}
	else if (citation_suffix == "_a")
	{
	//	citation_suffix = name.substr(name.length-2, 2);
		name = name.substring(0, name.length-2);
	}

/*	var citation_suffix = name.substr(name.length-2, 2);
	
	if ((citation_suffix == "_a") || (citation_suffix.indexOf("_b") >= 0))
	{
		name = name.substring(0, name.length-2);
		tempPubtype = document.getElementById('pubtype'+citation_suffix).value;
	}
	else 
	{
		citation_suffix = "";
		tempPubtype = document.getElementById('pubtype').value;
	}*/
	
	auto.generateRequest = function(sQuery) {  
		return "?field=" + name + "&query=" + sQuery + "&pubtype=" + tempPubtype;  
	}; 
	
    
    // Define an event handler to populate a hidden form field
    // when an item gets selected
 //   var myHiddenField = YAHOO.util.Dom.get("myHidden");
    var myHandler = function(sType, aArgs) {
    //    var myAC = aArgs[0]; // reference back to the AC instance
    //   var elLI = aArgs[1]; // reference to the selected LI element
        var oData = aArgs[2]; // object literal of selected item's result data
		
		if ((name == "author0ln") || (name == "author1ln") || (name == "author2ln") || (name == "author3ln") || (name == "author4ln") || (name == "author5ln")) 
		{
			if (oData[0].substring(0,4) == "----") 
			{	
				for (var i=0; i < ele_list.length; i++) 
				{
					if (document.getElementById(ele_list[i]+citation_suffix)) {
						document.getElementById(ele_list[i]+citation_suffix).value = oData[i+1]; 
					}
				}
			}
			else
			{
				for (var i=0; i < ele_list.length; i++) 
				{
					var tempfn = 'author' + name.substring(6,7) + 'fn';
					var tempid = 'author' + name.substring(6,7) + 'id';
					if ((document.getElementById(ele_list[i]+citation_suffix)) && ((ele_list[i] == name) || (ele_list[i] == tempfn) || (ele_list[i] == tempid))) {
							document.getElementById(ele_list[i]+citation_suffix).value = oData[i+1]; 
					}
				}
			}

		}

    };
    auto.itemSelectEvent.subscribe(myHandler);
	
	var myHandler2 = function(sType, aArgs) {
		var tempid = 'author' + auto.getInputEl().name.substring(6,7) + 'id' + citation_suffix;
		document.getElementById(tempid).value = "";
	}
		 
		 
	auto.unmatchedItemSelectEvent.subscribe(myHandler2);
	
}();


}

function get_citation_suffix(name)
{
	var citation_suffix = "";
	if (name.indexOf("_a") >= 0)
	{
		citation_suffix = "_a";
	}
	else if (name.indexOf("_b") >= 0)
	{
		citation_suffix = name.substr(name.length-3, 3);
	}
	return citation_suffix;
}

function buildContextMenuLists(the_target)
{
	var full_citation_suffix = "";
	var pubtype = "";
	if (the_target.indexOf("raw") >= 0)//((the_target == "raw") || (the_target == "raw_a0...") || (the_target == "raw_b0..."))
		{
			if (the_target.indexOf("_") >= 0)
			{
				full_citation_suffix = Page.get_citation_suffix_of_active_tab(tabView_b);
				pubtype = document.getElementById('pubtype'+full_citation_suffix).value; //For putting value into a b similar tab		
			}
			else
			{
				pubtype = document.getElementById('pubtype').value;
			}
			
		var all_list = ["Title","Year"];
		var ele_list = ["title"+full_citation_suffix,"year"+full_citation_suffix];
	
		var author_list = ["Author 1 Lastname","Author 2 Lastname","Author 3 Lastname","Author 4 Lastname","Author 5 Lastname","Author 6 Lastname"];
		var auth_ele_list = ["author0ln"+full_citation_suffix,"author1ln"+full_citation_suffix,"author2ln"+full_citation_suffix,"author3ln"+full_citation_suffix,"author4ln"+full_citation_suffix,"author5ln"+full_citation_suffix];
		
		all_list = all_list.concat(author_list);
		ele_list = ele_list.concat(auth_ele_list);
		
		var labels = new Array();
		var ids = new Array();
		// Build all_list and ele_list based on pubtype.
		for(var i in Page.pubtypes_json[pubtype].fields)
		{
			var id = Page.pubtypes_json[pubtype].fields[i];
			if(Page.fields_arr[id] != undefined) 
			{
				labels[labels.length] = Page.fields_arr[id][0];
				ids[ids.length] = id+full_citation_suffix; 
			}
		}
		
		all_list = all_list.concat(labels);
		ele_list = ele_list.concat(ids);
		
		return new Array(all_list, ele_list);
	}
	else
	{
		return new Array([],[]);
	}
}


function initializeContextMenu(the_panel)
{		
	var all_list;
	var ele_list;
		
	var yahooContextMenu = the_panel;
	var contextmenu = "contextmenu"+the_panel;
	
	YAHOO.util.Event.onContentReady(yahooContextMenu, function () {
																		
																		try{
        																	oContextMenu.destroy();
        																}
    																	catch(e){}
																		
																		var oContextMenu = new YAHOO.widget.ContextMenu(contextmenu, { 	
																			trigger: yahooContextMenu, 
																			lazyload: false,
																		//	zindex:10000
																		});
																		
																		oContextMenu.render(yahooContextMenu);
																		oContextMenu.clickEvent.subscribe(onContextMenuClick, yahooContextMenu);
																		oContextMenu.subscribe("beforeShow", onContextMenuBeforeShow);
															
																	});
	
	var lists;
	function onContextMenuBeforeShow(p_sType, p_aArgs) {
		lists = buildContextMenuLists(this.contextEventTarget.id);
		all_list = lists[0];
		ele_list = lists[1];
		this.clearContent();
		if (all_list.length != 0)
		{
        	this.addItems(all_list);
			this.render();
		}
	}
	
	// Call back function
	var onContextMenuClick = function(p_sType, p_aArgs) {	
		var task = p_aArgs[1]; 
		document.getElementById(ele_list[task.index]).value = returnSelectionText(this.contextEventTarget.id);	
	};
	
	
	
}

function returnSelectionText(html_element) {	
	var obj = document.getElementById(html_element);
	var startPos = obj.selectionStart;
	var endPos = obj.selectionEnd;
	var doc = document.selection;

	if (doc && doc.createRange().text.length != 0) {  //IE
		return doc.createRange().text;
	}
	else if (!doc && obj.value.substring(startPos,endPos).length != 0) {  // Mozilla & Safari
		return obj.value.substring(startPos,endPos);
	}
	else {
		alert("Empty");
		//return "Error";  // Return empty string instead of "Error" string.
		return "";
	}
}


function print_r(theObj){
  if(theObj.constructor == Array ||
     theObj.constructor == Object){
    document.write("<ul>")
    for(var p in theObj){
      if(theObj[p].constructor == Array||
         theObj[p].constructor == Object){
document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
        document.write("<ul>")
        print_r(theObj[p]);
        document.write("</ul>")
      } else {
document.write("<li>["+p+"] => "+theObj[p]+"</li>");
      }
    }
    document.write("</ul>")
  }
}


var disableSubmit = false;
function enter_pressed(e){
	var keycode;
	if (window.event) keycode = window.event.keyCode;
	else if (e) keycode = e.which;
	else return false;
	
	if(keycode == 13)
	{
		if(disableSubmit) return false;
		else return true;
	}
	else return false;
}

function trim(stringToTrim) {
	return stringToTrim.replace(/^\s+|\s+$/g,"");
}
function ltrim(stringToTrim) {
	return stringToTrim.replace(/^\s+/,"");
}
function rtrim(stringToTrim) {
	return stringToTrim.replace(/\s+$/,"");
}

function checkFirstnameFormat(str)
{
	var initials_pattern = /([A-Z][.][ ]*)+/;  	// 1 or more of initial pattern.
	str = trim(str);
	var str_array = str.split(' ');
	if (str_array[0].length > 0)
	{
		return str_array[0].match(initials_pattern) // match() returns true on a match on any part of string.		
	}
	else
	{
		return false;
	}
}

function initializeTabFramework()
{
	YAHOO.widget.Tab.prototype.ACTIVE_TITLE = ''; 
	var container_tabs_a = "tabs_container_a";

	var arrow_tab = function(tabView, label_arg) {
		var newTab = new YAHOO.widget.Tab({
			label: label_arg,
			content: '<div style="height:500px"></div>',
			active: false,
		});
	
		// Add listener to tab.
		var pass_obj = [tabView, newTab];	
		YAHOO.util.Event.on(newTab.get('labelEl'), 'click', arrowTabActiveChange_handler, pass_obj);
		return newTab;
	}
	
	YAHOO.util.Event.onContentReady(container_tabs_a, function() {
    	tabView_a = new YAHOO.widget.TabView();
    	tabView_a.appendTo(container_tabs_a);
		
		tabView_a.addTab(arrow_tab(tabView_a, '<<'));
		tabView_a.addTab(arrow_tab(tabView_a, '>>'));
		
		tabView_a.getTab(0).setStyle('display', 'none'); //hide
		tabView_a.getTab(1).setStyle('display', 'none'); //hide
	});
	
	var container_tabs_b = "tabs_container_b";
	YAHOO.util.Event.onContentReady(container_tabs_b, function() {
    	tabView_b = new YAHOO.widget.TabView();
    	tabView_b.appendTo(container_tabs_b);
		
		tabView_b.addTab(arrow_tab(tabView_b, '<<'));
		tabView_b.addTab(arrow_tab(tabView_b, '>>'));
		
		tabView_b.getTab(0).setStyle('display', 'none'); //hide
		tabView_b.getTab(1).setStyle('display', 'none'); //hide
	});
}

function arrowTabActiveChange_handler(e, pass_obj) 
{
	YAHOO.util.Event.stopEvent(e);			// Convenience method for stopPropagation + preventDefault. (With this, we no longer need "beforeActiveTabChange")
	
	var tabView = pass_obj[0];
	var cur_tab = pass_obj[1];
		
	var label = cur_tab.get('label');
	var cur_index = tabView.getTabIndex(cur_tab);
	var total_tabs = tabView.get('tabs').length-2; 	// minus << and >>
	var prevIndex = tabView.get('activeIndex'); 	// because the active index of the tabView hasn't changed yet //tabView.getTabIndex(e.prevValue);
	
	
	if (label == '<<')
	{
		for (var j = prevIndex-1; j > 0; j--)  //diff
		{
			if (tabView.getTab(j).getStyle('display') == 'none') // find first hidden at beginning  //same
			{
				tabView.getTab(j).setStyle('display', ''); //show first hidden at beginning  //same
	
				if (j == 1)  // This check if there's no more tab on the left side
				{
					tabView.getTab(0).setStyle('display', 'none'); // hide
				}
				
				if (tabView.get('tabs').length-1 > j+Page.numViewableTabsLimit)  // Check for next tab is not null
				{
					tabView.getTab(j+Page.numViewableTabsLimit).setStyle('display', 'none'); // hide first shown at end
					
					if ((j+Page.numViewableTabsLimit) <= total_tabs)  //
					{
						tabView.getTab(tabView.get('tabs').length-1).setStyle('display', ''); // show >>
					}
					
					// Reassign activeIndex when it is at the left most viewable limit
					if (prevIndex == j+Page.numViewableTabsLimit)
					{
						tabView.set('activeIndex', prevIndex-1);  // Equivalent: tabView.selectTab(prevIndex-1);
					}
				}
									
				break;
			}
		}		
	}
	else if (label == '>>')
	{
		for (var j = prevIndex+1; j < tabView.get('tabs').length-1; j++)
		{
			if (tabView.getTab(j).getStyle('display') == 'none')  // find first hidden at end
			{
				tabView.getTab(j).setStyle('display', ''); // show first hidden at end
				
				if (j == total_tabs)
				{
					tabView.getTab(tabView.get('tabs').length-1).setStyle('display', 'none'); // hide >> if needed
				}
				
				tabView.getTab(j-Page.numViewableTabsLimit).setStyle('display', 'none'); // hide first shown at beginning
				tabView.getTab(0).setStyle('display', ''); // show <<
				
				// Reassign activeIndex when it is at the left most viewable limit
				if (prevIndex == j-Page.numViewableTabsLimit)
				{
					tabView.set('activeIndex', prevIndex+1);  // Equivalent: tabView.selectTab(prevIndex+1);
				}
									
				break;
			}
		}
	}	
	
	/*else
	{
		var cit_suffix, cit_suffix2;
		cit_suffix = Page.get_citation_suffix_from_label(label);
		
	//	var pubtype = document.getElementById('pubtype'+cit_suffix).value;

		if (cit_suffix.indexOf("_b") >= 0) 
		{
			cit_suffix2 = Page.get_citation_suffix_of_active_tab(tabView_a);
		}
		else
		{
			cit_suffix2 = Page.get_citation_suffix_of_active_tab(tabView_b);
		}
		
		if (Page.interval_value != '')
		{
			window.clearInterval(Page.interval_value);
		}
		var action_string = 'checkScroll(document.getElementById("scrollable_div' + cit_suffix+'"), document.getElementById("scrollable_div' + cit_suffix2 + '"))';
		Page.interval_value = window.setInterval(action_string, 1);
		if (!Page.movingTab)
		{
			Page.alignActiveTabs();	
		}
	}	*/
}

/*************/

Page.alignActiveTabs = function()
{		
	var divs_array = ["top_div","author_fields", "constantByPubtype_fields", "pubtype_div", "changingByPubtype_fields", "abstract_div", "keyword_div"]; //"abstract_keyword_div", "additional_fields"];
	
	// Check Panel2 visibility using YUI config property.
	if(Page.panel2.cfg.getProperty('visible') == false) {
		return false;
	}
	else
	{
	}
	// Doesnt work right now! 
	// TO-DO: Check if one of the tabs is empty and handle the sizes approriately
	// tabview empty() function?

	if (tabView_a.get('tabs').length <= 2 || tabView_b.get('tabs').length <= 2 || tabView_a.getTab(1).get('label') == '&nbsp;' || tabView_b.getTab(1).get('label') == '&nbsp;') {
		document.getElementById('scrollable_div_c').style.display = 'none'; 		// Hide
		return false;	
	}
	else
	{
		document.getElementById('scrollable_div_c').style.display = ''; 		// Show
	}

	var citation_suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	var citation_suffix_b = Page.get_citation_suffix_of_active_tab(tabView_b);

//	alignTabPaddingDivs(citation_suffix_a, citation_suffix_b, "tab_div_padding");

//	var temp = findPos(document.getElementById('author_fields'+citation_suffix_a))[1] - findPos(document.getElementById('top_div_buttons'))[1];

//document.getElementById('top_div_buttons').style.height = temp + 'px';

	var temp = findPos(document.getElementById('top_div'+citation_suffix_a))[1] - findPos(document.getElementById('top_div_buttons'))[1];
	
	document.getElementById('padding_div').style.height = temp + 'px';
	
	//temp = findPos(document.getElementById('author_fields'+citation_suffix_a))[1] - findPos(document.getElementById('top_div_buttons'))[1];

//document.getElementById('top_div_buttons').style.height = temp + 'px';
	
	printChangingByPubTypeButtonsTable(citation_suffix_a, citation_suffix_b);
	
	for(var i = 0; i < divs_array.length; i++) // element_array.length
	{
		var div_name_a = divs_array[i] + citation_suffix_a;
		var div_name_b = divs_array[i] + citation_suffix_b;
		var div_name_c = divs_array[i] + '_buttons';
		resizeContainerDivs(document.getElementById(div_name_a), document.getElementById(div_name_b), document.getElementById(div_name_c)); 
	}

	return true;
}

function printChangingByPubTypeButtonsTable(citation_suffix_a, citation_suffix_b)
{
	var html = '';
	var move_buttons_tt_array = Page.move_buttons_tt.cfg.getProperty("context");
	var pubtype_a = '';
	var pubtype_b = '';
	if (document.getElementById('pubtype'+citation_suffix_a))
	{
		pubtype_a = document.getElementById('pubtype'+citation_suffix_a).value;
	}
	else
	{
		return;
	}
	
	if (document.getElementById('pubtype'+citation_suffix_b))
	{
		pubtype_b = document.getElementById('pubtype'+citation_suffix_b).value;
	}
	else
	{
		return;
	}
	
	if  (pubtype_a == pubtype_b)
	{
		html += Page.printTableTop('changingByPubtype', 'buttons');
		
		for (var j in Page.pubtypes_json[pubtype_b].fields)
		{		
			var field_name = Page.pubtypes_json[pubtype_b].fields[j];
			if(Page.fields_arr[field_name] != undefined) { 
				html += Page.printOneMoveRightButton(field_name);
				move_buttons_tt_array.push(field_name + '_move_right_button');
			}
		}
		html += '</table>';
	}	
	else
	{
		for (var j in Page.pubtypes_json[pubtype_b].fields)
		{
			var field_name = Page.pubtypes_json[pubtype_b].fields[j];
			move_buttons_tt_array.push(field_name + '_move_right_button');
		}
	}
	document.getElementById('changingByPubtype_fields_buttons').innerHTML = html;
	Page.move_buttons_tt.cfg.setProperty("context", move_buttons_tt_array);
}

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
	//		curbottom += curtop + obj.offsetHeight;
		} while (obj = obj.offsetParent);
		return [curleft,curtop]; // return [ X, Y ];
	}
	return false; // Object / Element doesn't exist.
}

function alignTabPaddingDivs(citation_suffix_a, citation_suffix_b, tab_padding_div)
{	
	// First array of element used for padding.
	top_ele_a = document.getElementById(tab_padding_div+citation_suffix_a);
	top_ele_b = document.getElementById(tab_padding_div+citation_suffix_b);
	top_ele_c = document.getElementById(tab_padding_div);

	// Reset padding div
	top_ele_a.style.height = '0px';
	top_ele_b.style.height = '0px';
	top_ele_c.style.height = '0px';
	 
	// Get Y position of next_ele.
	var pos_b = findPos(top_ele_b)[1];
	var pos_a = findPos(top_ele_a)[1];
	var pos_c = findPos(top_ele_c)[1];	
		
	var max_ele_height = Math.max(pos_a,pos_b,pos_c);
	
	top_ele_a.style.height = (max_ele_height-pos_a)+'px';
	top_ele_b.style.height = (max_ele_height-pos_b)+'px';
	top_ele_c.style.height = (max_ele_height-pos_c)+'px';
		
	return true;
}

function resizeContainerDivs(div_a, div_b, div_c)
{
	div_a.style.height = div_b.style.height = div_c.style.height = 'auto';
	var max_div_height = Math.max(div_a.offsetHeight, div_b.offsetHeight) - 4 ;	// -4 is to account for the 2px border around the div
	div_a.style.height = div_b.style.height = div_c.style.height = max_div_height+'px';	
	return true;
}

var lastSeen = [0, 0];
function checkScroll(div1, div2) {
	
	if (!div1 || !div2 || !document.getElementById('scrollable_div_c')) 
	{
		return;
	}
	var control = null;
	
	if (div1.scrollTop != lastSeen[0]) 
	{
		control = div1;
	}
	else if (div2.scrollTop != lastSeen[1]) 
	{
		control = div2;
	}
	
	if (control == null) 
	{
		return;
	}
	else 
	{
		document.getElementById('scrollable_div_c').scrollTop = div1.scrollTop = div2.scrollTop = control.scrollTop;
	}
	lastSeen[0] = div1.scrollTop;
	lastSeen[1] = div2.scrollTop;
}













