var Page = new Object();

// Page object variables
//Page.entry = 0;
Page.state = 1; // For now: 3=Paging through all, unverified or a collection; 2=Paging through newly added citations; 1=Everything else
Page.movingTab = false;
Page.current_citation_num = 0; 
Page.current_row_num = 0;
Page._citation;  // goes later 
Page._citations = '';
Page._current_citation = "";
Page._current_citation2 = "";

Page.pre_merge_id1 = "";  
Page.pre_merge_id2 = "";
//Page.similar_citation1 = "";
//Page.similar_citation2 = "";
//Page.mergedCitation = ""; 

Page.collections;
Page.default_collections; // all and unverified

Page.working_citation = "";

Page.currentFormat = "apa";
Page.currentExportFormat = '';
Page.currentTimestamp = "all";
Page.currentCollection = "all"; 
Page.currentCollection_name = "";
//Page.currentCollectionForManagingCitations = "new";
Page.current_get_type = 'getCitations_byFac_all';
Page.currentNewAuthorNum = -1;
Page.collectionNames;
Page.selectAllOrNone = "select"; 
Page.notify_toggle = false;
Page.citation_toggle = false;
Page.inputArray;
Page.submitter;
Page.owner = '';
Page.owner_fullname = '';
Page.user;
Page.proxies = new Array();
Page.hasProxy = false;

Page.loggedIn = false;
Page.ldap_dom;
Page.input_method = 0;
Page.citation_input_method = 1;
Page.facID = 0;
Page.zCounter = 9999;
Page.panel1;
Page.panel2;
Page.panel4;
Page.junk = "sdfsdaf";
Page.sentData;
Page.data_keys;
Page.panel_open = 0;
Page.panel1_open = "";
Page.panel3_open = 0;
Page.currentMissingField = false;
Page.temp_citation_ids;
Page.savingCitationEnteredByHandIntoCollection = false;
Page.authorNameSubstitutions = new Array();
Page.current_upload_dialog = "";

//Page.citations_per_page = 40;
Page.current_page = 1;
Page.total_count = 0;

Page.max_pages_displayed = 10;
Page.citations_per_page = 50;
Page.current_viewable_pages = new Array();
Page.keywords = '';
Page.sort_order = 'author0ln';
Page.show_citation_id_flag = 0;
Page.set_compact_view_flag = 0;
Page.show_collections_flag = 1;
Page.show_notes_flag = 1;
Page.show_abstracts_flag = 0;
Page.show_URLs_flag = 1;

Page.similar_citations_array = new Array();
Page.selected_citations = [];

//Page.oContextMenu = "";
//Page.oContextMenu2 = "";
Page.newly_added_citations = [];
Page.newly_added_similar_citations_array = [];
Page.current_newly_added_num = 0;

Page.highlight_citations_with_missing_info_flag = 0;

Page.newEntries = ""; // Textarea string entries to be parsed.

var tabView_a = "";
var tabView_b = "";

Page.numViewableTabsLimit = 4;

Page.citations_array_a = "";
Page.citations_array_b = "";

Page.tt = "";
Page.tt_col = "";
Page.tt_col_arr = Array();

Page.move_buttons_tt = "";
Page.close_buttons_tt = "";

Page.collection_to_be_renamed = "";

Page.tab_deleting_citation_suffix = '';

Page.interval_value = '';

Page.baseURL = document.URL.substring(0, document.URL.indexOf('/', 14)) + "/";
Page.rootDirectory = document.URL.substring(Page.baseURL.length,(document.URL.lastIndexOf("/")));  // pubstest, pubsdev or pubs
Page.basePdfDirectory = Page.baseURL + 'pubspdf/' + Page.rootDirectory + '/'; 

Page.document_root = '';

Page.user_back_button_state = 'user'; // or 'admin'

Page.feedback_list = '';

Page.initializePanel = function() 
{
	var html;
	Page.panel1 = new YAHOO.widget.Panel("panel1", { width:"570px", height: "300px", fixedcenter: "contained", visible:false, draggable:true, close:true, modal:true, constraintoviewport:true } );
	html = '';
	Page.panel1.setBody(html);
	Page.panel1.render("container");
	
	//Page.panel2 = new YAHOO.widget.Panel("panel2", { width:"1100px", height: "700px", fixedcenter: "contained", visible:false, draggable:true, close:true, modal:true, constraintoviewport:true } );
	Page.panel2 = new YAHOO.widget.Panel("panel2", { fixedcenter: "contained", visible:false, draggable:false, close:true, modal:true, constraintoviewport:true } );
	html = '';
	html += '<center>';
	//html += '<iframe id="upload_target2" name="upload_target2" src="#" style="width:800px;height:100px;border:2px solid #7D110C;"></iframe>';
	html += '<iframe id="upload_target2" name="upload_target2" src="#" style="width:0px;height:0px;border:0px solid #7D110C;"></iframe>';
	html += '<form name="cForm" action="pubs/services/uploadpdf.php" method="post" enctype="multipart/form-data" target="upload_target2" onsubmit="startUpload2();" >';
	html += '<input type="hidden" id="upload_citation_suffix" name="upload_citation_suffix" value="">';		// Updated when cForm submit button is clicked.
	html += '<input type="hidden" id="attached_filename" name="attached_filename" value="">';				// Keep track of attached_filename to be saved.
	
	html += '<div id="panel2_div"></div>';
	html += '<div id="panel1_div">';
	
	html += '<div id="back_to_similar_div" style="text-align:left;display:none"><input type="button" value="Back To Similar" onclick="Page.swapPanels2And3();"></div>';

	html += '<div id="saved_success"></div>';
	html += '<table id="panel1_table" width="970" height="640" border="0">';

	// 1st column
	html += '<tr><td id="panel1_table_td" valign="top">';
	html += '<div id="author_fields" class="panel_div"></div>';
	html += '<div id="constantByPubtype_fields" class="panel_div"></div>';
	html +=	'<div id="pubtype_div" class="panel_div"></div>';
	html += '<div id="changingByPubtype_fields" class="panel_div"></div>';
	html += '</td>';
	
	// 2nd column (spacing column)
	html += '<td>&nbsp;&nbsp;&nbsp;</td>'; 
	
	// 3rd column
	html += '<td width="400" valign="top">'; 
	html += '<div id="top_right_div" class="panel_div"></div>';
	html += '<div id="abstract_div" class="panel_div"></div>';
	html += '<div id="keyword_div" class="panel_div"></div>';
	html += '<div id="additional_fields" class="panel_div"></div>';
	html += '</td></tr>';

	// Verified colspan
	html += '<tr height="100%" valign="top"><td colspan=3><div id="verified_div"></div></td></tr>';
	
	// Save colspan
	html += '<tr height="40px" valign="bottom" ><td colspan=3><div id="save_div"></div></td></tr>';

	html += '</table>';
	html += '</div>';
	
	// Panel 3
	html += '<div id="panel3_div">';
	html += '<table id="panel3_table2" border="0">';
	// 1st column
	html += '<tr><td width="535" id="panel3_table_td" valign="top">';
	html += '<div id="tabs_container_a"></div>';
	html += '</td>';
	// 2nd column (spacing column)
	html += '<td valign="top"><div id="buttons_div" style="vertical-align:top;"></div></td>';  //html += '<td>&nbsp;&nbsp;&nbsp;</td>'; 
	// 3rd column
	html += '<td width="535" valign="top">'; 
	html += '<div id="tabs_container_b"></div>';
	html += '</td></tr>';
	// Verified colspan
	//html += '<tr height="100%" valign="top"><td colspan=3><div id="verified_div"></div></td></tr>';
	// Merge colspan
	html += '<tr height="40px" valign="bottom" ><td colspan=3><div id="merge_div"></div></td></tr>';
	html += '</table>';
	html += '</div>';
	
	html += '</form>';
	
	html += '</center>';
	Page.panel2.setBody(html);
	Page.panel2.render("container");

	Page.panel4 = new YAHOO.widget.Panel("panel4", { width:"240px", fixedcenter: true, visible:false, draggable:false, close:false, modal:true, zindex:4 } );
	html = '';	
	Page.panel4.setHeader('Loading, please wait...'); 
	Page.panel4.setBody('<img src="' + Page.document_root + 'images/loading.gif" />');
	Page.panel4.render("container");
	
	Page.panel2.showEvent.subscribe(function() { 
		if (Page.panel3_open == 1)
		{
			Page.alignActiveTabs();
		}

	}, Page.panel2, true); 
	
	Page.panel2.hideEvent.subscribe(function() {								
		Page.removeAllTabs();	
		
		if (Page.interval_value != '')
		{
         	window.clearInterval(Page.interval_value);
		}
		Page.panel_open = 0;
		Page.panel3_open = 0;
		
		document.getElementById('attached_filename').value = ''; // Reset upload element
		
		if (document.getElementById('upload_div'))
		{
			document.getElementById('upload_div').style.display = 'none'; //hide. div is there even after hiding panel2.
		}
		
		document.getElementById('secondary').style.display = 'none';//hide
		document.getElementById('citations').style.display = '';	//show
		document.getElementById('options').style.display = '';		//show
		Page.right_column_display('all');
		
		//alert("Page.panel2.hideEvent.subscribe : Page._current_citation.citation_id : " + Page._current_citation.citation_id);

		Page.getCitationsGivenCollectionID(Page._current_citation.citation_id);
	}, Page.panel2, true);
												   
 	initializeTabFramework();
	initializeContextMenu("panel2"); // for panel2
}

Page.swapPanels2And3 = function()
{
	if (document.getElementById('panel3_div').style.display == 'none')			
	{
		document.getElementById('panel3_div').style.display = ''; 			// Show		
		document.getElementById('panel1_div').style.display = 'none'; 		// Hide

	}
	else			
	{
		document.getElementById('panel1_div').style.display = ''; 			// Show		
		document.getElementById('panel3_div').style.display = 'none'; 		// Hide
	}
}

Page.oneCitationInPanel = function(citationInPanel, fieldFlag, newFlag) {
	Page.panel_open = 1;

	Page._current_citation = citationInPanel;
	var back_to_similar_html = '';
	if (Page.panel3_open == 1)
	{
		document.getElementById('back_to_similar_div').style.display = ''; // Show
	}
	else
	{
		document.getElementById('back_to_similar_div').style.display = 'none'; // Hide
	}
	if(fieldFlag == "text")  //after save
	{
		var verified = (citationInPanel.verified == 1) ? "Yes" : "No";
		document.getElementById('author_fields').innerHTML = Page.enterAuthorInfo(citationInPanel, "text", "");
		document.getElementById('constantByPubtype_fields').innerHTML = Page.enterConstantByPubtypeInfo(citationInPanel, "text", "");
		document.getElementById('changingByPubtype_fields').innerHTML = Page.enterChangingByPubtypeInfo(citationInPanel, citationInPanel.pubtype, "text", "");
		document.getElementById('additional_fields').innerHTML = Page.enterAdditionalInfo(citationInPanel, "text", "");
		document.getElementById('pubtype_div').innerHTML = Page.pubtypeMenu(citationInPanel.pubtype, "text", "");
		document.getElementById('top_right_div').innerHTML = '';
		document.getElementById('save_div').innerHTML = Page.printCitationSaveButton(citationInPanel, "text", "");
		document.getElementById('abstract_div').innerHTML = Page.enterAbstractKeywords(citationInPanel.abstract, "text", "", 'abstract', 'Abstract');
		document.getElementById('keyword_div').innerHTML = Page.enterAbstractKeywords(citationInPanel.keywords, "text", "", 'keywords', 'Keywords');
		document.getElementById('saved_success').innerHTML = 'The following information has been saved successfully. '+Page.printCitationEditButton(Page._current_citation, '+0')+'<br><br>';
		document.getElementById('verified_div').innerHTML = '<table align="center" ><tr><td width="70%"><b><font size="+2">Verified:</font></b></td><td align="left" width="30%"><font size="+2">'+verified+'</font></td></tr></table>'; 
		
		// Set table height
		document.getElementById('panel1_table').height = '640px';
		Page.cache_all_request('owner');
	}
	else // field
	{
		document.getElementById('author_fields').innerHTML = Page.enterAuthorInfo(citationInPanel, "", "");
		document.getElementById('constantByPubtype_fields').innerHTML = Page.enterConstantByPubtypeInfo(citationInPanel, "", "");
		document.getElementById('changingByPubtype_fields').innerHTML = Page.enterChangingByPubtypeInfo(citationInPanel, citationInPanel.pubtype, "", "");
		document.getElementById('additional_fields').innerHTML = Page.enterAdditionalInfo(citationInPanel, "", "");
	//	document.getElementById('upload_div').innerHTML = '<br><p>' + Page.printUploadDialog(citationInPanel.filename) + '</p>';
		document.getElementById('pubtype_div').innerHTML = Page.pubtypeMenu(citationInPanel.pubtype, "", "");
		if(newFlag == "new") {
			document.getElementById('top_right_div').innerHTML = Page.enterRawInfo(citationInPanel, "new", "");
		}
		else {
			document.getElementById('top_right_div').innerHTML = Page.enterRawInfo(citationInPanel, "", "");
		}
		if(newFlag == "merge") {
			document.getElementById('save_div').innerHTML = Page.printCitationSaveButton(citationInPanel, "", "merge");
		}
		else {
			document.getElementById('save_div').innerHTML = Page.printCitationSaveButton(citationInPanel, "", "");
		}
		document.getElementById('abstract_div').innerHTML = Page.enterAbstractKeywords(citationInPanel.abstract, "", "", 'abstract', 'Abstract');
		document.getElementById('keyword_div').innerHTML = Page.enterAbstractKeywords(citationInPanel.keywords, "", "", 'keywords', 'Keywords');
		document.getElementById('saved_success').innerHTML = '';
		document.getElementById('verified_div').innerHTML = ''; 
		
		// Set table height
		document.getElementById('panel1_table').height = '660px';
		
		document.getElementById('the_preview_div').innerHTML = Page.printAPAStyleCitation(citationInPanel);
		initializeAutocompleteFields(""); 
	}
	
	Page.highlightRequiredInputFields(citationInPanel.pubtype);
	
	var cit_id = (citationInPanel.citation_id == -1) ? "Pending" : citationInPanel.citation_id;
	Page.panel2.setHeader("Citation #" + cit_id);
	document.getElementById("panel2").style.display = '';	//show 
	document.getElementById('panel1_div').style.display = ''; // Show
	document.getElementById('panel3_div').style.display = 'none'; 		// Hide
	// DEBUG: Required Fields
	//document.getElementById('changingByPubtype_fields').innerHTML += Page.pubtypes_json[citationInPanel.pubtype]['apa_required_fields'];
}

Page.editOneCitation = function(i)
{
	if (i == -1) // merged citation
	{
		Page.oneCitationInPanel(Page.working_citation, "", "merge"); 
	}
	else if (i == -2) // first newly added citation
	{
		Page.oneCitationInPanel(Page.newly_added_citations[Page.current_newly_added_num], "", ""); 
	}
	else if (i == -3) //
	{
		Page.oneCitationInPanel(Page.currentCitation2, "", ""); 
	}
	else if (i == -4) // edit a citation in a tab
	{
		Page.oneCitationInPanel(Page.working_citation, "", "edit_in_tabs");
	}
	else
	{
		Page.current_citation_num = i; 
		Page.oneCitationInPanel(Page._citations[i], "", ""); 
	}
	Page.panel_open = 1; 
	document.getElementById('panel2_div').innerHTML = ''; 
	Page.panel2.show();
	document.getElementById('panel1_div').style.display = '';
}


// setSubmitter	- Called by index.php
//				- set Page.submitter from php
Page.setSubmitter = function(user, document_root, owner, currentCollection)
{
	Page.user = user; //user;
	Page.submitter = user;

	if (owner == "")
	{
		Page.owner = user; // login username
	}
	else
	{
		Page.owner = owner; //owner as set in javascript and passed through cas via hidden field and session variable without change
		Page.currentCollection = currentCollection;
	}
	Page.document_root = document_root;
	if (user == '')
	{
		Page.loggedIn = false;
	}
	else 
	{
		Page.loggedIn = true;
	}
	Page.setHasProxy();
}

Page.rewritePage = function(responseObj)
{
	if (Page.panel_open == 0) {
		//alert("Page.rewritePage : responseObj.page :  " + responseObj.page);
		Page._citations = responseObj.citations; 
		Page.current_page = (responseObj.page == undefined) ? 1 : responseObj.page;
		Page.total_count = responseObj.total_count;
		Page.similar_citations_array = responseObj.similar_citations_array;
		Page.getCollectionNamesAndIds();
		//	alert('here2');
	}
}

// onResponse 	- Default callback method of an Ajax request
// 				- Check for XML response tag and handle case appropriately
Page.onResponse = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		
		alert(Ajax.request.responseText);

		var temp = "author" + 1 + "ln";
	
		if ((Page.input_method== 9) || (Page.input_method== 4)){	
			if (Page.panel_open == 0) {
				Page.rewritePage(responseObj);
/*				Page._citations = responseObj.citations; 
				Page.total_count = responseObj.total_count;
				Page.similar_citations_array = responseObj.similar_citations_array;
				Page.getCollectionNamesAndIds();*/
			}

		}
		if ((Page.input_method== 1) || (Page.input_method== 2) || (Page.input_method== 3)){	
			if (Page.panel_open == 0) {
				Page.getCollectionNamesAndIds();
			}

		}
		
		// UserManagement onresponse - No longer used.
		// Page.user_onresponse(responseObj);
	}
}


Page.onResponseCheckAuthors = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
	
		if (responseObj.citations[0].citation_id) // citation id is returned; therefore, returning a citation
		{
			// Reset Variables
			document.getElementById('attached_filename').value = '';
			
			Page.savingCitationEnteredByHandIntoCollection = false;  //irrelevant if not saving a citation entered by hand
			Page.showFeedbackAfterSave(responseObj);
		}
		else // no citation id; therefore returning authors array
		{
			Page.showCheckAuthorPanel(responseObj);
		}
	}
}

Page.showFeedbackAfterSave = function(responseObj)
{ 
	var sentCitation = Page._current_citation;
	//compare sent data with received data
	Page._current_citation = responseObj.citations[0];
	var differenceString = Page.CompareSentDataWithReceivedData();
	//send back either a success message or an error message.
	document.getElementById("panel1_div").style.display = 'none';	//hide
	document.getElementById("panel2_div").style.display = '';	//show
	if (differenceString == "") {
		for (var i=0; i<6; i++)
		{
			if (((sentCitation['author'+i+'ln'] != "") && (Page._current_citation['author'+i+'ln'] != "") && (Page._current_citation['author'+i+'ln'] != sentCitation['author'+i+'ln']))	|| ((sentCitation['author'+i+'ln'] != "") && (Page._current_citation['author'+i+'fn'] != "") && (Page._current_citation['author'+i+'fn'] != sentCitation['author'+i+'fn'])))
			{
				var personObj=new Object();
				personObj.orig_ln = sentCitation['author'+i+'ln'];
				personObj.orig_fn = sentCitation['author'+i+'fn'];;
				personObj.new_ln = Page._current_citation['author'+i+'ln'];
				personObj.new_fn = Page._current_citation['author'+i+'fn'];
				Page.authorNameSubstitutions.push(personObj);
			}
		}
		
		var html = '';
		Page.oneCitationInPanel(Page._current_citation, "text", ""); 
		document.getElementById('panel2_div').innerHTML = ''; 
		document.getElementById('panel1_div').style.display = '';	// Hide
		document.getElementById('panel2_div').innerHTML = html;	    // CHECK THIS: !! html is still empty!!

		if (Page.input_method== 9) 
		{
		//	Page.updateCitationInList(Page._current_citation);
		}
		else if (Page.input_method== 4)
		{
			if (Page._current_citation.verified == "1")
			{
				Page.writeOptionsForListCitations();
				Page.listCitations();
			}
		}
		if (Page.pre_merge_id1 != "")
		{
			var index_to_be_removed = tabView_a.get('activeIndex');
			tabView_a.removeTab(tabView_a.get('activeTab'));
			Page.citations_array_a.splice(index_to_be_removed-1, 1);
			
			if (tabView_a.get('tabs').length == 2)
			{
				Page.add_empty_tab(tabView_a);
				document.getElementById('back_to_similar_div').style.display = 'none'; // Hide 
			//	alert('no tabs on left');
			}
		
			Page.pre_merge_id1 = "";
			Page.pre_merge_id2 = "";
		}
	//	Page._citations[Page.current_citation_num] = Page._current_citation;
		Page.selected_citations = [];
		// update
		
		Page.updateCitationsArraysAndTabs_after_save();
	}
	else {
		document.getElementById('panel2_div').innerHTML = differenceString;
	}
}

Page.updateCitationsArraysAndTabs_after_save = function()
{
	Page.updateOneCitationsArray_after_save(Page._citations);
	Page.updateOneCitationsArray_after_save(Page.citations_array_a);
	Page.updateOneCitationsArray_after_save(Page.citations_array_b);
	Page.updateOneCitationsArray_after_save(Page.newly_added_citations);
	Page.updateOneCitationsArray_after_save(Page.newly_added_similar_citations_array);
	
	for (var i=0; i<Page.similar_citations_array.length; i++)
	{
		Page.updateOneCitationsArray_after_save(Page.similar_citations_array[i]);
	}
	
	Page.updateTabView(tabView_a, Page.citations_array_a);
	Page.updateTabView(tabView_b, Page.citations_array_b);
}

Page.updateOneCitationsArray_after_save = function(arr)
{
	for (var i=0; i<arr.length; i++)
	{
		if (Page._current_citation['citation_id'] == arr[i]['citation_id'])
		{
			arr[i] = Page._current_citation;
			break;
		}
	}
}

Page.updateCitationsArraysAndTabs_after_delete = function()
{
	Page.updateOneCitationsArray_after_delete(Page._citations);
	Page.updateOneCitationsArray_after_delete(Page.citations_array_a);
	Page.updateOneCitationsArray_after_delete(Page.citations_array_b);
	Page.updateOneCitationsArray_after_delete(Page.newly_added_citations);
	Page.updateOneCitationsArray_after_delete(Page.newly_added_similar_citations_array);
	
	if (Page.tab_deleting_citation_suffix.indexOf("_a") >= 0)
	{
		tabView_a.removeTab(tabView_a.getTab(tabView_a.get('activeIndex')));
		if (tabView_a.get('tabs').length == 0)
			{
				Page.add_empty_tab(tabView_a);
			}
		}
	else if (Page.tab_deleting_citation_suffix.indexOf("_b") >= 0 )
	{
		tabView_b.removeTab(tabView_b.getTab(tabView_b.get('activeIndex')));
		if (tabView_b.get('tabs').length == 0)
		{
			Page.add_empty_tab(tabView_b);
		}
	}
	
}

Page.updateOneCitationsArray_after_delete = function(arr)
{
	for (var i=0; i<arr.length; i++)
	{
		if (Page._current_citation['citation_id'] == arr[i]['citation_id'])
		{
			arr.splice(i,1);
			break;
		}
	}
}

Page.updateTabView = function(tabView, arr)
{
	for (var i=0; i<arr.length; i++)
	{
		if (Page._current_citation['citation_id'] == arr[i]['citation_id'])
		{
			var tab_to_be_updated = tabView.getTab(i+1);
			var full_citation_suffix = Page.get_citation_suffix_from_label(tab_to_be_updated.get('label'));
			tab_to_be_updated.set('content', Page.build_tab_content(Page._current_citation, full_citation_suffix));
		}
	}
}

Page.showCheckAuthorPanel = function(responseObj)
{
	var all_authors_empty = false;
	var html = '';
  html += 'The following author(s) are unverified: <br />';		
  for (var i = 0; i < 6; i++)
	{
		if (responseObj.citations[i][0] == -1)
		{	
			// COMMENTED: Use the value from responseObj instead since php does trimming.
			//var current_fn = document.getElementById('author' + i + 'fn').value;
			//var current_ln = document.getElementById('author' + i + 'ln').value;
			
			var current_fn = responseObj.citations[i][2];
			var current_ln = responseObj.citations[i][1];
	  	html += 'Last Name:<input type="text" value="'+current_ln+'" id="author'+i+'ln_check"/>&nbsp;';
			html += 'First Name:<input type="text" value="'+current_fn+'" id="author'+i+'fn_check"/>';
			html += '&nbsp;<input type="checkbox" id="author'+i+'ln_checkbox"/>';
			if (responseObj.citations[i][3].length > 0)
			{
				html += '<center><div align="left">';
				for (var j = 0; j < responseObj.citations[i][3].length; j++)
				{
					var tempLastname = 'author'+i+'ln_check';
					var tempFirstname = 'author'+i+'fn_check';
					html += '<br>Maybe: <a onclick="document.getElementById(\'' + tempLastname + '\').value = \'' + responseObj.citations[i][3][j][1]+ '\'; document.getElementById(\'' + tempFirstname + '\').value = \'' + responseObj.citations[i][3][j][2]+ '\';">' + responseObj.citations[i][3][j][1] + ', ' + responseObj.citations[i][3][j][2] + '</a>';
				}
				html += '</div></center>';
			}
			html += '<br><br>';
		}
		else if(responseObj.citations[i][0] == -2) // All 6 authors are empty.
		{
			all_authors_empty = true;	
		}
	}
	
	var merge = "";
	if((responseObj.pre_merge_id1 != undefined) && (responseObj.pre_merge_id2 != undefined)) merge = 'merge';
	
	if(all_authors_empty)
	{
		html += 'Please enter at least one author.<br><br>';
		html += '<input type="button" value="Save Anyway" onclick="Page.checkInputAndSave(\'create_authors\',\''+ merge +'\',0);" />&nbsp;&nbsp;';
		html += '<input type="button" value="Go Back And Add Authors" onclick="Page.panel1.hide();" />';
	}
	else
	{
			html += '<input type="button" title="Unchecked authors will be saved as unverified."  value="Verify Checked Authors" onclick="Page.checkInputAndSave1(\'create_authors\',\''+ merge +'\',0,1);" />&nbsp;&nbsp;';
		//html += '<input type="button" value="Do Not Add Authors" onclick="Page.uncheckAllAuthorCheckboxesInPanel();Page.checkInputAndSave1(\'create_authors\',\''+ merge +'\',0,0);" />';
	}
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

//abhinav


Page.checkInputAndSave1 = function(type, merge, timestamp,verified)
{	
	var response;
	if (type == "check_authors")
	{	
		// Continue 
		Page.create_page_dot_sentData(timestamp);
	}
	else if (type == "create_authors")
	{		
	
		if (Page.getCheckedValue(document.forms['cForm'].elements['verified']) == 1) 
		{
			if(Page.validate_verified_entry() == 0)
			{
				if(confirm("Entry will remain unverified because not all the authors are verified."))
				{
					if (Page.update_page_dot_sentData(timestamp) == false)
					{
						return false;
					}
					Page.sentData['verified'] = 0;
			//		Page.panel1.hide();
				}
				else
				{
					return;
				}	
			}
			else
			{
				
				if (Page.update_page_dot_sentData(timestamp) == false)
				{
					return false;
				}
			//	Page.panel1.hide();
			}
		}
		else
		{
			
			if (Page.update_page_dot_sentData(timestamp) == false)
			{
				//return false;
			}
		//	Page.panel1.hide();	
		}
	}
	var pre_merge_ids = "";
	if ((merge == "merge") && (Page.pre_merge_id1 != undefined) && (Page.pre_merge_id2 != undefined))
	{
		pre_merge_ids = '"pre_merge_id1": "' + Page.pre_merge_id1 + '", "pre_merge_id2": "' + Page.pre_merge_id2 + '", ';
	}
	Page.panel1.hide();
	//var jsonStr = '{"request": {"type": "' + type + '",  ' + pre_merge_ids + '"citations": ' + YAHOO.lang.JSON.stringify(Page.sentData) + '}}';
	
	var jsonStr = '{"request": {"type": "' + type + '","verified": "' + verified + '",  "coll_id":"' + Page.currentCollection + '", ' + pre_merge_ids + '"citations": ' + YAHOO.lang.JSON.stringify(Page.sentData) + '}}';
	Ajax.SendJSON('services/citations.php', Page.onResponseCheckAuthors, jsonStr);		
}







Page.uncheckAllAuthorCheckboxesInPanel = function()
{
	for(var i = 0; i < 6; i++)
	{
		if(document.getElementById('author'+i+'ln_checkbox')) // Check if exist
		{						
			document.getElementById('author'+i+'ln_checkbox').checked = false;
		}
	}
}

Page.createVerifiedAuthor_request = function(obj)
{
	
	// Check the form for checkboxes
	for(i=0; i<6; i++) {
		if(obj.elements['author'+i+'ln_checkbox'].checked){
		}
	} 
}

// SendText		- Used by
/*Page.SendText = function(callbackmethod, str, type)
{
	var jsonStr = '{"request": {"type": "'+type+'",  "entries": '+ YAHOO.lang.JSON.stringify(str) +', "submitter": "' + Page.submitter + '", "entryTime": ""}}';
	Ajax.SendJSON('services/parser.php', callbackmethod, jsonStr);
}*/

// SendXML 		- Call Ajax.SendXML
//				- Ajax.SendXML send data to php using XML (POST method) instead of URL (GET method)
Page.SendXML = function(callbackmethod, xmlStr)
{
		Ajax.SendXML('services/xml.php', callbackmethod, xmlStr);
}



// inputMethod	- This function is called from index.php
//				- It will check for the input from the menu in index.php
//				- Everything starts here
Page.inputMethod = function(input_method)
{
	var html = "";

	Page.input_method = input_method;

	if(input_method == 3)
	{		
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
	}


	else if(input_method == 6)
	{
		html += Page.createproxy_html(Page.submitter, true);
	}
	else if(input_method == 7)
	{
		Page.viewproxy_request(Page.submitter);	
	}
	
	//Abhinav
	else if(input_method == 0)
	{
		Page.viewsortproxy_request(Page.submitter);	
	}	
	
	else if(input_method == 8)
	{
		Page.viewuser_request(Page.submitter);	
	}
	else if(input_method == 9)
	{
		Page.citation_toggle = true;
		Page.input_method = 9;
		Page.current_page = 1;
		Page.current_viewable_pages = new Array();	
		Page.setOwnerDiv();
	}

	else if(input_method == 11)
	{
		var xmlStr = "<root><faculty><![CDATA["+Page.submitter+"]]></faculty></root>";
		Ajax.SendXML('services/faculty.php', Page.onResponse, xmlStr);	
	}

	else if(input_method == 15)
	{
		//	html += Page.selectOwner();
	}
	else if(input_method == 16)
	{
		html += Page.createuser_html(Page.submitter);
	}
	else{}
	
	if ((input_method == 6) || (input_method == 7) || (input_method == 8) || (input_method == 16) || (input_method == 0))  //Abhinav
	{
		document.getElementById('citations').style.display = 'none';
		Page.right_column_display('none');
		document.getElementById('secondary').innerHTML = html;
		document.getElementById('secondary').style.display = '';
	}
	else if (input_method != 3)
	{
		document.getElementById('welcome').style.display = 'none';		//hide		
		document.getElementById('top').style.display = '';				//show
		document.getElementById('home').style.display = 'none';			//hide
		document.getElementById('options').style.display = 'none';		//hide
		document.getElementById('insert').innerHTML = html;				//set
		document.getElementById('insert').style.display = '';			//show
		document.getElementById('citations').style.display = 'none';	//hide
		document.getElementById('secondary').style.display = 'none';	//hide
	}
	else 
	{}
	
	// Don't need this. Just call Page.initializeAutocomplete in nextEntry()
	/*if ((input_method == 3) || (input_method == 4) || (input_method == 5) || (input_method == 12)) {
		//Page.initializeAutocomplete('title');  //must be called after .innerHTML is set
		//Page.initializeAutocomplete('year');   //must be called after .innerHTML is set
	}*/
}


// *********************************************************************

Page.searchCitations_request = function(search_type)
{	
	Page.keywords = document.getElementById('search_keywords').value;
	Page.currentCollection = 'search';
	Page.current_get_type = search_type;
	Page.current_page = 1;
	Page.current_viewable_pages=new Array(); 
	Page.getCitations(Page.current_page, search_type);
}

// editFacultyInfoForm - Used by inputMethod(11)
Page.editFacultyInfoForm = function(response)
{
	var html = '<form name="editFacultyInfoForm">';
	html += '<center><p>Edit Faculty Page [';
	
	var element = response.getElementsByTagName('facultyinfo')[0];
	if(element != null && element.hasChildNodes()) 
		html += element.firstChild.data;
	else html += "Error: No User!";						
	
	html += ']</p></center>';
	html += '<table style="border: 2px solid #7D110C;">';
	html += '<th>Field</th><th>Value</th>';
	
	element = response.getElementsByTagName('title1')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Title 1","title1",element.firstChild.data,"60",""); 
	else html += Page.printTableRow("Title 1","title1","","60","");
	
	element = response.getElementsByTagName('title2')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Title 2","title2",element.firstChild.data,"60","");
	else html += Page.printTableRow("Title 2","title2","","60","");
	
	element = response.getElementsByTagName('title3')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Title 3","title3",element.firstChild.data,"60","");
	else html += Page.printTableRow("Title 3","title3","","60","");
	
	element = response.getElementsByTagName('phone')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Phone","phone",element.firstChild.data,"20","");
	else html += Page.printTableRow("Phone","phone","","20","");
	
	element = response.getElementsByTagName('office')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Office","office",element.firstChild.data,"20","");
	else html += Page.printTableRow("Office","office","","20","");
	
	element = response.getElementsByTagName('email')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Email","email",element.firstChild.data,"40","");
	else html += Page.printTableRow("Email","email","","40","");
	
	element = response.getElementsByTagName('link1')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Link 1","link1",element.firstChild.data,"60","");
	else html += Page.printTableRow("Link 1","link1","","60","");
	
	element = response.getElementsByTagName('link1_title')[0];
	if(element != null && element.hasChildNodes()) 
		html += Page.printTableRow("Link 1 Title","link1_title",element.firstChild.data,"60","");
	else html += Page.printTableRow("Link 1 Title","link1_title","","60","");
	
	element = response.getElementsByTagName('education')[0];
	if(element != null && element.hasChildNodes()) 
		html +=	Page.printTextareaRow("Education","education",element.firstChild.data,"5","70");
	else html += Page.printTextareaRow("Education","education","","5","70");
	
	element = response.getElementsByTagName('professional_experience')[0];
	if(element != null && element.hasChildNodes()) 
		html +=	Page.printTextareaRow("Professional Experience","professional_experience",element.firstChild.data,"10","70");
	else html += Page.printTextareaRow("Professional Experience","professional_experience","","10","70");
	
	element = response.getElementsByTagName('research_interests')[0];
	if(element != null && element.hasChildNodes()) 
		html +=	Page.printTextareaRow("Research Interests","research_interests",element.firstChild.data,"10","70");
	else html += Page.printTextareaRow("Research Interests","research_interests","","10","70");
	
	html += '</table>';
	html += "<p><a href='#' onclick=\"Page.saveFacultyInfo('"+response.getElementsByTagName('facultyinfo')[0].firstChild.data+"');return false;\">Save</a></p>";
	html += '</form>';
	return html;
}

// saveFacultyInfo - Used by editFacultyInfoForm
Page.saveFacultyInfo = function(user)
{
	var cdatastart = "<![CDATA[";
	var cdataend = "]]>";
	var xmlStr = "<root><save><username>"+user+"</username>";
	var theForm = document.forms[0]
	for(i=0; i<theForm.elements.length; i++){
		var value = "";
		xmlStr += "<"+theForm.elements[i].name+">"+cdatastart;
		if(theForm.elements[i].type == "text" || theForm.elements[i].type == "textarea" || theForm.elements[i].type == "button"){
			xmlStr += theForm.elements[i].value;
	  	}
	  	else if(theForm.elements[i].type == "checkbox"){
			xmlStr += theForm.elements[i].checked;
	  	}
	  	else if(theForm.elements[i].type == "select-one"){

	  	}
		xmlStr += cdataend+"</"+theForm.elements[i].name+">\n";
   	}
	xmlStr += "</save></root>";
		
	Ajax.SendXML('services/faculty.php', Page.onResponse, xmlStr);
}

Page.newVerifiedAuthor = function(i)
{
	var lastname, firstname;
	Page.currentNewAuthorNum = i;

	if (i < 0)
	{
		lastname = ""; 
		firstname = ""; 	
	}
	else
	{
		lastname = document.getElementById('author' + i + 'ln').value; 
		firstname = document.getElementById('author' + i + 'fn').value; 
	}

	var html = 'New Lastname: <input type="text" id="new_auth_ln" name="new_auth_ln" value="' + lastname + '"/> New Firstname: <input type="text" id="new_auth_fn" name="new_auth_fn" value="' + firstname + '" /><br><br> ';
	html += '<input type="button" value="Submit" onclick="Page.submitNewVerifiedAuthor();"';
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.submitNewVerifiedAuthor = function()
{
	
if ((document.getElementById('new_auth_ln').value == "") || (document.getElementById('new_auth_fn').value == ""))
	{
		Page.panel1_alert_message('Fields Empty!', '');
	}
	else {
		var jsonStr = '{"request": {"type": "new_author",  "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "lastname": "' + document.getElementById('new_auth_ln').value + '", "firstname": "' + document.getElementById('new_auth_fn').value + '"}}}';
		Page.panel1.hide();
		Ajax.SendJSON('services/citations.php', Page.newVerifiedAuthor_response, jsonStr);	
	}
}

Page.newVerifiedAuthor_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		// compare sent to received as error check
		
		document.getElementById('author' + Page.currentNewAuthorNum + 'ln').value = responseObj.lastname;
		document.getElementById('author' + Page.currentNewAuthorNum + 'fn').value = responseObj.firstname;

// remove U
	}		
}

Page.printCitationSaveButton = function(_citation, fieldFlag, newFlag) {
	var timestamp = _citation.entryTime;
	var html = "";
	//var pointer_style = 'onmouseover="this.style.cursor=\'pointer\';" onmouseout="this.style.cursor=\'default\'"';
	var pointer_style = 'class="pointerhand"';
	var merge = (newFlag == "merge") ? newFlag : "";
	
	html += '<table width="100%" border="0">';
	
	if(fieldFlag == "text")
	{
		html += Page.printBackTD(pointer_style);
		
		html += '<td><center>';
		html += Page.printCitationEditButton(Page._current_citation, '+3');
		
		html += Page.printNextTD(pointer_style);

		html += '</tr>';	
	}
	else
	{
		var tempType;
		if (Page.input_method == 1) {
			tempType = "save_byTimestamp_unverified";
		}
		else if (Page.input_method == 2) {
			tempType = "save_byTimestamp_unverified";
		}
		else if (Page.input_method == 4) {
			tempType = "save_byFac_unverified";
		}
		else if ((Page.input_method == 12) || (Page.input_method == 9)) {
			tempType = "save_byFac_one";
		}
		tempType = "check_authors";
		
		html += Page.printBackTD(pointer_style);
		
		html += '<td><center>' + Page.printTableRowCheckBox(_citation, 'Verified', 'verified') + '<br />';
		html += '<span class="link pointerhand" onclick="Page.checkInputAndSave(\'' + tempType + '\', \'' + merge + '\', \'' + timestamp + '\');return false;"><font size="+3">Save</font></span></center></td>';
		
		html += Page.printNextTD(pointer_style);
		
		html += '</tr>';
	}
	
	html += '</table>';
	return html;
}

Page.printBackTD = function(pointer_style)
{
	var html = '';
	if (((Page.state == 2) && (Page.current_newly_added_num > 0)) || ((Page.state == 3) && (Page.current_row_num > 0))) {
		
		html += '<tr><td id="goToBack" title="Previous citation in list" align="left" width="10%" ' + pointer_style + ' onclick="Page.goToBackCitation();"><font size="+2">Back</font></td>';
	}
	else {
		html += '<tr><td id="goToBack" align="left" width="10%"></td>';	
	}
	return html;
}

Page.printNextTD = function(pointer_style)
{
	var html = '';
	if (((Page.state == 2) && (Page.current_newly_added_num < Page.newly_added_citations.length-1)) || ((Page.state == 3) && (Page.current_row_num < Page._citations.length-1))) {
		html += '<td id="goToNext" title="Next citation in list" align="right" width="10%" ' + pointer_style + ' onclick="Page.goToNextCitation();"><font size="+2">Next</font></td>';
	}
	else {
		html += '<td id="goToNext" align="right" width="10%" ></td>';	
	}
	return html;
}

Page.checkForCitationChanges = function()
{

	var full_citation_suffix = "";
	var cit1 = '';
	var cit2 = '';
	if (document.getElementById('panel3_div').style.display == '')
	{
		full_citation_suffix = Page.get_citation_suffix_of_active_tab(tabView_b);
		Page.create_working_citation(full_citation_suffix);
		cit1 = Page.citations_array_b[tabView_b.get('activeIndex')-1];
		cit2 = Page.working_citation;
	}
	else if (!document.getElementById('author0ln'))  // don't check for changes if there is no citation.
	{
		return true;
	}
	else if (document.getElementById('panel1_div').style.display == '')
	{
		Page.create_working_citation('');
		cit1 = Page._citations[Page.current_row_num];
		cit2 = Page.working_citation;	
	}
	Page.data_keys = new Array();
	for (key in cit2)
	{
		if ((key != 'raw') && (key != 'owner') && (key != 'submitter') && (key != 'user_id') && (key != 'format'))
		{
			Page.data_keys.push(key);
		}
	}
	
	var difference_string = Page.compareTwoCitations(cit1, cit2);
/*	if (difference_string != '')
	{
		if (confirm('Changes have been made.  These changes will be lost unless you click Cancel and then Save.'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{*/
		return true;
//	}
}

Page.goToNextCitation = function()  
{
	if (Page.checkForCitationChanges())
	{
		if (Page.state == 2)
		{
			if (Page.current_newly_added_num < Page.newly_added_citations.length-1)
			{
				Page.current_newly_added_num++;
				if (Page.similar_citations_array[Page.newly_added_citations[Page.current_newly_added_num].citation_id])
				{
					Page.panel2.setHeader("Your new citation is on the left. Potential matches already in the database are on the right.");
		
					Page.editTwoCitations(new Array(Page.newly_added_citations[Page.current_newly_added_num]), Page.similar_citations_array[Page.newly_added_citations[Page.current_newly_added_num].citation_id]);
				}
				else
				{
					Page.oneCitationInPanel(Page.newly_added_citations[Page.current_newly_added_num], "", "");
					document.getElementById('panel2_div').innerHTML = ''; 
				}
			}
		}
		else // Page.state = 3
		{
			if (Page.current_row_num < Page._citations.length-1)
			{
				Page.current_row_num++;
				Page.oneCitationInPanel(Page._citations[Page.current_row_num], "", "");
			}
		}
	}
}

Page.goToBackCitation = function()
{
	if (Page.checkForCitationChanges())
	{
		if (Page.state == 2)
		{
			if (Page.current_newly_added_num > 0)
			{
				Page.current_newly_added_num--;
				if (Page.similar_citations_array[Page.newly_added_citations[Page.current_newly_added_num].citation_id])
				{
					Page.panel2.setHeader("Your new citation is on the left.");
		
					Page.editTwoCitations(new Array(Page.newly_added_citations[Page.current_newly_added_num]), Page.similar_citations_array[Page.newly_added_citations[Page.current_newly_added_num].citation_id]);					
				}
				else
				{
					Page.oneCitationInPanel(Page.newly_added_citations[Page.current_newly_added_num], "", "");
					document.getElementById('panel2_div').innerHTML = ''; 
				}
			}	
		}
		else // Page.state = 3
		{
			if (Page.current_row_num > 0)
			{
				Page.current_row_num--;  // Page counter
				Page.oneCitationInPanel(Page._citations[Page.current_row_num], "", "");
			}
		}
	}
}

Page.printCitationEditButton = function(_current_citation, size) {
	
	if(size == undefined) size = '+3';
	
	var html = "";
	html += '<span class="link pointerhand" onclick="Page.oneCitationInPanel(Page._current_citation, \'\', \'\'); document.getElementById(\'panel2_div\').innerHTML = \'\'; document.getElementById(\'panel1_div\').style.display = \'\';  return false;\"><font size="'+size+'">Edit</span></font>';
	return html;
}

Page.printUploadDialog = function(_citation, citation_suffix) {
	var filename = _citation.filename;
	var citation_id = _citation.citation_id;
	
	var html = '';
	var currentDirectory = unescape(document.URL.substring(0,(document.URL.lastIndexOf("/")) + 1));
	var upload_div = 'upload_div'+citation_suffix;
	var status = '';
	
	// Attach Label
	var attached_filename_suffix_obj = document.getElementById('attached_filename'+citation_suffix);
	var attached_filename_obj = document.getElementById('attached_filename');
	
	if(citation_suffix == "")   // Saving / Regular
	{
		if(attached_filename_obj.value != '' && attached_filename_obj.value != undefined) 
		{
			status = 'The file <b>' + attached_filename_obj.value + '</b> was attached successfully.<br/>To save the file, click on "Save".';
		}
	}
	else 
	{
		// Setting attached suffix value to empty.
		if(attached_filename_suffix_obj != null) attached_filename_suffix_obj.parentNode.removeChild(attached_filename_suffix_obj); // Remove element if it exists.
		attached_filename_obj.value = '';  	// Set to empty value if input suffix is undefined
		html += '<input type="hidden" id="attached_filename'+ citation_suffix +'" name="attached_filename'+ citation_suffix +'" value="">'; // Input element does not exist. Create it.
	}
	
	// Filename label
	if((filename == "") || (filename == undefined)) {
		html += '<b>File:</b> None. <br>';
	}
	else {
		html += '<b>File:</b> <a href="' + Page.basePdfDirectory + citation_id + '/' + filename + '" target="_blank">' + filename + '</a><br>';
	}
	
	// Print Upload Dialog Div or Form
	html += '<div id="'+upload_div+'">';
	html += Page.printFileUploadDiv(citation_id, citation_suffix, status);
	html += '</div>';
	
	return html;
}

Page.clearAttachedFile = function(citation_suffix)
{
	if(citation_suffix == '' || citation_suffix == undefined)
	{ 	
		citation_suffix = Page.get_citation_suffix_of_active_tab(tabView_b);
	}
	
	if(document.getElementById('attached_filename'+citation_suffix)) document.getElementById('attached_filename'+citation_suffix).value = ''; 
	if(document.getElementById('attached_filename')) document.getElementById('attached_filename').value = '';
	if(document.getElementById('status_div'+citation_suffix)) document.getElementById('status_div'+citation_suffix).innerHTML = '';
	if(document.getElementById('status_div')) document.getElementById('status_div').innerHTML = '';
	return true;
}

Page.printFileUploadDiv = function(citation_id, citation_suffix, status)
{
	var upload_div = 'upload_div'+citation_suffix;
	var label_div = 'label_div'+citation_suffix;
	var status_div = 'status_div'+citation_suffix;
	var loading_div = 'loading_div'+citation_suffix;
	var submit_button = 'submit_button'+citation_suffix;
	
	// Reset
	if(document.getElementById(upload_div)) document.getElementById(upload_div).innerHTML = '';
	if(document.getElementById('upload_citation_suffix')) document.getElementById('upload_citation_suffix').value = 'initial';
	
	var html = '';	
	var clear_attached_file_html = '&nbsp;&nbsp;<span class="pointerhand" style="color:#7D110C"; onclick="Page.clearAttachedFile(\''+citation_suffix+'\')">[Clear]</span>';
	
	//Status label
	html += '<div id="'+status_div+'" align="center" class="f1_upload_status">';
	if(status != '') 
	{	
		html += '<span>' + status + '</span>';
		html += clear_attached_file_html;
	}
	html += '</div>';

	// Can't have nested forms in HTML
	html += '<div id="'+label_div+'" align="center" class="f1_upload_form">';
	html += 'Attach a PDF (*.pdf) file.<br/>';
	
	// Hidden inputs to send to PHP via POST
	html += '<input type="hidden" name="upload_citation_id' + citation_suffix + '" value="' + citation_id + '">';  // Pass citation_id through.
	
	// Decide onchange html. Check for b only since a is always text.
	var onchange_html = 'document.cForm.'+submit_button+'.disabled=false';
	html += '<label>File:  <input id="myfile'+citation_suffix+'" name="myfile'+citation_suffix+'" type="file" size="30" onchange="'+onchange_html+'"/></label>';
	
	var onclick_html = 'onclick="Page.updateUploadGlobals(\''+citation_suffix+'\');"';
	html += '<input type="submit" name="'+submit_button+'" value="Attach" ' + onclick_html + ' disabled=true /><br />';
	html += '<label>(File size limit: 10 MB.)</label>';
	html += '</div>';
	
	// Note for debugging. Only one cForm target (upload_target2 iframe) is used.
	html += '<div id="'+loading_div+'" class="f1_upload_process">Loading...<br/><img src="' + Page.document_root + 'images/loading.gif" /><br/></div>';

	return html;
}

Page.updateUploadGlobals = function(citation_suffix)
{
	document.getElementById('upload_citation_suffix').value = citation_suffix;
	Page.upload_citation_suffix = citation_suffix;
}

// inputFields	- Called by inputMethod() [#3 Enter information by hand]
//				- Generate empty forms
Page.blankInputFields = function(_citation, returntype)
{
	var html = "";
	html += Page.printCitationBackClearSaveButtons();	// TO-DO: Needs Work
	html += Page.pubtypeMenu("","","");
	html += '<div id="input_fields">';
//	html += '<div id="input_fields"  style="background-color:#FF0">';

	html += Page.enterCitationInfo("","other");
	html += '</div>';
	html += Page.printCitationBackClearSaveButtons();   // TO-DO: Needs Work
	document.getElementById('citations').innerHTML = html;
	initializeAutocompleteFields("");
}

// EditLayout.js

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

function print_r2(theObj){
  var text = "";
  if(theObj.constructor == Array ||
     theObj.constructor == Object){
    text += "Array\n(\n";
    for(var p in theObj){
      if(theObj[p].constructor == Array||
         theObj[p].constructor == Object){
	text += "\t["+p+"] => "+typeof(theObj)+"\n";
    text += "Array\n(\n";
        text += print_r2(theObj[p]);
        text += ")\n";
      } else {
	text += "\t["+p+"] => "+theObj[p]+"\n";
      }
    }
    text += ")\n";
  }
  return text;
}

Page.writeOptionsForListCitations = function()
{
	var html = '';
	
	if (document.getElementById('exportformattype'))
	{
		document.getElementById('exportformattype').selectedIndex = "";
	}
	
	html += '<table width="100%">';
	html += '<tr style="vertical-align:top">';
	html += '<td style="text-align:left;vertical-align=top">View:&nbsp;</td><td>' + Page.printCollectionNamesMenuForViewing() + '</td>';

	html += '<td style="text-align:right;vertical-align=top">';
	html += 'Enter new citations by ' + Page.printInputOptionsMenu();
	html += '</td>';
	
	html += '</tr>';
	
	html += '</table>';		
//	if (Page.owner != "")
	if (Page.loggedIn && Page.hasProxy)
	{
		document.getElementById('options').innerHTML = html;
		document.getElementById('options').style.display = '';		//show
	}
	else
	{
		document.getElementById('options').style.display = 'none';		//hide
	}
	
	
	// Print right_col
	html = '';
	Page.selectAllOrNone = "select";
	
	if (Page._citations.length > 0)
	{
		html = Page.print_ViewOptions_rm_div();	
		html += Page.print_ExportCitations_rm_div();
	}
	//if (Page.owner != "")
	if (Page.loggedIn && Page.hasProxy)
	{
		html += Page.print_Collections_rm_div();
		html += Page.print_CompareMergeCitations_rm_div();
	}	
	if (navigator.userAgent.indexOf("Firefox") == -1)
	{
		html += Page.bestViewedWith();
	}
	document.getElementById('right_col').innerHTML = html;
	Page.right_column_display('all');
	
}

Page.bestViewedWith = function()
{
	var html = '';
	html += '<div id="footer" style="display:block;text-align:center;">';
	html += '<p><a href="http://www.mozilla.com"><img src="' + Page.document_root + 'images/fifefox.png" alt="Get Firefox!" border="0" style="vertical-align:middle;width:30px;height:30px;" />';
	html += '&nbsp;Best viewed with Firefox.</a></p>';
	html += '</div>';
	return html;
}

Page.copyCitationsForFormat = function(citations)
{
	var json_citations = YAHOO.lang.JSON.stringify(citations);
	var citations_copy = eval("(" + json_citations + ")");
	return citations_copy;	
}

Page.mapMissingFields = function(cit_copy, currentFormat)
{
	var missing_fields = new Array();
	
	// Loop here to map citation number	
	var pubtype = '';
	var emptyFields = false;
	Page.currentMissingField = false;
	
	for(var i = 0; i < cit_copy.length; i++)
	{
		// Refer value for easy coding
		var _citation = cit_copy[i];  // Single citation
		if(Page.pubtypes_json[_citation.pubtype] == undefined) { 
			pubtype = "misc";  // Default value if no pubtype available in pubtype_def
			_citation.pubtype = "misc";
		}
		else { pubtype = _citation.pubtype; }
		// Pinkified empty or undefined apa_required_fields		
		// Build required element list based on pubtype.
		if(Page.fillEmptyFields(_citation, pubtype, currentFormat) == true)
		{
			missing_fields.push(i);
		}
	}
	
	return missing_fields;
}

Page.fillEmptyFields = function(_citation, pubtype, format)
{
	var missing = false;	
	var	required_fields = 'apa_required_fields';
	
	if(format == 'bibtex') { 
		required_fields = 'bibtex_required_fields';}
	else if(format == 'mla') {
		required_fields = 'mla_required_fields';}
	else {
		required_fields = 'apa_required_fields';
	}
		
	// Build required element list based on pubtype.
	for(var i in Page.pubtypes_json[pubtype][required_fields])
	{
		var id = Page.pubtypes_json[pubtype][required_fields][i];
		
		if(Page.fields_arr[id] != undefined) 
		{
			if(_citation[id] == "" || _citation[id] == undefined || _citation[id] == "unknown" || _citation[id] == "unknown,")	{
				_citation[id] = '<b>['+Page.fields_arr[id][0].toUpperCase()+']</b>'; // Uppercase all letters.
				missing = true;
			}
			else {}
		}
		else if(id == "title" || id == "year") 
		{
			if(_citation[id] == "" || _citation[id] == undefined || _citation[id] == "unknown")	{
				_citation[id] = '<b>['+id.toUpperCase()+']</b>'; // Uppercase all letters.
				missing = true;
			}
			else {}
		}
		else { 
	
		} 
	}
	
	// need to check for first author as well.
	if ((_citation.author0ln == "") || (_citation.author0ln == undefined) || _citation[id] == "unknown"){
		if((_citation.author0fn == "") || (_citation.author0fn == undefined) || _citation[id] == "unknown"){
			_citation.author0ln = '<b>[AUTHOR 1 NAME]</b>';
			_citation.author0fn = '';
			missing = true;
		}
	}
	
	return missing;
}

// List Citations
Page.listCitations = function()
{	
	Page.state = 1;
	
	var paging_html = '';
	html = '';
	if (Page.currentCollection == 'search')
	{
		document.getElementById('search_keywords').value = Page.keywords;
	}
	
	if (Page._citations.length != 0)
	{
		paging_html = Page.printPagingAndFormatTable();
		html += paging_html;
	}
	else
	{
		html += 'No results were found.';
	}

	//var highlight = 'class="white_bgcolor grey_highlight"';  // Will highlight <tr> and <td>
//	var pointer_style = 'class="pointerhand"';
//	var td_highlight = '';

//	html += '<table style="background-color:#FFF">';

	var highlight = 'class="white_bgcolor grey_highlight"';  // Will highlight <tr> and <td>
	var pointer_style = 'class="pointerhand"';
	var td_highlight = '';

	html += '<table style="background-color:#FFF; border:0px;border-collapse: collapse;">';
	
	// Make a copy of citations
	var cit_copy = Page.copyCitationsForFormat(Page._citations);
	
	// Find missing fields
	var missing_fields = Page.mapMissingFields(cit_copy, Page.currentFormat);
	
	var anchor_is_in_current_page = false;
	
	Page.tt_col_arr = new Array();
	
	for (var i=0; i < cit_copy.length; i++) 
	{
		// If undefined, always on the same page, first citation.
		if(Page._current_citation.citation_id == undefined || cit_copy[i].citation_id == Page._current_citation.citation_id)
		{
			anchor_is_in_current_page = true; 		// Toggle for checking current citation on which page.
		}
		
		html += '<a id="a_'+cit_copy[i].citation_id+'" name="a_'+cit_copy[i].citation_id+'"></a>';  // Anchor used by moveWindow()
		
		
	//	html += '<tr id="row_' + i + '" align=\"left\" ' + highlight +'>';
		
	//	html += '<tr id="row_' + i + '" align=\"left\">';
		html += '<tr id="row_' + i + '" align=\"left\" ' + highlight +'>';
	
		/**************************************************/
		html += '<td style="width:100%"><table><tr>';
		// unverified icon
		if (cit_copy[i].verified == 0)
		{
			html += '<td id="verified_' + i + '" valign="top"><font color="red"><b>U</b></font></td>';
		}
		else
		{
			html += '<td id="verified_' + i + '" valign="top">&nbsp;</td>';
		}
		
		//if (Page.owner != "")
		if (Page.loggedIn && Page.hasProxy)
		{
		// check box	
			html += '<td style="vertical-align:top"><input type="checkbox" name="citation_checkboxes' + cit_copy[i].citation_id + '" id="cb_' + cit_copy[i].citation_id + '" value="' + cit_copy[i].citation_id + '"';
			if (Page.selected_citations.length != 0)
			{
				for (var j=0; j < Page.selected_citations.length; j++) 
				{
					if (Page.selected_citations[j] == i)
					{
						html += ' checked="checked"';
					}
				}
			}
			html += '></td>';
		}
							
		if(cit_copy[i].filename == undefined || cit_copy[i].filename == "" || cit_copy[i].filename == null)
		{
			html += '<td style="vertical-align:top"><img src="' + Page.document_root + 'images/blank_icon.gif"></td>'; 
		}
		else
		{
			var base_url = Page.basePdfDirectory;
			var cur_folder = cit_copy[i].citation_id;
			var cur_filename = cit_copy[i].filename;
			
			html += '<td style="vertical-align:top"><img src="' + Page.document_root + 'images/pdficon_small.gif" onclick="window.open(\''+base_url+cur_folder+'/'+escape(cur_filename)+'\');" ' + pointer_style + '></td>';	
			//' + cit_copy[i].citation_id + '
		}
		
		if (Page.show_citation_id_flag == 1)
		{
			html += '<td style="vertical-align:top">' + cit_copy[i].citation_id + '</td>';
		}
		
		// Check if TD should be highlighted due to missing fields (LOOP)
		var missing_field_exist = false;
		for(var j in missing_fields)
		{
			if(missing_fields[j] == i)
			{
				missing_field_exist = true;
			}
		}
		
		// Check for LOOP success
		if ((missing_field_exist) && (Page.highlight_citations_with_missing_info_flag == 1)) {
			//td_highlight = 'style="background-color:#FFCC99;"';
			//td_highlight = 'class="missing_field"';
			html += '<td width="95%" style="background-color:#FFCC99;" id="cell_' + i + '" ' + pointer_style;
			
		}
		else {
			
			//td_highlight = 'class="white_bgcolor"';	// Not needed but for consistency
			html += '<td width="95%"  id="cell_' + i + '" ' + pointer_style;
		}
		//html += '<td width="95%" td_highlight id="cell_' + i + '" ' + pointer_style;
		
		if (Page.loggedIn && Page.hasProxy)
		{
			html += ' onMouseUp="Page.state=3; Page.current_row_num=' + i + '; Page.editOneCitation(' + i + ');"';
		}
		html += '>';
	//	html += '<td width="95%" id="cell_' + i + '" ' + td_highlight +' style="vertical-align:top">';
		
		html += '<a name="' + cit_copy[i].citation_id + '"></a>';

		/*********************************/
		// Print format
		
		if (Page.currentFormat == 'apa')
		{
			html += Page.printAPAStyleCitation(cit_copy[i]);
			// Required fields
			//html += "<br><b>" + cit_copy[i].pubtype + ":</b> " + Page.pubtypes_json[cit_copy[i].pubtype].apa_required_fields + "";
		}
		else if (Page.currentFormat == 'mla')
		{
			html += Page.printMLAStyleCitation(cit_copy[i]);
		}
		else if (Page.currentFormat == 'bibtex')
		{
			html += Page.printBibtexStyleCitation(cit_copy[i]);
		}
		html += '</td>';
		html += '</tr></table></td>';
		//if (Page.owner != "")
		if (Page.loggedIn && Page.hasProxy)
		{			
			Page.tt_col_arr.push("coll_td_"+i);  // Array for tooltips Page.tt_col.
		//	html += '<td valign="top" ' + pointer_style + ' id="coll_td_' + i + '" title="" onmouseover="Page.getCollectionsGivenCitationID_request(' + cit_copy[i].citation_id + ');"><font color="red"><b>C</b></font></td>';
		var vertical_or_horizontal = 'vertical';
			if (Page.set_compact_view_flag == 1)
			{
				vertical_or_horizontal = 'horizontal';
			}
			html += '<td width="10%">&nbsp;</td><td>' + Page.writeListCitationsRightTable(vertical_or_horizontal, i, cit_copy, pointer_style) + '</td>';
	/*		html += '<td width="10%">&nbsp;</td><td><table>';
			
			html += '<tr><td valign="top" ' + pointer_style + ' onMouseUp="Page.state=3; Page.current_row_num=' + i + '; Page.editOneCitation(' + i + ');">Edit</td></tr>';
		
			// if (Page.inArray(cit_copy[i].citation_id, Page.similar_citations_array))
			if (Page.similar_citations_array[cit_copy[i].citation_id])
			{				
				html += '<tr><td id="similar' + i + '" valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.showSimilarCitations(' + i + ', ' + cit_copy[i].citation_id + ');">Show&nbsp;Similar&nbsp;Citations</td></tr>';
			}
		
			if ((Page.currentCollection == 'all') || (Page.currentCollection == 'unverified'))
			{
				html += '<tr><td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.deleteCitation_request('+ Page._citations[i].citation_id +');">';
				html += 'Delete</td></tr>';
			}
			else
			{
				html += '<tr><td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.deleteCitation_request('+ Page._citations[i].citation_id +');">';
				html += 'Remove&nbsp;from&nbsp;Collection</td></tr>';
			}
			
			if (Page.show_collections_flag == 1)
			{
				html += '<tr><td valign="top" ' + pointer_style + ' id="coll_td_' + i + '">All&nbsp;Collections</td></tr>';
			}
			html += '</table></td>';*/
		}
		html += '</tr>'; //Page.setCitationHighlight(' + i + ');

		html += '<tr id="rowafter_' + i + '"><td width="100%" colspan="3" background="/pubs/images/line.gif">&nbsp;</tr>';
	}
	html += '</table>';
	
	if(Page._citations.length > 20)
	{
		html += paging_html;
	}

	document.getElementById('citations').innerHTML = html;
	document.getElementById('citations').style.display = '';		//show
	document.getElementById('secondary').style.display = 'none';	//hide
	document.getElementById('right_col').style.display = '';		//show

	// Get id/anchor for to move viewing window
	var cur_id = 0;
	if(Page._current_citation.citation_id != undefined) {
		cur_id = Page._current_citation.citation_id; 
	}
	else if(Page._citations[0] != undefined) {
		cur_id = Page._citations[0].citation_id;
	}
	else {
		// just scroll to top	
	}

	Page.createToolTips();
		
	// Check if cur_id is within current page. Otherwise request the right page.
	if(anchor_is_in_current_page == true)
	{
		//alert("Page.listCitations : moveWindow : " + 'a_' + cur_id );
		moveWindow('a_'+cur_id);
	}
	else
	{
		//alert("Page.listCitations : not in the same page");
		// Scroll to top OR Page.getCitationsGivenCollectionID();?
		scrollToTop();
	}
	if (Page.show_collections_flag == 1)
	{
		if(Page.tt_col) 
		{
			Page.tt_col.destroy(); 
		}
		
		
		var tt_col = new YAHOO.widget.Tooltip("tt_col", { 
									  context:Page.tt_col_arr, 
									  showdelay:10});
		
		// Set the text for the tooltip just before we display it.
		tt_col.contextTriggerEvent.subscribe(
			function(type, args) {
				if(document.getElementById('tt_col'))
				{
					var context = args[0].id;
					//alert(context);
					var temp_row = context.split('_')[2];
					Page.getCollectionsGivenCitationID_request(Page._citations[temp_row].citation_id);
				//	this.cfg.setProperty("text", "new");
					var highest_index = getNextHighestZindex(document.body);
					document.getElementById('tt_col').style.zIndex = highest_index;
				}		
			}
		);
			
		Page.tt_col = tt_col;
	}
	
	//alert("Page.listCitations : Viewport "+ cur_id + " " + inViewPort(document.getElementById('a_'+cur_id)));
}

Page.writeListCitationsRightTable = function(verticalOrHorizontal, i, cit_copy, pointer_style)
{
	var html = '';
	
	var td_1 = '<td valign="top" ' + pointer_style + ' onMouseUp="Page.state=3; Page.current_row_num=' + i + '; Page.editOneCitation(' + i + ');">Edit</td>';
	var td_2 = '<td id="similar' + i + '" valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.showSimilarCitations(' + i + ', ' + cit_copy[i].citation_id + ');">Show&nbsp;Similar&nbsp;Citations</td>';
	var td_3 = '<td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.deleteCitation_request('+ Page._citations[i].citation_id +');" >Delete</td>';
	var td_4 = '<td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.deleteCitation_request('+ Page._citations[i].citation_id +');">Remove&nbsp;from&nbsp;Collection</td>';
	var td_5 = '<td valign="top" ' + pointer_style + ' id="coll_td_' + i + '">All&nbsp;Collections</td>';
	
	html += '<table border=0>';
	
	var tr_open = '<td>|</td>';
	var tr_close = '';
	
	if (verticalOrHorizontal == 'vertical') 
	{
		tr_open = '<tr>';
		tr_close = '</tr>';
	}
	
	// First TD only - have no tr_open for horizontal
	if (verticalOrHorizontal == 'horizontal') 
	{
		html += '<tr>';
		html += td_1;
	}
	else
	{
		html += tr_open + td_1 + tr_close;	
	}
	
	if (Page.similar_citations_array[cit_copy[i].citation_id])
	{	
		html += tr_open + td_2 + tr_close;
	}

	if ((Page.currentCollection == 'all') || (Page.currentCollection == 'unverified'))
	{
		html += tr_open + td_3 + tr_close;
	}
	else 
	{
		html += tr_open + td_4 + tr_close;
	}
	
	if (Page.show_collections_flag == 1)
	{
		html += tr_open + td_5 + tr_close;
	}
	
	if (verticalOrHorizontal == 'horizontal') 
	{
		html += '</tr>';
	}
	
	html += '</table>';
	return html;
}

Page.showSimilarCitations = function(row, citation_id)
{
	Page._current_citation = Page._citations[row];
	Page.panel2.setHeader("The citations on the left are similar to the citation you clicked on (on the right).");
	var current_citation_array = new Array(Page._current_citation);
	Page.editTwoCitations(Page.similar_citations_array[Page._current_citation.citation_id], current_citation_array);
}

Page.printPagingAndFormatTable = function()
{
	var html = '';
	html += '<table style="background-color:#f8f3d2; width:100%"><tr>';
//	if (Page.owner != "")
	if (Page.loggedIn && Page.hasProxy)
	{
		html += '<td style="text-align:left; width:15%">Select: ' + Page.printSelectCitationsOrCollectionsMenu('citations') + '</td>';
	}
	else if (Page.currentCollection != 'search')
	{
		html += '<td style="text-align:left; width:45%">';
		html += '<input type="button" value="Back" onclick="Page.get_faculty_request();" /><br>';
		html += '<b>Representative Publications of ' + Page.owner_fullname + '</b></td>';
	}
	html += '<td style="text-align:right; width:55%; vertical-align:bottom">' + Page.printPageViews() + '</td>';

//	html += '<td style="text-align:center; width:15%">Format: ' + Page.printFormatMenu() + '</td>';
//	html += '<td style="text-align:center; width:15%">' + Page.highlightMissingInfoCB() + 'Highlight Missing</td>';
	html += '</tr></table>';
	return html;
}

//Page.printPageViews = function()
//{
//	var html = '';	
//	
//		// Print total count
//	var first_citation_on_page = ((Page.current_page - 1) * Page.citations_per_page) + 1;	
//	var last_citation_on_page = first_citation_on_page + Page._citations.length - 1;
//	
//	if(Page.total_count == undefined || Page.total_count <= 0)
//	{
//		html += '&nbsp;&nbsp;&nbsp;Citations 0 of 0.';
//	}
//	else
//	{
//		html += '&nbsp;&nbsp;&nbsp;Citations ' + first_citation_on_page + ' - '+ last_citation_on_page + ' of ' + Page.total_count + '.';
//	}
//	
//	if (Page.current_viewable_pages.length == 0)
//	{	
//		for (var i=Page.current_page; i<Page.current_page + Page.max_pages_displayed; i++)
//		{
//			if (Page.total_count > i * Page.citations_per_page)
//			{
//				Page.current_viewable_pages.push(i);
//			}
//			else 
//			{
//				Page.current_viewable_pages.push(i);
//				break;
//			}
//		}	
//	}
//	
//	html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page: ';
//	
//	if (Page.current_viewable_pages[0] > 1)
//	{
//		//html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_backwards();">&lt;&lt;</span>';
//		html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_backwards();">Previous</span>';
//	}
//	if (Page.inArray(Page.current_page, Page.current_viewable_pages))
//	{
//		for (var i=0; i<Page.current_viewable_pages.length; i++)
//		{
//			if (Page.current_page == Page.current_viewable_pages[i])
//			{
//				html += '&nbsp;&nbsp;&nbsp;' + '<span class="current_page">' + Page.current_viewable_pages[i] + '</span>';
//			}
//			else 
//			{
//				html += '&nbsp;&nbsp;&nbsp;<span class="pagelink pointerhand" onclick="Page.getCitations('+Page.current_viewable_pages[i]+',Page.current_get_type);">' + Page.current_viewable_pages[i] + '</span>';
//			}
//		}
//		if (Page.total_count > Page.current_viewable_pages[Page.current_viewable_pages.length-1] * Page.citations_per_page)
//		{
//			//html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_forwards();">&gt;&gt;</span>'
//			html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_forwards();">Next</span>';
//		}
//	}
//	else
//	{
//		alert('Problem with pages: ' + Page.current_page);
//	}
//
//	return html;
//}

Page.printPageViews = function()
{
	var html = '';	
	
	// Calculate max page.
	var full_page = parseInt(Page.total_count / Page.citations_per_page);
	var extra_page = Page.total_count % Page.citations_per_page;
	var max_page = 0;
	if(extra_page == 0) max_page = full_page; else max_page = (full_page + 1);
	
	if(Page.current_page > max_page) Page.current_page = max_page;

	// Print total count
	var first_citation_on_page = ((Page.current_page - 1) * Page.citations_per_page) + 1;	
	var last_citation_on_page = first_citation_on_page + Page._citations.length - 1;

//alert("Page.current_page: "+ Page.current_page + "\nPage.total_count: " + Page.total_count + "\nPage.current_viewable_pages.length: "+ Page.current_viewable_pages.length
	 // + "\nPage.max_pages_displayed: " + Page.max_pages_displayed);
	
	if(Page.total_count == undefined || Page.total_count <= 0)
	{
		html += '&nbsp;&nbsp;&nbsp;Citations 0 of 0.';
	}
	else
	{
		html += '&nbsp;&nbsp;&nbsp;Citations ' + first_citation_on_page + ' - '+ last_citation_on_page + ' of ' + Page.total_count + '.';
	}
	
	if (Page.current_viewable_pages.length == 0)
	{	
		var tempInt = parseInt(Page.current_page) + parseInt(Page.max_pages_displayed);
		for (var i=Page.current_page; i < tempInt && i <= max_page; i++)
		{
			Page.current_viewable_pages.push(i);
		}	
		
	}
	
	html += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Page: ';
	
	if (Page.current_viewable_pages[0] > 1)
	{
		html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_backwards();">Previous</span>';
	}
	
	if (Page.inArray(Page.current_page, Page.current_viewable_pages))
	{
		for (var i=0; i<Page.current_viewable_pages.length; i++)
		{
			if (Page.current_page == Page.current_viewable_pages[i])
			{
				html += '&nbsp;&nbsp;&nbsp;' + '<span class="current_page">' + Page.current_viewable_pages[i] + '</span>';
			}
			else 
			{
				html += '&nbsp;&nbsp;&nbsp;<span class="pagelink pointerhand" onclick="Page.getCitations('+Page.current_viewable_pages[i]+',Page.current_get_type);">' + Page.current_viewable_pages[i] + '</span>';
			}
		}

		if (Page.total_count > Page.current_viewable_pages[Page.current_viewable_pages.length-1] * Page.citations_per_page)
		{
			html += '&nbsp;&nbsp;&nbsp;<span class="page_arrows pointerhand" onclick="Page.printPageViews_paging_forwards();">Next</span>';
		}
	}
	else
	{
		alert('Problem with pages: ' + Page.current_page);
	}
	
	
	//alert("Page.current_page: "+ Page.current_page + "\nPage.total_count: " + Page.total_count + "\nPage.current_viewable_pages.length: "+ Page.current_viewable_pages.length
	  //+ "\nPage.max_pages_displayed: " + Page.max_pages_displayed);

	return html;
}

Page.printPageViews_paging_forwards = function()
{
	Page.current_page=Page.current_viewable_pages[Page.current_viewable_pages.length-1]+1; 
	Page.current_viewable_pages=new Array(); 
	Page.getCitations(Page.current_page,Page.current_get_type);
}

Page.printPageViews_paging_backwards = function()
{
	Page.current_page=Page.current_viewable_pages[0]-1; 
	
	var new_first_viewable_page = Page.current_viewable_pages[0] - Page.max_pages_displayed;
	for (var i=0; i<Page.max_pages_displayed; i++)
	{
		Page.current_viewable_pages[i] = new_first_viewable_page + i;
	}
	Page.getCitations(Page.current_page,Page.current_get_type);
}

Page.inArray = function(val, arr)
{
	var valInArray = false;
	for (var i=0; i<arr.length; i++)
	{
		if (val == arr[i])
		{
			valInArray = true;
			break;
		}
	}
	return valInArray;
}

Page.getCitations = function(page, type, citation_id)
{ 
	Page.current_get_type = type;
	Page.current_page = page;
	
	if(citation_id == undefined)
	{
		citation_id = 0;	
	}
	
	if ((type == 'getCitations_byFac_all') || (type == 'getCitations_byFac_unverified'))
	{
		var jsonStr = '{"request": {"type": "' + type + '",  "page": "' + page + '", "citation_id_page": "' + citation_id + '", "citations_per_page": "' + Page.citations_per_page + '", "sort_order": "' + Page.sort_order + '", "citations": {"submitter": "' + Page.submitter + '",  "owner": "' + Page.owner + '", "entryTime": ""}}}';
		Ajax.SendJSON('services/citations.php', Page.onResponse, jsonStr);	
	}
	else if  ((type == 'journal') || (type == 'title') || (type == 'author') || (type == 'all') || (type == 'citation_id'))
	{
		var jsonStr = '{"request": {"type": "'+ type +'",  "keyword": ' + YAHOO.lang.JSON.stringify(Page.keywords) + ', "sort_order": "' + Page.sort_order + '", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "page": "' + page + '", "citations_per_page": "' + Page.citations_per_page + '"}}';
		//alert("Page.getCitations: " + jsonStr);
		Ajax.SendJSON('services/search.php', Page.searchCitations_response, jsonStr);	
	}	
	else if  (type == 'getCitations_byTimestamp_all')
	{ 
		var jsonStr = '{"request": {"type": "'+ type +'",  "page": "' + page + '", "citations_per_page": "' + Page.citations_per_page + '",  "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "entryTime": "' + Page.parsed_timestamp + '"}}}';
		
		Ajax.SendJSON('services/citations.php', Page.pageThroughCitations_response, jsonStr);	
	}
	else if (type == 'getCollection')
	{
		Page.getCitationsGivenCollectionID();  // Already have loading panel show
	}
}

Page.searchCitations_response = function()
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		Page.rewritePage(responseObj);
	}
}


Page.listCollections = function()
{	
	var html = '';
	Page.selectAllOrNone = "select"; // Might be irrelevant now.
	html = '<p>' + Page.printBackToCitationsButton() + '</p>';
	html += '<table style="border: 1px solid #7D110C; margin-bottom:5px; width:60%;"><tr><td>';
	html += 'Select: ' + Page.printSelectCitationsOrCollectionsMenu('collections') + '&nbsp;&nbsp;&nbsp;&nbsp;';
	html += '<input type="button" name="merge" value="Merge Selected Collections" onclick="Page.mergeSelectedCollections();">&nbsp;&nbsp;&nbsp;&nbsp;';
//	html += '<input type="button" name="collection_delete" value="Delete Selected Collections" onclick="Page.deleteSelectedCollections();">';
	html += '</td></tr></table>';
	
	//var highlight = 'onMouseOver="this.bgColor = \'#C0C0C0\'" onMouseOut ="this.bgColor = \'#FFFFFF\'" bgcolor="#FFFFFF"';
	//var pointer_style = 'onmouseover="this.style.cursor=\'pointer\';" onmouseout="this.style.cursor=\'default\'"';
	var highlight = 'class="grey_highlight"';
	var pointer_style = 'class="pointerhand"';

	html += '<table style="border: 4px solid #7D110C; width:60%;">';
	
	for (var i=0; i < Page.collections.length; i++) 
	{
		//	html += '<tr id="row_' + i + '" align=\"left\" ' + highlight +'>';
			html += '<tr id="row_' + i + '" align=\"left\">';
					
			// check box	
			html += '<td valign="top"><input type="checkbox" name="collection_checkboxes' + Page.collections[i].collection_id + '" id="collection_cb_' + Page.collections[i].collection_id + '" value="' + Page.collections[i].collection_id + '"></td>';
			
			html += '<td valign="top" width="90%" id="collection_cell_' + i + '" ' + pointer_style + ' onMouseUp="Page.current_page=1;Page.current_viewable_pages=new Array();Page.currentCollection=' + Page.collections[i].collection_id + ';  Page.getCitationsGivenCollectionID();">';
			
			html += Page.collections[i].collection_name + ' &nbsp; <b>(' + Page.collections[i].count + ' citations)</b></td>';
			
			//html += '<td valign="top">' + Page.collections[i].count + '</td>';
			html += '<td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.renameCollection_request(' + i + ');">[Rename]</td>';
			html += '<td valign="top" ' + pointer_style + ' onMouseUp="Page.current_row_num=' + i + '; Page.deleteCollection_request(' + i + ');">[Delete]</td></tr>'; 
			html += '<tr><td></td><td></td><td></td><td></td></tr>';
			//html += '<tr><td></td><td></td><td></td><td></td><td></td></tr>';
	}
	
	html += '</table>';
	
	Page.updateCollectionFromList();  // Update all collections dropdown menu from Page.collections.
	
	document.getElementById('options').style.display = '';		//show
	Page.right_column_display('none');
	//document.getElementById('right_col').style.display = '';	//show
	//document.getElementById('right_col2').style.display = '';	//show
	document.getElementById('citations').style.display = 'none';//hide
	document.getElementById('secondary').innerHTML = html;		//set
	document.getElementById('secondary').style.display = '';	//show
}

Page.printBackToCitationsButton = function()
{
	var html = '';
	html += '<input type="button" value="Back" onclick="Page.backToCitations();" />';
	return html;
}

Page.backToCitations = function()
{
	document.getElementById('citations').style.display = '';		//show
	document.getElementById('options').style.display = '';			//show
	document.getElementById('secondary').style.display = 'none';	//hide
	Page.right_column_display('all');
	//document.getElementById('right_col').style.display = '';		//show
	//document.getElementById('right_col2').style.display = '';		//show
	
	document.getElementById('exportformattype').selectedIndex = "";
}



Page.printFormatMenu = function() 
{
	var html = '';
	html += '<select name="formattype" id="formattype" onchange="Page.currentFormat=this.options[this.selectedIndex].value; Page.listCitations()";>';
	html += '<option value="apa"';
	if (Page.currentFormat == "apa")
	{
		html += ' selected';	
	}
	
	html += '>APA</option>';
	
	html += '<option value="mla"';
	if (Page.currentFormat == "mla")
	{
		html += ' selected';	
	}
	html += '>MLA</option>';
	html += '<option value="bibtex"';
	if (Page.currentFormat == "bibtex")
	{
		html += ' selected';	
	}
	html += '>BibTeX</option>';
	
	html += '</select>';
	return html;
}

Page.printSortOrderMenu = function() 
{
	var html = '';
	html += '<select name="sort_order_menu" id="sort_order_menu" onchange="Page.sort_order=this.options[this.selectedIndex].value; Page.sortCitations_request()";>';
	
	html += '<option value="author0ln"';
	if (Page.sort_order == "author0ln")
	{
		html += ' selected';	
	}
	html += '>First author</option>';
	
	html += '<option value="year_desc"';
	if (Page.sort_order == "year_desc")
	{
		html += ' selected';	
	}
	html += '>Latest Year</option>';
	
	html += '<option value="year_asc"';
	if (Page.sort_order == "year_asc")
	{
		html += ' selected';	
	}
	html += '>Oldest Year</option>';

	html += '</select>';
	return html;
}

Page.sortCitations_request = function()
{
	Page.current_page = 1;
	Page.getCitations(Page.current_page, Page.current_get_type);
}

Page.highlightMissingInfoCB = function() 
{
	var html = '';
	html += '<input type="checkbox" name="highlight_missing_info_cb" id="highlight_missing_info_cb"';
	if (Page.highlight_citations_with_missing_info_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setMissingInfoCB();"';
	html += '>';
	
	return html;
}

Page.showCitationID = function() 
{
	var html = '';
	html += '<input type="checkbox" name="show_citation_id_cb" id="show_citation_id_cb"';
	if (Page.show_citation_id_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setShowCitationIdCB()";'
	html += '>';
	return html;
}

Page.setCompactView = function() 
{
	var html = '';
	html += '<input type="checkbox" name="set_compact_view_cb" id="set_compact_view_cb"';
	if (Page.set_compact_view_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setCompactViewCB()";'
	html += '>';
	return html;
}


Page.showCollections = function() 
{
	var html = '';
	html += '<input type="checkbox" name="show_collections_cb" id="show_collections_cb"';
	if (Page.show_collections_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setShowCollectionsCB()";'
	html += '>';
	return html;
}

Page.showNotes = function() 
{
	var html = '';
	html += '<input type="checkbox" name="show_notes_cb" id="show_notes_cb"';
	if (Page.show_notes_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setShowNotesCB()";'
	html += '>';
	return html;
}

Page.showAbstracts = function() 
{
	var html = '';
	html += '<input type="checkbox" name="show_abstracts_cb" id="show_abstracts_cb"';
	if (Page.show_abstracts_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setShowAbstractsCB()";'
	html += '>';
	return html;
}

Page.showURLs = function() 
{
	var html = '';
	html += '<input type="checkbox" name="show_URLs_cb" id="show_URLs_cb"';
	if (Page.show_URLs_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setShowURLsCB()";'
	html += '>';
	return html;
}

Page.setMissingInfoCB = function()
{
	if (document.getElementById("highlight_missing_info_cb").checked == true)
	{
		Page.highlight_citations_with_missing_info_flag = 1;
	}
	else
	{
		Page.highlight_citations_with_missing_info_flag = 0;
	}
	Page.listCitations();
}

Page.setShowCitationIdCB = function()
{
	if (document.getElementById("show_citation_id_cb").checked == true)
	{
		Page.show_citation_id_flag = 1;
	}
	else
	{
		Page.show_citation_id_flag = 0;
	}
	Page.listCitations();
}

Page.setCompactViewCB = function()
{
	if (document.getElementById("set_compact_view_cb").checked == true)
	{
		Page.set_compact_view_flag = 1;
	}
	else
	{
		Page.set_compact_view_flag = 0;
	}
	Page.listCitations();
}

Page.setShowCollectionsCB = function()
{
	if (document.getElementById("show_collections_cb").checked == true)
	{
		Page.show_collections_flag = 1;
	}
	else
	{
		Page.show_collections_flag = 0;
	}
	Page.listCitations();
}

Page.setShowNotesCB = function()
{
	if (document.getElementById("show_notes_cb").checked == true)
	{
		Page.show_notes_flag = 1;
	}
	else
	{
		Page.show_notes_flag = 0;
	}
	Page.listCitations();
}

Page.setShowAbstractsCB = function()
{
	if (document.getElementById("show_abstracts_cb").checked == true)
	{
		Page.show_abstracts_flag = 1;
	}
	else
	{
		Page.show_abstracts_flag = 0;
	}
	Page.listCitations();
}

//abhinav

Page.highlightMissingInfoCB = function() 
{
	var html = '';
	html += '<input type="checkbox" name="highlight_missing_info_cb" id="highlight_missing_info_cb"';
	if (Page.highlight_citations_with_missing_info_flag == 1)
	{
		html += ' checked';
	}
	html += ' onclick="Page.setMissingInfoCB();"';
	html += '>';
	
	return html;
}





Page.setShowURLsCB = function()
{
	if (document.getElementById("show_URLs_cb").checked == true)
	{
		Page.show_URLs_flag = 1;
	}
	else
	{
		Page.show_URLs_flag = 0;
	}
	Page.listCitations();
}

Page.printExportMenu = function() 
{
	var html = '';
	html += '<select name="exportformattype" id="exportformattype" onchange="Page.currentExportFormat=this.options[this.selectedIndex].value;">';

	html += '<option value="">Select an export format:</option>';
	html += '<option value="apaformattedtext"';
	if (Page.currentExportFormat == "apaformattedtext")
	{
		html += ' selected';	
	}
	html += '>APA</option>';
	html += '<option value="mlaformattedtext"';
	if (Page.currentExportFormat == "mlaformattedtext")
	{
		html += ' selected';	
	}
	html += '>MLA</option>';
	html += '<option value="bibtexformattedtext"';
	if (Page.currentExportFormat == "bibtexformattedtext")
	{
		html += ' selected';	
	}
	html += '>BibTeX</option>';
	html += '<option value="endnote"';
	if (Page.currentExportFormat == "endnote")
	{
		html += ' selected';	
	}
	html += '>EndNote</option>';
	html += '<option value="htmllistprint"';
	if (Page.currentExportFormat == "htmllistprint")
	{
		html += ' selected';	
	}
	html += '>APA - HTML bulleted list</option>';
	html += '</select>';
	
	var onchange_html = 'Page.exportCitations();';
	html += '<br><br><center><input type="button" value="Export" onclick="' + onchange_html + '"/></center>';
	return html;
}

Page.printSelectCitationsOrCollectionsMenu = function(citationsOrCollections) 
{
	var html = '';
	html += '<select name="selectCitations" id="selectCitations" onchange="Page.selectAllOrNone=this.options[this.selectedIndex].value; Page.updateCheckboxes(\'' + citationsOrCollections+ '\');">';
	
	Page.selectAllOrNone = "none"; // Default to none.
	//html += '<option value="select">Select:</option>';
	
	html += '<option value="all"';
	if (Page.selectAllOrNone == "all")
	{
		html += ' selected';	
	}
	
	html += '>all</option>';
	
	html += '<option value="none"';
	if (Page.selectAllOrNone == "none")
	{
		html += ' selected';	
	}
	html += '>none</option>';
	html += '</select>';
	return html;
}

Page.exportCitations = function()
{
	var html = '<p>' + Page.printBackToCitationsButton() + '</p>';
	html += '<div style="text-align:left">';
//	html += '<html><head></head><body>';
	for (var i=0; i < Page._citations.length; i++) 
	{
		if (Page.currentExportFormat == 'endnote')
		{
			html += Page.printEndnoteStyleCitation(Page._citations[i]) + '<br><br>';
		}
		else if (Page.currentExportFormat == 'bibtexformattedtext')
		{
			html += Page.printBibtexStyleCitation(Page._citations[i]) + '<br><br>';
		}
		else if (Page.currentExportFormat == 'mlaformattedtext')
		{
			html += Page.printMLAStyleCitation(Page._citations[i]) + '<br><br>';
		}
		else if (Page.currentExportFormat == 'apaformattedtext')
		{
			html += Page.printAPAStyleCitation(Page._citations[i]) + '<br><br>';
		}
		else if (Page.currentExportFormat == 'htmllistprint')
		{
			html += Page.printHTMLListStyleCitation(Page._citations[i]) + '<br><br>';
		}
		else
		{
			Page.panel1.setBody('Please select an export format.');
			Page.panel1.show();
			return false;	
		}
	}
	html += '</div>';
//	html += '</body></html>';
	document.getElementById('secondary').innerHTML = html;
	document.getElementById('citations').style.display = 'none';	// Hide
	document.getElementById('secondary').style.display = '';	// Show
	Page.right_column_display('export_citations');
}
Page.updateCheckboxes = function(citationsOrCollections)
{
	var checkboxPrefix;
	var element;
	
	if (citationsOrCollections == 'citations')
	{
		checkboxPrefix = 'cb_';
		element = Page._citations;
	}
	else 
	{
		checkboxPrefix = 'collection_cb_';
		element = Page.collections;
	}
	for (var i=0; i < element.length; i++) 
	{	
		var element_id = (citationsOrCollections == 'citations') ? element[i].citation_id : element[i].collection_id;
		if (document.getElementById(checkboxPrefix + element_id))
		{
			if (Page.selectAllOrNone == "all")
			{
				document.getElementById(checkboxPrefix + element_id).checked = "checked";
			}
			else
			{
				document.getElementById(checkboxPrefix + element_id).checked = "";
			}
		}
	}
}


Page.deleteCitation_request = function(citation_id)  
{	
	var style = 'style="width: 100px"';
	var html = '';
	html += '<div style="vertical-align:middle;position:relative;height:230px;text-align:center">';
	
	if ((Page.currentCollection == "all") || (Page.currentCollection == "unverified") || (Page.panel_open == 1))	// Delete permanently
	{
		html += '<center><p>Delete citation ' + citation_id + ' permanently?</p></center>';
	}
	else																			// Delete from collection only
	{
		html += '<center><p>Remove citation  ' + citation_id + ' from collection "' + Page.currentCollection_name + '"?</p></center>';
	}
	//html += '<input type="button" '+style+' value="OK" onclick="Page.deleteCitationHelper_request('+i+',Page._citations[' + i + ']);"/>';
	html += '<input type="button" '+style+' value="OK" onclick="Page.deleteCitationHelper_request(\'' + citation_id + '\');Page.getCitationsGivenCollectionID();"/>';
	html += '&nbsp;&nbsp;&nbsp;';
	html += '<input type="button" '+style+' value="Cancel" onclick="Page.panel1.hide();"/></div>';
	
	Page.panel1.setBody(html);
	Page.panel1.show();
}

Page.deleteCitationHelper_request = function(citation_id)
{
	Page.panel1.setBody('');  // Clear panel 1 since input button could still accept input.
	Page.panel1.hide();

	var callback_function = 'Page.deleteCitation_response';

	if ((Page.current_get_type == 'getCitations_byFac_all') || (Page.current_get_type == 'getCitations_byFac_unverified') )
	{
		var jsonStr = '{"request": {"type": "delete",  "current_get_type": "' + Page.current_get_type + '", "sort_order": "' + Page.sort_order + '", "citations_per_page": "' + Page.citations_per_page + '", "page": "' + Page.current_page + '", "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "citation_id": "' + citation_id + '"}}}';
		Ajax.SendJSON('services/citations.php', Page.deleteCitation_response, jsonStr);	
	}
	else if (Page.current_get_type == 'getCollection')
	{
		var jsonStr = '{"request": {"type": "delete",  "current_get_type": "' + Page.current_get_type + '", "sort_order": "' + Page.sort_order + '",  "collection_id": "' + Page.currentCollection + '", "page": "' + Page.current_page + '", "citations_per_page": "' + Page.citations_per_page + '", "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "citation_id": "' + citation_id + '"}}}';
	//	callback_function = 'Page.onResponseGetCitationsGivenCollectionID';
		Ajax.SendJSON('services/citations.php', Page.deleteCitation_response, jsonStr);	
	}
	else if ((Page.current_get_type == 'journal') || (Page.current_get_type == 'title'))
	{
		var jsonStr = '{"request": {"type": "delete",  "current_get_type": "' + Page.current_get_type + '", "sort_order": "' + Page.sort_order + '", "citations_per_page": "' + Page.citations_per_page + '", "page": "' + Page.current_page + '", "keyword": "' + Page.keywords + '", "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "citation_id": "' + citation_id + '"}}}';
 
		Ajax.SendJSON('services/citations.php', Page.deleteCitation_response, jsonStr);	
	}	
	else if (Page.panel_open == 1)
	{
		var jsonStr = '{"request": {"type": "delete",  "current_get_type": "' + Page.current_get_type + '", "sort_order": "' + Page.sort_order + '", "citations_per_page": "' + Page.citations_per_page + '", "page": "' + Page.current_page + '", "keyword": "' + Page.keywords + '", "citations": {"submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "entryTime": "' + Page.parsed_timestamp + '", "citation_id": "' + citation_id + '"}}}';
		Ajax.SendJSON('services/citations.php', Page.deleteCitation_response, jsonStr);	
	}	
//	Ajax.SendJSON('services/citations.php', callback_function, jsonStr);
}

Page.deleteCitation_response = function() 
{
	if (Ajax.CheckReadyState(Ajax.request)) 
	{	
	//	alert("Page.deleteCitation_response : Ajax.request.responseText : " + Ajax.request.responseText);
		var responseObj = eval("(" + Ajax.request.responseText + ")");
						
		if(responseObj.error == 0)
		{
			if (Page.panel_open == 1)
			{
				Page.updateCitationsArraysAndTabs_after_delete();
				Page.panel1_alert_message('Citation was deleted successfully.', '');

			}
			else
			{
				Page.panel1_alert_message('Citation was deleted successfully.', '');
				Page.rewritePage(responseObj);
			}
		}
		else
		{
			Page.panel1_alert_message('Error deleting citation!', '');	
		}
	}
}

// Maybe no longer used.
Page.setCitationHighlight = function(row)
{
	document.getElementById("row_" + row).style.backgroundColor = '#C0C0C0';
}

Page.updateCitationInList = function(_citation)
{
	if(_citation.verified == 1)
	{
		document.getElementById("verified_" + Page.current_citation_num).innerHTML = '&nbsp;';
	}
	else
	{
		document.getElementById("verified_" + Page.current_citation_num).innerHTML = '<font color="red"><b>U</b></font>';
	}
	
	// Make a copy of citation
	var cit_copy = Page.copyCitationsForFormat(_citation)

	document.getElementById("cell_" + Page.current_citation_num).innerHTML = Page.printAPAStyleCitation(cit_copy);
	Page._citations[Page.current_row_num] = _citation;
}

// alertInputEntry		- Alert form error
Page.alertInputEntry = function(err_elements, element_array, data_key, data_value)
{
	var msg = "Following field(s) have errors:\n";
	for(var i = 0; i < err_elements.length; i++)
	{
		msg += " - " + err_elements[i] + "\n";	
	}
	alert(msg);
}

// validateInputEntry	- Validate form
Page.validateInputEntry = function(element_array, data_key, data_value)
{
	var error = true;				// There's no error by default
	var err_array = new Array(); 	// Contain elements that have the error
	//var tmp = "";
	
	for(var i = 0; i < data_value.length; i++)
	{
		//tmp += i + ": Err: " + element_array[data_key[i]] + "\tData: " + data_value[i] + "\tKey: " + data_key[i] + "\n\n";
		var str = data_value[i];				// Make data_value[i] into string
		if(element_array[data_key[i]] == "year")
		{	
			if(!isEmpty(str)) {
				var result = checkYear(str);
				if(!result){
					error = false;
					err_array.push(element_array[data_key[i]]); // Year
				}
			}
		}
		else if(element_array[data_key[i]] == "title")
		{	
			if(isEmpty(str)){
				error = false;
				err_array.push(element_array[data_key[i]]); // Title
			}
		}
		else if(element_array[data_key[i]] == "author0ln")
		{		
			if(isEmpty(str)){
				error = false;
				err_array.push(element_array[data_key[i]]); // First Author Lastname
			}
		}
		else if(element_array[data_key[i]] == "author0fn")
		{		
			if(isEmpty(str)){
				error = false;
				err_array.push(element_array[data_key[i]]); // First Author Firstname
			}
		}
		else{}
	}
	
	// Return true if there's no error OR err_array if there's an error
	if(error) {
		return error;
	}
	else {
		return err_array;
	}
}

// clearInputEntry	- Clear entry form
Page.clearInputEntry = function()
{
	var theForm = document.forms[0];
	for(i=0; i<theForm.elements.length; i++) {
		if(theForm.elements[i].type == "text" || theForm.elements[i].type == "textarea"){
			theForm.elements[i].value = "";
		}
	}
}

// Writing the pubtype menu
Page.pubtypeMenu = function(pubtype, fieldFlag, citation_suffix) {
	citation_suffix = (citation_suffix == undefined) ? "" : citation_suffix;
	var html = "";
	var pubtypes = new Array();
	var pubtypes_value = new Array();
	var count = 0;
	for(var i in Page.pubtypes_json) {
		pubtypes[count] = Page.pubtypes_json[i].dropdown_name; 
		pubtypes_value[count] = i;
		count++;
	}
	
	// Check for default pubtype
	if (pubtype == "") { pubtype = "misc"; }
	else if(pubtype == "unknown") {	pubtype = "misc"; }	
	
	html += Page.printTableTop('pubtype', citation_suffix);
	html += '<tr><td>';
		
	if(fieldFlag == "text")
	{
		/*for(var i = 0; i < pubtypes_value.length; i++){
			if(pubtype == pubtypes_value[i]){
				html += '<b>Publication Type: </b>' + pubtypes[i] + '<br /><br />';
			}
		}*/	
		html += '<b>Publication Type: </b>' + Page.pubtypes_json[pubtype].dropdown_name; 
		//html += '<br /><br />';
		
		if (citation_suffix.indexOf("_a") >= 0)
		{
			var name = 'pubtype' + citation_suffix;
			html += '<input type="hidden" id="' + name +'" name="' + name +'" value="' + pubtype + '">';
		}
	}
	else
	{
		var onchange_html = '';
		if (citation_suffix != "" && citation_suffix != undefined)
		{
			if (citation_suffix.indexOf("_b") >= 0)
			{
				onchange_html += 'onchange="document.getElementById(\'changingByPubtype_fields' + citation_suffix + '\').innerHTML = Page.changePubtypeInfoInTab(this.value,\'' + citation_suffix + '\'); Page.alignActiveTabs(); initializeAutocompleteFields(\''+citation_suffix+'\');"'; 
			}
			else
			{
				onchange_html += 'onchange="document.getElementById(\'changingByPubtype_fields' + citation_suffix + '\').innerHTML = Page.enterChangingByPubtypeInfo(Page._current_citation,this.value,\'\',\'' + citation_suffix + '\'); Page.alignActiveTabs(); initializeAutocompleteFields(\''+citation_suffix+'\');"';
			}
	//		onchange_html += 'Page.alignActiveTabs();';
		}
		else
		{
			onchange_html += 'onchange="Page.changePubtypeOneCitation(\'' + citation_suffix + '\',this.value)"';
		}
		
		html += '<b>Publication Type: ';
		
			//	html += '<select name="pubtype" id="pubtype" onchange="Page.inputFields(this.options[this.selectedIndex].value)">';
		html += '<select name="pubtype'+citation_suffix+'" id="pubtype'+citation_suffix+'" ';
		html += onchange_html;
		html += '>';
		
		for(var i = 0; i < pubtypes_value.length; i++){
			if(pubtype == pubtypes_value[i]){
				html += '<option selected value="'+pubtypes_value[i]+'">'+pubtypes[i]+'</option>';
			}
			else{
				html += '<option value="'+pubtypes_value[i]+'">'+pubtypes[i]+'</option>';
			}		
		}
		html += '</select></b>';
	}
	
	html += '</td>';
	
	html += '<td><div id="inbook_search_button_div">';
	if (pubtype == 'inbook')
	{
		html += 'Find book';
	}
	html += '</div></td>';
	html += '</tr>';
	html += '</table>';
	return html;
}

Page.changePubtypeOneCitation = function(citation_suffix, pubtype)
{
	document.getElementById('changingByPubtype_fields').innerHTML = Page.enterChangingByPubtypeInfo(Page._current_citation,pubtype,'','');
	initializeAutocompleteFields(citation_suffix);
	if (pubtype == 'inbook')
	{
		Page.getInbookRelatedBooks_request();
	}
	else
	{
		document.getElementById('inbook_search_button_div').innerHTML = '';
	}
	
	// Dynamically highlight missing info
	Page.highlightRequiredInputFields(pubtype); 
}

Page.getInbookRelatedBooks_request = function()
{
	var jsonStr = '{"request": {"type": "title",  "keyword": "' + document.getElementById('booktitle').value + '", "sort_order": "' + Page.sort_order + '", "submitter": "' + Page.submitter + '", "owner": "' + Page.owner + '", "page": "1", "citations_per_page": "' + Page.citations_per_page + '"}}';
	Ajax.SendJSON('services/search.php', Page.getInbookRelatedBooks_response, jsonStr);
}

Page.getInbookRelatedBooks_response = function() 
{
	if (Ajax.CheckReadyState(Ajax.request))  
	{	
		var responseObj = eval("(" + Ajax.request.responseText + ")");
		var inbook_citations = responseObj.citations;
		var html = '';
		html += '<select>';
		for (var i=0; i<inbook_citations.length; i++)
		{
			html += '<option width="200px">' + inbook_citations[i].title.substring(0,60) + '</option>';
			if (i > 10) {break;}
		}
		html += '</select>';
		document.getElementById('inbook_search_button_div').innerHTML = html;
		//document.getElementById('inbook_search_button_div').innerHTML ='Find book';
	}
}

Page.changePubtypeTabs = function()
{
}


Page.printCheckBox = function(group, value, checked, disabled)
{
	var html = '';
	html = '<input type="checkbox" id="' + group +'" name="' + group +'" value="' + value + '" ' + checked + ' ' + disabled + ' />'; 
	return html;
}

// getCheckedValue	- Used by saveEntry(), nextInputEntry()
//					- Find the radiobutton and check the value
Page.getCheckedValue = function(radioObj) 
{
	if(!radioObj)
	{
		return "";
	}
	else if(radioObj.type == "checkbox")  // to check for verified value
	{
		var radioLength = radioObj.length;
		if(radioLength == undefined)
			if(radioObj.checked)
				return "1";
			else
				return "0";
		for(var i = 0; i < radioLength; i++) {
			if(radioObj[i].checked) {
				return "1";
			}
		}
		return "0";	
	}
	else {
		var radioLength = radioObj.length;
		if(radioLength == undefined)
			if(radioObj.checked)
				return radioObj.value;
			else
				return "";
		for(var i = 0; i < radioLength; i++) {
			if(radioObj[i].checked) {
				return radioObj[i].value;
			}
		}
		return "";
	}
}

// setCheckedValue	- Currently not in used
//					- Useful to set radio button's value
Page.setCheckedValue = function(radioObj, newValue) {
	if(!radioObj)
		return;
	var radioLength = radioObj.length;
	if(radioLength == undefined) {
		radioObj.checked = (radioObj.value == newValue.toString());
		return;
	}
	for(var i = 0; i < radioLength; i++) {
		radioObj[i].checked = false;
		if(radioObj[i].value == newValue.toString()) {
			radioObj[i].checked = true;
		}
	}
}


Page.checkAuthorFirstnameForInitials = function()
{
	var result_arr = new Array();
	
	for(var i = 0; i < 6; i++)
	{
		var str = '' + document.forms['cForm'].elements['author'+i+'fn'].value;
		if(checkFirstnameFormat(str))
		{
			result_arr[i] = 'author'+i+'fn';
		}
	}
	
	return result_arr;
}

Page.alertOnSavingFirstnameAsInitials = function()
{

        var result = Page.checkAuthorFirstnameForInitials();
    if(result.length > 0)
        {
                var info = '';
                for(var i in result) {
            if (document.getElementById('author'+i+'ln_checkbox') && (document.getElementById('author'+i+'ln_checkbox').checked == true))
                        {
                                info += 'Author ' + (parseInt(i)+1) + ' firstname contains initials. \n';
                        }
                }
                if (info != '')
        {
                        info += '\nSave anyways? \n';
                }
            return info;
        }
        else return false;
}

Page.checkInputAndSave = function(type, merge, timestamp)
{	
	var response;
	if (type == "check_authors")
	{	
		// Continue 
		Page.create_page_dot_sentData(timestamp);
	}
	else if (type == "create_authors")
	{		
	
		if (Page.getCheckedValue(document.forms['cForm'].elements['verified']) == 1) 
		{
			if(Page.validate_verified_entry() == 0)
			{
				if(confirm("Entry will remain unverified because not all the authors are verified."))
				{
					if (Page.update_page_dot_sentData(timestamp) == false)
					{
						return false;
					}
					Page.sentData['verified'] = 0;
			//		Page.panel1.hide();
				}
				else
				{
					return;
				}	
			}
			else
			{
				
				if (Page.update_page_dot_sentData(timestamp) == false)
				{
					return false;
				}
			//	Page.panel1.hide();
			}
		}
		else
		{
			
			if (Page.update_page_dot_sentData(timestamp) == false)
			{
				//return false;
			}
		//	Page.panel1.hide();	
		}
	}
	var pre_merge_ids = "";
	if ((merge == "merge") && (Page.pre_merge_id1 != undefined) && (Page.pre_merge_id2 != undefined))
	{
		pre_merge_ids = '"pre_merge_id1": "' + Page.pre_merge_id1 + '", "pre_merge_id2": "' + Page.pre_merge_id2 + '", ';
	}
	Page.panel1.hide();
	//var jsonStr = '{"request": {"type": "' + type + '",  ' + pre_merge_ids + '"citations": ' + YAHOO.lang.JSON.stringify(Page.sentData) + '}}';
	
	var jsonStr = '{"request": {"type": "' + type + '",  "coll_id":"' + Page.currentCollection + '", ' + pre_merge_ids + '"citations": ' + YAHOO.lang.JSON.stringify(Page.sentData) + '}}';
	
	Ajax.SendJSON('services/citations.php', Page.onResponseCheckAuthors, jsonStr);		
}

Page.create_page_dot_sentData = function(timestamp)
{	
	var element_array = new Array("citation_id","user_id","pubtype","cit_key","abstract","keywords","doi","url","address","annote","author","booktitle","chapter","crossref","edition","editor","translator","howpublished","institution","journal","bibtex_key","month","note","number","organization","pages","publisher","location","school","series","title","type","volume","year", "format", "filename", "date_retrieved", "author0id","author0ln","author0fn","author1id","author1ln","author1fn","author2id","author2ln","author2fn","author3id","author3ln","author3fn","author4id","author4ln","author4fn","author5id","author5ln","author5fn");  //,"raw"
	
	Page.data_keys = new Array();			// Map data_value index to element_array key.
	var data_key = new Array();
	var data_value = new Array();		// Contains the data mapped by element_array
	var err_elements = new Array();
	
	Page.sentData = {};
	
	for( var count = 0; count < element_array.length; count++)
	{
		// Check for empty element and skip
		if(document.getElementById(element_array[count])){
			var value = document.getElementById(element_array[count]).value;
			Page.sentData[element_array[count]] = value;
			Page.data_keys.push(element_array[count]);
		}	
	}
	
	Page.sentData["verified"] = Page.getCheckedValue(document.forms['cForm'].elements['verified']);
	Page.sentData["representative"] = Page.getCheckedValue(document.forms['cForm'].elements['representative']);
	Page.sentData["submitter"] = Page.submitter;
	Page.sentData["owner"] = Page.owner;
	Page.sentData["entryTime"] = timestamp;
	
	// Check for attached_filename.
	if(document.getElementById('attached_filename').value != "") 
	{
		Page.sentData["filename"] = document.getElementById('attached_filename').value;
	}
	else	// Send empty filename to indicate no change in filename
	{
		Page.sentData["filename"] = "";	
	}
	
	if (Page.savingCitationEnteredByHandIntoCollection == true)
	{
		Page.sentData["collection_id"] = Page.currentCollection;
	}
	else
	{
		Page.sentData["collection_id"] = "";
	}
	
	tempArray = new Array("verified","representative", "submitter", "owner", "entryTime", "filename", "collection_id");
	Page.data_keys = Page.data_keys.concat(tempArray);
}

Page.validate_verified_entry = function()
{
	var valid = 1;
	var empty_checkbox_count = 0;
	for(var i=0; i<6; i++) {
		// if the checkbox exists 
		if (document.getElementById('author'+i+'ln_checkbox'))
		{
			if(!document.getElementById('author'+i+'ln_checkbox').checked)
			{
				valid = 0;
			}
		}
		/*else
		{
			empty_checkbox_count++;
			valid = 0;
		}*/
	}
	return valid;
}

Page.update_page_dot_sentData = function(timestamp)
{
	var info = Page.alertOnSavingFirstnameAsInitials();
	if(info != false) {
		if(!confirm(info)) return false;	// Quit function	
	}
		
	for(var i=0; i<6; i++) {
		// if the checkbox exists 
		if (document.getElementById('author'+i+'ln_checkbox'))
		{
			if (document.getElementById('author'+i+'ln_checkbox').checked)
			{
				Page.sentData['author'+i+'id'] = -1; // php reads -1 as verify
			}
			else
			{
				Page.sentData['author'+i+'id'] = -2; // php reads leave unverified
			}
			Page.sentData['author'+i+'ln'] = document.getElementById('author'+i+'ln_check').value;
			Page.sentData['author'+i+'fn'] = document.getElementById('author'+i+'fn_check').value;
		}
	}
	return true;
}



// showHome	- Used by homePage()
Page.showHome = function() {
	clearAllRadios("input_method");
	document.getElementById('welcome').style.display = '';				//show
	document.getElementById('top').style.display = 'none';				//hide
	document.getElementById('home').style.display = '';					//show
	document.getElementById('options').style.display = 'none';			//hide
	document.getElementById('insert').style.display = 'none';			//hide
	Page.right_column_display('none');
	//document.getElementById('right_col').style.display = 'none';		//hide
	//document.getElementById('right_col2').style.display = 'none';		//hide
	document.getElementById('citations').style.display = 'none';		//hide
	document.getElementById('secondary').style.display = 'none';		//hide
}

Page.buildXMLText = function(data_key, data_value) {
			
			var xml_text = "<citation>";
			for(var i = 0; i < data_key.length; i++)
			{
				xml_text += "<"+data_key[i]+"><![CDATA["+data_value[i]+"]]></"+data_key[i]+">";
			}
			xml_text += "</citation>";
			return xml_text;
}

Page.CompareSentDataWithReceivedData = function() {
	
	var cit1 = Page._current_citation;
	var cit2 = Page.sentData;
	
	return Page.compareTwoCitations(cit1, cit2);
}

Page.compareTwoCitations = function(cit1, cit2)
{
	var differences_found = 0;
	var differenceString = "<table border='1'><tr><th>Field</th><th>Sent</th><th>Received</th></tr>";
	for (var i = 0; i < Page.data_keys.length; i++)
	{		
		// Convert to strings before doing comparisons
		var cit1_untrimmed = '' + cit1[Page.data_keys[i]]; 
		var cit2_untrimmed = '' + cit2[Page.data_keys[i]];
		
		// Trim empty spaces at the start and end
		var cit1_value = trim(cit1_untrimmed);
		var cit2_value = trim(cit2_untrimmed);
		
		if (Page.data_keys[i] == undefined) // Field name
		{
			// Skip
		}
		else if((cit1_value == undefined) || (cit2_value == undefined))
		{
			// Skip or Alert(?)
		}
		else if (cit1_value != cit2_value)
		{
			if (Page.data_keys[i] == "citation_id" && cit2[Page.data_keys[i]] == "-1") // Check for -1 citation_id => new citation
			{
				// Skip
			}
			else if (Page.data_keys[i] == "entryTime") // Code later
			{
				
			}
			else if ((Page.data_keys[i] != "representative") && (Page.data_keys[i] != "filename") && (Page.data_keys[i] != "collection_id") && (Page.data_keys[i] != "author0id") && (Page.data_keys[i] != "author1id") && (Page.data_keys[i] != "author2id") && (Page.data_keys[i] != "author3id") && (Page.data_keys[i] != "author4id") && (Page.data_keys[i] != "author5id")) {  //Deal with representative later; filename sent and filename received are different b/c filename is being changed.
				differences_found = 1;
				differenceString += "<tr>";
				differenceString += "<td>" + Page.data_keys[i] + ":  </td><td>" + cit2_value + "</td><td>" + cit1_value + "</td>";
				differenceString += "</tr>";
			}
		}
	}
	differenceString += "</table>";
	if (differences_found == 0)
	{
		differenceString = "";
	}
	return differenceString;	
}

// Hide or show right column options based on current_view
Page.right_column_display = function(current_view)
{
	// current_view: all, none, export_citations
	document.getElementById('right_col').style.display = 'none';					//hide
	if ((Page._citations) && (Page._citations.length > 0))
	{
		if (current_view == 'all') 
		{
			document.getElementById('right_col').style.display = '';					//show
			document.getElementById('view_options_rm').style.display = '';				//show
			document.getElementById('export_citations_rm').style.display = '';			//show
		//	if ((Page.owner != "") && (Page._citations.length > 0))
			if ((Page.loggedIn && Page.hasProxy) && (Page._citations.length >0))
			{
				document.getElementById('collections_rm').style.display = '';				//show
				document.getElementById('compare_merge_citations_rm').style.display = '';	//show
			}
		}
		else if(current_view == 'none')
		{
			document.getElementById('right_col').style.display = 'none';					//hide
		}
		else if(current_view == 'export_citations')
		{
			document.getElementById('right_col').style.display = '';					//show
			document.getElementById('view_options_rm').style.display = 'none';			//hide
			document.getElementById('export_citations_rm').style.display = '';		//show
			//if (Page.owner != "")
			if (Page.loggedIn && Page.hasProxy)
			{
				document.getElementById('collections_rm').style.display = 'none';				//hide
				document.getElementById('compare_merge_citations_rm').style.display = 'none';	//hide
			}
		}
		else 
		{
			alert('default');
			document.getElementById('right_col').style.display = '';					//show
		}
	}
}

Page.print_ViewOptions_rm_div = function()
{
	var html = '<div id="view_options_rm">';
	html += '<table style="margin-top:4px; width:100%; border:2px solid #7D110C;">';
	html += '<th style="background-color:#f8f3d2"><b>View options</b></th>';
	html += '<tr style="background-color:#FFFFFF"><td>';
	html += '<p></p>Format: ' + Page.printFormatMenu() + '<br><br>';
	html += 'Sort order: ' + Page.printSortOrderMenu() + '<br><br>';
	html += Page.showURLs() + 'Show URLs & DOIs<br>';
	html += Page.showNotes() + 'Show Notes<br>';
	html += Page.showAbstracts() + 'Show Abstracts<br>';
	html += Page.highlightMissingInfoCB() + 'Highlight Missing Info<br>';
	html += Page.showCitationID() + 'Show Citation IDs<br>';
	html += Page.setCompactView() + 'Compact View<br>';

//	html += Page.showCollections() + 'Show Collections<br><br>';
	html += '</td></tr></table>';
	html += '</div>';
	return html;
}

Page.print_ExportCitations_rm_div = function()
{
	var html = '<div id="export_citations_rm">';
	html += '<table style="margin-top:4px; width:100%; border:2px solid #7D110C;">';
	html += '<th style="background-color:#f8f3d2"><b>Export citations</b></th>';
	html += '<tr style="background-color:#FFFFFF"><td>';
	html += '<p></p>' + Page.printExportMenu() + '<br><br>';
	html += '</td></tr></table>';
	html += '</div>';
	return html;
}

Page.print_Collections_rm_div = function()
{	
	var html = '<div id="collections_rm">';
	html += '<table style="margin-top:4px; width:100%; border:2px solid #7D110C;">';
	html += '<th style="background-color:#f8f3d2"><b>Collections</b></th>';
	html += '<tr style="background-color:#FFFFFF"><td>';
	html += '<p></p>Add selected citations to<br><br>';
	html += Page.printCollectionNamesMenuForManagingCitations() + '<br><center><input type="button" name="addToCollection" value="Add" onMouseUp="Page.addCitationsToCollection();"></center><br>';
	html += '</td></tr>';
	html += '<tr style="background-color:#FFFFFF"><td>';
	html += '<br><center><input type="button" name="editCollections" value="Manage Collections" onMouseUp="Page.listCollections();"></center><br>';
	html += '</td></tr></table>';
	html += '</div>';
	return html;
}

Page.print_CompareMergeCitations_rm_div = function()
{
	var html = '<div id="compare_merge_citations_rm">';
	html += '<table style="margin-top:4px; width:100%; border:2px solid #7D110C;">';
	html += '<th style="background-color:#f8f3d2"><b>Compare / Merge</b></th>';
	html += '<tr style="background-color:#FFFFFF"><td>';
	html += '<p></p><center><input type="button" name="compareCitations" value="Compare / Merge Citations" onMouseUp="Page.compareCitations();"></center><br>';
	html += '</td></tr></table>';
	html += '</div>';
	return html;
}

Page.panel1_alert_message = function(msg, panel1_open)
{
	var html = '<div class="panel1_message"><p>'+msg+'</p>';
	html += '<input type="button" value="OK" onclick="Page.panel1.hide();" /></div>';
	Page.panel1_open = panel1_open;
	Page.panel1.setBody(html);
	Page.panel1.show();	
}

Page.createToolTips = function()
{
	var tt_array = new Array();
	for (var i=0; i<Page._citations.length; i++)
	{
		if (document.getElementById('verified_'+i).innerHTML != '&nbsp;')
		{
			tt_array.push('verified_'+i);
		}
	}
	
	if(Page.tt) 
	{
		Page.tt.destroy();
	}
	
	var tt = "";
	tt = new YAHOO.widget.Tooltip("tt", { context:tt_array, showdelay:10, text:"This citation is unverified.  To verify the citation, click on its [Edit] link."});

	// Set the text for the tooltip just before we display it.
	tt.contextTriggerEvent.subscribe(
		function(type, args) {

			if(document.getElementById('tt'))
			{
				var highest_index = getNextHighestZindex(document.body);
				document.getElementById('tt').style.zIndex = highest_index;
			}		
		}
	);
	
	Page.tt = tt;
}
