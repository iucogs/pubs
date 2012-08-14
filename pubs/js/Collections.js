// Used by listCollections for merging.
Page.mergeSelectedCollections = function()
{
	var html = '';
	var selected = '';
	var merge_array = Page.getSelectedCheckBoxesCollectionIds();
	
	var collection_ids = merge_array[0];
	var collection_keys = merge_array[1];
	
	if(collection_ids.length < 2)
	{
		html += '<center>Please select at least two collections to merge. </center>'; 
	}
	else
	{	
		html += '<center>Select a name for the merged collection: <br><br>'; 
		html += '<select id="mergeCollectionsSelect" onchange="Page.clearNewNameTextbox();">';
		html += '<option value="none"></option>';
		for(var i in collection_ids)
		{ 
			var collection_name = Page.collections[collection_keys[i]].collection_name;
			var c_count = Page.collections[collection_keys[i]].count;
			if(i == 0) selected = 'selected'; else selected = "";
			
			html += '<option value="'+collection_ids[i]+'" ' + selected + '>' + Page.collections[collection_keys[i]].collection_name + ' &nbsp;(' + c_count + ')</option>';
		}
		html += '</select>';
		html += '<br /><br />';
		html += 'OR<br><br>';
		html += 'New name: <input type="text" id="mergeCollectionsTextbox" onkeyup="Page.clearSelectOptions();"><br><br>';
		html += '<input type="button" name="submitCollectionNameBut" value="Merge" onclick="Page.mergeCollectionsGetName();"></center>';
	}
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.clearSelectOptions = function()
{
	if (trim(document.getElementById('mergeCollectionsTextbox').value) != "")
	{
		document.getElementById('mergeCollectionsSelect').options[0].selected=true;
	}
}

Page.clearNewNameTextbox = function()
{
	document.getElementById('mergeCollectionsTextbox').value = "";
}

Page.mergeCollectionsGetName = function()
{
	var obj = document.getElementById('mergeCollectionsSelect'); 	
	var textbox_val = trim(document.getElementById('mergeCollectionsTextbox').value); 
	var new_name = '';
	if (textbox_val == "")
	{
		new_name = obj.options[obj.selectedIndex].value;
	}
	else
	{
		new_name = textbox_val;
	}
	
	if ((trim(new_name) == "") || (new_name == 'none'))
	{
		alert('Please enter a name for the collection');
	}
	else
	{
		Page.mergeCollections_request(new_name, Page.temp_collection_ids);
	}
}

Page.goToNewCollection = function(collection_id, newCollectionName) // Used for going to existing collection as well.
{
	Page.currentCollection = collection_id; 						// Can be "all", "unverified", "search"
	Page.currentCollection_name = newCollectionName;
	document.getElementById('secondary').style.display = 'none';//hide
	document.getElementById('citations').style.display = '';	//show
	Page.current_page=1; 
	Page.current_viewable_pages=new Array();
	Page.getCitationsGivenCollectionID();
//	Page.panel1_open == "added_citations";
	Page.panel1.hide();
}

Page.pageThroughCitations_request = function()
{
//	Page.current_page = 1;
//	Page.current_viewable_pages = new Array();
	document.getElementById('secondary').style.display = 'none';//hide
	document.getElementById('citations').style.display = '';	//show
	Page.getCitations(1,'getCitations_byTimestamp_all');
	Page.panel1.hide();
}

Page.pageThroughCitations_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.newly_added_citations = responseObj.citations;
		Page.currentTimestamp = Page.newly_added_citations[0]['entryTime'];
		Page.newly_added_similar_citations_array = responseObj.similar_citations_array;

		for (var i in Page.newly_added_similar_citations_array) {
			if (!Page.similar_citations_array[i])
			{
				Page.similar_citations_array[i] = Page.newly_added_similar_citations_array[i];
			}
		}
		
		Page.state = 2; // Paging through newly edited citations
		Page.current_newly_added_num = 0;

		if (Page.similar_citations_array[Page.newly_added_citations[0].citation_id])
		{
			Page.panel2.setHeader("Your new citation is on the left.");
	
			Page.editTwoCitations(new Array(Page.newly_added_citations[0]), Page.similar_citations_array[Page.newly_added_citations[0].citation_id]);
		}
		else
		{
			Page.current_newly_added_num = 0;
			Page.editOneCitation(-2);
		}
	}
}

Page.printCollectionNamesMenuForViewing = function() 
{
	var html = '<div id="collectionNamesMenuForViewing_div">';
	html += Page.printCollectionNamesMenuForViewing_helper();
	html += '</div>';
	return html;
}

// Used by Page.updateCollectionFromList() and Page.printCollectionNamesMenuForViewing(). Page.updateCollectionFromList() is called from Page.listCollections everytime.
Page.printCollectionNamesMenuForViewing_helper = function()  
{
	var html = '<select style="width:92%" name="collectionNamesMenuForViewing" id="collectionNamesMenuForViewing" onchange="Page.selectCollection(this);">';
	html += '<option value="none" id="none"'; 
	if ((Page.currentCollection == 'none') || (Page.currentCollection == 'search'))
	{
		html += ' selected';	
	}
	
	html += '></option>';
	
	html += '<option value="all" id="all"';
	if (Page.currentCollection == 'all')
	{
		html += ' selected';	
	}
	html += '>All My Citations ' + ' &nbsp;(' + Page.default_collections[0].count + ')' + '</option>';
	
	html += '<option value="unverified" id="unverified"';
	if (Page.currentCollection == 'unverified')
	{
		html += ' selected';
	}
	html += '>All My Unverified Citations ' + ' &nbsp;(' + Page.default_collections[1].count + ')' + '</option>';

	for (var i=0; i < Page.collections.length; i++) {
		html += '<option onmouseover="this.parentNode.title=\''+Page.collections[i].collection_name+'\'" ';
		html += 'value="' + Page.collections[i].collection_id + '" id="' + Page.collections[i].collection_id + '"';
		if (Page.currentCollection == Page.collections[i].collection_id)
		{
			html += ' selected';	
		}
	
		html += '>';

		html += 'Collection: ';
		
		// Check collection's name length
		var c_name = Page.collections[i].collection_name;
		var c_count = Page.collections[i].count;
		if(c_name.length > 60)
		{
			c_name = c_name.substr(0,60) + ' ... (' + c_count+ ')';
		}
		else
		{
			c_name = c_name + ' &nbsp;(' + c_count+ ')';	
		}
		
		html += c_name + '</option>';
	}
	html += '</select>';
	return html;
}

Page.selectCollection = function(theMenu)
{
	var selection = theMenu.options[theMenu.selectedIndex].value; 
	if(selection != "none") {
		Page.current_page=1;
		Page.current_citation_num = 0;
		Page.current_viewable_pages=new Array(); 
		Page.set_current_get_type(selection); 
		Page.currentCollection = selection; 
		Page.currentCollection_name = Page.getCollectionNameFromPage(selection);
		Page.getCitationsGivenCollectionID();
	}
}

Page.printInputOptionsMenu = function() 
{
	var html = '';
	html += '<select name="inputOptionsMenu" id="inputOptionsMenu" onchange="Page.citation_input_method = this.options[this.selectedIndex].value;">';
	html += '<option value="1" id="paste_citations"';
	if (Page.citation_input_method == '1')
	{
		html += ' selected';	
	}
	html += '">pasting APA or MLA citations into a field</option>';
	html += '<option value="2" id="upload_citations"';
	if (Page.citation_input_method == '2')
	{
		html += ' selected';	
	}
	html += '">uploading a textfile of APA or MLA citations</option>';
	html += '<option value="3" id="enter_citations_by_hand"';
	if (Page.citation_input_method == '3')
	{
		html += ' selected';	
	}
	html += '">entering citation information into fields by hand</option>';
	html += '</select>';
	html += '&nbsp;&nbsp;<input type="button" name="showEnterCitations" value="Go" onclick="Page.showEnterCitations();">';
	html += '<br>';
	
	if ((Page.currentCollection != "all") && (Page.currentCollection != "unverified") && (Page.currentCollection != "search") && (Page.currentCollection != "none"))
	{
		html += '<input type="checkbox" name="add_to_current_collection_cb" id="add_to_current_collection_cb" value="1">'; 
		html += 'Add new citation(s) to current collection <div style="display:inline;margin-left:7.5em;"></div>';
	}
	return html;
}

Page.showEnterCitations = function()
{
	var html = '<p>' + Page.printBackToCitationsButton() + '</p>';
	
	if (document.getElementById('inputOptionsMenu').value == "1")  //paste citations
	{
		Page.input_method = 1;
		Page.citation_input_method = 1;
		
		var temp_collection_name = "";
		if (document.getElementById('add_to_current_collection_cb')) 
		{
			if (Page.getCheckedValue(document.getElementById('add_to_current_collection_cb')) == 1)
			{
				temp_collection_name = Page.currentCollection_name;
			}
		}

		html += '<form name="textareaform" onsubmit="return false;" ';
		html += 'onKeyPress="if(enter_pressed(event)){Page.parseTextIntoCollection_request(Page.parseTextIntoCollection_response, textareaform.citationInput.value, \'new\', textareaform.entryName.value);}">';
		
		html += '<div align="left" id="myAutoComplete" style="vertical-align:middle; height:22px; z-index:' + Page.zCounter-- + '; width:50em" class="yui-ac">'; 
		html += 'Collection name:&nbsp;<input type="text" name="entryName" id="entryName" style="width:60%" value="' + temp_collection_name + '" class="yui-ac-input" autocomplete="off">';
		html += '<div align="left" style="vertical-align: middle; left: 0px; top: 22px; width:61%;" id="autocomplete_entryName" class="yui-ac-container"></div>';
		html += '</div><br>';
					
		html += '<textarea name="citationInput" id="citationInput" style="width:99%;height:300px;" onFocus="disableSubmit=true;" onBlur="disableSubmit=false;"></textarea>';

		html += '<p><input type="button" onclick="Page.parsetext(textareaform.citationInput.value);Page.parseTextIntoCollection_request(Page.parseTextIntoCollection_response, textareaform.citationInput.value, \'new\', textareaform.entryName.value);" value="Insert citation(s)"></p>';
		html += '</form>';
		
		document.getElementById('secondary').innerHTML = html;			// Set
		document.getElementById('citations').style.display = 'none';	// Hide
		document.getElementById('secondary').style.display = '';		// Show	
		Page.right_column_display('none');
		initializeAutocompleteCollectionNames("entryName");

	}
	else if (document.getElementById('inputOptionsMenu').value == "2") //upload citations
	{
		Page.input_method = 2;
		Page.citation_input_method = 2;
		
		var temp_collection_name = "";
		if (document.getElementById('add_to_current_collection_cb')) 
		{
			if (Page.getCheckedValue(document.getElementById('add_to_current_collection_cb')) == 1)
			{
				temp_collection_name = Page.currentCollection_name;
			}
		}
		
		html += '<form name="uploadForm" action="'+Page.document_root+'services/parser.php" method="post" enctype="multipart/form-data" target="upload_target" onsubmit="startUploadPanel();" >';
		//html += 'Collection name: <input type="text" name="entryName" size=30 value="' + temp_collection_name + '"><br><br>';
		html += '<div align="left" id="myAutoComplete" style="vertical-align:middle; height:22px; z-index:' + Page.zCounter-- + '; width:50em" class="yui-ac">'; 
		html += 'Collection name:&nbsp;<input type="text" name="entryName" id="entryName" style="width:60%" value="' + temp_collection_name + '" class="yui-ac-input" autocomplete="off">';
		html += '<div align="left" style="vertical-align: middle; left: 0px; top: 22px; width:61%;" id="autocomplete_entryName" class="yui-ac-container"></div>';
		html += '</div><br>';
		
		// Hidden input as arguments for parser.php during file upload.
		html += '<input type="hidden" name="parse_action" size=30 value="new">';
		html += '<input type="hidden" name="submitter" size=30 value="'+Page.submitter+'">';
		html += '<input type="hidden" name="owner" size=30 value="'+Page.owner+'">';
		
	    html += '<p id="f1_upload_form" align="left"><br/>';
		html += 'Please upload a text (*.txt) file only!<br/><br/>';
        html += '<label>File:  ';
        html += '<input name="myfile" type="file" size="30" onchange="document.uploadForm.submitBtn.disabled=false"/>';
        html += '</label>';
        html += '<label>';
        html += '<input type="submit" name="submitBtn" class="sbtn" value="Upload" disabled="true" />';
        html += '</label><br />';
		html += '<label>(File size limit: 10 MB.)</label>';
        html += '</p>';
        html += '<iframe id="upload_target" name="upload_target" src="#" style="width:200px;height:300px;border:1px solid #fff;"></iframe>';
		//html += '<iframe id="upload_target" name="upload_target" src="#" style="width:600px;height:800px;border:4px solid #3300CC"></iframe>';
        html += '</form>';
		
		document.getElementById('secondary').innerHTML = html;
		document.getElementById('citations').style.display = 'none';	// Hide
		document.getElementById('secondary').style.display = '';	// Show
		Page.right_column_display('none');
		initializeAutocompleteCollectionNames("entryName");
	}
	else if (document.getElementById('inputOptionsMenu').value == "3") //enter citations by hand
	{
		Page.input_method = 3;
		Page.citation_input_method = 3;
		// Reset variables
		Page.current_row_num = 0
	
		var responseText = '{"error": "0", "total_count": "", "citations":[{"citation_id":"-1","user_id":"","pubtype":"article","cit_key":"","abstract":"","keywords":"","doi":"","url":"","address":"","annote":"","author0ln":"","author0fn":"","author0id":"","author1ln":"","author1fn":"","author1id":"","author2ln":"","author2fn":"","author2id":"","author3ln":"","author3fn":"","author3id":"","author4ln":"","author4fn":"","author4id":"","author5ln":"","author5fn":"","author5id":"","booktitle":"","chapter":"","crossref":"","edition":"","editor":"","translator":"","howpublished":"","institution":"","journal":"","bibtex_key":"","month":"","note":"","number":"","organization":"","pages":"","publisher":"","location":"","school":"","series":"","title":"","type":"","volume":"","year":"","raw":"","verified":"","format":"","filename":"","submitter":"'+Page.submitter+'","owner":"'+Page.owner+'","entryTime":"0"}]}';
																																																																																																																																																																																																																																																															  		var responseObj = eval("(" + responseText + ")");

		Page._citations = responseObj.citations;
		
		Page.current_citation_num = 0; 
		Page.oneCitationInPanel(Page._citations[0], "", "new"); 
		Page.panel_open = 1; 
		document.getElementById('panel2_div').innerHTML = ''; 
		Page.panel2.show();
		document.getElementById('panel1_div').style.display = '';
		if (Page.getCheckedValue(document.getElementById('add_to_current_collection_cb')) == 1)
		{
			Page.savingCitationEnteredByHandIntoCollection = true;
		}
	}
}

//abhinav

Page.parsetext = function(txt) 
{
	var stringArray = new Array();
    	stringArray=txt.split("\n");
	
	//alert(stringArray[0]);
	//alert(stringArray[1]);
	//alert(stringArray[2]);
	
	var tempArray =new Array();
	var flag1=0;
	var flag2=0;
		
	for(var i=0;i < stringArray.length -1; i++)
	{
		if(flag1==1)
			i=i+1;
			//alert(stringArray[i]);
		var count1= stringArray[i].split('(').length-1;
		var count2= stringArray[i].split(')').length-1;
		
		if (count1!=0 && count1 != count2)
		{
			//alert(1111);
		tempArray[i]=stringArray[i].concat(stringArray[i+1]);
		flag1=1;
		flag2=1;
		}
		else{
		tempArray[i]=stringArray[i];
		flag1=0;
		}
			
	}
	if(flag2==1)
	document.getElementById("citationInput").value = tempArray.join("\n");

//document.getElementById("citationInput").value=txt.replace(/(\r\n|\n|\r)/gm," ");
}



Page.set_current_get_type = function(type)
{
	if (type == 'all')
	{
		Page.current_get_type = 'getCitations_byFac_all';
	}
	else if (type == 'unverified')
	{
		Page.current_get_type = 'getCitations_byFac_unverified';
	}
	else if (type == 'none')
	{
		Page.current_get_type = 'search';
	}
	else
	{
		Page.current_get_type = 'getCollection';
	}
}

Page.printCollectionNamesMenuForManagingCitations = function() 
{
	var html = '<div id="collectionNamesMenuForManagingCitations_div">';
	html += Page.printCollectionNamesMenuForManagingCitations_helper();
	html += '</div>';
	return html;
}

// Used by Page.updateCollectionFromList() and Page.printCollectionNamesMenuForManagingCitations(). Page.updateCollectionFromList() is called from Page.listCollections everytime.
Page.printCollectionNamesMenuForManagingCitations_helper = function()
{
	var html = '<select name="collectionNamesMenuForManagingCitations" id="collectionNamesMenuForManagingCitations">'; 
	//onchange="Page.currentCollectionForManagingCitations=this.options[this.selectedIndex].value;">';
	html += '<option value="new" id="new" >New Collection</option>';
	
	for (var i=0; i < Page.collections.length; i++) {
	  html += '<option onmouseover="this.parentNode.title=\''+Page.collections[i].collection_name+'\'" ';
	  html += 'value="' + Page.collections[i].collection_id + '" id="' + Page.collections[i].collection_id + '"';
	  if (Page.currentCollectionForAddingCitations == Page.collections[i].collection_id)
	  {
		  html += ' selected';	
	  }
  
	  html += '>';
	  
	  // Check collection's name length
	  var c_name = ""+Page.collections[i].collection_name;
	  if(c_name.length > 20)
	  {
		  c_name = c_name.substr(0,20) + ' ...';
	  }
	  else{}
  
	  html += c_name + '</option>';
	}
	html += '</select>';
	return html;
}

Page.getCollectionInfoAndListCitations = function()
{
	//get collection info
	var jsonStr = '{"request": {"type": "getCollectionNamesAndCitationIds", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '"}}';
	Ajax.SendJSON('services/collections.php', Page.onResponseCollections, jsonStr);
}

Page.getCollectionNamesAndIds = function()
{
	//get collection info
	var jsonStr = '{"request": {"type": "getCollectionNamesAndIds", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '"}}';
	Ajax.SendJSON('services/collections.php', Page.onResponseCollections, jsonStr);
}

Page.onResponseCollections = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.collections = responseObj.collections;
		Page.default_collections = responseObj.default_collections; // all and unverified count.
		Page.writeOptionsForListCitations();
		Page.listCitations();
	}
}

Page.getSelectedCheckBoxesCitationIds = function()  // Get selected/checked checkboxes on citations listing.
{
	Page.temp_citation_ids = new Array();
	var temp_citation_id;
	for (var i=0; i<Page._citations.length; i++)  
	{
		temp_citation_id = Page._citations[i].citation_id;
		if (document.getElementById('cb_'+temp_citation_id).checked)  {
			Page.temp_citation_ids.push(document.getElementById('cb_'+temp_citation_id).value);
		}
	}
	return Page.temp_citation_ids;
}

Page.getSelectedCheckBoxesCollectionIds = function()  // Get selected/checked checkboxes on collections listing.
{
	var temp_collection_keys = new Array(); 
	var temp_collection_ids = new Array();
	var temp_citation_id;
	for (var i=0; i<Page.collections.length; i++)  
	{
		temp_collection_id = Page.collections[i].collection_id;
		if (document.getElementById('collection_cb_'+temp_collection_id).checked)  {
			temp_collection_ids.push(document.getElementById('collection_cb_'+temp_collection_id).value);
			temp_collection_keys.push(i);
		}
	}
	Page.temp_collection_ids = temp_collection_ids;
	return Array(temp_collection_ids, temp_collection_keys);
}

Page.mergeCollections_request = function(selected_collection_id, collection_ids_arr)
{
	var jsonStr = '{"request": {"type": "merge", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_id": ' + YAHOO.lang.JSON.stringify(selected_collection_id) + ', "collection_ids": '+ YAHOO.lang.JSON.stringify(collection_ids_arr) + '}}';
	Ajax.SendJSON('services/collections.php', Page.mergeCollections_response, jsonStr);	
}

Page.getCollectionNameFromPage = function(collection_id)
{
	for(var i in Page.collections)
	{
		if(Page.collections[i].collection_id == collection_id)
		{
			return Page.collections[i].collection_name;
		}
	}
	
	return false;
}

Page.mergeCollections_response = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.updateListCollection_request(); // Update global collection variable and relist collections
		
		var collection_id = responseObj.collection_id;
		
		var collection_name = trim(document.getElementById('mergeCollectionsTextbox').value); 
		if (collection_name == "")
		{
			collection_name = Page.getCollectionNameFromPage(collection_id);
		}
		
		var onclick_html = 'onclick="Page.goToNewCollection(\'' + collection_id + '\', \'' + collection_name + '\')"';
		
		var html = '';	
		html += '<center>Merged collections into "' + collection_name + '" <br />';
		html += '<br><input type="button" name="GoToNewCollection" value="Go to collection: ' + collection_name + '" ' + onclick_html + '>';
		html += '</center>';
		Page.panel1.setBody(html);
		Page.panel1.show();
	}
}

Page.getCollectionsGivenCitationID_request = function(citation_id)
{
	var jsonStr = '{"request": {"type": "getCollectionsGivenCitationID", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "citation_id": ' + YAHOO.lang.JSON.stringify(citation_id) + '}}';
	Ajax.SendJSON('services/collections.php', Page.getCollectionsGivenCitationID_response, jsonStr);		
}

Page.getCollectionsGivenCitationID_response = function()
{
	if (Ajax.CheckReadyStateNoLoading(Ajax.request) && Ajax.CheckReadyState(Ajax.request))
	{	
		//alert("response: " + Ajax.request.responseText); 
		Page.tooltip_request_count = 0;
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		//alert(responseObj.collections[30]);
		if (responseObj.collections.length > 0)
		{
			var tt_str = '';
			for(var i = 0; i < responseObj.collections.length; i++)
			{
				tt_str += ' - ' + responseObj.collections[i][1] + '<br>';	// Element 1 contains the name of collection, 0 is the collection id.
			}
			
			Page.tt_col.cfg.setProperty("text", tt_str);
		}
		else
		{
			Page.tt_col.cfg.setProperty("text", "No Collections Found.");
		}
	}
}

Page.getCitationsGivenCollectionID = function(citation_id)
{
	// Default value
	if(citation_id == undefined) { citation_id = 0; }
	
	if (Page.currentCollection == "all")
	{
		//Page.current_page = 1; //Commented so that page will stay at current page when panel closed
		Page.input_method = 9;
		Page.getCitations(Page.current_page, 'getCitations_byFac_all', citation_id);
	}
	else if (Page.currentCollection == "unverified")
	{
		//Page.current_page = 1; //Commented so that page will stay at current page when panel closed
		Page.input_method = 9;
		Page.getCitations(Page.current_page, 'getCitations_byFac_unverified');
	}
	else if (Page.currentCollection == "search")
	{
		Page.getCitations(Page.current_page, Page.current_get_type, citation_id);	
	}
	else
	{
		var jsonStr = '{"request": {"type": "getCitationsGivenCollectionID",  "page": "' + Page.current_page + '", "citation_id_page": "' + citation_id + '", "citations_per_page": "' + Page.citations_per_page + '", "sort_order": "' + Page.sort_order + '", "collection_id": "' + Page.currentCollection + '", "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "entryTime": ""}}}';
		Ajax.SendJSON('services/citations.php', Page.onResponseGetCitationsGivenCollectionID, jsonStr);
	}
}

Page.onResponseGetCitationsGivenCollectionID = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
 		Page.rewritePage(responseObj);
	}
}

Page.renameCollection_request = function(i)
{
	Page.collection_to_be_renamed = Page.collections[i];
	var html = '';
	html += '<div class="panel1_message">';
	html += '<p>Rename collection <b>"' + Page.collections[i].collection_name + '"</b> to: ';
	html += '<input type="text" name="collection_rename" id="collection_rename" value="" /></p>';
	html += '<input type="button" class="panel1_button" value="OK" onclick="Page.renameCollectionHelper_request('+Page.collections[i].collection_id+', document.getElementById(\'collection_rename\').value );"/>';
	html += '&nbsp;&nbsp;&nbsp;';
	html += '<input type="button" class="panel1_button" value="Cancel" onclick="Page.panel1.hide();"/></div>';
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.renameCollectionHelper_request = function(collection_id, collection_rename)
{		
	var jsonStr = '{"request": {"type": "rename", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_id": "' + collection_id + '", "collection_rename": "' + collection_rename + '"}}';
	Ajax.SendJSON('services/collections.php', Page.renameCollection_response, jsonStr);		 
}

Page.renameCollection_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
		if((responseObj.collection_id) && (responseObj.collection_id != ""))
		{
			if (responseObj.collection_id == -1)
			{
				var html = '';
				html += '<div class="panel1_message">';
				html += 'A collection with the name "' + responseObj.collection_name + '" already exists.<br> ';
				html += '<p>Rename collection <b>"' + Page.collection_to_be_renamed.collection_name + '"</b> to: ';
				html += '<input type="text" name="collection_rename" id="collection_rename" value="" /></p>';
				html += '<input type="button" class="panel1_button" value="OK" onclick="Page.renameCollectionHelper_request('+Page.collection_to_be_renamed.collection_id+', document.getElementById(\'collection_rename\').value );"/>';
				html += '&nbsp;&nbsp;&nbsp;';
				html += '<input type="button" class="panel1_button" value="Cancel" onclick="Page.panel1.hide();"/></div>';
				Page.panel1.setBody(html);
				Page.panel1.show();
				
			}
			else
			{
				Page.panel1.hide();											// Hide confirmation panel
				Page.updateListCollection_request();						// Update global collection variable and relist collections
			}
		}
		else
		{
			Page.panel1_alert_message('Error renaming citation!', '');
		}
	}
}

Page.deleteSelectedCollections = function()
{
	var result_arr = Page.getSelectedCheckBoxesCollectionIds();
	Page.temp_collection_ids = result_arr[0]
	
	var html = '';
	html += '<div class="panel1_message">';
	html += '<p>Delete selected collection(s)?</p>';
	html += '<input type="button" class="panel1_button" value="OK" onclick="Page.deleteCollectionHelper_request(Page.temp_collection_ids);"/>';	
	html += '&nbsp;&nbsp;&nbsp;';
	html += '<input type="button" class="panel1_button" value="Cancel" onclick="Page.panel1.hide();"/></div>';
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.deleteCollection_request = function(i)
{
	Page.temp_collection_ids = Array(Page.collections[i].collection_id); 
	
	var html = '';
	html += '<div class="panel1_message">';
	html += '<p>Delete collection "' + Page.collections[i].collection_name + '"?</p>';
	html += '<input type="button" class="panel1_button" value="OK" onclick="Page.deleteCollectionHelper_request(Page.temp_collection_ids);"/>';	
	html += '&nbsp;&nbsp;&nbsp;';
	html += '<input type="button" class="panel1_button" value="Cancel" onclick="Page.panel1.hide();"/></div>';
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.deleteCollectionHelper_request = function(collection_ids_arr)
{		
	var jsonStr = '{"request": {"type": "delete", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_ids": '+ YAHOO.lang.JSON.stringify(collection_ids_arr) + '}}';
	Ajax.SendJSON('services/collections.php', Page.deleteCollection_response, jsonStr);		 
}

Page.deleteCollection_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		
		if(responseObj.collections_deleted != undefined && responseObj.collections_deleted == "1")
		{
			Page.panel1.hide();											// Hide confirmation panel
			Page.updateListCollection_request();						// Update global collection variable and relist collections
		}
		else
		{
			Page.panel1_alert_message('Error deleting citation!', '');
		}
	}
}

Page.updateListCollection_request = function()
{
	var jsonStr = '{"request": {"type": "getCollectionNamesAndIds", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '"}}';
	Ajax.SendJSON('services/collections.php', Page.updateListCollection_response, jsonStr);
}

Page.updateListCollection_response = function()  // Update collection list in (manage) after delete and etc
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.collections = responseObj.collections;
		Page.listCollections();
	}
}

// Update all Collections related dropdown menu every time Page.listCitations is called.
Page.updateCollectionFromList = function()   
{
	if (document.getElementById('collectionNamesMenuForManagingCitations_div'))
	{
		var obj = document.getElementById('collectionNamesMenuForManagingCitations_div');
		obj.innerHTML = Page.printCollectionNamesMenuForManagingCitations_helper();
	}
	if (document.getElementById('collectionNamesMenuForViewing_div'))
	{
		var obj = document.getElementById('collectionNamesMenuForViewing_div');
		obj.innerHTML = Page.printCollectionNamesMenuForViewing_helper();
	}
}

/************** selected into Collections [START] ******************/
Page.addCitationsToCollection = function()    // Page.selectedIntoCollection_response
{
	var obj = document.getElementById('collectionNamesMenuForManagingCitations');
	//Page.currentCollectionForManagingCitations = obj.options[obj.selectedIndex].value;
	var collection_id = obj.options[obj.selectedIndex].value; //Page.currentCollectionForManagingCitations;
	
	Page.getSelectedCheckBoxesCitationIds(); // Could make it to return the array instead of global variable Page.temp_citation_ids.
 
	if (Page.temp_citation_ids.length > 0)
	{
		if (collection_id == "new") 
		{
			Page.selectedIntoCollectionHelper_request('', 'new', Page.temp_citation_ids);  // First argument is the collection_name. Function will ask for one if empty.
		}
		else
		{
			Page.selectedIntoCollectionHelper_request(collection_id, 'insert', Page.temp_citation_ids);
		}
	}
	else 
	{
		Page.panel1.setBody('<center>No citations have been selected.<br>Please select citations to add to the collection.</center>');
		Page.panel1.show();
	}
}

// Used by "Add selected citations to collection" right menu.
Page.selectedIntoCollectionHelper_request = function(collection_id_or_name, action, citation_ids)
{
	//Page.temp_citation_ids = citation_ids;   // Save citation_ids in global. Redundant!
	var collection_name; var collection_id; // TO-DO: Should change so that addTo accepts collection_name instead?
	if(action == 'new') 
	{
		collection_name = collection_id_or_name; 
		collection_id = -1;
	}
	else if(action == 'insert')
	{
		collection_name = ""; 
		collection_id = collection_id_or_name;
	}
	
	if(collection_name == "" && action != 'insert')
	{
		var onclick_html = 'onclick="Page.selectedIntoCollectionHelper_request(document.getElementById(\'new_collection_name\').value, \'new\', Page.temp_citation_ids);"';
		var html = 'Enter a name for the collection: ';	
		html += '<div align="left" id="myAutoComplete" style="display:inline-block;vertical-align:middle; height:22px; z-index:' + Page.zCounter-- + '; width:20em" class="yui-ac">'; 
		html += '<input type="text" name="new_collection_name" id="new_collection_name" style="width:20em" value="" class="yui-ac-input" autocomplete="off">';
		html += '<div align="left" style="vertical-align: middle; left: 0px; top: 22px; width:20em;" id="autocomplete_new_collection_name" class="yui-ac-container"></div>';
		html += '</div><br><br>';
		html += '<input type="button" name="new_collection" value="Submit" ' + onclick_html + '>';
		html = '<center>' + html + '</center>'; // Make it center.
		Page.panel1.setBody(html);
		initializeAutocompleteCollectionNames("new_collection_name");
		Page.panel1.show();
	}
	else if(citation_ids == undefined) // Create empty collection or error?
	{
		var jsonStr = '{"request": {"type": "new", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_name": "' + collection_name + '"}}';
		Ajax.SendJSON('services/collections.php', Page.selectedIntoCollection_response, jsonStr);	
	}
	else  // Create collection and add citations.
	{
		if(action == 'new') {
			var jsonStr = '{"request": {"type": "new_and_add", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_name": "' + collection_name + '", "citation_ids": '+ YAHOO.lang.JSON.stringify(citation_ids) + '}}';
			Ajax.SendJSON('services/collections.php', Page.selectedIntoCollection_response, jsonStr);
		}
		else if(action == 'insert') {
			var jsonStr = '{"request": {"type": "addTo", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "collection_id": ' + YAHOO.lang.JSON.stringify(collection_id) + ', "citation_ids": '+ YAHOO.lang.JSON.stringify(citation_ids) + '}}';
			Ajax.SendJSON('services/collections.php', Page.selectedIntoCollection_response, jsonStr);
		}
	}
}

Page.selectedIntoCollection_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.panelIntoCollection_response(responseObj, 'selected');	
	}
}
/************** Selected into Collections [END] ******************/


/************** Parse into Collections [START] ******************/
Page.parseFileIntoCollection_request = function(action, collection_name)
{
	if(collection_name == "")
	{
		Page.panel1_alert_message('Please enter a collection name.', '');
	}
	else
	{
		if(Page.panel1) Page.panel1.hide(); // Hide panel1
		if(Page.panel4) Page.panel4.show(); // Show loading panel
		document.uploadForm.entryName.value = collection_name;
		document.uploadForm.parse_action.value = action;
		document.uploadForm.submit();
	}	
}



Page.parseTextIntoCollection_request = function(callbackmethod, str, action, collection_name)
{
	// Save info before checking collection_name exist.
	Page.newEntries = str;
	
	if(collection_name == "") 
		Page.panel1_alert_message('Please enter a collection name.', '');
	else if(str == "")
		Page.panel1_alert_message('Please insert APA or MLA citation(s) in the field.', '');
	else {
		Page.parseTextIntoCollectionHelper_request(callbackmethod, action, collection_name);
	}
}

Page.parseTextIntoCollectionHelper_request = function(callbackmethod, action, collection_name)
{
	var str = Page.newEntries;  // Could be sent as an argument.
	var jsonStr = '{"request": {"entries": '+ YAHOO.lang.JSON.stringify(str) +', "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "action": "' + action + '", "collection_name": ' + YAHOO.lang.JSON.stringify(collection_name) + ', "entryTime": ""}}';
	Ajax.SendJSON('services/parser.php', callbackmethod, jsonStr);	
}

Page.parseTextIntoCollection_response = function()
{	
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.panelIntoCollection_response(responseObj, 'text');	
	}
}

Page.parseFileIntoCollection_response = function(responseText)
{
	if(Page.panel4) Page.panel4.hide(); // Hide loading panel
	var responseObj = eval("(" + responseText + ")");
	Page.panelIntoCollection_response(responseObj, 'file');
}
/************** Parse into Collections [END] ******************/


/************** Citations Into Collection Panel1 response [START] ******************/
Page.panelIntoCollection_response = function(responseObj, mode) // mode is either "text" or "file" or "selected"
{
	if(Page.panel4) Page.panel4.hide(); // Hide loading panel
	var html = '';			// html of panel1
	var onclick_html = ''; 	// helper variable
	
	var collection_id = '';
	var collection_name = '';
	
	if (responseObj.collection_id)
	{
		collection_id = responseObj.collection_id;
	}
	if (responseObj.collection_name)
	{
		collection_name = responseObj.collection_name;
	}
		
	// If collection exist. Ask for a new collection name or insert. Send to the same callbackmethod (this function).
	if(responseObj.collection_status == "exists")  // -1 means a collection exists. status: exists
	{
		html = new_collection_exists_message_html(collection_name);
		html += collection_exists_options_html(collection_id, collection_name, mode);
		
	}
	else if(responseObj.collection_status == "new_inserted")  // 1 means a collection does not exists. Insertion successful. status: new_inserted
	{
		//Added by Abhinav on 03/07/2012
		html += '<div class="panel1_message">';
		html += '<p> A new collection has been created</p>';
		html += '<input type="button" class="panel1_button" value="OK" onclick="Page.setFlag(\'responseObj\',\'collection_id\',\'collection_name\',\'mode\');Page.panel1.hide()"/>';	

	}
	else if(responseObj.collection_status == "ti") 	// "TI:" entries upload or paste. "ti" cases are handle in parser.php itself.
	{
		Page.parsed_timestamp = responseObj.parsed_timestamp;
		html = ti_html(Page.parsed_timestamp, mode);
		
	}
	else if((responseObj.collection_status == "exists_inserted") && (responseObj.collection_id != -1)) 	// collection_exists 0 means insert into existing collection successful.  status: exists_inserted
	{																					// collection_id -1 means error inserting into existing collection.	
		var duplicates = responseObj.duplicates;
		var insert_count = responseObj.insert_count;
	//	Page.newCollectionName = collection_name;
		
		html = insert_into_existing_collection_html(duplicates, insert_count, collection_name);
		html += created_collection_successfully_options_html(collection_id, collection_name, mode);
		
		Page.currentCollection = collection_id;  
		Page.currentCollection_name = collection_name; //might be redundant
		if ((mode == 'file') || (mode == 'text'))
		{
			Page.parsed_timestamp = responseObj.parsed_timestamp;
		}
	}
	else if(responseObj.collection_status == "empty_name")  // Means empty collection_name submitted during regular file upload. status: empty_name
	{
		html = Page.panel1_alert_message('Please enter a collection name.', '');
	
	}
	else
	{
		html = 'Error in: Page.panelIntoCollection_response('+mode+').';	
		
	}
		// Show message on panel1 
	Page.panel1.setBody(html);
	Page.panel1.show();
	//Page.newpanel1.show();
	Page.getCollectionNamesAndIds();
	
	}

//Added by Abhinav on 03/07/2012
Page.setFlag = function(responseObj,collection_id,collection_name,mode)
{
	
		var duplicates = responseObj.duplicates;
		var insert_count = responseObj.insert_count;
		
		html = new_collection_created_message_html(collection_name, duplicates, insert_count);
		html += created_collection_successfully_options_html(collection_id, collection_name, mode);
		
		Page.currentCollection = collection_id; 			// Set current collection
		Page.currentCollection_name = collection_name; 		// Set current collection
		if ((mode == 'file') || (mode == 'text'))
		{
			Page.parsed_timestamp = responseObj.parsed_timestamp;
		}
}

/************** Citations Into Collection Panel1 response [END] ******************/

	/************ Declare html functions [START] **************/
	var new_collection_exists_message_html = function(collection_name)
	{
		var html = 'A collection with the name "' + collection_name + '" already exists.<br> ';
		html += 'Do you want to add the citations to it or start a new collection?<br>';
		return html;
	};
	
	var new_collection_created_message_html = function(collection_name, duplicates, insert_count)
	{
		var html = 'A collection with the name "' + collection_name + '" has been created.<br>';
		html += insert_into_existing_collection_html(duplicates, insert_count, collection_name);
		return html;
	};
	
	var ti_html = function(parsed_timestamp, mode){ 
		var html = 'TI collections added from file successfully.<br>';
	//	html += created_collection_successfully_options_html('all', 'All My Citations', mode);
		return html;
	};
	
	var insert_into_existing_collection_html = function(duplicates, insert_count, collection_name)
	{
		var html = '';
		if (duplicates == 0)
		{
			if(insert_count == 1) html += '1 citation was added to collection "' + collection_name + '". <br>';
			else html += insert_count + ' citations were added to collection "' + collection_name + '". <br>';
		}
		else 
		{
			var attempted_count = parseInt(insert_count) + parseInt(duplicates); // maybe later make attempted_count a variable passed to the php and back.
			html += '';
			if(attempted_count == 1) html += 'Attempted to add 1 citation. <br>';
			else html += 'Attempted to add ' + attempted_count + ' citations. <br>'; 
			
			if(duplicates == 1) html += '1 was a duplicate of a citation already in the collection and was ignored. <br>';
			else html += duplicates + ' were duplicates of citations already in the collection and were ignored. <br>';
			
			if(insert_count == 1) html += '1 was added successfully. <br>';
			else html += insert_count + ' were added successfully. <br>';
		}
		return html;
	};
	
	var created_collection_successfully_options_html = function(collection_id, collection_name, option) // optional "option" argument.
	{
		if(!option) option = ''; // Default value
		
		var onclick_html = 'onclick="Page.goToNewCollection(\'' + collection_id + '\', \'' + collection_name + '\')"';
		var html = '<br><input type="button" name="GoToNewCollection" value="Go to collection: ' + collection_name + '" ' + onclick_html + '>';
		if(option == 'selected') {}  // No page through.
		else { html += '<br><input type="button" name="PageThroughCitations" value="Page Through Newly Added Citations" onclick="Page.pageThroughCitations_request()">'; }
		return html;	
	};
	
	var collection_exists_options_html = function(collection_id, collection_name, option)
	{
		var onclick_new; var onclick_insert; var html;
		if(!option) option = ''; // Default value

		if(option == 'file') {
			onclick_insert = 'onclick="Page.parseFileIntoCollection_request(\'insert\', \'' + collection_name + '\');"';
			onclick_new = 'onclick="Page.parseFileIntoCollection_request(\'new\', document.getElementById(\'new_collection_name\').value);"';
		}
		else if(option == 'selected') {
			onclick_insert = 'onclick="Page.selectedIntoCollectionHelper_request(' + collection_id + ', \'insert\', Page.temp_citation_ids);"';
			onclick_new = 'onclick="Page.selectedIntoCollectionHelper_request(document.getElementById(\'new_collection_name\').value, \'new\', Page.temp_citation_ids);"';
		}
		else if(option == 'text')
		{
			onclick_insert = 'onclick="Page.parseTextIntoCollectionHelper_request(Page.parseTextIntoCollection_response, \'insert\', \'' + collection_name + '\');"';
			onclick_new = 'onclick="Page.parseTextIntoCollection_request(Page.parseTextIntoCollection_response, Page.newEntries, \'new\', document.getElementById(\'new_collection_name\').value);"';
		}
			
		html = '<br><input type="button" name="add_citations_to_existing_collection" value="Add citations to \'' + collection_name + '\'" ' + onclick_insert + '><br>';
		html += '<br>OR<br><br>';
		html += '<input type="button" name="new_collection" value="Start a new collection named: " ' + onclick_new + '>&nbsp;&nbsp;';
		html += '<input type="text" name="new_collection_name" id="new_collection_name" /><br>';
		return html;	
	};
	/************ Declare html functions [END] **************/



// Create Misc in collections_table
Page.createMiscCollectionsTable_request = function()
{
	var jsonStr = '{"request": {"type": "create_misc"}}';
	Ajax.SendJSON('services/admin.php', Page.createMiscCollectionsTable_response, jsonStr);	
}

Page.createMiscCollectionsTable_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		alert("Create Misc response: "  + Ajax.request.responseText);
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
	}
}

// Populate collections_table
Page.populateCollectionsTable_request = function()
{
	var jsonStr = '{"request": {"type": "populate_all"}}';
	Ajax.SendJSON('services/admin.php', Page.populateCollectionsTable_response, jsonStr);	
}

Page.populateCollectionsTable_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		alert("Populate response: "  + Ajax.request.responseText);
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
	}
}

// Truncate collections_table
Page.truncateCollectionsTable_request = function()
{
	var jsonStr = '{"request": {"type": "truncate"}}';
	Ajax.SendJSON('services/admin.php', Page.truncateCollectionsTable_response, jsonStr);	
}

Page.truncateCollectionsTable_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		alert("Truncate response: "  + Ajax.request.responseText);
		var responseObj = eval("(" + Ajax.request.responseText + ")");	
	}
}
