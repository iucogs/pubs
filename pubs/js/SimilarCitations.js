Page.editTwoCitations = function(citations_array_a, citations_array_b)
{	
	Page.panel3_open = 1;
	Page.removeAllTabs();
	Page.citations_array_a = citations_array_a; // need to make global for movement between tabs
	Page.citations_array_b = citations_array_b; // need to make global for movement between tabs
	
	Page.interval_value = window.setInterval("checkScroll(document.getElementById('scrollable_div_a0'), document.getElementById('scrollable_div_b0'))", 1);
	
	var pointer_style = 'class="pointerhand"';
	var merge_div_html = '';
	merge_div_html += '<table width="100%" border="0"><tr>';
	merge_div_html += Page.printBackTD(pointer_style);

	//merge_div_html += '<td align="center"><span class="link pointerhand" onclick="Page.swapPanel3Divs2And3();"><font size="+1">[ More Info ]</font></span><br>';
	merge_div_html += '<td align="center">';
	merge_div_html += '<span class="link pointerhand" onclick="Page.mergeCitations();"><font size="+3">Merge</font></span></td>';
	merge_div_html += Page.printNextTD(pointer_style);
	merge_div_html += '</tr></table>';
	//document.getElementById('merge_div1').innerHTML = merge_div_html;
	document.getElementById('merge_div').innerHTML = merge_div_html;

	// **************************************
	document.getElementById("panel3_div").style.display = '';			//show
	document.getElementById('panel1_div').style.display = 'none'; 		// Hide
	Page.panel_open = 1;  
	 
	Page.create_tabs(tabView_b, '_b', citations_array_b);
	
	Page.create_tabs(tabView_a, '_a', citations_array_a);

	// Print buttons into 'buttons_div' and create tooltips
	Page.printMoveRightButtons(citations_array_b[0].pubtype, '_b0');
	Page.hideEmptyAuthorRowsAtEndOfTable();
	var citation_suffix_of_active_tab = Page.get_citation_suffix_of_active_tab(tabView_b);
	if ((citation_suffix_of_active_tab != '>') && (citation_suffix_of_active_tab != '<'))
	{
		initializeAutocompleteFields(citation_suffix_of_active_tab); 
	}

	Page.panel2.show();	
	
}

Page.create_tabs = function(tabView, citation_suffix, citations_array)
{
	for (var i=0; i < citations_array.length; i++) 
	{
		var citation_in_tab = citations_array[i];
		var full_citation_suffix = citation_suffix+i;

		var tab_content = Page.build_tab_content(citation_in_tab, full_citation_suffix);

		var tab_label = Page.build_tab_label(citation_in_tab, full_citation_suffix);

		var active_flag = (i == 0) ? true : false;
		
		var handleClose = function(e, tab) 
		{
        	YAHOO.util.Event.preventDefault(e);
			if (tabView.get('activeIndex') > tabView.getTabIndex(tab))
			{
				tabView.set('activeIndex', tabView.get('activeIndex')-1);
			}
			if (tabView.get('tabs').length == 1)
			{
				tab.set('label', '&nbsp;');
				tab.set('content', '<div style="height:500px"></div>');
			}
			else
			{
        		tabView.removeTab(tab); 
			}
			Page.alignActiveTabs();	
    	};
		var newTab = new YAHOO.widget.Tab({
			label: tab_label,
			content: tab_content,
			active: active_flag,
		});
	
		// Initialize tabs hiding and showing
		if(i >= Page.numViewableTabsLimit)
			newTab.setStyle('display', 'none'); //hide
		else 
			newTab.setStyle('display', ''); //show 
		
		tabView.addTab(newTab, tabView.get('tabs').length-1); 

		YAHOO.util.Event.on(newTab.getElementsByClassName('close')[0], 'click', handleClose, newTab);
	}
	
	// Show or hide << and >> tabs
	var total_tabs = tabView.get('tabs').length-2; // minus << and >>
	var right_arrow_index = tabView.get('tabs').length-1;  // Last index
	var cur_activeIndex = tabView.get('activeIndex');
	
	if(total_tabs <= Page.numViewableTabsLimit)
	{
		tabView.getTab(0).setStyle('display', 'none'); 
		tabView.getTab(right_arrow_index).setStyle('display', 'none');
	}
	else 
	{
		// Needs to check current active index is larger than Page.numViewableTabsLimit
		if(cur_activeIndex > Page.numViewableTabsLimit)  // Show >> only
			tabView.getTab(0).setStyle('display', '');
		else
			tabView.getTab(right_arrow_index).setStyle('display', ''); 
	}
	
	//tabView.getTab(0).setStyle('display', '');
	//tabView.getTab(right_arrow_index).setStyle('display', ''); 
	
	Page.createCloseTabToolTips(); 	// Create close tooltips for all tabs.
	
	tabView.addListener("activeTabChange", function(e) {

		var label = e.newValue.get('label');
		var prevIndex = tabView.getTabIndex(e.prevValue);
		if ((label != '<<') && (label != '<<'))
		{
			var cit_suffix, cit_suffix2;
			cit_suffix = Page.get_citation_suffix_from_label(label);
	
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
		}
	});	  		
} 

Page.editCitationInTab = function(citation_suffix)
{
	Page.create_working_citation(citation_suffix);
	Page.editOneCitation(-4);
}

Page.swapPanel3Divs2And3 = function()
{
	var suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	//var suffix_b = Page.get_citation_suffix_of_active_tab(tabView_b);
	var panel3_div1_a = 'panel3_div1' + suffix_a;
	//var panel3_div1_b = 'panel3_div1' + suffix_b;
	
	if (document.getElementById(panel3_div1_a).style.display == 'none')			// panel3_div1 is hidden
	{
		document.getElementById('buttons_div').style.display = ''; 	// Show		

		var tab_a = tabView_a.get('tabs');
		for(var i = 0; i < tab_a.length; i++)
		{
			document.getElementById('panel3_div2_a'+i).style.display = 'none'; 	// Hide		
			document.getElementById('panel3_div1_a'+i).style.display = ''; 		// Show
		}
		
		var tab_b = tabView_b.get('tabs');
		for(var i = 0; i < tab_b.length; i++)
		{
			document.getElementById('panel3_div2_b'+i).style.display = 'none'; 	// Hide		
			document.getElementById('panel3_div1_b'+i).style.display = ''; 		// Show
		}
	}
	else 																		// panel3_div1 is shown
	{
		document.getElementById('buttons_div').style.display = 'none'; 	// hide

		var tab_a = tabView_a.get('tabs');
		for(var i = 0; i < tab_a.length; i++)
		{
			document.getElementById('panel3_div1_a'+i).style.display = 'none'; 	// Hide		
			document.getElementById('panel3_div2_a'+i).style.display = ''; 		// Show
		}
		
		var tab_b = tabView_b.get('tabs');
		for(var i = 0; i < tab_b.length; i++)
		{
			document.getElementById('panel3_div1_b'+i).style.display = 'none'; 	// Hide		
			document.getElementById('panel3_div2_b'+i).style.display = ''; 		// Show
		}
	}
}

Page.compareCitations = function() 
{	
	var temp_citation_rows = new Array(); 
	
	for (var i=0; i<Page._citations.length; i++)  
	{
		if (document.getElementById('cb_'+Page._citations[i].citation_id).checked)  {
			temp_citation_rows.push(i);
		}
	}  
	if (temp_citation_rows.length == 2)
	{
		var temp_array1 = new Array(Page._citations[temp_citation_rows[0]]);
		var temp_array2 = new Array(Page._citations[temp_citation_rows[1]]);
		Page.selected_citations = temp_citation_rows;
		Page.editTwoCitations(temp_array1, temp_array2);
	}
	else 
	{
		Page.panel1_alert_message('Please choose exactly 2 citations.', '');
	}
	
}

Page.mergeCitations = function()
{
	var citation_suffix = Page.get_citation_suffix_of_active_tab(tabView_b);
	Page.create_working_citation(citation_suffix);
	Page.pre_merge_id1 = Page.working_citation['citation_id'];

	var citation_suffix_a = Page.get_citation_suffix_of_active_tab(tabView_a);
	Page.pre_merge_id2 = document.getElementById('citation_id'+citation_suffix_a).value;
	Page.editOneCitation(-1);
}

Page.create_working_citation = function(citation_suffix) 
{
	var element_array = new Array("citation_id","user_id","pubtype","cit_key","abstract","keywords","doi","url","address","annote","author","booktitle","chapter","crossref","edition","editor","translator","howpublished","institution","journal","bibtex_key","month","note","number","organization","pages","publisher","location","school","series","title","type","volume","year", "format", "filename", "author0id","author0ln","author0fn","author1id","author1ln","author1fn","author2id","author2ln","author2fn","author3id","author3ln","author3fn","author4id","author4ln","author4fn","author5id","author5ln","author5fn");  //,"raw"
	
	var responseText = '{"citation_id":"-1","user_id":"","pubtype":"article","cit_key":"","abstract":"","keywords":"","doi":"","url":"","address":"","annote":"","author0ln":"","author0fn":"","author0id":"","author1ln":"","author1fn":"","author1id":"","author2ln":"","author2fn":"","author2id":"","author3ln":"","author3fn":"","author3id":"","author4ln":"","author4fn":"","author4id":"","author5ln":"","author5fn":"","author5id":"","booktitle":"","chapter":"","crossref":"","edition":"","editor":"","translator":"","howpublished":"","institution":"","journal":"","bibtex_key":"","month":"","note":"","number":"","organization":"","pages":"","publisher":"","location":"","school":"","series":"","title":"","type":"","volume":"","year":"","raw":"","verified":"","format":"","filename":"","submitter":"","owner":"","entryTime":""}';
																																																																																																																																																																																																																																																															

	Page.working_citation = eval("(" + responseText + ")");
		
	for( var count = 0; count < element_array.length; count++)
	{
		// Check for empty element and skip
	//	if (citation_suffix != "")
	//	{				
			if (document.getElementById(element_array[count]+citation_suffix))
			{
				Page.working_citation[element_array[count]] = document.getElementById(element_array[count]+citation_suffix).value;
			}
			if (element_array[count] == 'author0ln')
				Page.working_citation['author0fn'] = document.getElementById('author0fn'+citation_suffix).value;
			if (element_array[count] == 'author1ln')
				Page.working_citation['author1fn'] = document.getElementById('author1fn'+citation_suffix).value;
			if (element_array[count] == 'author2ln')
				Page.working_citation['author2fn'] = document.getElementById('author2fn'+citation_suffix).value;
			if (element_array[count] == 'author3ln')
				Page.working_citation['author3fn'] = document.getElementById('author3fn'+citation_suffix).value;
			if (element_array[count] == 'author4ln')
				Page.working_citation['author4fn'] = document.getElementById('author4fn'+citation_suffix).value;
			if (element_array[count] == 'author5ln')
				Page.working_citation['author5fn'] = document.getElementById('author5fn'+citation_suffix).value;
		}
	//}
	
	if (citation_suffix == "")
	{
		Page.working_citation['verified'] = Page.getCheckedValue(document.forms['cForm'].elements['verified']);
	}
	else
	{
		// Update attached_filename input for saving.
		document.getElementById('attached_filename').value = document.getElementById('attached_filename'+citation_suffix).value;
	}
}

Page.get_citation_suffix_from_label = function(label)
{
	var return_val = '';
	if (label == '&nbsp;')
	{
		return return_val; 
	}
	
	if ((label.substr(53,1) == '<') || (label.substr(53,1) == '>'))
	{
		return_val = label.substr(53,1);
	}
	else if (label.substr(56,1) == '"')
	{
		return_val = label.substr(53,3);
	}
	else
	{
		return_val = label.substr(53,4);
	}	
	
	return return_val;
}

// Previous function. The wrong activeIndex returned during removeTab()
//Page.get_citation_suffix_of_active_tab = function(tabView)
//{
//	var label = tabView.getTab(tabView.get('activeIndex')).get('label');
//	return Page.get_citation_suffix_from_label(label);
//}

// TO-DO: Use this instead of using substring in Page.get_citation_suffix_from_label();
Page.get_citation_suffix_of_active_tab2 = function(tabView)
{
	var labelEl = tabView.get('activeTab').get('labelEl');
	return labelEl.firstChild.value;  // The value of hidden input 'tab_citation_suffix'
}

Page.get_citation_suffix_of_active_tab = function(tabView)
{
	var label = tabView.get('activeTab').get('label');
	return Page.get_citation_suffix_from_label(label); 
}

Page.get_citation_suffix_for_newly_added_tab = function(tabView)
{
	var label = tabView.getTab(tabView.get('tabs').length-2).get('label');
	var full_suffix = Page.get_citation_suffix_from_label(label);
	var citation_suffix = full_suffix.substr(0,2); // get _a or _b
	var citation_suffix_num = full_suffix.substr(2); // get number

	citation_suffix_num = parseInt(citation_suffix_num);
	citation_suffix_num++;
	return citation_suffix+citation_suffix_num;
}

Page.removeAllTabs = function()
{
	tabView_a.getTab(0).setStyle('display', 'none'); //hide
	tabView_a.getTab(tabView_a.get('tabs').length-1).setStyle('display', 'none'); //hide
	
	tabView_b.getTab(0).setStyle('display', 'none'); //hide
	tabView_b.getTab(tabView_b.get('tabs').length-1).setStyle('display', 'none'); //hide

	for (var i=1; i<=tabView_a.get('tabs').length-2; i++)
	{
		tabView_a.removeTab(tabView_a.getTab(i)); 
	} 
		
	for (var i=1; i<=tabView_b.get('tabs').length-2; i++)
	{
		tabView_b.removeTab(tabView_b.getTab(i)); 
	} 
	
	Page.citations_array_a = "";
	Page.citations_array_b = "";
}

Page.createCloseTabToolTips = function()
{
	var close_buttons_tt_array = new Array();
	
	if ((tabView_a.get('tabs').length > 0) && (tabView_a.getTab(0).get('label') != '&nbsp;'))
	{
		for (var i=0; i < tabView_a.get('tabs').length; i++)
		{
			var citation_suffix_num = tabView_a.getTab(i).get('label').substr(55,1); // 
			close_buttons_tt_array.push("close_x_a" + citation_suffix_num);
		} 
	}
	
	if ((tabView_b.get('tabs').length > 0) && (tabView_b.getTab(0).get('label') != '&nbsp;'))
	{
		for (var i=0; i < tabView_b.get('tabs').length; i++)
		{
			var citation_suffix_num = tabView_b.getTab(i).get('label').substr(55,1); // 
			close_buttons_tt_array.push("close_x_b" + citation_suffix_num);
		} 
	}
	
	if(Page.close_buttons_tt) 
	{
		Page.close_buttons_tt.destroy();
	}
	
	var close_buttons_tt = "";
	//Page.close_buttons_tt = new YAHOO.widget.Tooltip("close_buttons_tt", { context:close_buttons_tt_array, text:"move button", zIndex: Page.zCounter, iframe: true});
	close_buttons_tt = new YAHOO.widget.Tooltip("close_buttons_tt", { context:close_buttons_tt_array, showdelay:10, text:"Close"});

	// Set the text for the tooltip just before we display it.
	close_buttons_tt.contextTriggerEvent.subscribe(
		function(type, args) {
			//var context = args[0];
			//var field_id = context.id;
			//this.cfg.setProperty("text", "Close " + field_id);

			if(document.getElementById('close_buttons_tt'))
			{
				var highest_index = getNextHighestZindex(document.body);
				document.getElementById('close_buttons_tt').style.zIndex = highest_index;
			}		
		}
	);
	
	Page.close_buttons_tt = close_buttons_tt;
}

Page.createMoveButtonToolTips = function(move_buttons_tt_array)
{	
	if(Page.move_buttons_tt) 
	{
		Page.move_buttons_tt.destroy();
	}
	
	var move_buttons_tt = "";
	//Page.move_buttons_tt = new YAHOO.widget.Tooltip("move_buttons_tt", { context:move_buttons_tt_array, text:"move button", zIndex: Page.zCounter, iframe: true});
	move_buttons_tt = new YAHOO.widget.Tooltip("move_buttons_tt", { context:move_buttons_tt_array, showdelay:5});

	// Set the text for the tooltip just before we display it.
	move_buttons_tt.contextTriggerEvent.subscribe(
		function(type, args) {
			var context = args[0];
			//var field_id = context.id.substring(0, context.id.length-18);
			var field_name = context.name;
			var tt_str = "to the right";
			if(Page.author_and_constant_fields_arr[field_name]) {
				tt_str = Page.author_and_constant_fields_arr[field_name];
			}
			else {
				tt_str = Page.fields_arr[field_name][0];
			}
			
			this.cfg.setProperty("text", "Move " + tt_str);

			if(document.getElementById('move_buttons_tt'))
			{
				var highest_index = getNextHighestZindex(document.body);
				document.getElementById('move_buttons_tt').style.zIndex = highest_index;
			}		
		}
	);
	
	Page.move_buttons_tt = move_buttons_tt;
}


Page.move_tab_right = function(citation_id)
{
	var full_citation_suffix = '';
	if (tabView_a.get('tabs').length > 0)
	{
		Page.movingTab = true;
		var activeIndex = tabView_a.get('activeIndex');
		
		var tab_to_be_moved = tabView_a.get('activeTab');
		
		var citation_in_tab = Page.citations_array_a[activeIndex-1];
		
		if ((tabView_b.get('tabs').length > 2) && (tabView_b.getTab(1).get('label') != '&nbsp;'))
		{
			full_citation_suffix = Page.get_citation_suffix_for_newly_added_tab(tabView_b);  // on b side
		}
		else
		{
			full_citation_suffix = '_b0';
		}
			
		tabView_a.removeTab(tab_to_be_moved);

		if (tabView_a.get('tabs').length == 2)
		{
			Page.add_empty_tab(tabView_a);
		}
		
		Page.citations_array_b.push(citation_in_tab);
		Page.citations_array_a.splice(activeIndex-1, 1);
				
		tab_to_be_moved.set('content', Page.build_tab_content(citation_in_tab, full_citation_suffix));
		tab_to_be_moved.set('label', Page.build_tab_label(citation_in_tab, full_citation_suffix));
		
		if ((tabView_b.get('tabs').length == 3) && (tabView_b.getTab(1).get('label') == '&nbsp;'))
		{
			tabView_b.removeTab(tabView_b.getTab(1)); 
		}
		
		tabView_b.addTab(tab_to_be_moved, tabView_b.get('tabs').length-1);
		tabView_b.set('activeIndex', tabView_b.get('tabs').length-2);
		
		if (tabView_a.get('tabs').length > 2)
		{
			if(activeIndex == 1)
				tabView_a.set('activeIndex', activeIndex);
			else
				tabView_a.set('activeIndex', activeIndex-1);
		}
		
		Page.createCloseTabToolTips(); // recreate all close tooltips - redundant but ok
		Page.movingTab = false;
		Page.alignActiveTabs();
	}
}

Page.move_tab_left = function(citation_id)
{
	var full_citation_suffix = '';
	if (tabView_b.get('tabs').length > 0)
	{
		Page.movingTab = true;

		var activeIndex = tabView_b.get('activeIndex');
		var tab_to_be_moved = tabView_b.getTab(activeIndex);

		var temp = Page.get_citation_suffix_of_active_tab(tabView_b);

		Page.create_working_citation(temp);
		
		var citation_in_tab = Page.working_citation;
		
		if ((tabView_a.get('tabs').length > 2) && (tabView_a.getTab(1).get('label') != '&nbsp;'))
		{
			full_citation_suffix = Page.get_citation_suffix_for_newly_added_tab(tabView_a);  // on a side
		}
		else
		{
			full_citation_suffix = '_a0';
		}
		
		Page.citations_array_a.push(citation_in_tab);
		Page.citations_array_b.splice(activeIndex-1, 1);
		
		tab_to_be_moved.set('content', Page.build_tab_content(citation_in_tab, full_citation_suffix));
		tab_to_be_moved.set('label', Page.build_tab_label(citation_in_tab, full_citation_suffix));
		
		tabView_b.removeTab(tab_to_be_moved);	
		
		if (tabView_b.get('tabs').length == 2)
		{
			Page.add_empty_tab(tabView_b);
		}

		if ((tabView_a.get('tabs').length == 3) && (tabView_a.getTab(1).get('label') == '&nbsp;'))
		{
			tabView_a.removeTab(tabView_a.getTab(1)); 
		}
		
		tabView_a.addTab(tab_to_be_moved, tabView_a.get('tabs').length-1);
		tabView_a.set('activeIndex', tabView_a.get('tabs').length-2);
		if (tabView_b.get('tabs').length > 2)
		{
			if(activeIndex == 1)
				tabView_b.set('activeIndex', activeIndex);
			else
				tabView_b.set('activeIndex', activeIndex-1);
		}
		Page.createCloseTabToolTips(); // recreate all close tooltips - redundant but ok
		Page.movingTab = false;
		Page.alignActiveTabs();
	}
}

Page.add_empty_tab = function(tabView)
{
	var newTab = new YAHOO.widget.Tab({
		label: '&nbsp;',
		content: '<div style="height:500px"></div>',
		active: true,
	});
	
	tabView.addTab(newTab, 1);
}


Page.build_tab_label = function(citation_in_tab, full_citation_suffix)
{
	var tab_label = '';
	if ((full_citation_suffix == '<') || (full_citation_suffix == '>'))
	{
 		tab_label += '<input type="hidden" id="tab_citation_suffix" value="' + full_citation_suffix + '"><span id="close_x' + full_citation_suffix + '" class="close">' + full_citation_suffix + '</span>';
	}
	else
	{
		tab_label += '<input type="hidden" id="tab_citation_suffix" value="' + full_citation_suffix + '">' + citation_in_tab.citation_id + '&nbsp;&nbsp;<span id="close_x' + full_citation_suffix + '" class="close">x</span>';
	}
	return tab_label;
}

Page.build_tab_content = function(citation_in_tab, full_citation_suffix)
{
	var tab_content = '';
	
	var fieldFlag = "";
	if (full_citation_suffix.indexOf("_a") >= 0) // used for pubtype only
	{
		fieldFlag = "text";
	} 

	/******** panel3_div1 ********/
	tab_content += '<div id="panel3_div1'+full_citation_suffix+'">';
//	tab_content += '<div id="tab_div_padding'+full_citation_suffix+'" style="height:0px;"></div>';

	tab_content += '<div id="toolbar_div'+full_citation_suffix+'">';
	tab_content += '<table width="100%"><tr><td>';
	tab_content += '<input type="button" value="Edit/Save" onclick="Page.editCitationInTab(\'' + full_citation_suffix + '\');">';
	tab_content += '<input type="button" value="Delete" onclick="Page.tab_deleting_citation_suffix = \'' + full_citation_suffix + '\'; Page.deleteCitation_request(document.getElementById(\'citation_id' + full_citation_suffix + '\').value);">';

	if (full_citation_suffix.indexOf("_a") >= 0)
	{				
		tab_content += '<input type="button" value="Move Tab Right" onclick="Page.move_tab_right(document.getElementById(\'citation_id' + full_citation_suffix + '\').value);">';
	}
	if (full_citation_suffix.indexOf("_b") >= 0)
	{				
		tab_content += '<input type="button" value="Move Tab Left" onclick="Page.move_tab_left(document.getElementById(\'citation_id' + full_citation_suffix + '\').value);">';
	}
	tab_content += '</td></tr></table>';
	tab_content += '</div>';
	
	var scrollable_div_height = Page.getViewportHeightForScrollableDivs();
	tab_content += '<div id="scrollable_div'+full_citation_suffix+'" style="height: '+scrollable_div_height+'px;overflow: auto; border: 2px solid black; padding:2px">';
//	tab_content += '<div id="top_div'+full_citation_suffix+'" style="border: 2px solid green; padding:0px; margin-bottom:3px">' + Page.enterRawInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '<div id="top_div'+full_citation_suffix+'" class="panel_div">' + Page.enterRawInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '<div id="author_fields'+full_citation_suffix+'"  class="panel_div">' + Page.enterAuthorInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '<div id="constantByPubtype_fields'+full_citation_suffix+'" class="panel_div">' + Page.enterConstantByPubtypeInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '<div id="pubtype_div'+full_citation_suffix+'" class="panel_div">' + Page.pubtypeMenu(citation_in_tab.pubtype, fieldFlag, full_citation_suffix) + '</div>';
	tab_content += '<div id="changingByPubtype_fields'+full_citation_suffix+'" class="panel_div">' + Page.enterChangingByPubtypeInfo(citation_in_tab, citation_in_tab.pubtype, "", full_citation_suffix) + '</div>';
//	tab_content += '</div>';
	
	/******** panel3_div2 ********/
//	tab_content += '<div id="panel3_div2'+full_citation_suffix+'" style="display:none;">';  // Hidden by default.
//	tab_content += '<div id="top_div'+full_citation_suffix+'">' + Page.enterRawInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '<div id="abstract_div'+full_citation_suffix+'" class="panel_div">' + Page.enterAbstractKeywords(citation_in_tab.abstract, "", full_citation_suffix, 'abstract', 'Abstract') + '</div>';
	
	tab_content += '<div id="keyword_div'+full_citation_suffix+'" class="panel_div">' + Page.enterAbstractKeywords(citation_in_tab.keywords, "", full_citation_suffix, 'keywords', 'Keywords') + '</div>'; 

	tab_content += '<div id="additional_fields'+full_citation_suffix+'" class="panel_div">' + Page.enterAdditionalInfo(citation_in_tab, "", full_citation_suffix) + '</div>';
	tab_content += '</div>';
	tab_content += '</div>';  // Currently closing panel3_div1 div

	return tab_content;
}

Page.getViewportHeightForScrollableDivs = function()
{
	return YAHOO.util.Dom.getViewportHeight() - 200;
}

Page.getTabAsDOMElement = function(tabView_container, num)
{
	document.getElementById(tabView_container).childNodes[0].childNodes[0].childNodes[num];
}